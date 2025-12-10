<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Registrar Nueva Recepción') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="POST" action="{{ route('material-receptions.store') }}" id="recepcion-form" enctype="multipart/form-data"
                          x-data="{ 
                              materialType: '{{ old('material_type', '') }}',
                              hasCertificate: {{ old('quality_certificate') ? 'true' : 'false' }},
                              submitting: false
                          }"
                          @submit="submitting = true">
                        @csrf

                        <!-- SECCIÓN BASE: Terminal, Fecha y Tipo de Material -->
                        <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">📋 Información Base</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Terminal -->
                                <div>
                                    <x-input-label for="terminal_id" :value="__('Terminal')" />
                                    <select id="terminal_id" name="terminal_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm" required>
                                        @foreach ($terminals as $terminal)
                                            <option value="{{ $terminal->id }}" {{ count($terminals) == 1 ? 'selected' : '' }}>
                                                {{ $terminal->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('terminal_id')" class="mt-2" />
                                </div>

                                <!-- Fecha de Recepción -->
                                <div>
                                    <x-input-label for="reception_date" :value="__('Fecha de Recepción')" />
                                    <x-text-input id="reception_date" class="block mt-1 w-full" type="date" name="reception_date" :value="old('reception_date', date('Y-m-d'))" required />
                                    <x-input-error :messages="$errors->get('reception_date')" class="mt-2" />
                                </div>

                                <!-- Tipo de Material -->
                                <div>
                                    <x-input-label for="material_type" :value="__('Tipo de Material')" />
                                    <select id="material_type" name="material_type" x-model="materialType" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm" required>
                                        <option value="" disabled>Selecciona un tipo</option>
                                        <option value="CONSUMIBLE" {{ old('material_type') == 'CONSUMIBLE' ? 'selected' : '' }}>CONSUMIBLE</option>
                                        <option value="SPARE_PART" {{ old('material_type') == 'SPARE_PART' ? 'selected' : '' }}>SPARE PART</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('material_type')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN CONSUMIBLE -->
                        <!-- Usamos x-bind:disabled para deshabilitar todos los inputs dentro cuando no es visible -->
                        <fieldset x-show="materialType === 'CONSUMIBLE'" x-bind:disabled="materialType !== 'CONSUMIBLE'"
                                  x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" 
                                  class="mt-4 p-4 bg-green-50 dark:bg-green-900/30 rounded-lg border border-green-200 dark:border-green-700" style="display: none;">
                            
                            <h3 class="text-sm font-semibold text-green-800 dark:text-green-200 mb-4">📦 Entrada de Consumible</h3>
                            
                            <!-- Selector de Inventario -->
                            <div class="mb-4">
                                <x-input-label for="consumable_id" :value="__('Seleccionar del Catálogo')" />
                                <select id="consumable_id" name="consumable_id" 
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm"
                                        @change="if($event.target.value) { 
                                            const text = $event.target.options[$event.target.selectedIndex].text;
                                            const parts = text.split(' - ');
                                            const name = parts[1] ? parts[1].split('(')[0].trim() : '';
                                            document.getElementById('description_consumible').value = name;
                                        } else {
                                            document.getElementById('description_consumible').value = '';
                                        }">
                                    <option value="">-- Seleccionar producto --</option>
                                    @foreach($consumables as $consumable)
                                        <option value="{{ $consumable->id }}">
                                            {{ $consumable->sku }} - {{ $consumable->name }} (Stock: {{ number_format($consumable->current_stock, 2) }} {{ $consumable->unit_of_measure }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-green-600 dark:text-green-300">💡 El stock se actualizará automáticamente</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Proveedor -->
                                <div>
                                    <x-input-label for="provider_consumible" :value="__('Proveedor')" />
                                    <x-text-input id="provider_consumible" class="block mt-1 w-full" type="text" name="provider" :value="old('provider')" required />
                                </div>

                                <!-- Descripción (auto-completada) -->
                                <div>
                                    <x-input-label for="description_consumible" :value="__('Descripción (Auto-completada)')" />
                                    <x-text-input id="description_consumible" class="block mt-1 w-full bg-gray-100 dark:bg-gray-700" type="text" name="description" :value="old('description')" readonly />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                <!-- Orden de Compra -->
                                <div>
                                    <x-input-label for="purchase_order_consumible" :value="__('Orden de Compra (OC)')" />
                                    <x-text-input id="purchase_order_consumible" class="block mt-1 w-full" type="text" name="purchase_order" :value="old('purchase_order')" required />
                                </div>

                                <!-- Cantidad -->
                                <div>
                                    <x-input-label for="quantity_consumible" :value="__('Cantidad Recibida')" />
                                    <x-text-input id="quantity_consumible" class="block mt-1 w-full" type="number" step="0.01" name="quantity" :value="old('quantity')" required />
                                </div>

                                <!-- Ubicación de Almacenamiento -->
                                <div>
                                    <x-input-label for="inventory_location_id" :value="__('Ubicación de Almacenamiento')" />
                                    <select id="inventory_location_id" name="inventory_location_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm">
                                        <option value="">-- Seleccionar ubicación --</option>
                                        @if(isset($inventoryLocations))
                                            @foreach($inventoryLocations as $location)
                                                <option value="{{ $location->id }}">{{ $location->code }} - {{ $location->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <!-- Documentación Consumible -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 pt-4 border-t border-green-200 dark:border-green-700">
                                <div>
                                    <x-input-label for="invoice_consumible" :value="__('Factura (PDF)')" />
                                    <input id="invoice_consumible" type="file" name="invoice_file" accept=".pdf" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:bg-gray-700 dark:border-gray-600">
                                </div>
                                <div>
                                    <x-input-label for="remission_consumible" :value="__('Remisión (PDF)')" />
                                    <input id="remission_consumible" type="file" name="remission_file" accept=".pdf" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:bg-gray-700 dark:border-gray-600">
                                </div>
                            </div>
                        </fieldset>

                        <!-- SECCIÓN SPARE PART -->
                        <fieldset x-show="materialType === 'SPARE_PART'" x-bind:disabled="materialType !== 'SPARE_PART'"
                                  x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                                  class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/30 rounded-lg border border-blue-200 dark:border-blue-700" style="display: none;">
                            
                            <h3 class="text-sm font-semibold text-blue-800 dark:text-blue-200 mb-4">🔧 Entrada de Spare Part</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Descripción Manual -->
                                <div>
                                    <x-input-label for="description_spare" :value="__('Descripción del Material')" />
                                    <x-text-input id="description_spare" class="block mt-1 w-full" type="text" name="description" :value="old('description')" required />
                                </div>

                                <!-- Proveedor -->
                                <div>
                                    <x-input-label for="provider_spare" :value="__('Proveedor')" />
                                    <x-text-input id="provider_spare" class="block mt-1 w-full" type="text" name="provider" :value="old('provider')" required />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                <!-- Número de Item -->
                                <div>
                                    <x-input-label for="item_number" :value="__('Número de Item')" />
                                    <x-text-input id="item_number" class="block mt-1 w-full" type="text" name="item_number" :value="old('item_number')" required />
                                </div>

                                <!-- Orden de Compra -->
                                <div>
                                    <x-input-label for="purchase_order_spare" :value="__('Orden de Compra (OC)')" />
                                    <x-text-input id="purchase_order_spare" class="block mt-1 w-full" type="text" name="purchase_order" :value="old('purchase_order')" required />
                                </div>

                                <!-- Cantidad -->
                                <div>
                                    <x-input-label for="quantity_spare" :value="__('Cantidad Recibida')" />
                                    <x-text-input id="quantity_spare" class="block mt-1 w-full" type="number" step="0.01" name="quantity" :value="old('quantity')" required />
                                </div>
                            </div>

                            <!-- Campos Opcionales (OT y SAP) -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-700">
                                <div class="col-span-2">
                                    <p class="text-xs text-yellow-700 dark:text-yellow-300 mb-2">⚠️ Campos opcionales - Si no se completan, el estado será "PENDIENTE_OT"</p>
                                </div>
                                
                                <!-- Orden de Trabajo (TEXTO) -->
                                <div>
                                    <x-input-label for="work_order" :value="__('Orden de Trabajo (Opcional)')" />
                                    <x-text-input id="work_order" class="block mt-1 w-full" type="text" name="work_order" :value="old('work_order')" placeholder="Ej: OT-12345" />
                                </div>

                                <!-- Confirmación SAP -->
                                <div>
                                    <x-input-label for="sap_confirmation" :value="__('Confirmación SAP (Opcional)')" />
                                    <x-text-input id="sap_confirmation" class="block mt-1 w-full" type="text" name="sap_confirmation" :value="old('sap_confirmation')" placeholder="Ej: 45000..." />
                                </div>
                            </div>

                            <!-- Certificado de Calidad -->
                            <div class="mt-4 pt-4 border-t border-blue-200 dark:border-blue-700">
                                <label for="quality_certificate" class="inline-flex items-center cursor-pointer">
                                    <input id="quality_certificate" type="checkbox" value="1" name="quality_certificate" 
                                           class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                           x-model="hasCertificate"
                                           {{ old('quality_certificate') ? 'checked' : '' }}>
                                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('¿Cuenta con Certificado de Calidad?') }}</span>
                                </label>

                                <div class="mt-3" x-show="hasCertificate" x-transition>
                                    <x-input-label for="certificate_file" :value="__('Certificado de Calidad (PDF)')" />
                                    <input id="certificate_file" type="file" name="certificate_file" accept=".pdf" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:bg-gray-700 dark:border-gray-600">
                                </div>
                            </div>

                            <!-- Documentación Spare Part -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 pt-4 border-t border-blue-200 dark:border-blue-700">
                                <div>
                                    <x-input-label for="remission_spare" :value="__('Remisión (PDF)')" />
                                    <input id="remission_spare" type="file" name="remission_file" accept=".pdf" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:bg-gray-700 dark:border-gray-600">
                                </div>
                                <div>
                                    <x-input-label for="invoice_spare" :value="__('Factura (PDF)')" />
                                    <input id="invoice_spare" type="file" name="invoice_file" accept=".pdf" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:bg-gray-700 dark:border-gray-600">
                                </div>
                            </div>
                        </fieldset>

                        <!-- Botones de Acción -->
                        <div class="flex items-center justify-end mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('material-receptions.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                {{ __('Cancelar') }}
                            </a>
                            
                            <x-primary-button class="ms-4 flex items-center" id="submit-form-btn" x-bind:disabled="!materialType || submitting">
                                <svg x-show="submitting" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="submitting ? 'Guardando...' : 'Guardar Recepción'"></span>
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>