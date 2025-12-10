<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Registrar Nuevo Consumible') }}
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

                    <form method="POST" action="{{ route('consumables.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- STATUS SWITCH -->
                        <div class="flex justify-end mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
                                <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Producto Activo</span>
                            </label>
                        </div>

                        <!-- TERMINAL -->
                        <div class="mb-6">
                            <x-input-label for="terminal_id" :value="__('Terminal')" />
                            <select id="terminal_id" name="terminal_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm" required>
                                @foreach ($terminals as $terminal)
                                    <option value="{{ $terminal->id }}" {{ count($terminals) == 1 ? 'selected' : '' }}>{{ $terminal->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- SKU y NOMBRE -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <x-input-label for="sku" :value="__('SKU (Código Único)')" />
                                <x-text-input id="sku" class="block mt-1 w-full bg-yellow-50 dark:bg-gray-700" type="text" name="sku" :value="old('sku')" required placeholder="Ej: CON-001" />
                                <p class="mt-1 text-xs text-gray-500">Debe ser único en el sistema</p>
                            </div>
                            <div>
                                <x-input-label for="name" :value="__('Nombre del Producto')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required />
                            </div>
                        </div>

                        <!-- DESCRIPCIÓN -->
                        <div class="mb-6">
                            <x-input-label for="description" :value="__('Descripción')" />
                            <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 dark:bg-gray-700 rounded-md shadow-sm">{{ old('description') }}</textarea>
                        </div>

                        <!-- CATEGORÍA Y UNIDAD -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <x-input-label for="category" :value="__('Categoría')" />
                                <x-text-input id="category" class="block mt-1 w-full" type="text" name="category" :value="old('category')" placeholder="Ej: Eléctrico, Plomería, Limpieza" />
                            </div>
                            <div>
                                <x-input-label for="unit_of_measure" :value="__('Unidad de Medida')" />
                                <select id="unit_of_measure" name="unit_of_measure" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" required>
                                    <option value="PZA">PZA - Pieza</option>
                                    <option value="KG">KG - Kilogramo</option>
                                    <option value="LT">LT - Litro</option>
                                    <option value="M">M - Metro</option>
                                    <option value="M2">M² - Metro Cuadrado</option>
                                    <option value="M3">M³ - Metro Cúbico</option>
                                    <option value="PAR">PAR - Par</option>
                                    <option value="CAJA">CAJA - Caja</option>
                                    <option value="PAQUETE">PAQUETE - Paquete</option>
                                </select>
                            </div>
                        </div>

                        <!-- CONTROL DE STOCK -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Control de Inventario</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                <div>
                                    <x-input-label for="current_stock" :value="__('Stock Inicial')" />
                                    <x-text-input id="current_stock" class="block mt-1 w-full bg-green-50 dark:bg-gray-700" type="number" step="0.01" name="current_stock" :value="old('current_stock', 0)" required />
                                </div>
                                <div>
                                    <x-input-label for="min_stock" :value="__('Stock Mínimo (Alerta)')" />
                                    <x-text-input id="min_stock" class="block mt-1 w-full bg-yellow-50 dark:bg-gray-700" type="number" step="0.01" name="min_stock" :value="old('min_stock', 0)" required />
                                </div>
                                <div>
                                    <x-input-label for="max_stock" :value="__('Stock Máximo (Opcional)')" />
                                    <x-text-input id="max_stock" class="block mt-1 w-full" type="number" step="0.01" name="max_stock" :value="old('max_stock')" />
                                </div>
                            </div>
                        </div>

                        <!-- COSTO Y UBICACIÓN -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <x-input-label for="unit_cost" :value="__('Costo Unitario (Opcional)')" />
                                <x-text-input id="unit_cost" class="block mt-1 w-full" type="number" step="0.01" name="unit_cost" :value="old('unit_cost')" placeholder="0.00" />
                            </div>
                            <div>
                                <x-input-label for="location_id" :value="__('Almacén / Área')" />
                                <select id="location_id" name="location_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                                    <option value="">-- Seleccionar Almacén --</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id }}">{{ $location->code }} - {{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- UBICACIÓN ESPECÍFICA -->
                        <div class="mb-6">
                            <x-input-label for="specific_location" :value="__('Ubicación Específica (Opcional)')" />
                            <x-text-input id="specific_location" class="block mt-1 w-full" type="text" name="specific_location" :value="old('specific_location')" placeholder="Ej: Pasillo 3, Rack A-2" />
                        </div>

                        <!-- IMAGEN -->
                        <div class="mb-6">
                            <x-input-label for="product_image" :value="__('Foto del Producto')" />
                            <input id="product_image" type="file" name="product_image" accept="image/*" class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:bg-gray-700 dark:border-gray-600">
                        </div>

                        <div class="flex items-center justify-end mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('consumables.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button class="ml-4">
                                {{ __('Guardar Consumible') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
