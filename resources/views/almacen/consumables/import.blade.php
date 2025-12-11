<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Importación Masiva de Consumibles') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
<div class="sm:flex sm:items-center sm:justify-between mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Importación Masiva</h1>
        <p class="mt-2 text-sm text-gray-700 dark:text-gray-400">Carga inventario desde un archivo Excel o CSV.</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <a href="{{ route('consumables.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            Volver al listado
        </a>
    </div>
</div>

<div class="max-w-3xl mx-auto">
    <!-- Instrucciones -->
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Estructura del Archivo</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p class="mb-2">El archivo debe contener las siguientes columnas (la primera fila debe ser encabezado):</p>
                    <ul class="list-disc list-inside space-y-1">
                        <li><strong>sku</strong> (Requerido): Código único del producto.</li>
                        <li><strong>name</strong> (Requerido): Nombre del producto.</li>
                        <li><strong>description</strong> (Opcional): Descripción detallada.</li>
                        <li><strong>category</strong> (Opcional): Categoría (ej: Papelería, Limpieza).</li>
                        <li><strong>unit_of_measure</strong> (Opcional): Unidad (ej: Pieza, Caja).</li>
                        <li><strong>current_stock</strong> (Opcional): Stock actual (solo para nuevos productos).</li>
                        <li><strong>min_stock</strong> (Opcional): Stock mínimo para alertas.</li>
                        <li><strong>unit_cost</strong> (Opcional): Costo unitario.</li>
                        <li><strong>location_code</strong> (Opcional): Código de la ubicación existente (ej: A-01-1).</li>
                    </ul>
                    <p class="mt-2 font-bold">Nota: Si el SKU ya existe, se actualizarán sus datos (excepto el stock).</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario -->
    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ route('consumables.import.process') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Seleccionar archivo (.xlsx, .xls, .csv)
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors cursor-pointer" id="drop-zone">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 dark:text-gray-400 justify-center">
                                <label for="file-upload" class="relative cursor-pointer bg-white dark:bg-gray-700 rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                    <span>Subir un archivo</span>
                                    <input id="file-upload" name="file" type="file" class="sr-only" accept=".xlsx, .xls, .csv">
                                </label>
                                <p class="pl-1">o arrastrar y soltar</p>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400" id="file-name">
                                XLSX, XLS o CSV hasta 10MB
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Importar Archivo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Script simple para mostrar el nombre del archivo seleccionado y Drag & Drop visual
    const fileInput = document.getElementById('file-upload');
    const fileNameDisplay = document.getElementById('file-name');
    const dropZone = document.getElementById('drop-zone');

    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            fileNameDisplay.textContent = 'Archivo seleccionado: ' + this.files[0].name;
            dropZone.classList.add('border-indigo-500', 'bg-indigo-50');
        } else {
            fileNameDisplay.textContent = 'XLSX, XLS o CSV hasta 10MB';
            dropZone.classList.remove('border-indigo-500', 'bg-indigo-50');
        }
    });

    // Drag and drop events
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        dropZone.classList.add('border-indigo-500', 'bg-indigo-50');
    }

    function unhighlight(e) {
        dropZone.classList.remove('border-indigo-500', 'bg-indigo-50');
    }

    dropZone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        fileInput.files = files;
        
        // Trigger change event manually
        const event = new Event('change');
        fileInput.dispatchEvent(event);
    }
</script>
        </div>
    </div>
</x-app-layout>
