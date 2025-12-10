<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaterialOutput;
use App\Models\MaterialReception;
use App\Models\HazmatProduct;
use App\Models\Consumable;
use App\Models\InventoryLocation;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // ========================================
        // 1. ALERTAS CRÍTICAS
        // ========================================
        
        // Productos con stock bajo
        $lowStockProducts = Consumable::where('is_active', true)
            ->whereColumn('current_stock', '<=', 'min_stock')
            ->with('location')
            ->orderBy('current_stock', 'asc')
            ->take(10)
            ->get();
        
        // Pendientes (recepciones y salidas sin completar)
        $pendingOutputs = MaterialOutput::where('status', 'PENDIENTE_OT')
            ->with('terminal')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        $pendingReceptions = MaterialReception::whereIn('status', ['PENDIENTE_OT', 'PENDIENTE_UBICACION'])
            ->with('terminal')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // ========================================
        // 2. KPIs CON TENDENCIAS (vs mes anterior)
        // ========================================
        
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $lastMonth = now()->subMonth();
        
        // Salidas
        $salidasMes = MaterialOutput::whereMonth('output_date', $currentMonth)
            ->whereYear('output_date', $currentYear)->count();
        $salidasMesAnterior = MaterialOutput::whereMonth('output_date', $lastMonth->month)
            ->whereYear('output_date', $lastMonth->year)->count();
        $salidasTendencia = $salidasMesAnterior > 0 
            ? round((($salidasMes - $salidasMesAnterior) / $salidasMesAnterior) * 100, 1) 
            : ($salidasMes > 0 ? 100 : 0);
        
        // Entradas
        $entradasMes = MaterialReception::whereMonth('reception_date', $currentMonth)
            ->whereYear('reception_date', $currentYear)->count();
        $entradasMesAnterior = MaterialReception::whereMonth('reception_date', $lastMonth->month)
            ->whereYear('reception_date', $lastMonth->year)->count();
        $entradasTendencia = $entradasMesAnterior > 0 
            ? round((($entradasMes - $entradasMesAnterior) / $entradasMesAnterior) * 100, 1) 
            : ($entradasMes > 0 ? 100 : 0);
        
        // Otras métricas
        $stockBajo = $lowStockProducts->count();
        $pendientesTotal = MaterialOutput::where('status', '!=', 'COMPLETO')->count() + 
                          MaterialReception::where('status', '!=', 'COMPLETO')->count();
        $consumablesActivos = Consumable::where('is_active', true)->count();
        $valorInventario = Consumable::where('is_active', true)
            ->selectRaw('SUM(current_stock * unit_cost) as total')
            ->value('total') ?? 0;
        $almacenesActivos = InventoryLocation::where('is_active', true)->count();
        $hazmatActivos = HazmatProduct::where('is_active', true)->count();

        $kpis = [
            'salidas_mes' => $salidasMes,
            'salidas_tendencia' => $salidasTendencia,
            'entradas_mes' => $entradasMes,
            'entradas_tendencia' => $entradasTendencia,
            'stock_bajo' => $stockBajo,
            'pendientes' => $pendientesTotal,
            'consumables_activos' => $consumablesActivos,
            'valor_inventario' => $valorInventario,
            'almacenes' => $almacenesActivos,
            'hazmat_total' => $hazmatActivos,
        ];

        // ========================================
        // 3. GRÁFICA DE BARRAS: Movimientos últimos 6 meses
        // ========================================
        $months = collect([]);
        $dataSalidas = collect([]);
        $dataEntradas = collect([]);

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months->push($date->translatedFormat('M Y'));
            
            $dataSalidas->push(MaterialOutput::whereMonth('output_date', $date->month)
                                             ->whereYear('output_date', $date->year)
                                             ->count());
                                             
            $dataEntradas->push(MaterialReception::whereMonth('reception_date', $date->month)
                                               ->whereYear('reception_date', $date->year)
                                               ->count());
        }

        // ========================================
        // 4. GRÁFICA: Distribución por Almacén
        // ========================================
        $stockPorAlmacen = Consumable::where('is_active', true)
            ->whereNotNull('location_id')
            ->select('location_id', DB::raw('SUM(current_stock) as total_stock'))
            ->groupBy('location_id')
            ->with('location')
            ->get()
            ->mapWithKeys(fn($item) => [$item->location?->name ?? 'Sin ubicación' => $item->total_stock]);

        // ========================================
        // 5. GRÁFICA DE DONA: Hazmat por Estado Físico
        // ========================================
        $hazmatStats = HazmatProduct::where('is_active', true)
            ->select('physical_state', DB::raw('count(*) as total'))
            ->groupBy('physical_state')
            ->pluck('total', 'physical_state');

        // ========================================
        // 6. TABLAS: Actividad Reciente
        // ========================================
        $recentOutputs = MaterialOutput::with('terminal')
            ->latest()
            ->take(5)
            ->get();
            
        $recentReceptions = MaterialReception::with('terminal')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'kpis', 
            'months', 
            'dataSalidas', 
            'dataEntradas', 
            'hazmatStats',
            'stockPorAlmacen',
            'recentOutputs', 
            'recentReceptions',
            'lowStockProducts',
            'pendingOutputs',
            'pendingReceptions'
        ));
    }
}