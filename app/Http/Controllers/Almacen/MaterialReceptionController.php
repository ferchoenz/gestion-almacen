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
        return view('almacen.material-receptions.create', compact('terminals'));
    }

    // Guarda la nueva recepción
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Validación de datos
        $validatedData = $request->validate([
            'terminal_id' => ['required', $user->role->name === 'Administrador' ? Rule::exists('terminals', 'id') : Rule::in([$user->terminal_id])],
            'material_type' => ['required', Rule::in(['CONSUMIBLE', 'SPARE_PART'])],
            'description' => ['required', 'string', 'max:255'],
            'provider' => ['required', 'string', 'max:255'],
            'purchase_order' => ['required', 'string', 'max:100'],
            'reception_date' => ['required', 'date'],
            'quantity' => ['required', 'numeric', 'min:0'],
            
            // SAP es Opcional al crear (nullable)
            'sap_confirmation' => ['nullable', 'string', 'max:100'],
            
            'item_number' => ['nullable', 'required_if:material_type,SPARE_PART', 'string', 'max:100'],
            'storage_location' => ['nullable', 'string', 'max:255'],
            'quality_certificate' => ['nullable', 'boolean'], 

            // Validación de Archivos (PDF hasta 10MB)
            'invoice_file' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'remission_file' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'certificate_file' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $validatedData['user_id'] = $user->id;
        $validatedData['quality_certificate'] = $request->boolean('quality_certificate');
        
        // Lógica de Status
        $isComplete = $request->filled('storage_location') && 
                      ($validatedData['material_type'] !== 'SPARE_PART' || $request->filled('sap_confirmation'));
        
        $validatedData['status'] = $isComplete ? 'COMPLETO' : 'PENDIENTE_UBICACION';
        
        // GUARDADO DE ARCHIVOS (CORREGIDO)
        // Usamos el disco 'public' para facilitar el acceso luego
        if ($request->hasFile('invoice_file')) {
            $validatedData['invoice_path'] = $request->file('invoice_file')->store('receptions/invoices', 'public');
        }
        if ($request->hasFile('remission_file')) {
            $validatedData['remission_path'] = $request->file('remission_file')->store('receptions/remissions', 'public');
        }
        if ($request->hasFile('certificate_file')) {
            $validatedData['certificate_path'] = $request->file('certificate_file')->store('receptions/certificates', 'public');
        }

        MaterialReception::create($validatedData);

        return redirect()->route('material-receptions.index')->with('success', 'Recepción registrada exitosamente.');
    }
    
    // FUNCIÓN CORREGIDA: Ver archivos adjuntos
    public function viewFile(MaterialReception $recepcione, $type)
    {
        // Mapeamos el tipo de archivo a la columna de la base de datos
        $path = match($type) {
            'invoice' => $recepcione->invoice_path,
            'remission' => $recepcione->remission_path,
            'certificate' => $recepcione->certificate_path,
            default => null
        };

        // Verificamos si el archivo existe en el disco 'public'
        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404, 'Archivo no encontrado en el servidor.');
        }
        
        // Servimos el archivo para visualización en el navegador
        // Usamos el método 'path' para obtener la ruta absoluta del sistema
        return response()->file(Storage::disk('public')->path($path));
    }

    // Muestra el formulario de edición
    public function edit(MaterialReception $recepcione)
    {
        $user = Auth::user();
        $terminals = $user->role->name === 'Administrador' ? Terminal::all() : Terminal::where('id', $user->terminal_id)->get();
        return view('almacen.material-receptions.edit', compact('recepcione', 'terminals'));
    }

    // Actualiza la recepción
    public function update(Request $request, MaterialReception $recepcione)
    {
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
            // Podrías añadir validación de archivos aquí si permites actualizar
        ]);

        $validatedData['quality_certificate'] = $request->has('quality_certificate') ? true : false;
        
        // Recalcular status
        $isComplete = $request->filled('storage_location') && 
                      ($validatedData['material_type'] !== 'SPARE_PART' || $request->filled('sap_confirmation'));
        $validatedData['status'] = $isComplete ? 'COMPLETO' : 'PENDIENTE_UBICACION';

        $recepcione->update($validatedData);
        return redirect()->route('material-receptions.index')->with('success', 'Recepción actualizada exitosamente.');
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
