<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Salidas de Inventario') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- BARRA DE HERRAMIENTAS -->
            <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-t-lg border-b border-gray-200 dark:border-gray-600">
                <form method="GET" action="{{ route('inventory-exits.index') }}">
                    <div class="flex flex-col gap-4">
                        <!-- FILA 1: Búsqueda y botones -->
                        <div class="flex flex-col md:flex-row justify-between gap-4">
                            <div class="relative flex-grow md:max-w-lg">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por producto, receptor, documento..." class="block w-full p-2.5 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-white focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                            </div>

                            <div class="flex gap-2">
                                @if(in_array(Auth::user()->role?->name, ['Administrador', 'Almacenista']))
                                    <a href="{{ route('inventory-exits.create') }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                        Nueva Salida
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- FILA 2: FILTROS -->
                        <div class="flex flex-wrap items-center gap-2 bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm border border-gray-200 dark:border-gray-600">
                            <span class="text-xs font-bold text-gray-500 uppercase mr-2">Filtrar:</span>
                            
                            @if(Auth::user()->role->name === 'Administrador')
                                <select name="terminal_id" class="text-sm border-gray-300 rounded-md focus:ring-indigo-500 dark:bg-gray-700 dark:text-white h-9 py-1">
                                    <option value="">-- Terminal --</option>
                                    @foreach($terminals as $t)
                                        <option value="{{ $t->id }}" {{ $filterTerminal == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                                    @endforeach
                                </select>
                            @endif

                            <select name="department" class="text-sm border-gray-300 rounded-md focus:ring-indigo-500 dark:bg-gray-700 dark:text-white h-9 py-1">
                                <option value="">-- Departamento --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept }}" {{ $filterDepartment == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                                @endforeach
                            </select>

                            <select name="month" class="text-sm border-gray-300 rounded-md focus:ring-indigo-500 dark:bg-gray-700 dark:text-white h-9 py-1">
                                <option value="">-- Mes --</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $filterMonth == $i ? 'selected' : '' }}>{{ DateTime::createFromFormat('!m', $i)->format('F') }}</option>
                                @endfor
                            </select>

                            <select name="year" class="text-sm border-gray-300 rounded-md focus:ring-indigo-500 dark:bg-gray-700 dark:text-white h-9 py-1">
                                <option value="">-- Año --</option>
                                @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                    <option value="{{ $y }}" {{ $filterYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>

                            <button type="submit" class="ml-auto text-white bg-gray-800 hover:bg-gray-900 font-medium rounded-lg text-xs px-4 py-2 dark:bg-gray-600 dark:hover:bg-gray-500">
                                APLICAR
                            </button>
                            <a href="{{ route('inventory-exits.index') }}" class="text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 font-medium rounded-lg text-xs px-4 py-2 dark:bg-gray-800 dark:text-white dark:border-gray-600">
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
                                    <th class="px-4 py-3">Fecha</th>
                                    <th class="px-4 py-3">Producto</th>
                                    <th class="px-4 py-3 text-right">Cantidad</th>
                                    <th class="px-4 py-3">Receptor</th>
                                    <th class="px-4 py-3">Departamento</th>
                                    <th class="px-4 py-3 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($exits as $exit)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-4 py-4 font-medium">
                                        {{  $exit->exit_date->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="font-bold">{{ $exit->consumable->sku }}</div>
                                        <div class="text-xs text-gray-500">{{ $exit->consumable->name }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <span class="font-bold text-red-600">-{{ number_format($exit->quantity, 2) }}</span>
                                        <span class="text-xs text-gray-500">{{ $exit->consumable->unit_of_measure }}</span>
                                    </td>
                                    <td class="px-4 py-4">{{ $exit->recipient_name }}</td>
                                    <td class="px-4 py-4 text-sm">{{ $exit->department ?? '-' }}</td>
                                    <td class="px-4 py-4 text-right">
                                        <a href="{{ route('inventory-exits.show', $exit) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                            Ver Detalle
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td colspan="6" class="px-6 py-4 text-center">No hay salidas registradas.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $exits->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
