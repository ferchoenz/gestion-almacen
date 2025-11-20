<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $viewDeleted ? __('Materiales Peligrosos ELIMINADOS') : __('Listado Maestro de Materiales Peligrosos') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ showCancelModal: false, cancelUrl: '' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- ============================================================== -->
            <!-- BARRA DE HERRAMIENTAS MEJORADA -->
            <!-- ============================================================== -->
            <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-t-lg border-b border-gray-200 dark:border-gray-600">
                <form method="GET" action="{{ route('hazmat.index') }}">
                    
                    <!-- Mantenemos el estado de la papelera al filtrar -->
                    @if($viewDeleted)
                        <input type="hidden" name="view_deleted" value="1">
                    @endif

                    <div class="flex flex-col gap-4">
                        
                        <!-- FILA 1: BUSCADOR Y ACCIONES PRINCIPALES -->
                        <div class="flex flex-col md:flex-row justify-between gap-4">
                            
                            <!-- Buscador Grande (Izquierda) -->
                            <div class="relative flex-grow md:max-w-lg">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por nombre, químico o CAS..." class="block w-full p-2.5 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-white focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                            </div>

                            <!-- Botones de Acción (Derecha) -->
                            <div class="flex flex-wrap gap-2 justify-end">
                                
                                <!-- Botón Excel -->
                                <a href="{{ route('hazmat.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    Excel
                                </a>

                                <!-- Toggle Papelera / Activos -->
                                @if($viewDeleted)
                                    <a href="{{ route('hazmat.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Ver Activos
                                    </a>
                                @else
                                    <a href="{{ route('hazmat.index', ['view_deleted' => 1]) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Papelera
                                    </a>
                                @endif

                                <!-- Botón Nuevo Material (Solo si NO estamos en papelera) -->
                                @if(in_array(Auth::user()->role?->name, ['Administrador', 'Seguridad y Salud']) && !$viewDeleted)
                                    <a href="{{ route('hazmat.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        Nuevo Material
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- FILA 2: FILTROS SECUNDARIOS -->
                        <div class="flex flex-wrap items-center gap-2 bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm border border-gray-200 dark:border-gray-600">
                            <span class="text-xs font-bold text-gray-500 uppercase mr-2 hidden md:inline">Filtrar por:</span>
                            
                            <!-- Filtro Terminal -->
                            @if(Auth::user()->role->name === 'Administrador')
                                <select name="terminal_id" class="text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white h-9 py-1">
                                    <option value="">-- Terminal --</option>
                                    @foreach($terminals as $t)
                                        <option value="{{ $t->id }}" {{ $filterTerminal == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                                    @endforeach
                                </select>
                            @endif

                            <!-- Filtro Estado Físico -->
                            <select name="physical_state" class="text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white h-9 py-1">
                                <option value="">-- Estado Físico --</option>
                                <option value="Líquido" {{ $filterState == 'Líquido' ? 'selected' : '' }}>Líquido</option>
                                <option value="Sólido" {{ $filterState == 'Sólido' ? 'selected' : '' }}>Sólido</option>
                                <option value="Gas" {{ $filterState == 'Gas' ? 'selected' : '' }}>Gas</option>
                                <option value="Gel" {{ $filterState == 'Gel' ? 'selected' : '' }}>Gel</option>
                            </select>

                            <!-- Filtro Palabra Advertencia -->
                            <select name="signal_word" class="text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white h-9 py-1">
                                <option value="">-- Palabra Adv. --</option>
                                <option value="PELIGRO" {{ $filterSignal == 'PELIGRO' ? 'selected' : '' }}>PELIGRO</option>
                                <option value="ATENCION" {{ $filterSignal == 'ATENCION' ? 'selected' : '' }}>ATENCIÓN</option>
                                <option value="SIN PALABRA" {{ $filterSignal == 'SIN PALABRA' ? 'selected' : '' }}>SIN PALABRA</option>
                            </select>

                            <!-- Botón Aplicar -->
                            <button type="submit" class="ml-auto text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-xs px-4 py-2 dark:bg-gray-600 dark:hover:bg-gray-500 dark:focus:ring-gray-700">
                                APLICAR
                            </button>
                            
                            <!-- Botón Limpiar -->
                            <a href="{{ route('hazmat.index') }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-xs px-4 py-2 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">
                                Limpiar
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- TARJETA DE LA TABLA -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-b-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-200 rounded-lg">{{ session('success') }}</div>
                    @endif

                    <div class="relative overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-6 py-3">Producto</th>
                                    <th class="px-6 py-3">Terminal</th>
                                    <th class="px-6 py-3">Ubicación</th>
                                    
                                    @if($viewDeleted)
                                        <th class="px-6 py-3 text-red-600">Eliminado el</th>
                                        <!-- Si tienes columna motivo, descomenta: -->
                                        <!-- <th class="px-6 py-3">Motivo</th> -->
                                    @else
                                        <th class="px-6 py-3">Status</th>
                                        <th class="px-6 py-3">Palabra Adv.</th>
                                        <th class="px-6 py-3">Pictogramas</th>
                                        <th class="px-6 py-3">HDS</th>
                                        <th class="px-6 py-3 text-right">Acciones</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($products as $product)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900 dark:text-white">{{ $product->product_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $product->chemical_name }}</div>
                                        <div class="text-xs text-gray-400">CAS: {{ $product->cas_number }}</div>
                                    </td>
                                    
                                    <td class="px-6 py-4 font-bold">{{ $product->terminal->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">{{ $product->location }}</td>
                                    
                                    @if($viewDeleted)
                                        <td class="px-6 py-4">{{ $product->deleted_at->format('d/m/Y H:i') }}</td>
                                        <!-- <td class="px-6 py-4">{{ $product->cancellation_reason }}</td> -->
                                    @else
                                        <!-- Status -->
                                        <td class="px-6 py-4">
                                            @if($product->is_active)
                                                <span class="flex items-center text-green-600 text-xs font-bold"><span class="w-2 h-2 bg-green-600 rounded-full mr-2"></span> Activo</span>
                                            @else
                                                <span class="flex items-center text-red-600 text-xs font-bold"><span class="w-2 h-2 bg-red-600 rounded-full mr-2"></span> Inactivo</span>
                                            @endif
                                        </td>

                                        <!-- Palabra Advertencia -->
                                        <td class="px-6 py-4">
                                            @if($product->signal_word == 'PELIGRO')
                                                <span class="bg-red-100 text-red-800 text-xs font-bold px-2 py-1 rounded">PELIGRO</span>
                                            @elseif($product->signal_word == 'ATENCION')
                                                <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-1 rounded">ATENCIÓN</span>
                                            @else
                                                <span class="text-gray-400 text-xs">-</span>
                                            @endif
                                        </td>

                                        <!-- Pictogramas -->
                                        <td class="px-6 py-4 text-xl">
                                            @if($product->pictograms)
                                                @foreach($product->pictograms as $pic)
                                                    @switch($pic)
                                                        @case('flame') <span title="Inflamable">🔥</span> @break
                                                        @case('exploding_bomb') <span title="Explosivo">💣</span> @break
                                                        @case('skull_and_crossbones') <span title="Tóxico">☠️</span> @break
                                                        @case('environment') <span title="Medio Ambiente">🐟</span> @break
                                                        @case('corrosion') <span title="Corrosivo">🧪</span> @break
                                                        @case('health_hazard') <span title="Peligro Salud">☣️</span> @break
                                                        @case('exclamation_mark') <span title="Irritante">❗</span> @break
                                                        @case('gas_cylinder') <span title="Gas a Presión">⚗️</span> @break
                                                        @case('flame_over_circle') <span title="Comburente">⭕</span> @break
                                                    @endswitch
                                                @endforeach
                                            @endif
                                        </td>

                                        <!-- HDS Link -->
                                        <td class="px-6 py-4">
                                            @if($product->hds_path)
                                                <a href="{{ route('hazmat.view-hds', $product) }}" target="_blank" class="text-blue-600 hover:underline flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                    Ver
                                                </a>
                                            @else
                                                <span class="text-gray-400">N/A</span>
                                            @endif
                                        </td>
                                        
                                        <!-- Acciones -->
                                        <td class="px-6 py-4 text-right whitespace-nowrap">
                                            
                                            <!-- Etiqueta -->
                                            <a href="{{ route('hazmat.label', $product) }}" target="_blank" class="inline-flex items-center text-purple-600 hover:text-purple-900 text-sm font-medium mr-3" title="Imprimir Etiqueta">
                                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                                Etiqueta
                                            </a>
                                            
                                            <!-- Detalle -->
                                            <a href="{{ route('hazmat.edit', $product) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-900 text-sm font-medium mr-3" title="Ver Detalle">
                                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                Detalle
                                            </a>

                                            <!-- Eliminar -->
                                            @if(in_array(Auth::user()->role?->name, ['Administrador', 'Seguridad y Salud']))
                                                <button 
                                                    @click="showCancelModal = true; cancelUrl = '{{ route('hazmat.destroy', $product) }}'"
                                                    class="inline-flex items-center text-red-600 hover:text-red-900 text-sm font-medium"
                                                    title="Eliminar"
                                                >
                                                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    Eliminar
                                                </button>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                                @empty
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"><td colspan="9" class="px-6 py-4 text-center">No hay materiales registrados.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginación -->
                    <div class="mt-4">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL DE ELIMINACIÓN -->
        <div x-show="showCancelModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" @click="showCancelModal = false"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form :action="cancelUrl" method="POST" class="p-6">
                        @csrf @method('DELETE')
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Eliminar Material') }}</h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('¿Estás seguro de que deseas eliminar este material del listado maestro?') }}</p>
                        
                        <div class="mt-6">
                            <x-input-label for="cancellation_reason" value="{{ __('Motivo de Eliminación') }}" class="sr-only" />
                            <x-text-input id="cancellation_reason" name="cancellation_reason" type="text" class="mt-1 block w-full" placeholder="Motivo (Opcional)..." />
                        </div>

                        <div class="mt-6 flex justify-end">
                            <x-secondary-button type="button" @click="showCancelModal = false">{{ __('Cancelar') }}</x-secondary-button>
                            <x-danger-button class="ms-3">{{ __('Eliminar') }}</x-danger-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>