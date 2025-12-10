<?php

namespace App\Http\Controllers\Almacen;

use App\Http\Controllers\Controller;
use App\Models\MaterialReception;
use App\Models\Terminal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage; // Importante para el manejo de archivos
use App\Exports\MaterialReceptionsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class MaterialReceptionController extends Controller
{
    // Muestra la lista de recepciones con filtros
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $filterMonth = $request->input('month');
        $filterYear = $request->input('year');
        $filterTerminal = $request->input('terminal_id');
        $search = $request->input('search');
        $viewDeleted = $request->boolean('view_deleted');

        $query = MaterialReception::with(['user', 'terminal']); 

        // Filtros de Seguridad por Rol
        if ($user->role->name !== 'Administrador') {
            $query->where('terminal_id', $user->terminal_id);
        } else {
            if ($filterTerminal) {
                $query->where('terminal_id', $filterTerminal);
            }
        }

        if ($filterMonth) $query->whereMonth('reception_date', $filterMonth);
        if ($filterYear) $query->whereYear('reception_date', $filterYear);
        
        // Buscador mejorado
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('provider', 'like', "%{$search}%")
                  ->orWhere('purchase_order', 'like', "%{$search}%")
                  ->orWhere('item_number', 'like', "%{$search}%");
            });
        }

        if ($viewDeleted) {
            $query->onlyTrashed();
        }

        $receptions = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        
        $terminals = Terminal::all();

        return view('almacen.material-receptions.index', [
            'receptions' => $receptions,
            'selectedMonth' => $filterMonth,
            'selectedYear' => $filterYear,
            'selectedTerminal' => $filterTerminal,
            'searchTerm' => $search,
            'terminals' => $terminals,
            'viewDeleted' => $viewDeleted,
        ]);
    }

    // Muestra el formulario de creación
    public function create()
    {
        $user = Auth::user();
        $terminals = $user->role->name === 'Administrador' ? Terminal::all() : Terminal::where('id', $user->terminal_id)->get();
        
        // Cargar consumibles con lógica de rol
        $consumablesQuery = \App\Models\Consumable::where('is_active', true);
        if ($user->role->name !== 'Administrador') {
            $consumablesQuery->where('terminal_id', $user->terminal_id);
        }
        $consumables = $consumablesQuery->orderBy('name')->get();
        
        // Cargar ubicaciones de inventario con lógica de rol
        $locationsQuery = \App\Models\InventoryLocation::where('is_active', true);
        if ($user->role->name !== 'Administrador') {
            $locationsQuery->where('terminal_id', $user->terminal_id);
        }
        $inventoryLocations = $locationsQuery->orderBy('code')->get();
        
        return view('almacen.material-receptions.create', compact('terminals', 'consumables', 'inventoryLocations'));
    }

    // Guarda la nueva recepción
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Validación de datos condicional
        $validatedData = $request->validate([
            'terminal_id' => ['required', $user->role->name === 'Administrador' ? Rule::exists('terminals', 'id') : Rule::in([$user->terminal_id])],
            'material_type' => ['required', Rule::in(['CONSUMIBLE', 'SPARE_PART'])],
            'reception_date' => ['required', 'date'],
            
            // Campos CONSUMIBLE
            'consumable_id' => ['nullable', 'exists:consumables,id'],
            'inventory_location_id' => ['nullable', 'exists:inventory_locations,id'],
            
            // Campos SPARE_PART  
            'item_number' => ['nullable', 'required_if:material_type,SPARE_PART', 'string', 'max:100'],
            'sap_confirmation' => ['nullable', 'string', 'max:100'],
            'work_order' => ['nullable', 'string', 'max:100'], // Ahora es texto
            
            // Campos comunes
            'description' => ['nullable', 'string', 'max:255'],
            'provider' => ['required', 'string', 'max:255'],
            'purchase_order' => ['required', 'string', 'max:100'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'quality_certificate' => ['nullable', 'boolean'],

            // Archivos (PDF hasta 10MB)
            'invoice_file' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'remission_file' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'certificate_file' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        // Si seleccionó consumible, sincronizar descripción
        if (!empty($validatedData['consumable_id'])) {
            $consumable = \App\Models\Consumable::find($validatedData['consumable_id']);
            $validatedData['description'] = $consumable->name;
        }

        $validatedData['user_id'] = $user->id;
        $validatedData['quality_certificate'] = $request->boolean('quality_certificate');
        
        // LÓGICA DE STATUS según tipo de material
        if ($validatedData['material_type'] === 'CONSUMIBLE') {
            $validatedData['status'] = 'COMPLETO';
        } else {
            // Spare Parts: PENDIENTE_OT si falta OT o SAP
            $hasWorkOrder = !empty($validatedData['work_order']);
            $hasSAP = !empty($validatedData['sap_confirmation']);
            $validatedData['status'] = ($hasWorkOrder && $hasSAP) ? 'COMPLETO' : 'PENDIENTE_OT';
        }
        
        // GUARDADO DE ARCHIVOS
        if ($request->hasFile('invoice_file')) {
            $validatedData['invoice_path'] = $request->file('invoice_file')->store('receptions/invoices', 'public');
        }
        if ($request->hasFile('remission_file')) {
            $validatedData['remission_path'] = $request->file('remission_file')->store('receptions/remissions', 'public');
        }
        if ($request->hasFile('certificate_file')) {
            $validatedData['certificate_path'] = $request->file('certificate_file')->store('receptions/certificates', 'public');
        }

        $reception = MaterialReception::create($validatedData);

        // ACTUALIZAR INVENTARIO
        if ($reception->consumable_id) {
            $consumable = \App\Models\Consumable::find($reception->consumable_id);
            $consumable->addStock(
                quantity: $reception->quantity,
                referenceType: MaterialReception::class,
                referenceId: $reception->id,
                notes: "Entrada de material - OC: {$reception->purchase_order} - Proveedor: {$reception->provider}"
            );
        }

        return redirect()->route('material-receptions.index')->with('success', 'Recepción registrada exitosamente. ' . ($reception->consumable_id ? 'Inventario actualizado.' : ''));
    }
    
    // ... viewFile ...

    // ... edit ...

    // Actualiza la recepción
    public function update(Request $request, MaterialReception $recepcione)
    {
        $user = Auth::user();
        $validatedData = $request->validate([
            'terminal_id' => ['required', $user->role->name === 'Administrador' ? Rule::exists('terminals', 'id') : Rule::in([$user->terminal_id])],
            'material_type' => ['required', Rule::in(['CONSUMIBLE', 'SPARE_PART'])],
            'description' => ['nullable', 'string', 'max:255'],
            'provider' => ['required', 'string', 'max:255'],
            'purchase_order' => ['required', 'string', 'max:100'],
            'reception_date' => ['required', 'date'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'sap_confirmation' => ['nullable', 'string', 'max:100'],
            'item_number' => ['nullable', 'required_if:material_type,SPARE_PART', 'string', 'max:100'],
            'inventory_location_id' => ['nullable', 'exists:inventory_locations,id'],
            'quality_certificate' => ['nullable', 'boolean'],
            'work_order' => ['nullable', 'string', 'max:100'], // Ahora texto
        ]);

        $validatedData['quality_certificate'] = $request->boolean('quality_certificate');
        
        // LÓGICA DE STATUS según tipo de material
        if ($validatedData['material_type'] === 'CONSUMIBLE') {
            $validatedData['status'] = 'COMPLETO';
        } else {
            // Spare Parts: verificar OT y SAP
            $hasWorkOrder = !empty($validatedData['work_order']);
            $hasSAP = !empty($validatedData['sap_confirmation']);
            $validatedData['status'] = ($hasWorkOrder && $hasSAP) ? 'COMPLETO' : 'PENDIENTE_OT';
        }

        $recepcione->update($validatedData);
        
        $statusMessage = $validatedData['status'] === 'COMPLETO' ? ' Estado actualizado a COMPLETO.' : '';
        return redirect()->route('material-receptions.index')->with('success', 'Recepción actualizada exitosamente.' . $statusMessage);
    }

    // Eliminar (Cancelar) recepción
    public function destroy(Request $request, MaterialReception $recepcione)
    {
        $request->validate(['cancellation_reason' => 'required|string|max:255']);
        $recepcione->cancellation_reason = $request->cancellation_reason;
        $recepcione->save();
        $recepcione->delete();
        return redirect()->route('material-receptions.index')->with('success', 'Registro cancelado correctamente.');
    }

    public function export(Request $request)
    {
        return Excel::download(new MaterialReceptionsExport($request), 'reporte_recepciones.xlsx');
    }

    public function downloadPDF(MaterialReception $recepcione)
    {
        $pdf = Pdf::loadView('almacen.material-receptions.vale_entrada_pdf', ['reception' => $recepcione]);
        return $pdf->stream('vale_entrada_' . $recepcione->id . '.pdf');
    }
}
