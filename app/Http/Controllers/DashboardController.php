<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaterialOutput;
use App\Models\MaterialReception;
use App\Models\HazmatProduct;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. TARJETAS SUPERIORES (KPIs)
        $kpis = [
            'salidas_mes' => MaterialOutput::whereMonth('output_date', now()->month)->count(),
            'entradas_mes' => MaterialReception::whereMonth('reception_date', now()->month)->count(),
            'hazmat_total' => HazmatProduct::where('is_active', true)->count(),
            'pendientes' => MaterialOutput::where('status', '!=', 'COMPLETO')->count() + 
                           MaterialReception::where('status', '!=', 'COMPLETO')->count(),
        ];

        // 2. GRÁFICA DE BARRAS: Movimientos últimos 6 meses
        $months = collect([]);
        $dataSalidas = collect([]);
        $dataEntradas = collect([]);

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months->push($date->format('M Y'));
            
            $dataSalidas->push(MaterialOutput::whereMonth('output_date', $date->month)
                                             ->whereYear('output_date', $date->year)
                                             ->count());
                                             
            $dataEntradas->push(MaterialReception::whereMonth('reception_date', $date->month)
                                               ->whereYear('reception_date', $date->year)
                                               ->count());
        }

        // 3. GRÁFICA DE DONA: Hazmat por Estado Físico
        $hazmatStats = HazmatProduct::select('physical_state', DB::raw('count(*) as total'))
                                    ->groupBy('physical_state')
                                    ->pluck('total', 'physical_state');

        // 4. TABLA: Actividad Reciente (Unimos las 5 últimas de cada uno)
        $recentOutputs = MaterialOutput::with('terminal')->latest()->take(5)->get();
        $recentReceptions = MaterialReception::with('terminal')->latest()->take(5)->get();

        return view('dashboard', compact(
            'kpis', 
            'months', 
            'dataSalidas', 
            'dataEntradas', 
            'hazmatStats', 
            'recentOutputs', 
            'recentReceptions'
        ));
    }
}