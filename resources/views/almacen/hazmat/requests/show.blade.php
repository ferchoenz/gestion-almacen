<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detalle de Solicitud #') }}{{ $hazmatRequest->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Solicitud #{{ $hazmatRequest->id }}: {{ $hazmatRequest->trade_name }}
            </h2>
            <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                <div class="mt-2 flex items-center text-sm text-gray-500">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $hazmatRequest->status_color }}-100 text-{{ $hazmatRequest->status_color }}-800">
                        {{ $hazmatRequest->status }}
                    </span>
                </div>
                <div class="mt-2 flex items-center text-sm text-gray-500">
                    Solicitado por: {{ $hazmatRequest->user->name }}
                </div>
            </div>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-2">
            @if($hazmatRequest->status === 'APPROVED')
                <a href="{{ route('hazmat-requests.pdf', $hazmatRequest) }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Descargar Solicitud (PDF)
                </a>
            @endif
            @if($hazmatRequest->hds_path)
                <a href="{{ route('hazmat-requests.hds', $hazmatRequest) }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    Ver HDS
                </a>
            @endif
        </div>
    </div>

    <!-- Info Grid -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Datos del Material</h3>
        </div>
        <div class="border-t border-gray-200">
            <dl>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Terminal</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $hazmatRequest->terminal->name ?? 'N/A' }}
                        </span>
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Nombre Químico</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $hazmatRequest->chemical_name }}</dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Área de uso</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $hazmatRequest->usage_area }}</dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Uso Previsto</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $hazmatRequest->intended_use }}</dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Almacenamiento (Propuesto)</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $hazmatRequest->storage_location }}</dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Cantidades</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        Máx: {{ $hazmatRequest->max_storage_quantity }} | Mín: {{ $hazmatRequest->min_storage_quantity }} | Mensual: {{ $hazmatRequest->monthly_consumption }}
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Otros</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        Muestra: {{ $hazmatRequest->is_sample ? 'Sí' : 'No' }} <br>
                        Importación: {{ $hazmatRequest->is_import ? 'Sí' : 'No' }} <br>
                        MOC: {{ $hazmatRequest->moc_id ?? 'N/A' }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Safety Validation (Solo visible si es Safety/Admin y está PENDING o ya fue PROCESADO) -->
    @if($isSafety && $hazmatRequest->status === 'PENDING')
        <div class="bg-yellow-50 shadow sm:rounded-lg overflow-hidden border border-yellow-200">
            <div class="px-4 py-5 sm:px-6 bg-yellow-100">
                <h3 class="text-lg leading-6 font-bold text-yellow-800">Evaluación de Seguridad</h3>
            </div>
            <div class="p-6">
                <form action="{{ route('hazmat-requests.update', $hazmatRequest) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4 mb-6">
                        <h4 class="font-medium text-gray-900">Checklist de Validación</h4>
                        
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input name="can_be_substituted" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label class="font-medium text-gray-700">¿Se puede sustituir por otra sustancia de menor riesgo?</label>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input name="hds_compliant" type="checkbox" checked class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label class="font-medium text-gray-700">¿La HDS es vigente y cumple con la NOM-018-STPS?</label>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input name="has_training" type="checkbox" checked class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label class="font-medium text-gray-700">¿Se tiene capacitación para el manejo del material?</label>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input name="has_ppe" type="checkbox" checked class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label class="font-medium text-gray-700">¿Se tiene el EPP adecuado?</label>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input name="has_containment" type="checkbox" checked class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label class="font-medium text-gray-700">¿Se tiene material de contención de derrames?</label>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input name="moc_managed" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label class="font-medium text-gray-700">¿Gestionado mediante MOC?</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700">¿En dónde se debe almacenar la sustancia química (Final)? *</label>
                        <input type="text" name="final_storage_location" value="{{ $hazmatRequest->storage_location }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        <p class="text-xs text-gray-500">Confirmar o ajustar la ubicación final autorizada.</p>
                    </div>

                    <div class="mb-6 border-t pt-4">
                        <label class="block text-sm font-medium text-red-700">Motivo de Rechazo (Solo si se rechaza)</label>
                        <textarea name="rejection_reason" rows="2" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                    </div>

                    <div class="flex space-x-3">
                        <button type="submit" name="action" value="approve" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            ✓ APROBAR SOLICITUD
                        </button>
                        <button type="submit" name="action" value="reject" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            ✕ RECHAZAR SOLICITUD
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @elseif($hazmatRequest->status !== 'PENDING')
        <!-- Mostrar resultado de evaluación -->
        <div class="bg-gray-50 shadow sm:rounded-lg overflow-hidden border border-gray-200">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Resultado de Evaluación</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <p><strong>Estado:</strong> {{ $hazmatRequest->status }}</p>
                <p><strong>Evaluado por:</strong> {{ $hazmatRequest->approver ? $hazmatRequest->approver->name : 'N/A' }}</p>
                @if($hazmatRequest->status === 'APPROVED')
                    <p><strong>Ubicación Final Autorizada:</strong> {{ $hazmatRequest->final_storage_location }}</p>
                @endif
                @if($hazmatRequest->status === 'REJECTED')
                    <p class="text-red-600"><strong>Motivo:</strong> {{ $hazmatRequest->rejection_reason }}</p>
                @endif
            </div>
        </div>
    @endif

        </div>
    </div>
</x-app-layout>
