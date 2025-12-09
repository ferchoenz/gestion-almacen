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

                    <!-- Formulario de Creación -->
                    <!-- Usamos x-data para controlar el estado del checkbox -->
                    <form method="POST" action="{{ route('material-receptions.store') }}" id="recepcion-form" enctype="multipart/form-data"
                          x-data="{ hasCertificate: {{ old('quality_certificate') ? 'true' : 'false' }} }">
                        @csrf

                        <!-- Fila 1: Terminal y Fecha -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Terminal -->
                            <div>
                                <x-input-label for="terminal_id" :value="__('Terminal')" />
                                <select id="terminal_id" name="terminal_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
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
                        </div>

                        <!-- Fila 2: Tipo de Material y Descripción -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            <!-- Tipo de Material -->
                            <div>
                                <x-input-label for="material_type" :value="__('Tipo de Material')" />
                                <select id="material_type" name="material_type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    <option value="" disabled selected>Selecciona un tipo</option>
                                    <option value="CONSUMIBLE" {{ old('material_type') == 'CONSUMIBLE' ? 'selected' : '' }}>CONSUMIBLE</option>
                                    <option value="SPARE_PART" {{ old('material_type') == 'SPARE_PART' ? 'selected' : '' }}>SPARE PART</option>
                                </select>
                                <x-input-error :messages="$errors->get('material_type')" class="mt-2" />
                            </div>
                            <!-- Descripción -->
                            <!-- NUEVO: Producto del Inventario o Descripción Manual -->
                            <div>
                                <x-input-label for="consumable_id" :value="__('Producto del Inventario (Opcional)')" />
                                <select id="consumable_id" name="consumable_id" 
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm"
                                        onchange="if(this.value) { 
                                            const text = this.options[this.selectedIndex].text;
                                            const parts = text.split(' - ');
                                            const name = parts[1] ? parts[1].split('(')[0].trim() : '';
                                            document.getElementById('description').value = name;
                                            document.getElementById('description').classList.add('bg-gray-100');
                                            document.getElementById('desc-label').innerHTML = 'Descripción (Auto-completado)';
                                        } else {
                                            document.getElementById('description').value = '';
                                            document.getElementById('description').classList.remove('bg-gray-100');
                                            document.getElementById('desc-label').innerHTML = 'Descripción del Material';
                                        }">
                                    <option value="">-- Seleccionar del catálogo (opcional) --</option>
                                    @foreach($consumables as $consumable)
                                        <option value="{{ $consumable->id }}">
                                            {{ $consumable->sku }} - {{ $consumable->name }} ({{ number_format($consumable->current_stock, 2) }} {{ $consumable->unit_of_measure }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-blue-600 dark:text-blue-400">💡 Si seleccionas un producto, el stock se actualizará automáticamente</p>
                                
                                <x-input-label for="description" class="mt-3" id="desc-label" :value="__('Descripción del Material')" />
                                <x-text-input id="description" class="block mt-1 w-full" type="text" name="description" :value="old('description')" placeholder="O escribe descripción manual si no seleccionaste del catálogo" />
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Fila 3: Proveedor y Orden de Compra (OC) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            <!-- Proveedor -->
                            <div>
                                <x-input-label for="provider" :value="__('Proveedor')" />
                                <x-text-input id="provider" class="block mt-1 w-full" type="text" name="provider" :value="old('provider')" required />
                                <x-input-error :messages="$errors->get('provider')" class="mt-2" />
                            </div>
                            <!-- Orden de Compra (OC) -->
                            <div>
                                <x-input-label for="purchase_order" :value="__('Orden de Compra (OC)')" />
                                <x-text-input id="purchase_order" class="block mt-1 w-full" type="text" name="purchase_order" :value="old('purchase_order')" required />
                                <x-input-error :messages="$errors->get('purchase_order')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Fila 4: No. Item, Cantidad, SAP y Ubicación -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            <!-- No. de Item -->
                            <div>
                                <x-input-label for="item_number" :value="__('No. de Item (Solo si es Spare Part)')" />
                                <x-text-input id="item_number" class="block mt-1 w-full" type="text" name="item_number" :value="old('item_number')" />
                                <x-input-error :messages="$errors->get('item_number')" class="mt-2" />
                            </div>
                            <!-- Cantidad -->
                            <div>
                                <x-input-label for="quantity" :value="__('Cantidad Recibida')" />
                                <x-text-input id="quantity" class="block mt-1 w-full" type="number" step="0.01" name="quantity" :value="old('quantity')" required />
                                <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            <!-- Confirmación de SAP (Ahora opcional) -->
                            <div>
                                <x-input-label for="sap_confirmation" :value="__('Confirmación de SAP (Opcional)')" />
                                <x-text-input id="sap_confirmation" class="block mt-1 w-full" type="text" name="sap_confirmation" :value="old('sap_confirmation')" />
                                <x-input-error :messages="$errors->get('sap_confirmation')" class="mt-2" />
                            </div>
                            <!-- Ubicación (Opcional) -->
                            <div>
                                <x-input-label for="storage_location" :value="__('Ubicación de Almacenamiento (Opcional)')" />
                                <x-text-input id="storage_location" class="block mt-1 w-full" type="text" name="storage_location" :value="old('storage_location')" />
                                <x-input-error :messages="$errors->get('storage_location')" class="mt-2" />
                            </div>
                        </div>


                        <!-- Fila 5: Checkbox de Certificado -->
                        <div class="block mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <label for="quality_certificate" class="inline-flex items-center cursor-pointer">
                                <!-- Conectamos el checkbox al modelo de Alpine 'hasCertificate' -->
                                <input id="quality_certificate" type="checkbox" value="1" name="quality_certificate" 
                                       class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                       x-model="hasCertificate"
                                       {{ old('quality_certificate') ? 'checked' : '' }}>
                                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('¿Cuenta con Certificado de Calidad? (SI/NO)') }}</span>
                            </label>
                            <x-input-error :messages="$errors->get('quality_certificate')" class="mt-2" />
                        </div>


                        <!-- ARCHIVOS ADJUNTOS -->
                        <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-4">Documentación Adjunta</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="invoice_file" :value="__('Factura (PDF)')" />
                                    <input id="invoice_file" type="file" name="invoice_file" accept=".pdf" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                                    <x-input-error :messages="$errors->get('invoice_file')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="remission_file" :value="__('Remisión (PDF)')" />
                                    <input id="remission_file" type="file" name="remission_file" accept=".pdf" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                                    <x-input-error :messages="$errors->get('remission_file')" class="mt-2" />
                                </div>
                            </div>

                            <!-- CAMPO CERTIFICADO (Condicional con Alpine) -->
                            <!-- Se muestra solo si hasCertificate es true -->
                            <div class="mt-6" x-show="hasCertificate" x-transition>
                                <x-input-label for="certificate_file" :value="__('Certificado Calidad (PDF) - Requerido si marcó SI')" />
                                <input id="certificate_file" type="file" name="certificate_file" accept=".pdf" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                                <x-input-error :messages="$errors->get('certificate_file')" class="mt-2" />
                            </div>
                        </div>


                        <!-- Botones de Acción -->
                        <div class="flex items-center justify-end mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('material-receptions.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button class="ms-4" id="submit-form-btn">
                                {{ __('Guardar Recepción') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>