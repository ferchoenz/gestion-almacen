<?php

namespace App\Imports;

use App\Models\Consumable;
use App\Models\InventoryLocation;
use App\Models\InventoryMovement;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ConsumablesImport implements ToCollection, WithHeadingRow
{
    protected $terminalId;

    public function __construct($terminalId)
    {
        $this->terminalId = $terminalId;
    }

    /**
    * Procesa la colección de filas del archivo Excel.
    *
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Validación básica de la fila
            if (!isset($row['sku']) || !isset($row['name'])) {
                continue; // Saltar filas inválidas o vacías 
            }

            // Normalizar datos
            $sku = trim($row['sku']);
            $locationCode = isset($row['location_code']) ? trim($row['location_code']) : null;
            
            // Buscar si ya existe el consumible en esta terminal
            $consumable = Consumable::where('terminal_id', $this->terminalId)
                ->where('sku', $sku)
                ->first();

            // Resolver ID de ubicación si se proporciona código
            $locationId = null;
            if ($locationCode) {
                $location = InventoryLocation::where('code', $locationCode)
                    ->where('terminal_id', $this->terminalId) // Asumiendo que ubicaciones pertenecen a terminal
                    ->first();
                if ($location) {
                    $locationId = $location->id;
                }
            }

            if ($consumable) {
                // --- ACTUALIZAR EXISTENTE ---
                // Solo actualizamos datos maestros, NO el stock (para eso son los movimientos/ajustes)
                // A menos que el usuario especificamente quiera sobreescribir stock (riesgoso)
                // Por seguridad detallada en el plan: "Only update details".
                
                $consumable->update([
                    'name' => $row['name'],
                    'description' => $row['description'] ?? $consumable->description,
                    'category' => $row['category'] ?? $consumable->category,
                    'unit_of_measure' => $row['unit_of_measure'] ?? $consumable->unit_of_measure,
                    'min_stock' => $row['min_stock'] ?? $consumable->min_stock,
                    'unit_cost' => $row['unit_cost'] ?? $consumable->unit_cost,
                    // Actualizar ubicación solo si se especifica una nueva válida
                    'location_id' => $locationId ?? $consumable->location_id,
                ]);
            } else {
                // --- CREAR NUEVO ---
                $currentStock = isset($row['current_stock']) ? (float)$row['current_stock'] : 0;
                
                $consumable = Consumable::create([
                    'terminal_id' => $this->terminalId,
                    'sku' => $sku,
                    'name' => $row['name'],
                    'description' => $row['description'] ?? null,
                    'category' => $row['category'] ?? 'General',
                    'unit_of_measure' => $row['unit_of_measure'] ?? 'Pieza',
                    'current_stock' => $currentStock,
                    'min_stock' => $row['min_stock'] ?? 0,
                    'unit_cost' => $row['unit_cost'] ?? 0,
                    'location_id' => $locationId,
                    'is_active' => true,
                ]);

                // Registrar movimiento inicial si hay stock
                if ($currentStock > 0) {
                    InventoryMovement::create([
                        'consumable_id' => $consumable->id,
                        'terminal_id' => $this->terminalId,
                        'movement_type' => 'ENTRADA',
                        'quantity' => $currentStock,
                        'previous_stock' => 0,
                        'new_stock' => $currentStock,
                        'unit_cost' => $consumable->unit_cost,
                        'notes' => 'Carga inicial masiva',
                        'user_id' => Auth::id(),
                    ]);
                }
            }
        }
    }
}
