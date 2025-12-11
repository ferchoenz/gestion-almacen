<?php

namespace App\Exports;

use App\Models\Consumable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class ConsumablesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $terminalId;

    public function __construct($terminalId)
    {
        $this->terminalId = $terminalId;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = Consumable::with(['location']);

        // Si no es admin, filtrar por terminal
        if ($this->terminalId) {
            $query->where('terminal_id', $this->terminalId);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'SKU',
            'Nombre',
            'Categoría',
            'Stock Actual',
            'Unidad',
            'Ubicación',
            'Costo Unitario',
            'Valor Total',
            'Estado',
            'Fecha Creación',
        ];
    }

    public function map($consumable): array
    {
        return [
            $consumable->id,
            $consumable->sku,
            $consumable->name,
            $consumable->category,
            $consumable->current_stock,
            $consumable->unit_of_measure,
            $consumable->location ? $consumable->location->full_name : 'Sin ubicación',
            $consumable->unit_cost,
            $consumable->current_stock * $consumable->unit_cost,
            $consumable->is_active ? 'Activo' : 'Inactivo',
            $consumable->created_at->format('d/m/Y'),
        ];
    }
}
