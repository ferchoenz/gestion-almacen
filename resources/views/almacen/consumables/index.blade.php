<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Catálogo de Consumibles') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- BARRA DE HERRAMIENTAS -->
            <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-t-lg border-b border-gray-200 dark:border-gray-600">
                <form method="GET" action="{{ route('consumables.index') }}">
                    <div class="flex flex-col gap-4">
                        
                        <!-- FILA 1 -->
                        <div class="flex flex-col md:flex-row justify-between gap-4">
                            <div class="relative flex-grow md:max-w-lg">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <input type="text" name="search" value="{{ $search }}" placeholder="Buscar SKU, nombre, descripción..." class="block w-full p-2.5 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-white focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                            </div>

                            <div class="flex flex-wrap gap-2 justify-end">
                                <a href="{{ route('consumables.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    Excel
                                </a>

                                @if(in_array(Auth::user()->role?->name, ['Administrador', 'Almacenista']))
                                    <a href="{{ route('consumables.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        Nuevo Consumible
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- FILA 2: FILTROS -->
                        <div class="flex flex-wrap items-center gap-2 bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm border border-gray-200 dark:border-gray-600">
                            <span class="text-xs font-bold text-gray-500 uppercase mr-2 hidden md:inline">Filtrar por:</span>
                            
                            @if(Auth::user()->role->name === 'Administrador')
                                <select name="terminal_id" class="text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white h-9 py-1">
                                    <option value="">-- Terminal --</option>
                                    @foreach($terminals as $t)
                                        <option value="{{ $t->id }}" {{ $filterTerminal == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                                    @endforeach
                                </select>
                            @endif

                            <select name="category" class="text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white h-9 py-1">
                                <option value="">-- Categoría --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ $filterCategory == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>

                            <select name="status" class="text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white h-9 py-1">
                                <option value="">-- Estado --</option>
                                <option value="active" {{ $filterStatus == 'active' ? 'selected' : '' }}>Activos</option>
                                <option value="low_stock" {{ $filterStatus == 'low_stock' ? 'selected' : '' }}>Stock Bajo</option>
                            </select>

                            <button type="submit" class="ml-auto text-white bg-gray-800 hover:bg-gray-900 font-medium rounded-lg text-xs px-4 py-2 dark:bg-gray-600 dark:hover:bg-gray-500">
                                APLICAR
                            </button>
                            <a href="{{ route('consumables.index') }}" class="text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 font-medium rounded-lg text-xs px-4 py-2 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700">
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
                                    <th class="px-3 py-3 w-16">Imagen</th>
                                    <th class="px-4 py-3">SKU / Producto</th>
                                    <th class="px-4 py-3">Categoría</th>
                                    <th class="px-4 py-3">Ubicación</th>
                                    <th class="px-4 py-3 text-center">Stock</th>
                                    <th class="px-4 py-3">Estado</th>
                                    <th class="px-4 py-3 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($consumables as $item)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <!-- Imagen -->
                                    <td class="px-3 py-4">
                                        @if($item->image_path && Storage::disk('public')->exists($item->image_path))
                                            <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}" class="w-12 h-12 object-cover rounded border border-gray-300">
                                        @else
                                            <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                            </div>
                                        @endif
                                    </td>
                                    
                                    <!-- SKU / Producto -->
                                    <td class="px-4 py-4">
                                        <div class="font-bold text-gray-900 dark:text-white">{{ $item->sku }}</div>
                                        <div class="text-xs text-gray-500 mt-1">{{ $item->name }}</div>
                                    </td>

                                    <!-- Categoría -->
                                    <td class="px-4 py-4">
                                        @if($item->category)
                                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded dark:bg-blue-900 dark:text-blue-300">{{ $item->category }}</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>

                                    <!-- Ubicación -->
                                    <td class="px-4 py-4 text-sm">
                                        <div class="font-medium">{{ $item->location?->name ?? '-' }}</div>
                                        @if($item->specific_location)
                                            <div class="text-xs text-gray-500 mt-1">📍 {{ $item->specific_location }}</div>
                                        @endif
                                    </td>

                                    <!-- Stock -->
                                    <td class="px-4 py-4">
                                        <div class="text-center">
                                            <div class="font-bold {{ $item->isLowStock() ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">
                                                {{ number_format($item->current_stock, 2) }}
                                            </div>
                                            <div class="text-xs text-gray-500">{{ $item->unit_of_measure }}</div>
                                            @if($item->isLowStock())
                                                <span class="text-xs text-red-600 font-bold">⚠️ Bajo</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Estado -->
                                    <td class="px-4 py-4">
                                        @if($item->is_active)
                                            <span class="inline-flex items-center bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-green-900 dark:text-green-300">
                                                <span class="w-2 h-2 me-1 bg-green-500 rounded-full"></span>
                                                Activo
                                            </span>
                                        @else
                                            <span class="inline-flex items-center bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-red-900 dark:text-red-300">
                                                <span class="w-2 h-2 me-1 bg-red-500 rounded-full"></span>
                                                Inactivo
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Acciones -->
                                    <td class="px-4 py-4 text-right whitespace-nowrap">
                                        <div class="flex justify-end items-center gap-3">
                                            <a href="{{ route('consumables.show', $item) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium flex items-center" title="Ver Detalle">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                Detalle
                                            </a>
                                            
                                            @if(in_array(Auth::user()->role?->name, ['Administrador', 'Almacenista']))
                                                <a href="{{ route('consumables.edit', $item) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium flex items-center" title="Editar">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                    Editar
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"><td colspan="7" class="px-6 py-4 text-center">No hay consumibles registrados.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $consumables->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
