<?php

namespace App\Exports;

use App\Models\MaterialReception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MaterialReceptionsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $month;
    protected $year;
    protected $terminal_id; // Nuevo filtro
    protected $user;

    public function __construct(Request $request)
    {
        $this->month = $request->input('month');
        $this->year = $request->input('year');
        $this->terminal_id = $request->input('terminal_id'); // Nuevo filtro
        $this->user = Auth::user();
    }

    public function collection()
    {
        $query = MaterialReception::with(['user', 'terminal']);

        if ($this->user->role->name !== 'Administrador') {
            $query->where('terminal_id', $this->user->terminal_id);
        } else {
            // Si es Admin y seleccionó una terminal en el filtro...
            if ($this->terminal_id) {
                $query->where('terminal_id', $this->terminal_id);
            }
        }

        if ($this->month) $query->whereMonth('reception_date', $this->month);
        if ($this->year) $query->whereYear('reception_date', $this->year);
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID', 'Terminal', 'Registró', 'Tipo', 'No. Item', 'Descripción', 
            'Proveedor', 'Orden Compra', 'Fecha Recepción', 'Cantidad', 
            'Cert. Calidad', 'Conf. SAP', 'Ubicación', 'Status', 'Fecha Registro'
        ];
    }

    public function map($reception): array
    {
        return [
            $reception->id,
            $reception->terminal->name ?? 'N/A',
            $reception->user->name ?? 'N/A',
            $reception->material_type,
            $reception->item_number ?? 'N/A',
            $reception->description,
            $reception->provider,
            $reception->purchase_order,
            $reception->reception_date->format('d/m/Y'),
            $reception->quantity,
            $reception->quality_certificate ? 'SI' : 'NO',
            $reception->sap_confirmation ?? 'N/A',
            $reception->storage_location ?? 'Pendiente',
            $reception->status,
            $reception->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('1:1')->getFont()->setBold(true);
        $sheet->setAutoFilter($sheet->calculateWorksheetDimension());
    }
}