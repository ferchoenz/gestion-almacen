<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Registrar Nueva Salida de Material') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <form method="POST" action="{{ route('material-outputs.store') }}" id="salida-form">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="terminal_id" :value="__('Terminal')" />
                                <select id="terminal_id" name="terminal_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    @foreach($terminals as $terminal)
                                        <option value="{{ $terminal->id }}" {{ count($terminals) == 1 ? 'selected' : '' }}>
                                            {{ $terminal->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('terminal_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="output_date" :value="__('Fecha de Salida')" />
                                <x-text-input id="output_date" class="block mt-1 w-full" type="date" name="output_date" :value="old('output_date', date('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('output_date')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            <div>
                                <x-input-label for="material_type" :value="__('Tipo de Material')" />
                                <select id="material_type" name="material_type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    <option value="" disabled selected>Selecciona un tipo</option>
                                    <option value="CONSUMIBLE" {{ old('material_type') == 'CONSUMIBLE' ? 'selected' : '' }}>CONSUMIBLE</option>
                                    <option value="SPARE_PART" {{ old('material_type') == 'SPARE_PART' ? 'selected' : '' }}>SPARE PART</option>
                                </select>
                                <x-input-error :messages="$errors->get('material_type')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="work_order" :value="__('Orden de Trabajo (OT) (Opcional)')" />
                                <x-text-input id="work_order" class="block mt-1 w-full" type="text" name="work_order" :value="old('work_order')" />
                                <x-input-error :messages="$errors->get('work_order')" class="mt-2" />
                            </div>
                        </div>

                        <!-- SECCIÓN CONDICIONAL: SPARE PART -->
                        <div id="spare-part-section" style="display:none;" class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg">
                            <h3 class="text-sm font-semibold text-yellow-800 dark:text-yellow-200 mb-3">🔧 Spare Part</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="item_number" :value="__('No. de Item (Requerido)')" />
                                    <x-text-input id="item_number" class="block mt-1 w-full" type="text" name="item_number" :value="old('item_number')" />
                                    <x-input-error :messages="$errors->get('item_number')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="description" :value="__('Descripción del Material')" />
                                    <x-text-input id="description" class="block mt-1 w-full" type="text" name="description" :value="old('description')" />
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <x-input-label for="quantity" :value="__('Cantidad')" />
                                    <x-text-input id="quantity" class="block mt-1 w-full" type="number" step="0.01" name="quantity" :value="old('quantity')" />
                                    <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="receiver_name" :value="__('Nombre de quien recibe')" />
                                    <x-text-input id="receiver_name" class="block mt-1 w-full" type="text" name="receiver_name" :value="old('receiver_name')" />
                                    <x-input-error :messages="$errors->get('receiver_name')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN CONDICIONAL: CONSUMIBLE -->
                        <div id="consumible-section" style="display:none;" class="mt-4 p-4 bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg" x-data="{ selectedStock: 0, warehouseId: '', filteredConsumables: [] }">
                            <h3 class="text-sm font-semibold text-green-800 dark:text-green-200 mb-3">📦 Consumible - Inventario</h3>
                            
                            <!-- Selector de Almacén -->
                            <div class="mb-4">
                                <x-input-label for="warehouse_filter" :value="__('Almacén Origen')" />
                                <select id="warehouse_filter" 
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm"
                                        @change="warehouseId = $event.target.value; document.getElementById('consumable_id').value = ''; selectedStock = 0; document.getElementById('description_consumible').value = ''"
                                        required>
                                    <option value="">-- Seleccionar Almacén --</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}">{{ $location->code }} - {{ $location->name }}</option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Primero selecciona el almacén para filtrar los consumibles</p>
                            </div>

                            <div class="mb-4">
                                <x-input-label for="consumable_id" :value="__('Seleccionar del Catálogo (Opcional)')" />
                                <select id="consumable_id" name="consumable_id" 
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm"
                                        x-bind:disabled="!warehouseId"
                                        @change="if($event.target.value) { 
                                            const text = $event.target.options[$event.target.selectedIndex].text;
                                            const parts = text.split(' - ');
                                            const name = parts[1] ? parts[1].split('(')[0].trim() : '';
                                            const stockMatch = text.match(/Stock: ([\d.]+)/);
                                            selectedStock = stockMatch ? parseFloat(stockMatch[1]) : 0;
                                            document.getElementById('description_consumible').value = name;
                                        } else {
                                            selectedStock = 0;
                                            document.getElementById('description_consumible').value = '';
                                        }">
                                    <option value="">-- Dejar vacío para salida manual --</option>
                                    @foreach($consumables as $consumable)
                                        <option value="{{ $consumable->id }}" 
                                                data-warehouse="{{ $consumable->location_id }}"
                                                x-show="warehouseId == '{{ $consumable->location_id }}'">
                                            {{ $consumable->sku }} - {{ $consumable->name }} (Stock: {{ number_format($consumable->current_stock, 2) }} {{ $consumable->unit_of_measure }})
                                            @if($consumable->specific_location)
                                                - 📍 {{ $consumable->specific_location }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-green-600 dark:text-green-300">
                                    💡 Si seleccionas, el stock se reducirá automáticamente
                                </p>
                                <p x-show="selectedStock > 0" class="mt-1 text-sm font-bold text-green-700">
                                    ✅ Stock disponible: <span x-text="selectedStock.toFixed(2)"></span> unidades
                                </p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="description_consumible" :value="__('Descripción')" />
                                    <x-text-input id="description_consumible" class="block mt-1 w-full" type="text" name="description" :value="old('description')" placeholder="Auto-completa o escribe manual" />
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="quantity_consumible" :value="__('Cantidad')" />
                                    <x-text-input id="quantity_consumible" class="block mt-1 w-full" type="number" step="0.01" name="quantity" :value="old('quantity')" />
                                    <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                                </div>
                            </div>
                            <div class="mt-4">
                                <x-input-label for="receiver_name_consumible" :value="__('Nombre de quien recibe')" />
                                <x-text-input id="receiver_name_consumible" class="block mt-1 w-full" type="text" name="receiver_name" :value="old('receiver_name')" />
                                <x-input-error :messages="$errors->get('receiver_name')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-gray-100">Firma de Quien Recibe</h3>
                                <div class="mt-2 p-2 bg-gray-100 dark:bg-gray-900 rounded-md shadow-inner">
                                    <canvas id="signature-pad-receiver" class="w-full h-48 bg-white dark:bg-gray-200 rounded-md"></canvas>
                                </div>
                                <button type="button" id="clear-receiver" class="mt-2 text-sm text-gray-600 dark:text-gray-400 hover:text-red-500">Limpiar Firma</button>
                                <x-input-error :messages="$errors->get('receiver_signature')" class="mt-2" />
                            </div>

                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-gray-100">Firma de Quien Entrega ({{ Auth::user()->name }})</h3>
                                <div class="mt-2 p-2 bg-gray-100 dark:bg-gray-900 rounded-md shadow-inner">
                                    <canvas id="signature-pad-deliverer" class="w-full h-48 bg-white dark:bg-gray-200 rounded-md"></canvas>
                                </div>
                                <button type="button" id="clear-deliverer" class="mt-2 text-sm text-gray-600 dark:text-gray-400 hover:text-red-500">Limpiar Firma</button>
                                <x-input-error :messages="$errors->get('deliverer_signature')" class="mt-2" />
                            </div>
                        </div>
                        
                        <input type="hidden" name="receiver_signature" id="receiver_signature">
                        <input type="hidden" name="deliverer_signature" id="deliverer_signature">


                        <div class="flex items-center justify-end mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('material-outputs.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button class="ms-4" id="submit-form-btn">
                                {{ __('Guardar Salida') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <x-slot name="scripts">
        
        <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>

        <script>
            // ¡Ya no necesitamos 'import' ni 'type="module"'!
    
            // --- Esta función es un truco para que los canvas funcionen bien ---
            function resizeCanvas(canvas) {
                const ratio =  Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
            }
    
            // --- Espera a que toda la página esté cargada ---
            document.addEventListener('DOMContentLoaded', () => {
                
                try {
                    const canvasReceiver = document.getElementById('signature-pad-receiver');
                    const canvasDeliverer = document.getElementById('signature-pad-deliverer');
                    
                    const clearReceiverBtn = document.getElementById('clear-receiver');
                    const clearDelivererBtn = document.getElementById('clear-deliverer');
        
                    const hiddenInputReceiver = document.getElementById('receiver_signature');
                    const hiddenInputDeliverer = document.getElementById('deliverer_signature');
                    
                    const form = document.getElementById('salida-form');
                    const submitBtn = document.getElementById('submit-form-btn');

                    if (!form || !canvasReceiver || !canvasDeliverer) {
                        return;
                    }
        
                    // --- Redimensionamos los canvas para alta definición ---
                    resizeCanvas(canvasReceiver);
                    resizeCanvas(canvasDeliverer);
        
                    // --- Inicializamos la librería en nuestros dos canvas ---
                    // La librería ahora existe en el objeto global 'window.SignaturePad'
                    const sigPadReceiver = new SignaturePad(canvasReceiver);
                    const sigPadDeliverer = new SignaturePad(canvasDeliverer);
        
                    // --- Programamos los botones de "Limpiar" ---
                    clearReceiverBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        sigPadReceiver.clear();
                        hiddenInputReceiver.value = '';
                    });
        
                    clearDelivererBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        sigPadDeliverer.clear();
                        hiddenInputDeliverer.value = '';
                    });
        
                    // --- ¡LA PARTE MÁS IMPORTANTE! ---
                    form.addEventListener('submit', (e) => {
                        
                        submitBtn.disabled = true;
                        submitBtn.innerText = 'Guardando...';
        
                        if (sigPadReceiver.isEmpty()) {
                            alert('Por favor, añade la firma de quien recibe.');
                            e.preventDefault();
                            submitBtn.disabled = false;
                            submitBtn.innerText = 'Guardar Salida';
                            return;
                        }
                        
                        if (sigPadDeliverer.isEmpty()) {
                            alert('Por favor, añade la firma de quien entrega.');
                            e.preventDefault();
                            submitBtn.disabled = false;
                            submitBtn.innerText = 'Guardar Salida';
                            return;
                        }
        
                        hiddenInputReceiver.value = sigPadReceiver.toDataURL('image/png');
                        hiddenInputDeliverer.value = sigPadDeliverer.toDataURL('image/png');
                    });
                } catch (error) {
                    console.error('Error al inicializar los pads de firma:', error);
                }

                // NUEVO: Lógica para mostrar/ocultar secciones según tipo de material
                const materialTypeSelect = document.getElementById('material_type');
                const sparePartSection = document.getElementById('spare-part-section');
                const consumibleSection = document.getElementById('consumible-section');

                if (materialTypeSelect) {
                    materialTypeSelect.addEventListener('change', function() {
                        const selectedType = this.value;
                        
                        if (selectedType === 'SPARE_PART') {
                            sparePartSection.style.display = 'block';
                            consumibleSection.style.display = 'none';
                        } else if (selectedType === 'CONSUMIBLE') {
                            sparePartSection.style.display = 'none';
                            consumibleSection.style.display = 'block';
                        } else {
                            sparePartSection.style.display = 'none';
                            consumibleSection.style.display = 'none';
                        }
                    });

                    // Ejecutar al cargar para mantener estado si hay old() values
                    if (materialTypeSelect.value) {
                        materialTypeSelect.dispatchEvent(new Event('change'));
                    }
                }
            });
        </script>
    </x-slot>

</x-app-layout>