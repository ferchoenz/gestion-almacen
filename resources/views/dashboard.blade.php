<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Ejecutivo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- 1. TARJETAS DE KPI (Indicadores Clave) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Salidas del Mes -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Salidas (Este Mes)</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $kpis['salidas_mes'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Entradas del Mes -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-500">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Entradas (Este Mes)</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $kpis['entradas_mes'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Hazmat Activos -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-purple-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-500">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Hazmat Activos</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $kpis['hazmat_total'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Pendientes -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-500">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pendientes (OT/Ubic)</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $kpis['pendientes'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. GRÁFICAS -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                
                <!-- Gráfica de Barras (Movimientos) -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 lg:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Movimientos últimos 6 meses</h3>
                    <canvas id="movementsChart" height="150"></canvas>
                </div>

                <!-- Gráfica de Dona (Hazmat) -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Hazmat por Estado Físico</h3>
                    <div class="relative h-64">
                        <canvas id="hazmatChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- 3. TABLAS DE ACTIVIDAD RECIENTE -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Últimas Salidas -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Últimas Salidas</h3>
                        <a href="{{ route('material-outputs.index') }}" class="text-sm text-indigo-600 hover:underline">Ver todo</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-2">Fecha</th>
                                    <th class="px-4 py-2">Material</th>
                                    <th class="px-4 py-2">Cant.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOutputs as $out)
                                <tr class="border-b dark:border-gray-700">
                                    <td class="px-4 py-2">{{ \Carbon\Carbon::parse($out->output_date)->format('d/m') }}</td>
                                    <td class="px-4 py-2 truncate max-w-[150px]">{{ $out->description }}</td>
                                    <td class="px-4 py-2 font-bold text-red-600">-{{ $out->quantity }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Últimas Entradas -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Últimas Recepciones</h3>
                        <a href="{{ route('material-receptions.index') }}" class="text-sm text-indigo-600 hover:underline">Ver todo</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-2">Fecha</th>
                                    <th class="px-4 py-2">Material</th>
                                    <th class="px-4 py-2">Cant.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentReceptions as $in)
                                <tr class="border-b dark:border-gray-700">
                                    <td class="px-4 py-2">{{ $in->reception_date->format('d/m') }}</td>
                                    <td class="px-4 py-2 truncate max-w-[150px]">{{ $in->description }}</td>
                                    <td class="px-4 py-2 font-bold text-green-600">+{{ $in->quantity }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- SCRIPTS PARA GRÁFICAS (Chart.js) -->
    <!-- Cargamos Chart.js desde CDN (No requiere instalación) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // 1. Configuración Gráfica de Barras (Movimientos)
            const ctxMoves = document.getElementById('movementsChart').getContext('2d');
            new Chart(ctxMoves, {
                type: 'bar',
                data: {
                    labels: @json($months), // Datos desde el controlador
                    datasets: [
                        {
                            label: 'Entradas',
                            data: @json($dataEntradas),
                            backgroundColor: 'rgba(34, 197, 94, 0.6)', // Verde
                            borderColor: 'rgba(34, 197, 94, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Salidas',
                            data: @json($dataSalidas),
                            backgroundColor: 'rgba(59, 130, 246, 0.6)', // Azul
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: { y: { beginAtZero: true } }
                }
            });

            // 2. Configuración Gráfica de Dona (Hazmat)
            const ctxHazmat = document.getElementById('hazmatChart').getContext('2d');
            const hazmatData = @json($hazmatStats);
            
            new Chart(ctxHazmat, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(hazmatData),
                    datasets: [{
                        data: Object.values(hazmatData),
                        backgroundColor: [
                            'rgba(239, 68, 68, 0.7)',  // Rojo
                            'rgba(59, 130, 246, 0.7)', // Azul
                            'rgba(245, 158, 11, 0.7)', // Amarillo
                            'rgba(16, 185, 129, 0.7)', // Verde
                        ],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right' }
                    }
                }
            });
        });
    </script>
</x-app-layout>