<?php

namespace App\Http\Controllers\Almacen;

use App\Http\Controllers\Controller;
use App\Models\Consumable;
use App\Models\InventoryLocation;
use App\Models\Terminal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

use App\Imports\ConsumablesImport;
use App\Exports\ConsumablesExport;
use Maatwebsite\Excel\Facades\Excel;

class ConsumableController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $search = $request->input('search');
        $filterTerminal = $request->input('terminal_id');
        $filterCategory = $request->input('category');
        $filterStatus = $request->input('status'); // all, active, low_stock
        
        $query = Consumable::with(['terminal', 'location']);
        
        // Filter by terminal
        if ($user->role->name !== 'Administrador') {
            $query->where('terminal_id', $user->terminal_id);
        } elseif ($filterTerminal) {
            $query->where('terminal_id', $filterTerminal);
        }
        
        // Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Filter by category
        if ($filterCategory) {
            $query->where('category', $filterCategory);
        }
        
        // Filter by status
        if ($filterStatus === 'active') {
            $query->where('is_active', true);
        } elseif ($filterStatus === 'low_stock') {
            $query->lowStock();
        }
        
        $consumables = $query->orderBy('name')->paginate(20)->withQueryString();
        $terminals = Terminal::all();
        $categories = Consumable::distinct()->pluck('category')->filter();
        
        return view('almacen.consumables.index', compact(
            'consumables',
            'terminals',
            'categories',
            'search',
            'filterTerminal',
            'filterCategory',
            'filterStatus'
        ));
    }

    public function create()
    {
        $user = Auth::user();
        $terminals = $user->role->name === 'Administrador' 
            ? Terminal::all() 
            : Terminal::where('id', $user->terminal_id)->get();
        
        $locations = InventoryLocation::where('is_active', true)->get();
        
        return view('almacen.consumables.create', compact('terminals', 'locations'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'terminal_id' => [
                'required',
                $user->role->name === 'Administrador' 
                    ? Rule::exists('terminals', 'id') 
                    : Rule::in([$user->terminal_id])
            ],
            'sku' => 'required|string|max:255|unique:consumables,sku',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'unit_of_measure' => 'required|string|max:50',
            'current_stock' => 'required|numeric|min:0',
            'min_stock' => 'required|numeric|min:0',
            'max_stock' => 'nullable|numeric|min:0',
            'unit_cost' => 'nullable|numeric|min:0',
            'location_id' => 'nullable|exists:inventory_locations,id',
            'specific_location' => 'nullable|string|max:255',
            'product_image' => 'nullable|image|max:5120',
            'is_active' => 'nullable|boolean',
        ]);
        
        // Handle image upload
        if ($request->hasFile('product_image')) {
            $validated['image_path'] = $request->file('product_image')->store('consumables/images', 'public');
        }
        
        $validated['is_active'] = $request->has('is_active');
        
        $consumable = Consumable::create($validated);
        
        // Create initial stock movement if stock > 0
        if ($consumable->current_stock > 0) {
            $consumable->movements()->create([
                'terminal_id' => $consumable->terminal_id,
                'movement_type' => 'ENTRADA',
                'quantity' => $consumable->current_stock,
                'previous_stock' => 0,
                'new_stock' => $consumable->current_stock,
                'unit_cost' => $consumable->unit_cost,
                'notes' => 'Stock inicial al crear el producto',
                'user_id' => Auth::id(),
            ]);
        }
        
        return redirect()->route('consumables.index')->with('success', 'Consumible creado correctamente.');
    }

    public function show(Consumable $consumable)
    {
        $consumable->load(['terminal', 'location', 'movements.user']);
        $recentMovements = $consumable->movements()->latest()->take(10)->get();
        
        return view('almacen.consumables.show', compact('consumable', 'recentMovements'));
    }

    public function edit(Consumable $consumable)
    {
        $user = Auth::user();
        $terminals = $user->role->name === 'Administrador' 
            ? Terminal::all() 
            : Terminal::where('id', $user->terminal_id)->get();
        
        $locations = InventoryLocation::where('is_active', true)->get();
        
        return view('almacen.consumables.edit', compact('consumable', 'terminals', 'locations'));
    }

    public function update(Request $request, Consumable $consumable)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'terminal_id' => [
                'required',
                $user->role->name === 'Administrador' 
                    ? Rule::exists('terminals', 'id') 
                    : Rule::in([$user->terminal_id])
            ],
            // SKU no se puede cambiar
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'unit_of_measure' => 'required|string|max:50',
            // Stock NO se edita aquí, solo con movimientos
            'min_stock' => 'required|numeric|min:0',
            'max_stock' => 'nullable|numeric|min:0',
            'unit_cost' => 'nullable|numeric|min:0',
            'location_id' => 'nullable|exists:inventory_locations,id',
            'specific_location' => 'nullable|string|max:255',
            'product_image' => 'nullable|image|max:5120',
            'is_active' => 'nullable|boolean',
        ]);
        
        // Handle image upload
        if ($request->hasFile('product_image')) {
            // Delete old image
            if ($consumable->image_path) {
                Storage::disk('public')->delete($consumable->image_path);
            }
            $validated['image_path'] = $request->file('product_image')->store('consumables/images', 'public');
        }
        
        $validated['is_active'] = $request->has('is_active');
        
        $consumable->update($validated);
        
        return redirect()->route('consumables.index')->with('success', 'Consumible actualizado correctamente.');
    }

    public function destroy(Consumable $consumable)
    {
        $consumable->delete();
        return redirect()->route('consumables.index')->with('success', 'Consumible eliminado correctamente.');
    }

    /**
     * Retorna consumibles en formato JSON para selects dinámicos
     */
    public function search(Request $request)
    {
        $term = $request->input('q');
        $terminalId = $request->input('terminal_id');
        
        $consumables = Consumable::where('is_active', true)
            ->where('terminal_id', $terminalId)
            ->where(function($query) use ($term) {
                $query->where('sku', 'like', "%{$term}%")
                      ->orWhere('name', 'like', "%{$term}%");
            })
            ->limit(20)
            ->get(['id', 'sku', 'name', 'current_stock', 'unit_of_measure']);
        
        return response()->json($consumables);
    }

    /**
     * Muestra la vista de importación masiva.
     */
    public function import()
    {
        return view('almacen.consumables.import');
    }

    /**
     * Procesa el archivo de importación.
     */
    public function processImport(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        $user = Auth::user();
        
        // Determinar terminal: Admin puede elegir (si se implementara en UI), resto usa la suya.
        // Por simplicidad en esta fase, usaremos la terminal del usuario actual.
        $terminalId = $user->terminal_id; 

        try {
            Excel::import(new ConsumablesImport($terminalId), $request->file('file'));
            
            return redirect()->route('consumables.index')
                ->with('success', 'Importación completada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error en la importación: ' . $e->getMessage());
        }
    }

    /**
     * Exporta los consumibles a Excel.
     */
    public function export()
    {
        $user = Auth::user();
        $terminalId = $user->role->name === 'Administrador' ? null : $user->terminal_id;
        
        return Excel::download(new ConsumablesExport($terminalId), 'consumibles_inventario.xlsx');
    }
}
