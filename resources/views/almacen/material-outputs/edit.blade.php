<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Registro de Salida') }} (ID: {{ $salida->id }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="POST" action="{{ route('material-outputs.update', $salida) }}" id="salida-form">
                        @csrf
                        @method('PATCH') 

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="terminal_id" :value="__('Terminal')" />
                                <select id="terminal_id" name="terminal_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    @foreach($terminals as $terminal)
                                        <option value="{{ $terminal->id }}" {{ old('terminal_id', $salida->terminal_id) == $terminal->id ? 'selected' : '' }}>
                                            {{ $terminal->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('terminal_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="output_date" :value="__('Fecha de Salida')" />
                                <x-text-input id="output_date" class="block mt-1 w-full" type="date" name="output_date" :value="old('output_date', $salida->output_date)" required />
                                <x-input-error :messages="$errors->get('output_date')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            <div>
                                <x-input-label for="material_type" :value="__('Tipo de Material')" />
                                <select id="material_type" name="material_type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    <option value="CONSUMIBLE" {{ old('material_type', $salida->material_type) == 'CONSUMIBLE' ? 'selected' : '' }}>CONSUMIBLE</option>
                                    <option value="SPARE_PART" {{ old('material_type', $salida->material_type) == 'SPARE_PART' ? 'selected' : '' }}>SPARE PART</option>
                                </select>
                                <x-input-error :messages="$errors->get('material_type')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="item_number" :value="__('No. de Item (Obligatorio si es Spare Part)')" />
                                <x-text-input id="item_number" class="block mt-1 w-full" type="text" name="item_number" :value="old('item_number', $salida->item_number)" />
                                <x-input-error :messages="$errors->get('item_number')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            <div>
                                <x-input-label for="description" :value="__('Descripción o Nombre del Material')" />
                                <x-text-input id="description" class="block mt-1 w-full" type="text" name="description" :value="old('description', $salida->description)" required />
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="quantity" :value="__('Cantidad (unidades retiradas)')" />
                                <x-text-input id="quantity" class="block mt-1 w-full" type="number" step="0.01" name="quantity" :value="old('quantity', $salida->quantity)" required />
                                <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-4">
                            <div>
                                <x-input-label for="receiver_name" :value="__('Nombre de la persona que recibe')" />
                                <x-text-input id="receiver_name" class="block mt-1 w-full" type="text" name="receiver_name" :value="old('receiver_name', $salida->receiver_name)" required />
                                <x-input-error :messages="$errors->get('receiver_name')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div>
                                <x-input-label for="work_order" :value="__('Orden de Trabajo (OT)')" />
                                <x-text-input id="work_order" class="block mt-1 w-full" type="text" name="work_order" :value="old('work_order', $salida->work_order)" />
                                <x-input-error :messages="$errors->get('work_order')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="sap_confirmation" :value="__('Confirmación de SAP')" />
                                <x-text-input id="sap_confirmation" class="block mt-1 w-full" type="text" name="sap_confirmation" :value="old('sap_confirmation', $salida->sap_confirmation)" />
                                <x-input-error :messages="$errors->get('sap_confirmation')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-gray-100">Firma de Quien Recibió:</h3>
                                <div class="mt-2 p-2 bg-gray-100 dark:bg-gray-900 rounded-md shadow-inner">
                                    <img src="{{ $salida->receiver_signature }}" alt="Firma Recibe" class="w-full h-48 bg-white dark:bg-gray-200 rounded-md object-contain">
                                </div>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-gray-100">Firma de Quien Entregó:</h3>
                                <div class="mt-2 p-2 bg-gray-100 dark:bg-gray-900 rounded-md shadow-inner">
                                    <img src="{{ $salida->deliverer_signature }}" alt="Firma Entrega" class="w-full h-48 bg-white dark:bg-gray-200 rounded-md object-contain">
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('material-outputs.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button class="ms-4" id="submit-form-btn">
                                {{ __('Actualizar Salida') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>