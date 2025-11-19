<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Registrar Nueva Recepción de Material') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Formulario de Creación -->
                    <form method="POST" action="{{ route('material-receptions.store') }}" id="recepcion-form">
                        @csrf

                        <!-- Fila 1: Terminal y Fecha -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Terminal -->
                            <div>
                                <x-input-label for="terminal_id" :value="__('Terminal')" />
                                <select id="terminal_id" name="terminal_id"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    required>
                                    @foreach ($terminals as $terminal)
                                        <option value="{{ $terminal->id }}"
                                            {{ count($terminals) == 1 ? 'selected' : '' }}>
                                            {{ $terminal->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('terminal_id')" class="mt-2" />
                            </div>
                            <!-- Fecha de Recepción -->
                            <div>
                                <x-input-label for="reception_date" :value="__('Fecha de Recepción')" />
                                <x-text-input id="reception_date" class="block mt-1 w-full" type="date"
                                    name="reception_date" :value="old('reception_date', date('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('reception_date')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Fila 2: Tipo de Material y Descripción -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            <!-- Tipo de Material -->
                            <div>
                                <x-input-label for="material_type" :value="__('Tipo de Material')" />
                                <select id="material_type" name="material_type"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    required>
                                    <option value="" disabled selected>Selecciona un tipo</option>
                                    <option value="CONSUMIBLE"
                                        {{ old('material_type') == 'CONSUMIBLE' ? 'selected' : '' }}>CONSUMIBLE</option>
                                    <option value="SPARE_PART"
                                        {{ old('material_type') == 'SPARE_PART' ? 'selected' : '' }}>SPARE PART</option>
                                </select>
                                <x-input-error :messages="$errors->get('material_type')" class="mt-2" />
                            </div>
                            <!-- Descripción -->
                            <div>
                                <x-input-label for="description" :value="__('Descripción o Nombre del Material')" />
                                <x-text-input id="description" class="block mt-1 w-full" type="text"
                                    name="description" :value="old('description')" required />
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Fila 3: Proveedor y Orden de Compra (OC) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            <!-- Proveedor -->
                            <div>
                                <x-input-label for="provider" :value="__('Proveedor')" />
                                <x-text-input id="provider" class="block mt-1 w-full" type="text" name="provider"
                                    :value="old('provider')" required />
                                <x-input-error :messages="$errors->get('provider')" class="mt-2" />
                            </div>
                            <!-- Orden de Compra (OC) -->
                            <div>
                                <x-input-label for="purchase_order" :value="__('Orden de Compra (OC)')" />
                                <x-text-input id="purchase_order" class="block mt-1 w-full" type="text"
                                    name="purchase_order" :value="old('purchase_order')" required />
                                <x-input-error :messages="$errors->get('purchase_order')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Fila 4: No. Item y Cantidad -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            <!-- No. de Item -->
                            <div>
                                <x-input-label for="item_number" :value="__('No. de Item (Obligatorio si es Spare Part)')" />
                                <x-text-input id="item_number" class="block mt-1 w-full" type="text"
                                    name="item_number" :value="old('item_number')" />
                                <x-input-error :messages="$errors->get('item_number')" class="mt-2" />
                            </div>
                            <!-- Cantidad -->
                            <div>
                                <x-input-label for="quantity" :value="__('Cantidad Recibida')" />
                                <x-text-input id="quantity" class="block mt-1 w-full" type="number" step="0.01"
                                    name="quantity" :value="old('quantity')" required />
                                <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Fila 5: Confirmación SAP y Ubicación (Opcional) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            <!-- Confirmación de SAP -->
                            <div>
                                <x-input-label for="sap_confirmation" :value="__('Confirmación de SAP (Obligatorio si es Spare Part)')" />
                                <x-text-input id="sap_confirmation" class="block mt-1 w-full" type="text"
                                    name="sap_confirmation" :value="old('sap_confirmation')" />
                                <x-input-error :messages="$errors->get('sap_confirmation')" class="mt-2" />
                            </div>
                            <!-- Ubicación de Almacenamiento -->
                            <div>
                                <x-input-label for="storage_location" :value="__('Ubicación de Almacenamiento (Opcional)')" />
                                <x-text-input id="storage_location" class="block mt-1 w-full" type="text"
                                    name="storage_location" :value="old('storage_location')" />
                                <x-input-error :messages="$errors->get('storage_location')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Fila 6: Certificado de Calidad (Checkbox) -->
                        <div class="block mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <label for="quality_certificate" class="inline-flex items-center">
                                <input id="quality_certificate" type="checkbox" value="1"
                                    name="quality_certificate"
                                    class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                    {{ old('quality_certificate') ? 'checked' : '' }}>
                                <span
                                    class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('¿Cuenta con Certificado de Calidad? (SI/NO)') }}</span>
                            </label>
                            <x-input-error :messages="$errors->get('quality_certificate')" class="mt-2" />
                        </div>


                        <!-- Botones de Acción -->
                        <div
                            class="flex items-center justify-end mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('material-receptions.index') }}"
                                class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
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
