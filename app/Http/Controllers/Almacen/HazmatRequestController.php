<?php

namespace App\Http\Controllers\Almacen;

use App\Http\Controllers\Controller;
use App\Models\HazmatProduct;
use App\Models\HazmatRequest;
use App\Models\Terminal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class HazmatRequestController extends Controller
{
    /**
     * Bandeja de entrada de solicitudes (Admin/Safety) o Mis Solicitudes (Usuario normal)
     */
    public function index()
    {
        $user = Auth::user();
        
        $query = HazmatRequest::with('user', 'terminal');

        // Si es rol seguridad/admin ve todo lo de su terminal (o todo si es Admin global)
        $isSafety = $user->hasRole(['Administrador', 'Seguridad y Salud']);
        
        if ($isSafety) {
            if ($user->role->name !== 'Administrador') {
                $query->where('terminal_id', $user->terminal_id);
            }
        } else {
            // Usuario normal solo ve sus propias solicitudes
            $query->where('user_id', $user->id);
        }

        $requests = $query->latest()->paginate(15);
        
        return view('almacen.hazmat.requests.index', compact('requests', 'isSafety'));
    }

    public function create()
    {
        // Solo usuarios pueden crear solcitudes
        $terminals = Terminal::all();
        return view('almacen.hazmat.requests.create', compact('terminals'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'terminal_id' => 'required|exists:terminals,id',
            'trade_name' => 'required|string|max:255',
            'chemical_name' => 'required|string|max:255',
            'usage_area' => 'required|string|max:255',
            'intended_use' => 'required|string',
            'storage_location' => 'required|string|max:255',
            'max_storage_quantity' => 'required|string|max:255',
            'min_storage_quantity' => 'nullable|string|max:255',
            'monthly_consumption' => 'nullable|string|max:255',
            'entry_date' => 'nullable|date',
            'moc_id' => 'nullable|string|max:255',
            'hds_file' => 'required|file|mimes:pdf|max:10240',
        ]);

        // Guardar archivo HDS
        $path = $request->file('hds_file')->store('hazmat/hds-requests', 'public');

        $hazmatRequest = HazmatRequest::create([
            'user_id' => $user->id,
            'terminal_id' => $validated['terminal_id'], // Terminal seleccionada en el formulario
            'status' => 'PENDING',
            'trade_name' => $validated['trade_name'],
            'chemical_name' => $validated['chemical_name'],
            'usage_area' => $validated['usage_area'],
            'intended_use' => $validated['intended_use'],
            'storage_location' => $validated['storage_location'],
            'max_storage_quantity' => $validated['max_storage_quantity'],
            'min_storage_quantity' => $validated['min_storage_quantity'] ?? null,
            'monthly_consumption' => $validated['monthly_consumption'] ?? null,
            'entry_date' => $validated['entry_date'] ?? now(),
            'moc_id' => $validated['moc_id'] ?? null,
            'is_sample' => $request->has('is_sample'),
            'is_import' => $request->has('is_import'),
            'hds_path' => $path,
        ]);

        return redirect()->route('hazmat-requests.index')
            ->with('success', 'Solicitud enviada correctamente para revisión.');
    }

    public function show(HazmatRequest $hazmatRequest)
    {
        $user = Auth::user();
        
        // Autorización simple
        if ($hazmatRequest->user_id !== $user->id && !$user->hasRole(['Administrador', 'Seguridad y Salud'])) {
            abort(403);
        }

        $isSafety = $user->hasRole(['Administrador', 'Seguridad y Salud']);

        if ($user->hasRole('Administrador')) {
             // Admin sees all (implicit) -> but check if we need to load relations
        }

        $hazmatRequest->load(['user', 'terminal']); // Ensure relations are loaded

        $isSafety = $user->hasRole(['Administrador', 'Seguridad y Salud']);

        return view('almacen.hazmat.requests.show', compact('hazmatRequest', 'isSafety'));
    }

    /**
     * Acción para que Seguridad acepte/rechace (Update con checklist)
     */
    public function update(Request $request, HazmatRequest $hazmatRequest)
    {
        // Solo seguridad
        $this->authorizeSafety();

        if ($request->input('action') === 'approve') {
            $request->validate([
                'final_storage_location' => 'required|string',
            ]);

            $hazmatRequest->update([
                'status' => 'APPROVED',
                'approver_id' => Auth::id(),
                'can_be_substituted' => $request->has('can_be_substituted'),
                'hds_compliant' => $request->has('hds_compliant'),
                'has_training' => $request->has('has_training'),
                'has_ppe' => $request->has('has_ppe'),
                'has_containment' => $request->has('has_containment'),
                'moc_managed' => $request->has('moc_managed'),
                'final_storage_location' => $request->input('final_storage_location'),
            ]);

            return redirect()->route('hazmat-requests.show', $hazmatRequest)
                ->with('success', 'Solicitud APROBADA. Ahora puede generar el documento.');
                
        } elseif ($request->input('action') === 'reject') {
            $request->validate([
                'rejection_reason' => 'required|string',
            ]);

            $hazmatRequest->update([
                'status' => 'REJECTED',
                'approver_id' => Auth::id(),
                'rejection_reason' => $request->input('rejection_reason'),
            ]);

            return redirect()->route('hazmat-requests.index')
                ->with('success', 'Solicitud RECHAZADA.');
        }

        return back();
    }

    /**
     * Generar PDF del formato F01
     */
    public function downloadPdf(HazmatRequest $hazmatRequest)
    {
        if ($hazmatRequest->status !== 'APPROVED') {
            return back()->with('error', 'Solo solicitudes aprobadas pueden generar el formato.');
        }

        $pdf = Pdf::loadView('almacen.hazmat.requests.pdf_form', ['r' => $hazmatRequest]);
        $pdf->setPaper('letter', 'portrait');
        return $pdf->stream('SOLICITUD-'.$hazmatRequest->id.'.pdf');
    }

    /**
     * Ver HDS adjunta
     */
    public function viewHds(HazmatRequest $hazmatRequest)
    {
        if (!$hazmatRequest->hds_path || !Storage::disk('public')->exists($hazmatRequest->hds_path)) {
            abort(404);
        }
        return response()->file(Storage::disk('public')->path($hazmatRequest->hds_path));
    }

    protected function authorizeSafety()
    {
        if (!Auth::user()->hasRole(['Administrador', 'Seguridad y Salud'])) {
            abort(403, 'No autorizado para revisar solicitudes.');
        }
    }
    }

    /**
     * Eliminar o retirar solicitud.
     */
    public function destroy(HazmatRequest $hazmatRequest)
    {
        $user = Auth::user();

        // Admin puede eliminar cualquiera
        // Usuario dueño puede eliminar si está PENDING
        $canDelete = $user->hasRole('Administrador') || 
                     ($hazmatRequest->user_id == $user->id && $hazmatRequest->status == 'PENDING');

        if (!$canDelete) {
            abort(403, 'No tienes permiso para eliminar esta solicitud.');
        }

        $hazmatRequest->delete();

        return redirect()->route('hazmat-requests.index')
            ->with('success', 'Solicitud eliminada correctamente.');
    }
}
