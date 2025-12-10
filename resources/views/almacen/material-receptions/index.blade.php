<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $viewDeleted ? __('Registro de Recepciones CANCELADAS') : __('Registro de Recepción de Materiales') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ showCancelModal: false, cancelUrl: '' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- BARRA DE HERRAMIENTAS MEJORADA -->
            <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-t-lg border-b border-gray-200 dark:border-gray-600">
                <form method="GET" action="{{ route('material-receptions.index') }}">
                    @if($viewDeleted)
                        <input type="hidden" name="view_deleted" value="1">
                    @endif

                    <div class="flex flex-col gap-4">
                        
                        <!-- FILA 1 -->
                        <div class="flex flex-col md:flex-row justify-between gap-4">
                            <!-- Buscador -->
                            <div class="relative flex-grow md:max-w-lg">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <input type="text" name="search" value="{{ $searchTerm }}" placeholder="Buscar por proveedor, OC, descripción..." class="block w-full p-2.5 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-white focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                            </div>

                            <!-- Botones -->
                            <div class="flex flex-wrap gap-2 justify-end">
                                <a href="{{ route('material-receptions.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    Excel
                                </a>

                                @if($viewDeleted)
                                    <a href="{{ route('material-receptions.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Ver Activos
                                    </a>
                                @else
                                    <a href="{{ route('material-receptions.index', ['view_deleted' => 1]) }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Ver Eliminados
                                    </a>
                                @endif

                                @if(Auth::user()->role?->name === 'Administrador' && !$viewDeleted)
                                    <a href="{{ route('material-receptions.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        Nueva Recepción
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- FILA 2 -->
                        <div class="flex flex-wrap items-center gap-2 bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm border border-gray-200 dark:border-gray-600">
                            <span class="text-xs font-bold text-gray-500 uppercase mr-2">Filtrar por:</span>
                            
                            @if(Auth::user()->role->name === 'Administrador')
                                <select name="terminal_id" class="text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white h-9 py-1">
                                    <option value="">Todas las Terminales</option>
                                    @foreach($terminals as $t)
                                        <option value="{{ $t->id }}" {{ $selectedTerminal == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                                    @endforeach
                                </select>
                            @endif

                            <select name="month" class="text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white h-9 py-1">
                                <option value="">Todos los Meses</option>
                                @foreach(['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] as $i => $m)
                                    <option value="{{ str_pad($i+1, 2, '0', STR_PAD_LEFT) }}" {{ $selectedMonth == str_pad($i+1, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>

                            <select name="year" class="text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white h-9 py-1">
                                <option value="">Todos los Años</option>
                                @for($y = date('Y'); $y >= date('Y')-5; $y--)
                                    <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>

                            <button type="submit" class="ml-auto text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-xs px-4 py-2 dark:bg-gray-600 dark:hover:bg-gray-500 dark:focus:ring-gray-700 dark:border-gray-700">
                                APLICAR
                            </button>
                            <a href="{{ route('material-receptions.index') }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-xs px-4 py-2 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">
                                Limpiar
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- TABLA -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-b-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-200 rounded-lg">{{ session('success') }}</div>
                    @endif

                    <div class="relative overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-6 py-3">Fecha</th>
                                    <th class="px-6 py-3">Terminal</th>
                                    <th class="px-6 py-3">Descripción</th>
                                    <th class="px-6 py-3">Proveedor</th>
                                    <th class="px-6 py-3">OC</th>
                                    <th class="px-6 py-3">Cant.</th>
                                    
                                    <!-- COLUMNA ARCHIVOS (NUEVA) -->
                                    <th class="px-6 py-3">Archivos</th>

                                    @if($viewDeleted)
                                        <th class="px-6 py-3 text-red-600">Motivo Cancelación</th>
                                        <th class="px-6 py-3">Cancelado el</th>
                                    @else
                                        <th class="px-6 py-3">Status</th>
                                        <th class="px-6 py-3 text-right">Acciones</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($receptions as $reception)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $reception->reception_date->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 font-bold">{{ $reception->terminal->name }}</td>
                                    <td class="px-6 py-4 max-w-xs break-words">{{ $reception->description }}</td>
                                    <td class="px-6 py-4 max-w-[150px] break-words">{{ $reception->provider }}</td>
                                    <td class="px-6 py-4">{{ $reception->purchase_order }}</td>
                                    <td class="px-6 py-4 font-bold">{{ $reception->quantity }}</td>
                                    
                                    <!-- CELDAS DE ARCHIVOS -->
                                    <td class="px-6 py-4">
                                        <div class="flex gap-2 items-center">
                                            @if($reception->invoice_path)
                                                <a href="{{ route('material-receptions.file', [$reception, 'invoice']) }}" target="_blank" class="text-blue-600 hover:scale-110 transition-transform" title="Ver Factura">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0112.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2h8.586a1 1 0 00.707-.293l.001-.001M9 12h6m-6 4h6M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                                                </a>
                                            @endif
                                            @if($reception->remission_path)
                                                <a href="{{ route('material-receptions.file', [$reception, 'remission']) }}" target="_blank" class="text-purple-600 hover:scale-110 transition-transform" title="Ver Remisión">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                                                </a>
                                            @endif
                                            @if($reception->certificate_path)
                                                <a href="{{ route('material-receptions.file', [$reception, 'certificate']) }}" target="_blank" class="text-green-600 hover:scale-110 transition-transform" title="Ver Certificado de Calidad">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                </a>
                                            @endif
                                            @if(!$reception->invoice_path && !$reception->remission_path && !$reception->certificate_path)
                                                <span class="text-gray-300 text-xs">Sin archivos</span>
                                            @endif
                                        </div>
                                    </td>

                                    @if($viewDeleted)
                                        <td class="px-6 py-4 text-red-600 font-bold max-w-xs break-words">{{ $reception->cancellation_reason }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $reception->deleted_at->format('d/m/Y H:i') }}</td>
                                    @else
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($reception->status == 'COMPLETO')
                                                <span class="bg-green-100 text-green-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">COMPLETO</span>
                                            @elseif($reception->status == 'PENDIENTE_OT')
                                                <span class="bg-orange-100 text-orange-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-orange-900 dark:text-orange-300">PENDIENTE OT</span>
                                            @else
                                                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">PENDIENTE UBICACIÓN</span>
                                            @endif
                                        </td>
                                        
                                        <td class="px-6 py-4 text-right whitespace-nowrap">
                                            <div class="flex justify-end items-center gap-3">
                                                <a href="{{ route('material-receptions.pdf', $reception) }}" target="_blank" class="text-green-600 hover:text-green-900 text-sm font-medium flex items-center" title="PDF">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                    PDF
                                                </a>
                                                
                                                @if(Auth::user()->role?->name === 'Administrador')
                                                    <a href="{{ route('material-receptions.edit', $reception) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium flex items-center" title="Editar">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                        Editar
                                                    </a>
                                                    
                                                    <!-- BOTÓN CANCELAR -->
                                                    <button 
                                                        @click="showCancelModal = true; cancelUrl = '{{ route('material-receptions.destroy', $reception) }}'"
                                                        class="text-red-600 hover:text-red-900 text-sm font-medium flex items-center"
                                                        title="Cancelar"
                                                    >
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                        Cancelar
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                                @empty
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"><td colspan="9" class="px-6 py-4 text-center">Sin registros.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $receptions->links() }}</div>
                </div>
            </div>
        </div>

        <!-- MODAL DE CANCELACIÓN -->
        <div x-show="showCancelModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" @click="showCancelModal = false"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form :action="cancelUrl" method="POST" class="p-6">
                        @csrf @method('DELETE')
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Cancelar Registro') }}</h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Ingresa el motivo de la cancelación.') }}</p>
                        <div class="mt-6">
                            <x-input-label for="cancellation_reason" value="{{ __('Motivo') }}" class="sr-only" />
                            <x-text-input id="cancellation_reason" name="cancellation_reason" type="text" class="mt-1 block w-full" placeholder="Motivo..." required />
                        </div>
                        <div class="mt-6 flex justify-end">
                            <x-secondary-button type="button" @click="showCancelModal = false">{{ __('Cerrar') }}</x-secondary-button>
                            <x-danger-button class="ms-3">{{ __('Confirmar') }}</x-danger-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>