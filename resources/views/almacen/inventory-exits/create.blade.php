<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Registrar Salida de Inventario') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 dark:bg-red-800 text-red-700 dark:text-red-200 rounded-lg">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('inventory-exits.store') }}" x-data="{ selectedConsumable: null, availableStock: 0 }">
                        @csrf

                        <!-- TERMINAL Y FECHA -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <x-input-label for="terminal_id" :value="__('Terminal')" />
                                <select id="terminal_id" name="terminal_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" required>
                                    @foreach ($terminals as $terminal)
                                        <option value="{{ $terminal->id }}" {{ count($terminals) == 1 ? 'selected' : '' }}>{{ $terminal->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-input-label for="exit_date" :value="__('Fecha de Salida')" />
                                <x-text-input id="exit_date" class="block mt-1 w-full" type="date" name="exit_date" :value="old('exit_date', date('Y-m-d'))" required />
                            </div>
                        </div>

                        <!-- PRODUCTO Y CANTIDAD -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">📦 Producto a Retirar</h3>
                            
                            <div class="mb-6">
                                <x-input-label for="consumable_id" :value="__('Seleccionar Producto')" />
                                <select id="consumable_id" name="consumable_id" 
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" 
                                        required
                                        @change="
                                            if($event.target.value) {
                                                const text = $event.target.options[$event.target.selectedIndex].text;
                                                const stockMatch = text.match(/Stock: ([\d.]+)/);
                                                availableStock = stockMatch ? parseFloat(stockMatch[1]) : 0;
                                                selectedConsumable = $event.target.value;
                                            } else {
                                                availableStock = 0;
                                                selectedConsumable = null;
                                            }
                                        ">
                                    <option value="">-- Seleccionar producto --</option>
                                    @foreach($consumables as $consumable)
                                        <option value="{{ $consumable->id }}">
                                            {{ $consumable->sku }} - {{ $consumable->name }} (Stock: {{ number_format($consumable->current_stock, 2) }} {{ $consumable->unit_of_measure }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="quantity" :value="__('Cantidad a Retirar')" />
                                    <x-text-input id="quantity" 
                                                  class="block mt-1 w-full" 
                                                  type="number" 
                                                  step="0.01" 
                                                  name="quantity" 
                                                  :value="old('quantity')" 
                                                  x-bind:max="availableStock"
                                                  required />
                                    <p x-show="availableStock > 0" class="mt-1 text-sm" x-bind:class="availableStock > 0 ? 'text-green-600 dark:text-green-400' : 'text-gray-500'">
                                        ✅ Disponible: <span x-text="availableStock.toFixed(2)"></span>
                                    </p>
                                </div>

                                <div>
                                    <x-input-label for="reference_document" :value="__('Documento de Referencia (Opcional)')" />
                                    <x-text-input id="reference_document" class="block mt-1 w-full" type="text" name="reference_document" :value="old('reference_document')" placeholder="Ej: OT-12345, Ticket-67" />
                                </div>
                            </div>
                        </div>

                        <!-- DESTINATARIO -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">👤 Información del Destinatario</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <x-input-label for="recipient_name" :value="__('Nombre del Receptor')" />
                                    <x-text-input id="recipient_name" class="block mt-1 w-full" type="text" name="recipient_name" :value="old('recipient_name')" required />
                                </div>

                                <div>
                                    <x-input-label for="department" :value="__('Departamento (Opcional)')" />
                                    <x-text-input id="department" class="block mt-1 w-full" type="text" name="department" :value="old('department')" placeholder="Ej: Mantenimiento, Producción" />
                                </div>
                            </div>

                            <div class="mb-6">
                                <x-input-label for="purpose" :value="__('Propósito / Uso (Opcional)')" />
                                <x-text-input id="purpose" class="block mt-1 w-full" type="text" name="purpose" :value="old('purpose')" placeholder="Ej: Reparación de equipo" />
                            </div>

                            <div>
                                <x-input-label for="notes" :value="__('Notas Adicionales (Opcional)')" />
                                <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full border-gray-300 dark:bg-gray-700 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <!-- BOTONES -->
                        <div class="flex items-center justify-end mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('inventory-exits.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 rounded-md">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button class="ml-4">
                                {{ __('Registrar Salida') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
