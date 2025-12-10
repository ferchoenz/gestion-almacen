<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Ubicación de Inventario') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <form method="POST" action="{{ route('inventory-locations.update', $inventoryLocation) }}">
                        @csrf
                        @method('PUT')

                        <!-- Terminal (Solo Admin) -->
                        @if(Auth::user()->role->name === 'Administrador')
                            <div class="mb-4">
                                <x-input-label for="terminal_id" :value="__('Terminal')" />
                                <select id="terminal_id" name="terminal_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600" required>
                                    @foreach($terminals as $terminal)
                                        <option value="{{ $terminal->id }}" {{ old('terminal_id', $inventoryLocation->terminal_id) == $terminal->id ? 'selected' : '' }}>
                                            {{ $terminal->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('terminal_id')" class="mt-2" />
                            </div>
                        @else
                            <input type="hidden" name="terminal_id" value="{{ $inventoryLocation->terminal_id }}">
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Código -->
                            <div>
                                <x-input-label for="code" :value="__('Código')" />
                                <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" :value="old('code', $inventoryLocation->code)" required placeholder="Ej: ALM-REF" />
                                <x-input-error :messages="$errors->get('code')" class="mt-2" />
                            </div>

                            <!-- Nombre -->
                            <div>
                                <x-input-label for="name" :value="__('Nombre del Almacén')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $inventoryLocation->name)" required placeholder="Ej: Almacén de Refacciones" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Descripción (Opcional)')" />
                            <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600" placeholder="Descripción adicional del almacén...">{{ old('description', $inventoryLocation->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Estado -->
                        <div class="mt-4">
                            <label for="is_active" class="inline-flex items-center">
                                <input id="is_active" type="checkbox" class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="is_active" value="1" {{ old('is_active', $inventoryLocation->is_active) ? 'checked' : '' }}>
                                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Almacén Activo') }}</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('inventory-locations.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button class="ms-4">
                                {{ __('Actualizar Almacén') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
