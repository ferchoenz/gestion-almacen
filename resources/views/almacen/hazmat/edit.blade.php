<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detalle de Material Peligroso') }}: {{ $product->product_name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <form method="POST" action="{{ route('hazmat.update', $product) }}">
                        @csrf
                        @method('PATCH')

                        <!-- SWITCH DE STATUS -->
                        <div class="flex justify-end mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ $product->is_active ? 'checked' : '' }}>
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
                                <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Material Activo</span>
                            </label>
                        </div>

                        <!-- CAMPO TERMINAL -->
                        <div class="mb-6">
                            <x-input-label for="terminal_id" :value="__('Terminal')" />
                            <select id="terminal_id" name="terminal_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm font-bold bg-gray-50 dark:bg-gray-700" required>
                                @foreach($terminals as $terminal)
                                    <option value="{{ $terminal->id }}" {{ old('terminal_id', $product->terminal_id) == $terminal->id ? 'selected' : '' }}>
                                        {{ $terminal->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- DATOS DEL PRODUCTO -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <x-input-label for="product_name" :value="__('Nombre Comercial')" />
                                <x-text-input id="product_name" class="block mt-1 w-full" type="text" name="product_name" :value="$product->product_name" required />
                            </div>
                            <div>
                                <x-input-label for="chemical_name" :value="__('Nombre Químico')" />
                                <x-text-input id="chemical_name" class="block mt-1 w-full" type="text" name="chemical_name" :value="$product->chemical_name" required />
                            </div>
                        </div>

                        <!-- NUEVO: DATOS DEL FABRICANTE (Implementados) -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <x-input-label for="manufacturer" :value="__('Fabricante / Proveedor')" />
                                <x-text-input id="manufacturer" class="block mt-1 w-full" type="text" name="manufacturer" :value="$product->manufacturer" />
                            </div>
                            <div>
                                <x-input-label for="emergency_phone" :value="__('Teléfono de Emergencia')" />
                                <x-text-input id="emergency_phone" class="block mt-1 w-full" type="text" name="emergency_phone" :value="$product->emergency_phone" />
                            </div>
                            <div>
                                <x-input-label for="cas_number" :value="__('No. CAS')" />
                                <x-text-input id="cas_number" class="block mt-1 w-full" type="text" name="cas_number" :value="$product->cas_number" />
                            </div>
                        </div>

                        <div class="mb-6">
                            <x-input-label for="address" :value="__('Dirección del Fabricante')" />
                            <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="$product->address" />
                        </div>

                        <!-- NUEVO CAMPO EP P -->
                        <div class="mb-6">
                            <x-input-label for="epp" :value="__('Equipo de Protección Personal (EPP) Sugerido')" />
                            <textarea id="epp" name="epp" rows="3"
                                class="block mt-1 w-full border-gray-300 dark:bg-gray-700 dark:border-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 rounded-md shadow-sm">{{ $product->epp }}</textarea>
                        </div>

                        <!-- DATOS OPERATIVOS -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <x-input-label for="physical_state" :value="__('Estado Físico')" />
                                <select id="physical_state" name="physical_state" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 rounded-md shadow-sm">
                                    <option value="Líquido" {{ $product->physical_state == 'Líquido' ? 'selected' : '' }}>Líquido</option>
                                    <option value="Sólido" {{ $product->physical_state == 'Sólido' ? 'selected' : '' }}>Sólido</option>
                                    <option value="Gas" {{ $product->physical_state == 'Gas' ? 'selected' : '' }}>Gas</option>
                                    <option value="Gel" {{ $product->physical_state == 'Gel' ? 'selected' : '' }}>Gel</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="max_quantity" :value="__('Cantidad Máxima')" />
                                <x-text-input id="max_quantity" class="block mt-1 w-full" type="number" step="0.01" name="max_quantity" :value="$product->max_quantity" required />
                            </div>
                             <div>
                                <x-input-label for="location" :value="__('Ubicación')" />
                                <select id="location" name="location" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 rounded-md shadow-sm">
                                    <option value="Almacen Hazmat" {{ $product->location == 'Almacen Hazmat' ? 'selected' : '' }}>Almacén Hazmat</option>
                                    <option value="Almacen Proceso" {{ $product->location == 'Almacen Proceso' ? 'selected' : '' }}>Almacén de Proceso</option>
                                    <option value="Taller Mantenimiento" {{ $product->location == 'Taller Mantenimiento' ? 'selected' : '' }}>Taller de Mantenimiento</option>
                                    <option value="Almacen Limpieza" {{ $product->location == 'Almacen Limpieza' ? 'selected' : '' }}>Almacén de Limpieza</option>
                                    <option value="Cuarto Baterias" {{ $product->location == 'Cuarto Baterias' ? 'selected' : '' }}>Cuarto de Baterías</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-6">
                            <x-input-label for="department" :value="__('Departamento')" />
                            <x-text-input id="department" class="block mt-1 w-full" type="text" name="department" :value="$product->department" required />
                        </div>

                        <!-- CLASIFICACIÓN GHS -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Clasificación NOM-018-STPS-2015</h3>

                            <div class="mb-4">
                                <x-input-label for="signal_word" :value="__('Palabra de Advertencia')" />
                                <select id="signal_word" name="signal_word" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 rounded-md shadow-sm font-bold">
                                    <option value="SIN PALABRA" {{ $product->signal_word == 'SIN PALABRA' ? 'selected' : '' }}>SIN PALABRA</option>
                                    <option value="ATENCION" class="text-yellow-600" {{ $product->signal_word == 'ATENCION' ? 'selected' : '' }}>ATENCIÓN</option>
                                    <option value="PELIGRO" class="text-red-600" {{ $product->signal_word == 'PELIGRO' ? 'selected' : '' }}>PELIGRO</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <x-input-label for="hazard_statements" :value="__('Indicaciones de Peligro (H)')" />
                                    <textarea id="hazard_statements" name="hazard_statements" rows="4" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 rounded-md shadow-sm">{{ $product->hazard_statements }}</textarea>
                                </div>
                                <div>
                                    <x-input-label for="precautionary_statements" :value="__('Consejos de Prudencia (P)')" />
                                    <textarea id="precautionary_statements" name="precautionary_statements" rows="4" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 rounded-md shadow-sm">{{ $product->precautionary_statements }}</textarea>
                                </div>
                            </div>

                            <!-- PICTOGRAMAS (Checkboxes marcados) -->
                            <div class="mb-6">
                                <span class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">Pictogramas Aplicables</span>
                                <div class="grid grid-cols-3 md:grid-cols-5 gap-4">
                                    @php
                                        $pictograms = [
                                            'flame' => 'Inflamable',
                                            'flame_over_circle' => 'Comburente',
                                            'exploding_bomb' => 'Explosivo',
                                            'corrosion' => 'Corrosivo',
                                            'gas_cylinder' => 'Gas a Presión',
                                            'skull_and_crossbones' => 'Toxicidad Aguda',
                                            'exclamation_mark' => 'Irritante / Nocivo',
                                            'health_hazard' => 'Peligro Salud',
                                            'environment' => 'Medio Ambiente',
                                        ];
                                        // Obtenemos los pictogramas guardados o un array vacío
                                        $savedPictograms = $product->pictograms ?? [];
                                    @endphp

                                    @foreach($pictograms as $key => $label)
                                        <label class="flex flex-col items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ in_array($key, $savedPictograms) ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900' : 'border-gray-200 dark:border-gray-700' }}">
                                            
                                            <input type="checkbox" name="pictograms[]" value="{{ $key }}" class="sr-only peer" {{ in_array($key, $savedPictograms) ? 'checked' : '' }}>
                                            
                                            <span class="text-2xl mb-1">
                                                @switch($key)
                                                    @case('flame') 🔥 @break
                                                    @case('exploding_bomb') 💣 @break
                                                    @case('skull_and_crossbones') ☠️ @break
                                                    @case('environment') 🐟 @break
                                                    @case('corrosion') 🧪 @break
                                                    @case('health_hazard') ☣️ @break
                                                    @case('exclamation_mark') ❗ @break
                                                    @case('gas_cylinder') ⚗️ @break
                                                    @case('flame_over_circle') ⭕ @break
                                                    @default ⚠️
                                                @endswitch
                                            </span>
                                            <span class="text-xs text-center peer-checked:font-bold">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('hazmat.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button class="ml-4">
                                {{ __('Actualizar Producto') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>