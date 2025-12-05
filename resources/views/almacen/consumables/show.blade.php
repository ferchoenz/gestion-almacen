<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detalle de Consumible') }}: {{ $consumable->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            
            <!-- INFO BÁSICA -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <!-- IMAGEN -->
                        <div class="md:col-span-1">
                            @if($consumable->image_path && Storage::disk('public')->exists($consumable->image_path))
                                <img src="{{ asset('storage/' . $consumable->image_path) }}" alt="{{ $consumable->name }}" class="w-full h-48 object-cover rounded-lg border-2 border-gray-300">
                            @else
                                <div class="w-full h-48 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center border-2 border-gray-300">
                                    <svg class="w-20 h-20 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                </div>
                            @endif
                            
                            <div class="mt-4">
                                <div class="text-sm text-gray-500">SKU</div>
                                <div class="font-mono font-bold text-lg">{{ $consumable->sku }}</div>
                            </div>

                            <div class="mt-4">
                                <div class="text-sm text-gray-500">Código de Barras</div>
                                <div class="font-mono text-sm">{{ $consumable->barcode }}</div>
                            </div>
                        </div>

                        <!-- DETALLES -->
                        <div class="md:col-span-3">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $consumable->name }}</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $consumable->description }}</p>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                <div>
                                    <div class="text-sm text-gray-500">Categoría</div>
                                    <div class="font-semibold">{{ $consumable->category ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500">Unidad</div>
                                    <div class="font-semibold">{{ $consumable->unit_of_measure }}</div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500">Terminal</div>
                                    <div class="font-semibold">{{ $consumable->terminal->name }}</div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500">Ubicación</div>
                                    <div class="font-semibold">{{ $consumable->location?->code ?? 'Sin asignar' }}</div>
                                </div>
                            </div>

                            <!-- STOCK WIDGET -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg border-2 border-green-200">
                                    <div class="text-sm text-green-700 dark:text-green-300">Stock Actual</div>
                                    <div class="text-3xl font-bold text-green-900 dark:text-green-100">{{ number_format($consumable->current_stock, 2) }}</div>
                                    <div class="text-xs text-green-600">{{ $consumable->unit_of_measure }}</div>
                                </div>

                                <div class="bg-yellow-50 dark:bg-yellow-900 p-4 rounded-lg border-2 border-yellow-200">
                                    <div class="text-sm text-yellow-700 dark:text-yellow-300">Stock Mínimo</div>
                                    <div class="text-3xl font-bold text-yellow-900 dark:text-yellow-100">{{ number_format($consumable->min_stock, 2) }}</div>
                                    <div class="text-xs text-yellow-600">Punto de reorden</div>
                                </div>

                                <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg border-2 border-blue-200">
                                    <div class="text-sm text-blue-700 dark:text-blue-300">Costo Unitario</div>
                                    <div class="text-3xl font-bold text-blue-900 dark:text-blue-100">${{ number_format($consumable->unit_cost ?? 0, 2) }}</div>
                                    <div class="text-xs text-blue-600">Valor total: ${{ number_format(($consumable->unit_cost ?? 0) * $consumable->current_stock, 2) }}</div>
                                </div>
                            </div>

                            @if($consumable->isLowStock())
                                <div class="mt-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
                                    <div class="flex">
                                        <svg class="h-6 w-6 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        <p class="font-bold">⚠️ El stock está por debajo del mínimo. Se recomienda reabastecer.</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- MOVIMIENTOS RECIENTES (KARDEX) -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">📋 Movimientos Recientes (Últimos 10)</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3">Fecha</th>
                                    <th class="px-4 py-3">Tipo</th>
                                    <th class="px-4 py-3 text-right">Cantidad</th>
                                    <th class="px-4 py-3 text-right">Stock Anterior</th>
                                    <th class="px-4 py-3 text-right">Stock Nuevo</th>
                                    <th class="px-4 py-3">Usuario</th>
                                    <th class="px-4 py-3">Notas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentMovements as $movement)
                                <tr class="border-b dark:border-gray-700">
                                    <td class="px-4 py-3">{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 text-xs font-bold rounded {{ $movement->movement_color == 'green' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $movement->movement_icon }} {{ $movement->movement_type }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold">{{ number_format($movement->quantity, 2) }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($movement->previous_stock, 2) }}</td>
                                    <td class="px-4 py-3 text-right font-bold">{{ number_format($movement->new_stock, 2) }}</td>
                                    <td class="px-4 py-3">{{ $movement->user->name }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-500">{{ $movement->notes }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">No hay movimientos registrados</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex justify-end gap-3">
                        <a href="{{ route('consumables.edit', $consumable) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                            Editar Producto
                        </a>
                        <a href="{{ route('consumables.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                            Volver al Listado
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
