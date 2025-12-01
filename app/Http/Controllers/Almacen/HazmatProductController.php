<?php

namespace App\Http\Controllers\Almacen;

use App\Http\Controllers\Controller;
use App\Models\HazmatProduct;
use App\Models\Terminal;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Exports\HazmatProductsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class HazmatProductController extends Controller
{
    protected $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        $search = $request->input('search');
        $filterTerminal = $request->input('terminal_id');
        $filterState = $request->input('physical_state');
        $filterSignal = $request->input('signal_word');
        $viewDeleted = $request->boolean('view_deleted');

        $query = HazmatProduct::with('terminal');

        if ($user->role->name !== 'Administrador') {
            $query->where('terminal_id', $user->terminal_id);
        } else {
            if ($filterTerminal) {
                $query->where('terminal_id', $filterTerminal);
            }
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('chemical_name', 'like', "%{$search}%")
                  ->orWhere('cas_number', 'like', "%{$search}%");
            });
        }

        if ($filterState) $query->where('physical_state', $filterState);
        if ($filterSignal) $query->where('signal_word', $filterSignal);
        
        if ($viewDeleted) {
            $query->onlyTrashed();
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $terminals = Terminal::all();

        return view('almacen.hazmat.index', compact('products', 'terminals', 'search', 'filterTerminal', 'filterState', 'filterSignal', 'viewDeleted'));
    }

    public function create()
    {
        $user = Auth::user();
        $terminals = $user->role->name === 'Administrador' ? Terminal::all() : Terminal::where('id', $user->terminal_id)->get();
        return view('almacen.hazmat.create', compact('terminals'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'terminal_id' => ['required', $user->role->name === 'Administrador' ? Rule::exists('terminals', 'id') : Rule::in([$user->terminal_id])],
            'product_name' => 'required|string|max:255',
            'chemical_name' => 'required|string|max:255',
            'cas_number' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'location' => 'required|string',
            'physical_state' => 'required|string',
            'max_quantity' => 'required|numeric',
            'department' => 'required|string',
            'signal_word' => 'required|in:PELIGRO,ATENCION,SIN PALABRA',
            'hazard_statements' => 'nullable|string',
            'precautionary_statements' => 'nullable|string',
            'epp' => 'nullable|string',
            'pictograms' => 'nullable|array',
            'hds_file' => 'nullable|file|mimes:pdf|max:10240',
            'product_image' => 'nullable|image|max:5120',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('hds_file')) {
            $path = $request->file('hds_file')->store('hazmat/hds', 'public');
            $validated['hds_path'] = $path;
        }

        if ($request->hasFile('product_image')) {
            $path = $request->file('product_image')->store('hazmat/images', 'public');
            $validated['image_path'] = $path;
        }

        $validated['is_active'] = $request->has('is_active');
        HazmatProduct::create($validated);

        return redirect()->route('hazmat.index')->with('success', 'Producto químico registrado correctamente.');
    }

    public function show(HazmatProduct $hazmat)
    {
        return view('almacen.hazmat.edit', ['product' => $hazmat, 'terminals' => Terminal::all()]);
    }

    public function edit(HazmatProduct $hazmat)
    {
        $user = Auth::user();
        $terminals = $user->role->name === 'Administrador' ? Terminal::all() : Terminal::where('id', $user->terminal_id)->get();
        return view('almacen.hazmat.edit', ['product' => $hazmat, 'terminals' => $terminals]);
    }

    public function update(Request $request, HazmatProduct $hazmat)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'terminal_id' => ['required', $user->role->name === 'Administrador' ? Rule::exists('terminals', 'id') : Rule::in([$user->terminal_id])],
            'product_name' => 'required|string|max:255',
            'chemical_name' => 'required|string|max:255',
            'cas_number' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'location' => 'required|string',
            'physical_state' => 'required|string',
            'max_quantity' => 'required|numeric',
            'department' => 'required|string',
            'signal_word' => 'required|in:PELIGRO,ATENCION,SIN PALABRA',
            'hazard_statements' => 'nullable|string',
            'precautionary_statements' => 'nullable|string',
            'epp' => 'nullable|string',
            'pictograms' => 'nullable|array',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        
        // (Aquí podrías agregar lógica para actualizar la imagen si se sube una nueva)
        
        $hazmat->update($validated);

        return redirect()->route('hazmat.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Request $request, HazmatProduct $hazmat)
    {
        $request->validate(['cancellation_reason' => 'required|string|max:255']);
        $hazmat->cancellation_reason = $request->cancellation_reason;
        $hazmat->save();
        $hazmat->delete();
        return redirect()->route('hazmat.index')->with('success', 'Material eliminado correctamente.');
    }

    public function analyze(Request $request)
    {
        $request->validate(['hds_analyze' => 'required|file|mimes:pdf|max:10240']);
        try {
            $file = $request->file('hds_analyze');
            $base64Pdf = base64_encode(file_get_contents($file->getRealPath()));
            $analysis = $this->gemini->analyzeHdsPdf($base64Pdf);
            
            // Validamos si la IA dice que NO es una HDS
            if (isset($analysis['is_valid_hds']) && $analysis['is_valid_hds'] === false) {
                return response()->json([
                    'error' => $analysis['error_msg'] ?? 'El documento no parece ser una HDS válida.'
                ], 422);
            }

            return response()->json($analysis);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function downloadLabel(HazmatProduct $hazmat)
    {
        $pdf = Pdf::loadView('almacen.hazmat.label_pdf', ['product' => $hazmat]);
        $pdf->setPaper('letter', 'landscape');
        return $pdf->stream('etiqueta.pdf');
    }

    public function viewHds(HazmatProduct $hazmat)
    {
        if (!$hazmat->hds_path || !Storage::disk('public')->exists($hazmat->hds_path)) {
            abort(404, 'Archivo HDS no encontrado en el servidor.');
        }
        return response()->file(Storage::disk('public')->path($hazmat->hds_path));
    }

    public function export(Request $request)
    {
        return Excel::download(new HazmatProductsExport($request), 'listado_maestro_hazmat.xlsx');
    }
}