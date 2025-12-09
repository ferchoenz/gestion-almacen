<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detalle de Salida') }} #{{ $inventoryExit->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- INFORMACIÓN GENERAL -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">📋 Información General</h3>
                            
                            <div class="space-y-3">
                                <div>
                                    <div class="text-sm text-gray-500">Fecha de Salida</div>
                                    <div class="font-semibold">{{ $inventoryExit->exit_date->format('d/m/Y') }}</div>
                                </div>

                                <div>
                                    <div class="text-sm text-gray-500">Terminal</div>
                                    <div class="font-semibold">{{ $inventoryExit->terminal->name }}</div>
                                </div>

                                <div>
                                    <div class="text-sm text-gray-500">Registrado por</div>
                                    <div class="font-semibold">{{ $inventoryExit->user->name }}</div>
                                </div>

                                @if($inventoryExit->reference_document)
                                    <div>
                                        <div class="text-sm text-gray-500">Documento de Referencia</div>
                                        <div class="font-semibold font-mono">{{ $inventoryExit->reference_document }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- PRODUCTO -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">📦 Producto Retirado</h3>
                            
                            <div class="bg-red-50 dark:bg-red-900 p-4 rounded-lg border-2 border-red-200">
                                <div class="text-sm text-red-700 dark:text-red-300">SKU</div>
                                <div class="font-mono font-bold text-lg text-red-900 dark:text-red-100">{{ $inventoryExit->consumable->sku }}</div>
                                
                                <div class="text-sm text-red-700 dark:text-red-300 mt-3">Producto</div>
                                <div class="font-bold text-red-900 dark:text-red-100">{{ $inventoryExit->consumable->name }}</div>
                                
                                <div class="text-sm text-red-700 dark:text-red-300 mt-3">Cantidad Retirada</div>
                                <div class="text-3xl font-bold text-red-900 dark:text-red-100">
                                    -{{ number_format($inventoryExit->quantity, 2) }}
                                </div>
                                <div class="text-sm text-red-600">{{ $inventoryExit->consumable->unit_of_measure }}</div>
                            </div>

                            <div class="mt-4 text-xs text-gray-500">
                                <strong>Stock actual del producto:</strong> {{ number_format($inventoryExit->consumable->current_stock, 2) }} {{ $inventoryExit->consumable->unit_of_measure }}
                            </div>
                        </div>
                    </div>

                    <!-- DESTINATARIO -->
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">👤 Destinatario</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <div class="text-sm text-gray-500">Receptor</div>
                                <div class="font-semibold">{{ $inventoryExit->recipient_name }}</div>
                            </div>

                            @if($inventoryExit->department)
                                <div>
                                    <div class="text-sm text-gray-500">Departamento</div>
                                    <div class="font-semibold">{{ $inventoryExit->department }}</div>
                                </div>
                            @endif

                            @if($inventoryExit->purpose)
                                <div class="md:col-span-2">
                                    <div class="text-sm text-gray-500">Propósito</div>
                                    <div class="font-semibold">{{ $inventoryExit->purpose }}</div>
                                </div>
                            @endif

                            @if($inventoryExit->notes)
                                <div class="md:col-span-2">
                                    <div class="text-sm text-gray-500">Notas</div>
                                    <div class="text-sm">{{ $inventoryExit->notes }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- BOTONES -->
                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('inventory-exits.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                            Volver al Listado
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
