<?php

namespace App\Exports;

use App\Models\MaterialOutput;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

// --- ¡SOLO NECESITAMOS ESTAS DOS! ---
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
// ------------------------------------


class MaterialOutputsExport implements FromCollection, 
                                      WithHeadings, 
                                      WithMapping, 
                                      ShouldAutoSize,
                                      WithStyles         // <-- Solo implementamos esta
{
    protected $month;
    protected $year;
    protected $user;

    public function __construct(Request $request)
    {
        $this->month = $request->input('month');
        $this->year = $request->input('year');
        $this->user = Auth::user();
    }

    public function collection()
    {
        // Esta consulta es idéntica a la del controlador
        $query = MaterialOutput::with(['user', 'terminal']);

        // Filtro de Rol
        if ($this->user->role->name !== 'Administrador') {
            $query->where('terminal_id', $this->user->terminal_id);
        }
        // Filtro de Mes
        if ($this->month) {
            $query->whereMonth('output_date', $this->month);
        }
        // Filtro de Año
        if ($this->year) {
            $query->whereYear('output_date', $this->year);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Define la Fila 1 (Cabeceras) del Excel.
     */
    public function headings(): array
    {
        return [
            'ID',
            'Terminal',
            'Almacenista',
            'Tipo',
            'No. Item',
            'Descripción',
            'Fecha Salida',
            'Cantidad',
            'Recibió',
            'Orden de Trabajo (OT)',
            'Confirmación SAP',
            'Status',
            'Fecha de Registro',
        ];
    }

    /**
     * Transforma los datos de cada fila.
     * ($output es cada registro de la base de datos)
     * ¡AQUÍ ESTABA EL ERROR! (Decía publicD)
     */
    public function map($output): array
    {
        return [
            $output->id,
            $output->terminal->name ?? 'N/A',
            $output->user->name ?? 'N/A', // Quién entregó
            $output->material_type,
            $output->item_number ?? 'N/A',
            $output->description,
            \Carbon\Carbon::parse($output->output_date)->format('d/m/Y'),
            $output->quantity,
            $output->receiver_name,
            $output->work_order ?? 'N/A',
            $output->sap_confirmation ?? 'N/A',
            $output->status,
            $output->created_at->format('d/m/Y H:i'),
        ];
    }

    // --- ¡FUNCIÓN ÚNICA PARA ESTILOS Y FILTROS! ---
    /**
     * Aplica estilos y filtros a la hoja de cálculo.
     */
    public function styles(Worksheet $sheet)
    {
        // 1. Pone la Fila 1 en negritas
        $sheet->getStyle('1:1')->getFont()->setBold(true);

        // 2. Añade los AutoFiltros a toda la tabla
        // (calcula automáticamente el tamaño de tus datos)
        $sheet->setAutoFilter($sheet->calculateWorksheetDimension());
    }
}