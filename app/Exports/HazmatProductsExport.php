<?php

namespace App\Exports;

use App\Models\HazmatProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HazmatProductsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $search;
    protected $filterTerminal;
    protected $filterState;
    protected $filterSignal;
    protected $user;

    public function __construct(Request $request)
    {
        $this->search = $request->input('search');
        $this->filterTerminal = $request->input('terminal_id');
        $this->filterState = $request->input('physical_state');
        $this->filterSignal = $request->input('signal_word');
        $this->user = Auth::user();
    }

    public function collection()
    {
        $query = HazmatProduct::with('terminal');

        // Filtro de Seguridad (Terminal)
        if ($this->user->role->name !== 'Administrador') {
            $query->where('terminal_id', $this->user->terminal_id);
        } else {
            if ($this->filterTerminal) {
                $query->where('terminal_id', $this->filterTerminal);
            }
        }

        // Buscador
        if ($this->search) {
            $query->where(function($q) {
                $q->where('product_name', 'like', "%{$this->search}%")
                  ->orWhere('chemical_name', 'like', "%{$this->search}%")
                  ->orWhere('cas_number', 'like', "%{$this->search}%");
            });
        }

        // Filtros específicos
        if ($this->filterState) $query->where('physical_state', $this->filterState);
        if ($this->filterSignal) $query->where('signal_word', $this->filterSignal);
        
        return $query->orderBy('product_name', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'ID', 'Terminal', 'Producto', 'Nombre Químico', 'CAS', 
            'Ubicación', 'Estado Físico', 'Cant. Máx', 'Departamento', 
            'Palabra Adv.', 'Status', 'Códigos H', 'Códigos P', 'Fecha Registro'
        ];
    }

    public function map($product): array
    {
        return [
            $product->id,
            $product->terminal->name ?? 'N/A',
            $product->product_name,
            $product->chemical_name,
            $product->cas_number,
            $product->location,
            $product->physical_state,
            $product->max_quantity,
            $product->department,
            $product->signal_word,
            $product->is_active ? 'ACTIVO' : 'INACTIVO',
            $product->hazard_statements,
            $product->precautionary_statements,
            $product->created_at->format('d/m/Y'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('1:1')->getFont()->setBold(true);
        $sheet->setAutoFilter($sheet->calculateWorksheetDimension());
    }
}