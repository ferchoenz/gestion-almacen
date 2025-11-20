<?php

namespace App\Http\Controllers\Almacen;

use App\Http\Controllers\Controller;
use App\Models\MaterialReception;
use App\Models\Terminal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Exports\MaterialReceptionsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class MaterialReceptionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $filterMonth = $request->input('month');
        $filterYear = $request->input('year');
        $filterTerminal = $request->input('terminal_id');
        $search = $request->input('search'); // Nuevo Buscador
        $viewDeleted = $request->boolean('view_deleted');

        $query = MaterialReception::with(['user', 'terminal']); 

        if ($user->role->name !== 'Administrador') {
            $query->where('terminal_id', $user->terminal_id);
        } else {
            if ($filterTerminal) {
                $query->where('terminal_id', $filterTerminal);
            }
        }

        if ($filterMonth) $query->whereMonth('reception_date', $filterMonth);
        if ($filterYear) $query->whereYear('reception_date', $filterYear);
        
        // Buscador (NUEVO)
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

        // Paginación (NUEVO)
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

    // ... create, store, edit, update, destroy (IGUAL QUE ANTES) ...
    // Te vuelvo a poner las funciones para que el archivo esté completo y funcional al copiar

    public function create() {
        $user = Auth::user();
        $terminals = $user->role->name === 'Administrador' ? Terminal::all() : Terminal::where('id', $user->terminal_id)->get();
        return view('almacen.material-receptions.create', compact('terminals'));
    }

    public function store(Request $request) {
        $user = Auth::user();
        $validatedData = $request->validate([
            'terminal_id' => ['required', $user->role->name === 'Administrador' ? Rule::exists('terminals', 'id') : Rule::in([$user->terminal_id])],
            'material_type' => ['required', Rule::in(['CONSUMIBLE', 'SPARE_PART'])],
            'description' => ['required', 'string', 'max:255'],
            'provider' => ['required', 'string', 'max:255'],
            'purchase_order' => ['required', 'string', 'max:100'],
            'reception_date' => ['required', 'date'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'sap_confirmation' => ['nullable', 'string', 'max:100'],
            'item_number' => ['nullable', 'required_if:material_type,SPARE_PART', 'string', 'max:100'],
            'storage_location' => ['nullable', 'string', 'max:255'],
            'quality_certificate' => ['nullable', 'boolean'], 
        ]);
        $validatedData['user_id'] = $user->id;
        $validatedData['quality_certificate'] = $request->boolean('quality_certificate');
        $validatedData['status'] = $request->filled('storage_location') ? 'COMPLETO' : 'PENDIENTE_UBICACION';
        MaterialReception::create($validatedData);
        return redirect()->route('material-receptions.index')->with('success', 'Recepción registrada exitosamente.');
    }

    public function edit(MaterialReception $recepcione) {
        $user = Auth::user();
        $terminals = $user->role->name === 'Administrador' ? Terminal::all() : Terminal::where('id', $user->terminal_id)->get();
        return view('almacen.material-receptions.edit', compact('recepcione', 'terminals'));
    }

    public function update(Request $request, MaterialReception $recepcione) {
        $user = Auth::user();
        $validatedData = $request->validate([
            'terminal_id' => ['required', $user->role->name === 'Administrador' ? Rule::exists('terminals', 'id') : Rule::in([$user->terminal_id])],
            'material_type' => ['required', Rule::in(['CONSUMIBLE', 'SPARE_PART'])],
            'description' => ['required', 'string', 'max:255'],
            'provider' => ['required', 'string', 'max:255'],
            'purchase_order' => ['required', 'string', 'max:100'],
            'reception_date' => ['required', 'date'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'sap_confirmation' => ['nullable', 'required_if:material_type,SPARE_PART', 'string', 'max:100'],
            'item_number' => ['nullable', 'required_if:material_type,SPARE_PART', 'string', 'max:100'],
            'storage_location' => ['nullable', 'string', 'max:255'],
            'quality_certificate' => ['nullable', 'boolean'], 
        ]);
        $validatedData['quality_certificate'] = $request->has('quality_certificate') ? true : false;
        $validatedData['status'] = $request->filled('storage_location') ? 'COMPLETO' : 'PENDIENTE_UBICACION';
        $recepcione->update($validatedData);
        return redirect()->route('material-receptions.index')->with('success', 'Recepción actualizada exitosamente.');
    }

    public function destroy(Request $request, MaterialReception $recepcione) {
        $request->validate(['cancellation_reason' => 'required|string|max:255']);
        $recepcione->cancellation_reason = $request->cancellation_reason;
        $recepcione->save();
        $recepcione->delete();
        return redirect()->route('material-receptions.index')->with('success', 'Registro cancelado correctamente.');
    }

    public function export(Request $request) {
        return Excel::download(new MaterialReceptionsExport($request), 'reporte_recepciones.xlsx');
    }

    public function downloadPDF(MaterialReception $recepcione) {
        $pdf = Pdf::loadView('almacen.material-receptions.vale_entrada_pdf', ['reception' => $recepcione]);
        return $pdf->stream('vale_entrada_' . $recepcione->id . '.pdf');
    }
}