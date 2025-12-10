<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dashboard Ejecutivo') }}
            </h2>
            <span class="text-sm text-gray-500 dark:text-gray-400">
                📅 {{ now()->translatedFormat('l, d F Y') }}
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- ========================================== -->
            <!-- SECCIÓN DE ALERTAS -->
            <!-- ========================================== -->
            @if($lowStockProducts->count() > 0 || $pendingOutputs->count() > 0 || $pendingReceptions->count() > 0)
            <div class="bg-gradient-to-r from-red-500 to-orange-500 rounded-2xl shadow-lg p-6 text-white">
                <div class="flex items-center mb-4">
                    <svg class="w-8 h-8 mr-3 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <h3 class="text-xl font-bold">⚠️ Alertas del Sistema</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Stock Bajo -->
                    @if($lowStockProducts->count() > 0)
                    <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold">🔴 Stock Bajo</span>
                            <span class="bg-white text-red-600 px-2 py-1 rounded-full text-xs font-bold">{{ $lowStockProducts->count() }}</span>
                        </div>
                        <ul class="text-sm space-y-1 max-h-24 overflow-y-auto">
                            @foreach($lowStockProducts->take(3) as $product)
                                <li class="truncate">• {{ $product->name }} ({{ number_format($product->current_stock, 0) }}/{{ $product->min_stock }})</li>
                            @endforeach
                            @if($lowStockProducts->count() > 3)
                                <li class="text-yellow-200">+ {{ $lowStockProducts->count() - 3 }} más...</li>
                            @endif
                        </ul>
                    </div>
                    @endif

                    <!-- Salidas Pendientes -->
                    @if($pendingOutputs->count() > 0)
                    <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold">🟠 Salidas Pendientes OT</span>
                            <span class="bg-white text-orange-600 px-2 py-1 rounded-full text-xs font-bold">{{ $pendingOutputs->count() }}</span>
                        </div>
                        <ul class="text-sm space-y-1">
                            @foreach($pendingOutputs->take(3) as $output)
                                <li class="truncate">• {{ Str::limit($output->description, 25) }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- Recepciones Pendientes -->
                    @if($pendingReceptions->count() > 0)
                    <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold">🟡 Recepciones Pendientes</span>
                            <span class="bg-white text-yellow-600 px-2 py-1 rounded-full text-xs font-bold">{{ $pendingReceptions->count() }}</span>
                        </div>
                        <ul class="text-sm space-y-1">
                            @foreach($pendingReceptions->take(3) as $reception)
                                <li class="truncate">• {{ Str::limit($reception->description, 25) }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- ========================================== -->
            <!-- KPIs PRINCIPALES -->
            <!-- ========================================== -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Salidas del Mes -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg p-5 text-white transform hover:scale-105 transition-transform">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm">Salidas (Mes)</p>
                            <p class="text-3xl font-bold">{{ $kpis['salidas_mes'] }}</p>
                            <div class="flex items-center mt-1">
                                @if($kpis['salidas_tendencia'] >= 0)
                                    <span class="text-green-300 text-xs">↑ {{ $kpis['salidas_tendencia'] }}%</span>
                                @else
                                    <span class="text-red-300 text-xs">↓ {{ abs($kpis['salidas_tendencia']) }}%</span>
                                @endif
                                <span class="text-blue-200 text-xs ml-1">vs anterior</span>
                            </div>
                        </div>
                        <div class="p-3 bg-white/20 rounded-xl">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Entradas del Mes -->
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-lg p-5 text-white transform hover:scale-105 transition-transform">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm">Entradas (Mes)</p>
                            <p class="text-3xl font-bold">{{ $kpis['entradas_mes'] }}</p>
                            <div class="flex items-center mt-1">
                                @if($kpis['entradas_tendencia'] >= 0)
                                    <span class="text-green-300 text-xs">↑ {{ $kpis['entradas_tendencia'] }}%</span>
                                @else
                                    <span class="text-red-300 text-xs">↓ {{ abs($kpis['entradas_tendencia']) }}%</span>
                                @endif
                                <span class="text-green-200 text-xs ml-1">vs anterior</span>
                            </div>
                        </div>
                        <div class="p-3 bg-white/20 rounded-xl">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Stock Bajo -->
                <div class="bg-gradient-to-br {{ $kpis['stock_bajo'] > 0 ? 'from-red-500 to-red-600' : 'from-gray-400 to-gray-500' }} rounded-2xl shadow-lg p-5 text-white transform hover:scale-105 transition-transform">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="{{ $kpis['stock_bajo'] > 0 ? 'text-red-100' : 'text-gray-100' }} text-sm">Stock Bajo</p>
                            <p class="text-3xl font-bold">{{ $kpis['stock_bajo'] }}</p>
                            <p class="{{ $kpis['stock_bajo'] > 0 ? 'text-red-200' : 'text-gray-200' }} text-xs mt-1">
                                {{ $kpis['stock_bajo'] > 0 ? '¡Requiere atención!' : 'Todo en orden' }}
                            </p>
                        </div>
                        <div class="p-3 bg-white/20 rounded-xl">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Pendientes -->
                <div class="bg-gradient-to-br from-yellow-500 to-orange-500 rounded-2xl shadow-lg p-5 text-white transform hover:scale-105 transition-transform">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-yellow-100 text-sm">Pendientes</p>
                            <p class="text-3xl font-bold">{{ $kpis['pendientes'] }}</p>
                            <p class="text-yellow-200 text-xs mt-1">OT/Ubicación</p>
                        </div>
                        <div class="p-3 bg-white/20 rounded-xl">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KPIs SECUNDARIOS -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Consumibles Activos -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-5 border-l-4 border-indigo-500 hover:shadow-xl transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-300">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Consumibles</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $kpis['consumables_activos'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Valor en Inventario -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-5 border-l-4 border-emerald-500 hover:shadow-xl transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-emerald-100 dark:bg-emerald-900 text-emerald-600 dark:text-emerald-300">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Valor Inventario</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-white">${{ number_format($kpis['valor_inventario'], 0) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Almacenes -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-5 border-l-4 border-cyan-500 hover:shadow-xl transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-cyan-100 dark:bg-cyan-900 text-cyan-600 dark:text-cyan-300">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Almacenes</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $kpis['almacenes'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Hazmat -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-5 border-l-4 border-purple-500 hover:shadow-xl transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-300">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Hazmat</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $kpis['hazmat_total'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- GRÁFICAS -->
            <!-- ========================================== -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Gráfica de Movimientos -->
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                        <span class="w-3 h-3 bg-indigo-500 rounded-full mr-2"></span>
                        Movimientos - Últimos 6 meses
                    </h3>
                    <div class="h-72">
                        <canvas id="movementsChart"></canvas>
                    </div>
                </div>

                <!-- Gráfica de Distribución por Almacén -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                        <span class="w-3 h-3 bg-emerald-500 rounded-full mr-2"></span>
                        Stock por Almacén
                    </h3>
                    <div class="h-72">
                        <canvas id="warehouseChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TABLAS DE ACTIVIDAD -->
            <!-- ========================================== -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Últimas Salidas -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                            <span class="w-3 h-3 bg-blue-500 rounded-full mr-2"></span>
                            Últimas Salidas
                        </h3>
                        <a href="{{ route('material-outputs.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">Ver todo →</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500 dark:text-gray-400 border-b dark:border-gray-700">
                                    <th class="pb-3">Fecha</th>
                                    <th class="pb-3">Material</th>
                                    <th class="pb-3 text-right">Cant.</th>
                                    <th class="pb-3 text-right">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y dark:divide-gray-700">
                                @forelse($recentOutputs as $out)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="py-3 text-gray-600 dark:text-gray-300">{{ \Carbon\Carbon::parse($out->output_date)->format('d/m') }}</td>
                                    <td class="py-3 text-gray-800 dark:text-gray-100 truncate max-w-[150px]">{{ $out->description }}</td>
                                    <td class="py-3 text-right font-bold text-red-600">-{{ number_format($out->quantity, 0) }}</td>
                                    <td class="py-3 text-right">
                                        @if($out->status === 'COMPLETO')
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">✓</span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">⏳</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="py-4 text-center text-gray-400">Sin registros</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Últimas Recepciones -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                            <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                            Últimas Recepciones
                        </h3>
                        <a href="{{ route('material-receptions.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">Ver todo →</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500 dark:text-gray-400 border-b dark:border-gray-700">
                                    <th class="pb-3">Fecha</th>
                                    <th class="pb-3">Material</th>
                                    <th class="pb-3 text-right">Cant.</th>
                                    <th class="pb-3 text-right">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y dark:divide-gray-700">
                                @forelse($recentReceptions as $in)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="py-3 text-gray-600 dark:text-gray-300">{{ $in->reception_date->format('d/m') }}</td>
                                    <td class="py-3 text-gray-800 dark:text-gray-100 truncate max-w-[150px]">{{ $in->description }}</td>
                                    <td class="py-3 text-right font-bold text-green-600">+{{ number_format($in->quantity, 0) }}</td>
                                    <td class="py-3 text-right">
                                        @if($in->status === 'COMPLETO')
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">✓</span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">⏳</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="py-4 text-center text-gray-400">Sin registros</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- PRODUCTOS CON STOCK BAJO (SI HAY) -->
            <!-- ========================================== -->
            @if($lowStockProducts->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-red-600 dark:text-red-400 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Productos con Stock Bajo
                    </h3>
                    <a href="{{ route('consumables.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 font-medium">Ver catálogo →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 dark:text-gray-400 border-b dark:border-gray-700">
                                <th class="pb-3">SKU</th>
                                <th class="pb-3">Producto</th>
                                <th class="pb-3">Almacén</th>
                                <th class="pb-3 text-center">Stock Actual</th>
                                <th class="pb-3 text-center">Mínimo</th>
                                <th class="pb-3 text-right">Déficit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-700">
                            @foreach($lowStockProducts as $product)
                            <tr class="hover:bg-red-50 dark:hover:bg-red-900/20">
                                <td class="py-3 font-mono text-gray-600 dark:text-gray-300">{{ $product->sku }}</td>
                                <td class="py-3 text-gray-800 dark:text-gray-100 font-medium">{{ $product->name }}</td>
                                <td class="py-3 text-gray-600 dark:text-gray-300">{{ $product->location?->name ?? 'Sin asignar' }}</td>
                                <td class="py-3 text-center">
                                    <span class="px-2 py-1 rounded-full text-xs font-bold {{ $product->current_stock == 0 ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ number_format($product->current_stock, 0) }}
                                    </span>
                                </td>
                                <td class="py-3 text-center text-gray-600 dark:text-gray-300">{{ $product->min_stock }}</td>
                                <td class="py-3 text-right font-bold text-red-600">-{{ $product->min_stock - $product->current_stock }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>
    </div>

    <!-- SCRIPTS PARA GRÁFICAS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Colores con gradientes
            Chart.defaults.font.family = "'Inter', 'Segoe UI', sans-serif";
            
            // 1. Gráfica de Movimientos (Barras con gradiente)
            const ctxMoves = document.getElementById('movementsChart').getContext('2d');
            
            const gradientGreen = ctxMoves.createLinearGradient(0, 0, 0, 300);
            gradientGreen.addColorStop(0, 'rgba(34, 197, 94, 0.8)');
            gradientGreen.addColorStop(1, 'rgba(34, 197, 94, 0.2)');
            
            const gradientBlue = ctxMoves.createLinearGradient(0, 0, 0, 300);
            gradientBlue.addColorStop(0, 'rgba(59, 130, 246, 0.8)');
            gradientBlue.addColorStop(1, 'rgba(59, 130, 246, 0.2)');

            new Chart(ctxMoves, {
                type: 'bar',
                data: {
                    labels: @json($months),
                    datasets: [
                        {
                            label: 'Entradas',
                            data: @json($dataEntradas),
                            backgroundColor: gradientGreen,
                            borderColor: 'rgb(34, 197, 94)',
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false,
                        },
                        {
                            label: 'Salidas',
                            data: @json($dataSalidas),
                            backgroundColor: gradientBlue,
                            borderColor: 'rgb(59, 130, 246)',
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: { usePointStyle: true, padding: 20 }
                        }
                    },
                    scales: {
                        y: { 
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });

            // 2. Gráfica de Stock por Almacén (Dona)
            const ctxWarehouse = document.getElementById('warehouseChart').getContext('2d');
            const warehouseData = @json($stockPorAlmacen);
            
            new Chart(ctxWarehouse, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(warehouseData),
                    datasets: [{
                        data: Object.values(warehouseData),
                        backgroundColor: [
                            'rgba(99, 102, 241, 0.8)',
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(239, 68, 68, 0.8)',
                            'rgba(139, 92, 246, 0.8)',
                            'rgba(6, 182, 212, 0.8)',
                        ],
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { usePointStyle: true, padding: 15 }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>