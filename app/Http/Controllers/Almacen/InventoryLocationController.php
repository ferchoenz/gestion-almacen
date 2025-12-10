<?php

namespace App\Http\Controllers\Almacen;

use App\Http\Controllers\Controller;
use App\Models\InventoryLocation;
use App\Models\Terminal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class InventoryLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');

        $query = InventoryLocation::with('terminal');

        // Filter by Terminal (Security)
        if ($user->role->name !== 'Administrador') {
            $query->where('terminal_id', $user->terminal_id);
        }

        // Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $locations = $query->orderBy('code')->paginate(15)->withQueryString();

        return view('almacen.inventory-locations.index', compact('locations', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $terminals = $user->role->name === 'Administrador' 
                        ? Terminal::all() 
                        : Terminal::where('id', $user->terminal_id)->get();

        return view('almacen.inventory-locations.create', compact('terminals'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'terminal_id' => ['required', 'exists:terminals,id'],
            'code' => [
                'required', 
                'string', 
                'max:50',
                Rule::unique('inventory_locations')->where(function ($query) use ($request) {
                    return $query->where('terminal_id', $request->terminal_id);
                })
            ],
            'name' => ['required', 'string', 'max:255'],
            'aisle' => ['nullable', 'string', 'max:50'],
            'rack' => ['nullable', 'string', 'max:50'],
            'level' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        // Security check for non-admins
        if ($user->role->name !== 'Administrador' && $request->terminal_id != $user->terminal_id) {
            abort(403, 'No tienes permiso para crear ubicaciones en esta terminal.');
        }

        InventoryLocation::create($request->all());

        return redirect()->route('inventory-locations.index')
            ->with('success', 'Ubicación creada exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InventoryLocation $inventoryLocation)
    {
        $user = Auth::user();

        // Security check
        if ($user->role->name !== 'Administrador' && $inventoryLocation->terminal_id !== $user->terminal_id) {
            abort(403);
        }

        $terminals = $user->role->name === 'Administrador' 
                        ? Terminal::all() 
                        : Terminal::where('id', $user->terminal_id)->get();

        return view('almacen.inventory-locations.edit', compact('inventoryLocation', 'terminals'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InventoryLocation $inventoryLocation)
    {
        $user = Auth::user();

        // Security check
        if ($user->role->name !== 'Administrador' && $inventoryLocation->terminal_id !== $user->terminal_id) {
            abort(403);
        }

        $request->validate([
            'terminal_id' => ['required', 'exists:terminals,id'],
            'code' => [
                'required', 
                'string', 
                'max:50',
                Rule::unique('inventory_locations')
                    ->where(function ($query) use ($request) {
                        return $query->where('terminal_id', $request->terminal_id);
                    })
                    ->ignore($inventoryLocation->id)
            ],
            'name' => ['required', 'string', 'max:255'],
            'aisle' => ['nullable', 'string', 'max:50'],
            'rack' => ['nullable', 'string', 'max:50'],
            'level' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ]);

        $inventoryLocation->update($request->all());

        return redirect()->route('inventory-locations.index')
            ->with('success', 'Ubicación actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InventoryLocation $inventoryLocation)
    {
        $user = Auth::user();

        // Security check
        if ($user->role->name !== 'Administrador' && $inventoryLocation->terminal_id !== $user->terminal_id) {
            abort(403);
        }

        // Check if location has consumables
        if ($inventoryLocation->consumables()->exists()) {
            return back()->with('error', 'No se puede eliminar la ubicación porque tiene consumibles asignados.');
        }

        $inventoryLocation->delete();

        return redirect()->route('inventory-locations.index')
            ->with('success', 'Ubicación eliminada exitosamente.');
    }
}
