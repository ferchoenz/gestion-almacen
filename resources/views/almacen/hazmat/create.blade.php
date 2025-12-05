<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Registrar Nuevo Material Peligroso') }}
        </h2>
    </x-slot>

    <!-- x-data controla el estado del formulario y la notificación -->
    <div class="py-12" x-data="{ ...hazmatForm(), submitting: false }">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 relative">

            <!-- TOAST DE ÉXITO (VERDE) -->
            <div x-show="showSuccess" x-transition
                class="fixed top-20 right-5 z-50 max-w-sm w-full bg-white dark:bg-gray-800 shadow-lg rounded-lg border-l-4 border-green-500 p-4 flex items-start"
                style="display: none;">
                <div class="flex-shrink-0"><svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg></div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">¡Análisis Completado!</p>
                    <p class="mt-1 text-sm text-gray-500">Datos extraídos correctamente.</p>
                </div>
                <button @click="showSuccess = false" class="ml-4 text-gray-400 hover:text-gray-500"><svg class="h-5 w-5"
                        viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg></button>
            </div>

            <!-- TOAST DE ERROR (ROJO) - NUEVO -->
            <div x-show="showErrorToast" x-transition
                class="fixed top-20 right-5 z-50 max-w-sm w-full bg-white dark:bg-gray-800 shadow-lg rounded-lg border-l-4 border-red-500 p-4 flex items-start"
                style="display: none;">
                <div class="flex-shrink-0"><svg class="h-6 w-6 text-red-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg></div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Atención</p>
                    <p class="mt-1 text-sm text-gray-500" x-text="errorMessage"></p>
                </div>
                <button @click="showErrorToast = false" class="ml-4 text-gray-400 hover:text-gray-500"><svg
                        class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg></button>
            </div>

            <!-- SECCIÓN DE ANÁLISIS CON IA -->
            <div class="bg-indigo-50 dark:bg-indigo-900 p-6 rounded-lg mb-6 border border-indigo-200 dark:border-indigo-700 shadow-sm">
                <h3 class="text-lg font-bold text-indigo-800 dark:text-indigo-200 mb-2 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Autocompletar información
                </h3>
                <p class="text-sm text-indigo-600 dark:text-indigo-300 mb-4">
                    Sube la Hoja de Datos de Seguridad (HDS) en PDF y el sistema extraerá la información automáticamente.
                </p>

                <div class="flex gap-4 items-center">
                    <input type="file" x-ref="hdsInput" accept=".pdf" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                    
                    <button type="button" @click="analyzePdf" 
                            class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center"
                            :disabled="loading">
                        <svg x-show="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span x-text="loading ? 'Analizando...' : 'Analizar PDF'"></span>
                    </button>
                </div>
                <p x-show="errorMessage" x-text="errorMessage" class="text-red-600 text-sm mt-2 font-bold"></p>
            </div>


            <!-- FORMULARIO PRINCIPAL -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- FORMULARIO CON ANIMACIÓN DE GUARDADO -->
                    <!-- Usamos @submit para activar la bandera 'submitting' -->
                    <form method="POST" action="{{ route('hazmat.store') }}" enctype="multipart/form-data"
                        @submit="submitting = true">
                        @csrf

                        <!-- SWITCH DE STATUS (Activo por defecto) -->
                        <div class="flex justify-end mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>
                                <div
                                    class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600">
                                </div>
                                <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">
                                    Material Activo
                                </span>
                            </label>
                        </div>

                        <!-- CAMPO TERMINAL -->
                        <div class="mb-6">
                            <x-input-label for="terminal_id" :value="__('Terminal')" />
                            <select id="terminal_id" name="terminal_id"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm font-bold bg-gray-50 dark:bg-gray-700"
                                required>
                                @foreach ($terminals as $terminal)
                                    <option value="{{ $terminal->id }}" {{ count($terminals) == 1 ? 'selected' : '' }}>
                                        {{ $terminal->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- DATOS DEL PRODUCTO -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <x-input-label for="product_name" :value="__('Nombre Comercial del Producto')" />
                                <x-text-input id="product_name" class="block mt-1 w-full bg-indigo-50 dark:bg-gray-700"
                                    type="text" name="product_name" x-model="form.product_name" required />
                            </div>
                            <div>
                                <x-input-label for="chemical_name" :value="__('Nombre Químico / Técnico')" />
                                <x-text-input id="chemical_name" class="block mt-1 w-full bg-indigo-50 dark:bg-gray-700"
                                    type="text" name="chemical_name" x-model="form.chemical_name" required />
                            </div>
                        </div>

                        <!-- DATOS DEL FABRICANTE (NUEVOS) -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <x-input-label for="manufacturer" :value="__('Fabricante / Proveedor')" />
                                <x-text-input id="manufacturer" class="block mt-1 w-full bg-indigo-50 dark:bg-gray-700"
                                    type="text" name="manufacturer" x-model="form.manufacturer" />
                            </div>
                            <div>
                                <x-input-label for="emergency_phone" :value="__('Teléfono de Emergencia')" />
                                <x-text-input id="emergency_phone"
                                    class="block mt-1 w-full bg-indigo-50 dark:bg-gray-700" type="text"
                                    name="emergency_phone" x-model="form.emergency_phone" />
                            </div>
                            <div>
                                <x-input-label for="cas_number" :value="__('No. CAS (Uno o varios)')" />
                                <x-text-input id="cas_number" class="block mt-1 w-full bg-indigo-50 dark:bg-gray-700"
                                    type="text" name="cas_number" x-model="form.cas_number" />
                            </div>
                        </div>

                        <!-- NUEVO CAMPO EPP -->
                        <div class="mb-6">
                            <x-input-label for="epp" :value="__('Equipo de Protección Personal (EPP) Sugerido')" />
                            <textarea id="epp" name="epp" x-model="form.epp" rows="3"
                                class="block mt-1 w-full border-gray-300 dark:bg-gray-700 rounded-md shadow-sm"></textarea>
                        </div>

                        <div class="mb-6">
                            <x-input-label for="address" :value="__('Dirección del Fabricante')" />
                            <x-text-input id="address" class="block mt-1 w-full bg-indigo-50 dark:bg-gray-700"
                                type="text" name="address" x-model="form.address" />
                        </div>

                        <!-- DATOS OPERATIVOS -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <x-input-label for="physical_state" :value="__('Estado Físico')" />
                                <select id="physical_state" name="physical_state"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 rounded-md shadow-sm"
                                    required>
                                    <option value="">-- Seleccionar --</option>
                                    <option value="Líquido">Líquido</option>
                                    <option value="Sólido">Sólido</option>
                                    <option value="Gas">Gas</option>
                                    <option value="Gel">Gel</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="max_quantity" :value="__('Cantidad Máxima (Kg/L)')" />
                                <x-text-input id="max_quantity" class="block mt-1 w-full" type="number"
                                    step="0.01" name="max_quantity" required />
                            </div>
                            <div>
                                <x-input-label for="location" :value="__('Ubicación de Almacenamiento')" />
                                <select id="location" name="location"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 rounded-md shadow-sm"
                                    required>
                                    <option value="">-- Seleccionar --</option>
                                    <option value="Almacen Hazmat">Almacén Hazmat</option>
                                    <option value="Almacen Proceso">Almacén de Proceso</option>
                                    <option value="Taller Mantenimiento">Taller de Mantenimiento</option>
                                    <option value="Almacen Limpieza">Almacén de Limpieza</option>
                                    <option value="Cuarto Baterias">Cuarto de Baterías</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-6">
                            <x-input-label for="department" :value="__('Departamento que usa')" />
                            <x-text-input id="department" class="block mt-1 w-full" type="text" name="department"
                                required />
                        </div>

                        <!-- CLASIFICACIÓN NOM-018 -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Clasificación
                                NOM-018-STPS-2015</h3>

                            <div class="mb-4">
                                <x-input-label for="signal_word" :value="__('Palabra de Advertencia')" />
                                <select id="signal_word" name="signal_word" x-model="form.signal_word"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 rounded-md shadow-sm font-bold"
                                    required>
                                    <option value="SIN PALABRA">SIN PALABRA</option>
                                    <option value="ATENCION" class="text-yellow-600">ATENCIÓN</option>
                                    <option value="PELIGRO" class="text-red-600">PELIGRO</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <x-input-label for="hazard_statements" :value="__('Indicaciones de Peligro (H)')" />
                                    <textarea id="hazard_statements" name="hazard_statements" x-model="form.hazard_statements" rows="4"
                                        class="block mt-1 w-full bg-indigo-50 dark:bg-gray-700 border-gray-300 dark:border-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 rounded-md shadow-sm"></textarea>
                                </div>
                                <div>
                                    <x-input-label for="precautionary_statements" :value="__('Consejos de Prudencia (P)')" />
                                    <textarea id="precautionary_statements" name="precautionary_statements" x-model="form.precautionary_statements"
                                        rows="4"
                                        class="block mt-1 w-full bg-indigo-50 dark:bg-gray-700 border-gray-300 dark:border-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 rounded-md shadow-sm"></textarea>
                                </div>
                            </div>

                            <!-- PICTOGRAMAS -->
                            <div class="mb-6">
                                <span
                                    class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">Pictogramas
                                    Aplicables</span>
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
                                            'health_hazard' => 'Peligro Salud (Cancerígeno)',
                                            'environment' => 'Medio Ambiente',
                                        ];
                                    @endphp

                                    @foreach ($pictograms as $key => $label)
                                        <label
                                            class="flex flex-col items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                            :class="form.pictograms.includes('{{ $key }}') ?
                                                'border-indigo-500 bg-indigo-50 dark:bg-indigo-900' :
                                                'border-gray-200 dark:border-gray-700'">
                                            <input type="checkbox" name="pictograms[]" value="{{ $key }}"
                                                class="sr-only" x-model="form.pictograms">
                                            <span class="text-2xl mb-1">
                                                @switch($key)
                                                    @case('flame')
                                                        🔥
                                                    @break

                                                    @case('exploding_bomb')
                                                        💣
                                                    @break

                                                    @case('skull_and_crossbones')
                                                        ☠️
                                                    @break

                                                    @case('environment')
                                                        🐟
                                                    @break

                                                    @case('corrosion')
                                                        🧪
                                                    @break

                                                    @case('health_hazard')
                                                        ☣️
                                                    @break

                                                    @case('exclamation_mark')
                                                        ❗
                                                    @break

                                                    @case('gas_cylinder')
                                                        ⚗️
                                                    @break

                                                    @case('flame_over_circle')
                                                        ⭕
                                                    @break

                                                    @default
                                                        ⚠️
                                                @endswitch
                                            </span>
                                            <span class="text-xs text-center">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- ARCHIVOS -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="hds_file" :value="__('Archivo HDS (PDF) - Para guardar')" />
                                    <input id="hds_file" type="file" name="hds_file" accept=".pdf"
                                        class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                                </div>
                                <div>
                                    <x-input-label for="product_image" :value="__('Foto del Producto')" />
                                    <input id="product_image" type="file" name="product_image" accept="image/*"
                                        class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <!-- BOTÓN GUARDAR ANIMADO (Corrección aplicada: x-bind) -->
                            <x-primary-button class="ml-4"
                                x-bind:class="{ 'opacity-50 cursor-not-allowed': submitting }"
                                x-bind:disabled="submitting">
                                <span x-show="!submitting">{{ __('Guardar en Listado Maestro') }}</span>
                                <span x-show="submitting" class="flex items-center" style="display: none;">
                                    <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Guardando...
                                </span>
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Script de Alpine -->
    <script>
        function hazmatForm() {
            return {
                loading: false,
                errorMessage: '',
                showSuccess: false,
                showErrorToast: false,
                form: {
                    product_name: '',
                    chemical_name: '',
                    cas_number: '',
                    manufacturer: '',
                    emergency_phone: '',
                    address: '',
                    signal_word: 'SIN PALABRA',
                    hazard_statements: '',
                    precautionary_statements: '',
                    epp: '',
                    pictograms: []
                },

                async analyzePdf() {
                    const fileInput = this.$refs.hdsInput;
                    if (!fileInput.files.length) {
                        this.errorMessage = 'Por favor selecciona un PDF primero.';
                        this.showErrorToast = true;
                        setTimeout(() => this.showErrorToast = false, 4000);
                        return;
                    }
                    this.loading = true;
                    this.showErrorToast = false;
                    this.showSuccess = false;

                    const formData = new FormData();
                    formData.append('hds_analyze', fileInput.files[0]);

                    try {
                        const response = await axios.post('{{ route('hazmat.analyze') }}', formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        });

                        const data = response.data;
                        if (data) {
                            this.form.product_name = data.product_name || '';
                            this.form.chemical_name = data.chemical_name || '';
                            this.form.cas_number = data.cas_number || '';
                            this.form.manufacturer = data.manufacturer || '';
                            this.form.emergency_phone = data.emergency_phone || '';
                            this.form.address = data.address || '';

                            if (['PELIGRO', 'ATENCION', 'SIN PALABRA'].includes(data.signal_word)) {
                                this.form.signal_word = data.signal_word;
                            }

                            this.form.hazard_statements = data.hazard_statements || '';
                            this.form.precautionary_statements = data.precautionary_statements || '';

                            if (Array.isArray(data.pictograms)) {
                                this.form.pictograms = data.pictograms;
                            }
                            this.form.epp = data.epp || '';

                            this.showSuccess = true;
                            setTimeout(() => {
                                this.showSuccess = false;
                            }, 4000);
                        }

                    } catch (error) {
                        this.errorMessage = error.response?.data?.error || 'Error al analizar el documento.';
                        this.showErrorToast = true;
                        setTimeout(() => this.showErrorToast = false, 5000);

                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</x-app-layout>
