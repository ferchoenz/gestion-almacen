<?php

namespace App\Http\Controllers\Almacen;

use App\Http\Controllers\Controller;
use App\Models\Consumable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Picqer\Barcode\BarcodeGeneratorPNG;

class LabelController extends Controller
{
    /**
     * Genera una etiqueta individual para un consumible
     */
    public function single(Consumable $consumable)
    {
        $user = Auth::user();
        
        // Verificar acceso por terminal
        if ($user->role->name !== 'Administrador' && $consumable->terminal_id !== $user->terminal_id) {
            abort(403, 'No tienes acceso a este producto.');
        }

        // Generar código de barras
        $generator = new BarcodeGeneratorPNG();
        $barcode = base64_encode($generator->getBarcode($consumable->barcode, $generator::TYPE_CODE_128, 2, 60));

        return view('almacen.labels.single', [
            'consumable' => $consumable,
            'barcode' => $barcode,
        ]);
    }

    /**
     * Genera múltiples etiquetas para un consumible
     */
    public function multiple(Request $request, Consumable $consumable)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'Administrador' && $consumable->terminal_id !== $user->terminal_id) {
            abort(403, 'No tienes acceso a este producto.');
        }

        $quantity = $request->input('quantity', 1);
        $quantity = min($quantity, 100); // Máximo 100 etiquetas

        $generator = new BarcodeGeneratorPNG();
        $barcode = base64_encode($generator->getBarcode($consumable->barcode, $generator::TYPE_CODE_128, 2, 60));

        return view('almacen.labels.multiple', [
            'consumable' => $consumable,
            'barcode' => $barcode,
            'quantity' => $quantity,
        ]);
    }

    /**
     * Genera etiquetas para múltiples productos seleccionados
     */
    public function batch(Request $request)
    {
        $user = Auth::user();
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return back()->with('error', 'No se seleccionaron productos.');
        }

        $query = Consumable::whereIn('id', $ids);
        
        if ($user->role->name !== 'Administrador') {
            $query->where('terminal_id', $user->terminal_id);
        }

        $consumables = $query->get();
        $generator = new BarcodeGeneratorPNG();

        $labels = $consumables->map(function ($consumable) use ($generator) {
            return [
                'consumable' => $consumable,
                'barcode' => base64_encode($generator->getBarcode($consumable->barcode, $generator::TYPE_CODE_128, 2, 60)),
            ];
        });

        return view('almacen.labels.batch', [
            'labels' => $labels,
        ]);
    }
}
