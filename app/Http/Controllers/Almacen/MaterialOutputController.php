<?php

namespace App\Http\Controllers\Almacen;

use App\Http\Controllers\Controller;
use App\Models\MaterialOutput;
use App\Models\Terminal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Exports\MaterialOutputsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class MaterialOutputController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Filtros
        $filterMonth = $request->input('month');
        $filterYear = $request->input('year');
        $filterTerminal = $request->input('terminal_id');
        $search = $request->input('search'); // Nuevo: Buscador
        
        // Filtro especial: ¿Ver cancelados?
        $viewDeleted = $request->boolean('view_deleted');

        $query = MaterialOutput::with(['user', 'terminal']);

        // Lógica de Roles
        if ($user->role->name !== 'Administrador') {
            $query->where('terminal_id', $user->terminal_id);
        } else {
            if ($filterTerminal) {
                $query->where('terminal_id', $filterTerminal);
            }
        }

        // Filtros de Fecha
        if ($filterMonth) $query->whereMonth('output_date', $filterMonth);
        if ($filterYear) $query->whereYear('output_date', $filterYear);
        
        // Lógica de Buscador (NUEVO)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('receiver_name', 'like', "%{$search}%")
                  ->orWhere('item_number', 'like', "%{$search}%")
                  ->orWhere('work_order', 'like', "%{$search}%");
            });
        }

        // Lógica de Cancelados
        if ($viewDeleted) {
            $query->onlyTrashed();
        }
        
        // CAMBIO: Usamos paginate en lugar de get
        // withQueryString asegura que los filtros se mantengan al cambiar de página
        $outputs = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        
        $terminals = Terminal::all();

        return view('almacen.material-outputs.index', [
            'outputs' => $outputs,
            'selectedMonth' => $filterMonth,
            'selectedYear' => $filterYear,
            'selectedTerminal' => $filterTerminal,
            'searchTerm' => $search, // Pasamos el término para rellenar el input
            'terminals' => $terminals,
            'viewDeleted' => $viewDeleted,
        ]);
    }

    public function create()
    {
        $user = Auth::user();
        $terminals = $user->role->name === 'Administrador' 
                        ? Terminal::all() 
                        : Terminal::where('id', $user->terminal_id)->get();

        // Cargar consumibles activos con stock disponible
        $consumables = \App\Models\Consumable::where('terminal_id', $user->terminal_id)
                                              ->where('is_active', true)
                                              ->where('current_stock', '>', 0)
                                              ->orderBy('name')
                                              ->get();

        return view('almacen.material-outputs.create', compact('terminals', 'consumables'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $validatedData = $request->validate([
            'terminal_id' => ['required', $user->role->name === 'Administrador' ? Rule::exists('terminals', 'id') : Rule::in([$user->terminal_id])],
            'material_type' => ['required', Rule::in(['CONSUMIBLE', 'SPARE_PART'])],
            
            // NUEVO: consumable_id es opcional
            'consumable_id' => ['nullable', 'exists:consumables,id'],
            'description' => ['nullable', 'required_without:consumable_id', 'string', 'max:255'],
            
            'output_date' => ['required', 'date'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'receiver_name' => ['required', 'string', 'max:255'],
            'item_number' => ['nullable', 'required_if:material_type,SPARE_PART', 'string', 'max:100'],
            'receiver_signature' => ['required', 'string'],
            'deliverer_signature' => ['required', 'string'],
            'work_order' => ['nullable', 'string', 'max:100'],
        ]);

        // Si seleccionó consumible, sincronizar descripción y VALIDAR STOCK
        if ($validatedData['consumable_id']) {
            $consumable = \App\Models\Consumable::findOrFail($validatedData['consumable_id']);
            $validatedData['description'] = $consumable->name;
            
            // 🔒 VALIDACIÓN DE STOCK DISPONIBLE
            if ($consumable->current_stock < $validatedData['quantity']) {
                return back()->withErrors([
                    'quantity' => "Stock insuficiente. Disponible: {$consumable->current_stock} {$consumable->unit_of_measure}"
                ])->withInput();
            }
        }

        $validatedData['user_id'] = $user->id;
        $validatedData['status'] = $request->filled('work_order') ? 'PENDIENTE_SAP' : 'PENDIENTE_OT';

        $output = MaterialOutput::create($validatedData);

        // 🔥 REDUCIR STOCK DEL INVENTARIO (Si hay consumible vinculado)
        if ($output->consumable_id) {
            $consumable = \App\Models\Consumable::find($output->consumable_id);
            $consumable->removeStock(
                quantity: $output->quantity,
                referenceType: MaterialOutput::class,
                referenceId: $output->id,
                notes: "Salida - Receptor: {$output->receiver_name}" . ($output->work_order ? " - OT: {$output->work_order}" : "")
            );
        }

        return redirect()->route('material-outputs.index')
                        ->with('success', 'Salida de material registrada exitosamente. ' . ($output->consumable_id ? 'Inventario actualizado.' : ''));
    }

    public function edit(MaterialOutput $salida)
    {
        $user = Auth::user();
        $terminals = $user->role->name === 'Administrador' ? Terminal::all() : Terminal::where('id', $user->terminal_id)->get();
        return view('almacen.material-outputs.edit', compact('salida', 'terminals'));
    }

    public function update(Request $request, MaterialOutput $salida)
    {
        $user = Auth::user();
        $validatedData = $request->validate([
            'terminal_id' => ['required', $user->role->name === 'Administrador' ? Rule::exists('terminals', 'id') : Rule::in([$user->terminal_id])],
            'material_type' => ['required', Rule::in(['CONSUMIBLE', 'SPARE_PART'])],
            'description' => ['required', 'string', 'max:255'],
            'output_date' => ['required', 'date'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'receiver_name' => ['required', 'string', 'max:255'],
            'item_number' => ['nullable', 'required_if:material_type,SPARE_PART', 'string', 'max:100'],
            'work_order' => ['nullable', 'string', 'max:100'],
            'sap_confirmation' => ['nullable', 'string', 'max:100'],
        ]);

        $newStatus = $salida->status;
        if ($salida->status === 'PENDIENTE_OT' && $request->filled('work_order')) $newStatus = 'PENDIENTE_SAP';
        if ($request->filled('sap_confirmation')) $newStatus = 'COMPLETO';
        $validatedData['status'] = $newStatus;

        $salida->update($validatedData);

        return redirect()->route('material-outputs.index')->with('success', 'Registro de salida actualizado exitosamente.');
    }

    public function destroy(Request $request, MaterialOutput $salida)
    {
        $request->validate(['cancellation_reason' => 'required|string|max:255']);
        $salida->cancellation_reason = $request->cancellation_reason;
        $salida->save();
        $salida->delete();

        return redirect()->route('material-outputs.index')->with('success', 'Registro cancelado correctamente.');
    }

    public function export(Request $request)
    {
        return Excel::download(new MaterialOutputsExport($request), 'reporte_salidas_material.xlsx');
    }

    public function downloadPDF(MaterialOutput $salida)
    {
        $pdf = Pdf::loadView('almacen.material-outputs.vale_pdf', ['output' => $salida]);
        return $pdf->stream('vale_salida_' . $salida->id . '.pdf');
    }
}