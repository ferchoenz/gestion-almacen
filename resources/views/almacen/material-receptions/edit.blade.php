<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Editar Recepción de Material') }} (Folio: {{ $recepcione->id }})
            </h2>
            <!-- Status Badge -->
            @if($recepcione->status === 'PENDIENTE_OT')
                <span class="px-3 py-1 text-sm font-semibold text-yellow-800 bg-yellow-100 rounded-full">
                    ⚠️ PENDIENTE OT/SAP
                </span>
            @elseif($recepcione->status === 'COMPLETO')
                <span class="px-3 py-1 text-sm font-semibold text-green-800 bg-green-100 rounded-full">
                    ✅ COMPLETO
                </span>
            @else
                <span class="px-3 py-1 text-sm font-semibold text-gray-800 bg-gray-100 rounded-full">
                    {{ $recepcione->status }}
                </span>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Alerta para PENDIENTE_OT -->
                    @if($recepcione->status === 'PENDIENTE_OT')
                        <div class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-lg">
                            <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                <strong>⚠️ Esta recepción está pendiente.</strong> Complete la Orden de Trabajo y/o Confirmación SAP para cambiar el estado a COMPLETO.
                            </p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('material-receptions.update', $recepcione) }}" enctype="multipart/form-data"
                          x-data="{ materialType: '{{ $recepcione->material_type }}' }">
                        @csrf
                        @method('PATCH')

                        <!-- SECCIÓN BASE: Terminal, Fecha y Tipo (Solo lectura) -->
                        <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">📋 Información Base</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Terminal -->
                                <div>
                                    <x-input-label for="terminal_id" :value="__('Terminal')" />
                                    <select id="terminal_id" name="terminal_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" required>
                                        @foreach($terminals as $terminal)
                                            <option value="{{ $terminal->id }}" {{ old('terminal_id', $recepcione->terminal_id) == $terminal->id ? 'selected' : '' }}>
                                                {{ $terminal->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Fecha de Recepción -->
                                <div>
                                    <x-input-label for="reception_date" :value="__('Fecha de Recepción')" />
                                    <x-text-input id="reception_date" class="block mt-1 w-full" type="date" name="reception_date" :value="old('reception_date', $recepcione->reception_date->format('Y-m-d'))" required />
                                </div>

                                <!-- Tipo de Material (Solo lectura) -->
                                <div>
                                    <x-input-label :value="__('Tipo de Material')" />
                                    <div class="mt-1 px-3 py-2 bg-gray-100 dark:bg-gray-700 rounded-md text-sm font-semibold">
                                        @if($recepcione->material_type === 'CONSUMIBLE')
                                            📦 CONSUMIBLE
                                        @else
                                            🔧 SPARE PART
                                        @endif
                                    </div>
                                    <input type="hidden" name="material_type" value="{{ $recepcione->material_type }}">
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN CONSUMIBLE -->
                        @if($recepcione->material_type === 'CONSUMIBLE')
                            <div class="mt-4 p-4 bg-green-50 dark:bg-green-900/30 rounded-lg border border-green-200 dark:border-green-700">
                                <h3 class="text-sm font-semibold text-green-800 dark:text-green-200 mb-4">📦 Datos del Consumible</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Descripción -->
                                    <div>
                                        <x-input-label for="description" :value="__('Descripción')" />
                                        <x-text-input id="description" class="block mt-1 w-full bg-gray-100 dark:bg-gray-700" type="text" name="description" :value="old('description', $recepcione->description)" readonly />
                                    </div>

                                    <!-- Proveedor -->
                                    <div>
                                        <x-input-label for="provider" :value="__('Proveedor')" />
                                        <x-text-input id="provider" class="block mt-1 w-full" type="text" name="provider" :value="old('provider', $recepcione->provider)" required />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                    <!-- Orden de Compra -->
                                    <div>
                                        <x-input-label for="purchase_order" :value="__('Orden de Compra')" />
                                        <x-text-input id="purchase_order" class="block mt-1 w-full" type="text" name="purchase_order" :value="old('purchase_order', $recepcione->purchase_order)" required />
                                    </div>

                                    <!-- Cantidad -->
                                    <div>
                                        <x-input-label for="quantity" :value="__('Cantidad')" />
                                        <x-text-input id="quantity" class="block mt-1 w-full" type="number" step="0.01" name="quantity" :value="old('quantity', $recepcione->quantity)" required />
                                    </div>

                                    <!-- Ubicación -->
                                    <div>
                                        <x-input-label for="inventory_location_id" :value="__('Ubicación')" />
                                        <select id="inventory_location_id" name="inventory_location_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                                            <option value="">-- Sin ubicación --</option>
                                            @if(isset($inventoryLocations))
                                                @foreach($inventoryLocations as $location)
                                                    <option value="{{ $location->id }}" {{ old('inventory_location_id', $recepcione->inventory_location_id) == $location->id ? 'selected' : '' }}>
                                                        {{ $location->code }} - {{ $location->name }}
                                                    </option>
                                                @endforeach
                                            @endif
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Editar Recepción de Material') }} (Folio: {{ $recepcione->id }})
            </h2>
            <!-- Status Badge -->
            @if($recepcione->status === 'PENDIENTE_OT')
                <span class="px-3 py-1 text-sm font-semibold text-yellow-800 bg-yellow-100 rounded-full">
                    ⚠️ PENDIENTE OT/SAP
                </span>
            @elseif($recepcione->status === 'COMPLETO')
                <span class="px-3 py-1 text-sm font-semibold text-green-800 bg-green-100 rounded-full">
                    ✅ COMPLETO
                </span>
            @else
                <span class="px-3 py-1 text-sm font-semibold text-gray-800 bg-gray-100 rounded-full">
                    {{ $recepcione->status }}
                </span>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Alerta para PENDIENTE_OT -->
                    @if($recepcione->status === 'PENDIENTE_OT')
                        <div class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-lg">
                            <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                <strong>⚠️ Esta recepción está pendiente.</strong> Complete la Orden de Trabajo y/o Confirmación SAP para cambiar el estado a COMPLETO.
                            </p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('material-receptions.update', $recepcione) }}" enctype="multipart/form-data"
                          x-data="{ materialType: '{{ $recepcione->material_type }}' }">
                        @csrf
                        @method('PATCH')

                        <!-- SECCIÓN BASE: Terminal, Fecha y Tipo (Solo lectura) -->
                        <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">📋 Información Base</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Terminal -->
                                <div>
                                    <x-input-label for="terminal_id" :value="__('Terminal')" />
                                    <select id="terminal_id" name="terminal_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" required>
                                        @foreach($terminals as $terminal)
                                            <option value="{{ $terminal->id }}" {{ old('terminal_id', $recepcione->terminal_id) == $terminal->id ? 'selected' : '' }}>
                                                {{ $terminal->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Fecha de Recepción -->
                                <div>
                                    <x-input-label for="reception_date" :value="__('Fecha de Recepción')" />
                                    <x-text-input id="reception_date" class="block mt-1 w-full" type="date" name="reception_date" :value="old('reception_date', $recepcione->reception_date->format('Y-m-d'))" required />
                                </div>

                                <!-- Tipo de Material (Solo lectura) -->
                                <div>
                                    <x-input-label :value="__('Tipo de Material')" />
                                    <div class="mt-1 px-3 py-2 bg-gray-100 dark:bg-gray-700 rounded-md text-sm font-semibold">
                                        @if($recepcione->material_type === 'CONSUMIBLE')
                                            📦 CONSUMIBLE
                                        @else
                                            🔧 SPARE PART
                                        @endif
                                    </div>
                                    <input type="hidden" name="material_type" value="{{ $recepcione->material_type }}">
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN CONSUMIBLE -->
                        @if($recepcione->material_type === 'CONSUMIBLE')
                            <div class="mt-4 p-4 bg-green-50 dark:bg-green-900/30 rounded-lg border border-green-200 dark:border-green-700">
                                <h3 class="text-sm font-semibold text-green-800 dark:text-green-200 mb-4">📦 Datos del Consumible</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Descripción -->
                                    <div>
                                        <x-input-label for="description" :value="__('Descripción')" />
                                        <x-text-input id="description" class="block mt-1 w-full bg-gray-100 dark:bg-gray-700" type="text" name="description" :value="old('description', $recepcione->description)" readonly />
                                    </div>

                                    <!-- Proveedor -->
                                    <div>
                                        <x-input-label for="provider" :value="__('Proveedor')" />
                                        <x-text-input id="provider" class="block mt-1 w-full" type="text" name="provider" :value="old('provider', $recepcione->provider)" required />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                    <!-- Orden de Compra -->
                                    <div>
                                        <x-input-label for="purchase_order" :value="__('Orden de Compra')" />
                                        <x-text-input id="purchase_order" class="block mt-1 w-full" type="text" name="purchase_order" :value="old('purchase_order', $recepcione->purchase_order)" required />
                                    </div>

                                    <!-- Cantidad -->
                                    <div>
                                        <x-input-label for="quantity" :value="__('Cantidad')" />
                                        <x-text-input id="quantity" class="block mt-1 w-full" type="number" step="0.01" name="quantity" :value="old('quantity', $recepcione->quantity)" required />
                                    </div>

                                    <!-- Ubicación -->
                                    <div>
                                        <x-input-label for="inventory_location_id" :value="__('Ubicación')" />
                                        <select id="inventory_location_id" name="inventory_location_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                                            <option value="">-- Sin ubicación --</option>
                                            @if(isset($inventoryLocations))
                                                @foreach($inventoryLocations as $location)
                                                    <option value="{{ $location->id }}" {{ old('inventory_location_id', $recepcione->inventory_location_id) == $location->id ? 'selected' : '' }}>
                                                        {{ $location->code }} - {{ $location->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- SECCIÓN SPARE PART -->
                        @if($recepcione->material_type === 'SPARE_PART')
                            <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/30 rounded-lg border border-blue-200 dark:border-blue-700">
                                <h3 class="text-sm font-semibold text-blue-800 dark:text-blue-200 mb-4">🔧 Datos del Spare Part</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Descripción -->
                                    <div>
                                        <x-input-label for="description" :value="__('Descripción')" />
                                        <x-text-input id="description" class="block mt-1 w-full" type="text" name="description" :value="old('description', $recepcione->description)" required />
                                    </div>

                                    <!-- Proveedor -->
                                    <div>
                                        <x-input-label for="provider" :value="__('Proveedor')" />
                                        <x-text-input id="provider" class="block mt-1 w-full" type="text" name="provider" :value="old('provider', $recepcione->provider)" required />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                    <!-- Número de Item -->
                                    <div>
                                        <x-input-label for="item_number" :value="__('Número de Item')" />
                                        <x-text-input id="item_number" class="block mt-1 w-full" type="text" name="item_number" :value="old('item_number', $recepcione->item_number)" required />
                                    </div>

                                    <!-- Orden de Compra -->
                                    <div>
                                        <x-input-label for="purchase_order" :value="__('Orden de Compra')" />
                                        <x-text-input id="purchase_order" class="block mt-1 w-full" type="text" name="purchase_order" :value="old('purchase_order', $recepcione->purchase_order)" required />
                                    </div>

                                    <!-- Cantidad -->
                                    <div>
                                        <x-input-label for="quantity" :value="__('Cantidad')" />
                                        <x-text-input id="quantity" class="block mt-1 w-full" type="number" step="0.01" name="quantity" :value="old('quantity', $recepcione->quantity)" required />
                                    </div>
                                </div>

                                <!-- Sección OT/SAP para completar -->
                                <div class="mt-4 p-3 {{ $recepcione->status === 'PENDIENTE_OT' ? 'bg-yellow-100 dark:bg-yellow-900/50 border-2 border-yellow-400' : 'bg-gray-50 dark:bg-gray-800' }} rounded-lg">
                                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-3">
                                        {{ $recepcione->status === 'PENDIENTE_OT' ? '⚠️ Complete estos campos para finalizar la recepción:' : 'Documentación OT/SAP:' }}
                                    </p>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <!-- Orden de Trabajo -->
                                        <div>
                                            <x-input-label for="work_order" :value="__('Orden de Trabajo (Opcional)')" />
                                            <x-text-input id="work_order" class="block mt-1 w-full {{ !$recepcione->work_order ? 'border-yellow-400' : '' }}" type="text" name="work_order" :value="old('work_order', $recepcione->work_order)" placeholder="Ej: OT-12345" />
                                        </div>

                                        <!-- Confirmación SAP -->
                                        <div>
                                            <x-input-label for="sap_confirmation" :value="__('Confirmación SAP (Opcional)')" />
                                            <x-text-input id="sap_confirmation" class="block mt-1 w-full {{ !$recepcione->sap_confirmation ? 'border-yellow-400' : '' }}" type="text" name="sap_confirmation" :value="old('sap_confirmation', $recepcione->sap_confirmation)" placeholder="Ej: 4500012345" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Certificado de Calidad -->
                                <div class="mt-4 pt-4 border-t border-blue-200 dark:border-blue-700">
                                    <label for="quality_certificate" class="inline-flex items-center cursor-pointer">
                                        <input id="quality_certificate" type="checkbox" value="1" name="quality_certificate" 
                                               class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                               {{ old('quality_certificate', $recepcione->quality_certificate) ? 'checked' : '' }}>
                                        <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('¿Cuenta con Certificado de Calidad?') }}</span>
                                    </label>

                                    @if($recepcione->certificate_path)
                                        <p class="mt-2 text-xs text-green-600">✅ Certificado cargado</p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Documentos existentes -->
                        @if($recepcione->invoice_path || $recepcione->remission_path || $recepcione->certificate_path)
                            <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">📎 Documentos Adjuntos</h3>
                                <div class="flex flex-wrap gap-2">
                                    @if($recepcione->invoice_path)
                                        <a href="{{ route('material-receptions.file', [$recepcione, 'invoice']) }}" target="_blank" class="inline-flex items-center px-3 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded-full hover:bg-blue-200">
                                            📄 Factura
                                        </a>
                                    @endif
                                    @if($recepcione->remission_path)
                                        <a href="{{ route('material-receptions.file', [$recepcione, 'remission']) }}" target="_blank" class="inline-flex items-center px-3 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded-full hover:bg-blue-200">
                                            📄 Remisión
                                        </a>
                                    @endif
                                    @if($recepcione->certificate_path)
                                        <a href="{{ route('material-receptions.file', [$recepcione, 'certificate']) }}" target="_blank" class="inline-flex items-center px-3 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded-full hover:bg-blue-200">
                                            📄 Certificado
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Botones de Acción -->
                        <div class="flex items-center justify-end mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('material-receptions.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button class="ms-4">
                                {{ __('Actualizar Recepción') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>