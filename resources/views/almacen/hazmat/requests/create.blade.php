@extends('layouts.app')

@section('title', 'Nueva Solicitud de Material Peligroso')

@section('content')
<div class="max-w-4xl mx-auto py-6">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                TRP-PO-SS-132-F01 Solicitud de Autorización de Ingreso
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Complete todos los campos para solicitar el ingreso de un nuevo material peligroso.
            </p>
        </div>
        
        <form action="{{ route('hazmat-requests.store') }}" method="POST" enctype="multipart/form-data" class="px-4 py-5 sm:p-6">
            @csrf
            
            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                
                <!-- Sección: Datos del Material -->
                <div class="sm:col-span-6 bg-gray-50 p-4 rounded-md mb-4">
                    <h4 class="text-base font-bold text-gray-800 mb-4">Datos del Material Peligroso</h4>

                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <!-- Fecha Ingreso (Estimada) -->
                        <div class="sm:col-span-3">
                            <label class="block text-sm font-medium text-gray-700">Fecha de Ingreso Estimada</label>
                            <input type="date" name="entry_date" value="{{ date('Y-m-d') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <!-- Nombre Comercial -->
                        <div class="sm:col-span-3">
                            <label class="block text-sm font-medium text-gray-700">Nombre Comercial / Fabricante *</label>
                            <input type="text" name="trade_name" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <!-- Nombre Químico -->
                        <div class="sm:col-span-3">
                            <label class="block text-sm font-medium text-gray-700">Nombre Químico *</label>
                            <input type="text" name="chemical_name" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <!-- Área de Uso -->
                        <div class="sm:col-span-3">
                            <label class="block text-sm font-medium text-gray-700">Área en la que se utiliza *</label>
                            <input type="text" name="usage_area" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <!-- Uso Previsto -->
                        <div class="sm:col-span-6">
                            <label class="block text-sm font-medium text-gray-700">Descripción del uso previsto *</label>
                            <textarea name="intended_use" rows="3" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                        </div>

                        <!-- Lugar Almacenamiento -->
                        <div class="sm:col-span-6">
                            <label class="block text-sm font-medium text-gray-700">Lugar(es) de almacenamiento propuesto *</label>
                            <input type="text" name="storage_location" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <!-- Cantidades -->
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Cant. Máxima *</label>
                            <input type="text" name="max_storage_quantity" required placeholder="Ej: 200 Litros" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Cant. Mínima</label>
                            <input type="text" name="min_storage_quantity" placeholder="Ej: 20 Litros" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Consumo Mensual</label>
                            <input type="text" name="monthly_consumption" placeholder="Ej: 50 Litros" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <!-- Checkboxes -->
                        <div class="sm:col-span-6 grid grid-cols-2 gap-4 mt-2">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input name="is_sample" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label class="font-medium text-gray-700">¿Es muestra?</label>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input name="is_import" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label class="font-medium text-gray-700">¿Es material de importación?</label>
                                </div>
                            </div>
                        </div>

                        <!-- MOC -->
                        <div class="sm:col-span-6 border-t pt-4">
                            <label class="block text-sm font-medium text-gray-700">MOC (Si aplica)</label>
                            <input type="text" name="moc_id" placeholder="ID del Control de Cambios" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <!-- Archivo HDS -->
                        <div class="sm:col-span-6 border-t pt-4">
                            <label class="block text-sm font-medium text-gray-700 text-red-600">Adjuntar HDS (Hoja de Datos de Seguridad) *</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <label for="hds_file" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                            <span>Subir PDF</span>
                                            <input id="hds_file" name="hds_file" type="file" class="sr-only" required accept="application/pdf">
                                        </label>
                                    </div>
                                    <p class="text-xs text-gray-500">PDF hasta 10MB</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="pt-5 flex justify-end">
                <a href="{{ route('hazmat-requests.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancelar
                </a>
                <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Enviar Solicitud
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
