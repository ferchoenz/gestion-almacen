<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Consumible') }}: {{ $consumable->sku }}
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

                    <form method="POST" action="{{ route('consumables.update', $consumable) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <!-- STATUS SWITCH -->
                         <div class="flex justify-end mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ $consumable->is_active ? 'checked' : '' }}>
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
                                <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Producto Activo</span>
                            </label>
                        </div>

                        <!-- TERMINAL -->
                        <div class="mb-6">
                            <x-input-label for="terminal_id" :value="__('Terminal')" />
                            <select id="terminal_id" name="terminal_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm" required>
                                @foreach ($terminals as $terminal)
                                    <option value="{{ $terminal->id }}" {{ old('terminal_id', $consumable->terminal_id) == $terminal->id ? 'selected' : '' }}>
                                        {{ $terminal->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- SKU (NO EDITABLE) y NOMBRE -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <x-input-label for="sku_display" :value="__('SKU (No editable)')" />
                                <x-text-input id="sku_display" class="block mt-1 w-full bg-gray-200 dark:bg-gray-600" type="text" :value="$consumable->sku" disabled />
                                <p class="mt-1 text-xs text-gray-500">El SKU no se puede modificar</p>
                            </div>
                            <div>
                                <x-input-label for="name" :value="__('Nombre del Producto')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $consumable->name)" required />
                            </div>
                        </div>

                        <!-- DESCRIPCIÓN -->
                        <div class="mb-6">
                            <x-input-label for="description" :value="__('Descripción')" />
                            <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 dark:bg-gray-700 rounded-md shadow-sm">{{ old('description', $consumable->description) }}</textarea>
                        </div>

                        <!-- CATEGORÍA Y UNIDAD -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <x-input-label for="category" :value="__('Categoría')" />
                                <x-text-input id="category" class="block mt-1 w-full" type="text" name="category" :value="old('category', $consumable->category)" />
                            </div>
                            <div>
                                <x-input-label for="unit_of_measure" :value="__('Unidad de Medida')" />
                                <select id="unit_of_measure" name="unit_of_measure" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" required>
                                    <option value="PZA" {{ $consumable->unit_of_measure == 'PZA' ? 'selected' : '' }}>PZA - Pieza</option>
                                    <option value="KG" {{ $consumable->unit_of_measure == 'KG' ? 'selected' : '' }}>KG - Kilogramo</option>
                                    <option value="LT" {{ $consumable->unit_of_measure == 'LT' ? 'selected' : '' }}>LT - Litro</option>
                                    <option value="M" {{ $consumable->unit_of_measure == 'M' ? 'selected' : '' }}>M - Metro</option>
                                    <option value="M2" {{ $consumable->unit_of_measure == 'M2' ? 'selected' : '' }}>M² - Metro Cuadrado</option>
                                    <option value="M3" {{ $consumable->unit_of_measure == 'M3' ? 'selected' : '' }}>M³ - Metro Cúbico</option>
                                    <option value="PAR" {{ $consumable->unit_of_measure == 'PAR' ? 'selected' : '' }}>PAR - Par</option>
                                    <option value="CAJA" {{ $consumable->unit_of_measure == 'CAJA' ? 'selected' : '' }}>CAJA - Caja</option>
                                    <option value="PAQUETE" {{ $consumable->unit_of_measure == 'PAQUETE' ? 'selected' : '' }}>PAQUETE - Paquete</option>
                                </select>
                            </div>
                        </div>

                        <!-- CONTROL DE STOCK (Solo min/max, NO current_stock) -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Control de Inventario</h3>
                            <p class="text-sm text-yellow-600 dark:text-yellow-400 mb-4">⚠️ El stock actual ({{ number_format($consumable->current_stock, 2) }} {{ $consumable->unit_of_measure }}) NO se edita aquí. Use Entradas/Salidas para modificarlo.</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <x-input-label for="min_stock" :value="__('Stock Mínimo (Alerta)')" />
                                    <x-text-input id="min_stock" class="block mt-1 w-full bg-yellow-50 dark:bg-gray-700" type="number" step="0.01" name="min_stock" :value="old('min_stock', $consumable->min_stock)" required />
                                </div>
                                <div>
                                    <x-input-label for="max_stock" :value="__('Stock Máximo (Opcional)')" />
                                    <x-text-input id="max_stock" class="block mt-1 w-full" type="number" step="0.01" name="max_stock" :value="old('max_stock', $consumable->max_stock)" />
                                </div>
                            </div>
                        </div>

                        <!-- COSTO Y UBICACIÓN -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <x-input-label for="unit_cost" :value="__('Costo Unitario (Opcional)')" />
                                <x-text-input id="unit_cost" class="block mt-1 w-full" type="number" step="0.01" name="unit_cost" :value="old('unit_cost', $consumable->unit_cost)" />
                            </div>
                            <div>
                                <x-input-label for="location_id" :value="__('Ubicación en Almacén')" />
                                <select id="location_id" name="location_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                                    <option value="">-- Sin asignar --</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id }}" {{ old('location_id', $consumable->location_id) == $location->id ? 'selected' : '' }}>
                                            {{ $location->code }} - {{ $location->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- IMAGEN -->
                        <div class="mb-6">
                            @if($consumable->image_path)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $consumable->image_path) }}" alt="{{ $consumable->name }}" class="w-32 h-32 object-cover rounded border">
                                </div>
                            @endif
                            <x-input-label for="product_image" :value="__('Cambiar Foto del Producto')" />
                            <input id="product_image" type="file" name="product_image" accept="image/*" class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:bg-gray-700 dark:border-gray-600">
                        </div>

                        <div class="flex items-center justify-end mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('consumables.show', $consumable) }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button class="ml-4">
                                {{ __('Actualizar Consumible') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
