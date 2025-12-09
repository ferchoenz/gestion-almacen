<?php

namespace App\Http\Controllers\Almacen;

use App\Http\Controllers\Controller;
use App\Models\InventoryExit;
use App\Models\Consumable;
use App\Models\Terminal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class InventoryExitController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $filterMonth = $request->input('month');
        $filterYear = $request->input('year');
        $filterTerminal = $request->input('terminal_id');
        $filterDepartment = $request->input('department');
        $search = $request->input('search');

        $query = InventoryExit::with(['user', 'terminal', 'consumable']);

        // Filtros de Seguridad por Rol
        if ($user->role->name !== 'Administrador') {
            $query->where('terminal_id', $user->terminal_id);
        } else {
            if ($filterTerminal) {
                $query->where('terminal_id', $filterTerminal);
            }
        }

        if ($filterMonth) $query->whereMonth('exit_date', $filterMonth);
        if ($filterYear) $query->whereYear('exit_date', $filterYear);
        if ($filterDepartment) $query->where('department', $filterDepartment);

        // Búsqueda
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('recipient_name', 'like', "%{$search}%")
                  ->orWhere('reference_document', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('consumable', function($q) use ($search) {
                      $q->where('sku', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        $exits = $query->orderBy('exit_date', 'desc')->paginate(15)->withQueryString();
        $terminals = Terminal::all();
        $departments = InventoryExit::distinct()->pluck('department')->filter();

        return view('almacen.inventory-exits.index', compact(
            'exits',
            'terminals',
            'departments',
            'filterMonth',
            'filterYear',
            'filterTerminal',
            'filterDepartment',
            'search'
        ));
    }

    public function create()
    {
        $user = Auth::user();
        $terminals = $user->role->name === 'Administrador' 
            ? Terminal::all() 
            : Terminal::where('id', $user->terminal_id)->get();

        // Cargar consumibles activos con stock disponible
        $consumables = Consumable::where('terminal_id', $user->terminal_id)
                                  ->where('is_active', true)
                                  ->where('current_stock', '>', 0)
                                  ->orderBy('name')
                                  ->get();

        return view('almacen.inventory-exits.create', compact('terminals', 'consumables'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'terminal_id' => ['required', $user->role->name === 'Administrador' ? Rule::exists('terminals', 'id') : Rule::in([$user->terminal_id])],
            'consumable_id' => ['required', 'exists:consumables,id'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'exit_date' => ['required', 'date'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'purpose' => ['nullable', 'string', 'max:255'],
            'reference_document' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        // 🔒 VALIDAR STOCK DISPONIBLE
        $consumable = Consumable::findOrFail($validated['consumable_id']);
        
        if ($consumable->current_stock < $validated['quantity']) {
            return back()->withErrors([
                'quantity' => "Stock insuficiente. Disponible: {$consumable->current_stock} {$consumable->unit_of_measure}"
            ])->withInput();
        }

        $validated['user_id'] = $user->id;

        $exit = InventoryExit::create($validated);

        // 🔥 REDUCIR STOCK DEL INVENTARIO
        $consumable->removeStock(
            quantity: $exit->quantity,
            referenceType: InventoryExit::class,
            referenceId: $exit->id,
            notes: "Salida para {$exit->department} - Recibió: {$exit->recipient_name}"
        );

        return redirect()->route('inventory-exits.index')
                        ->with('success', 'Salida registrada exitosamente. Stock actualizado.');
    }

    public function show(InventoryExit $inventoryExit)
    {
        $inventoryExit->load(['user', 'terminal', 'consumable.location']);
        return view('almacen.inventory-exits.show', compact('inventoryExit'));
    }

    public function destroy(Request $request, InventoryExit $inventoryExit)
    {
        // Al cancelar una salida, podríamos revertir el stock, pero por trazabilidad mejor solo soft delete
        $request->validate(['cancellation_reason' => 'nullable|string|max:255']);
        
        $inventoryExit->notes = ($inventoryExit->notes ? $inventoryExit->notes . ' | ' : '') 
                              . 'CANCELADO: ' . $request->cancellation_reason;
        $inventoryExit->save();
        $inventoryExit->delete();

        return redirect()->route('inventory-exits.index')
                        ->with('success', 'Salida cancelada correctamente.');
    }
}
