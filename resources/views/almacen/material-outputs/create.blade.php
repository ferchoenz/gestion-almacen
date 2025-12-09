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
            });
        </script>
    </x-slot>

</x-app-layout>