<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex" x-data="{ almacenOpen: false, hazmatOpen: false }">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <!-- USUARIOS (Solo Admin) -->
                    @if(Auth::user()->role?->name === 'Administrador')
                        <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                            {{ __('Usuarios') }}
                        </x-nav-link>
                    @endif

                    <!-- DROPDOWN ALMACÉN -->
                    @if(in_array(Auth::user()->role?->name, ['Administrador', 'Gerencia', 'Mantenimiento', 'Almacenista']))
                        <div class="relative" @mouseenter="almacenOpen = true" @mouseleave="almacenOpen = false">
                            <button class="inline-flex items-center px-1 pt-1 pb-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700 h-16 {{ request()->is('material-*') || request()->is('consumables*') ? 'border-indigo-400 dark:border-indigo-600 text-gray-900 dark:text-gray-100' : '' }}">
                                📦 Almacén
                                <svg class="ml-2 -mr-0.5 h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': almacenOpen }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <!-- Dropdown Content -->
                            <div x-show="almacenOpen"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute left-0 mt-2 w-56 origin-top-left rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 dark:divide-gray-700 z-50"
                                 style="display: none;">
                                
                                <!-- INVENTARIO SECTION -->
                                @if(in_array(Auth::user()->role?->name, ['Administrador', 'Almacenista']))
                                    <div class="py-1">
                                        <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase">Inventario</div>
                                        <a href="{{ route('consumables.index') }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-indigo-50 dark:hover:bg-gray-700 {{ request()->routeIs('consumables.*') ? 'bg-indigo-100 dark:bg-gray-700 font-semibold' : '' }}">
                                            <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                            Catálogo de Productos
                                        </a>
                                        <a href="{{ route('inventory-locations.index') }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-indigo-50 dark:hover:bg-gray-700 {{ request()->routeIs('inventory-locations.*') ? 'bg-indigo-100 dark:bg-gray-700 font-semibold' : '' }}">
                                            <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            Ubicaciones
                                        </a>
                                    </div>
                                @endif

                                <!-- MOVIMIENTOS SECTION -->
                                @if(in_array(Auth::user()->role?->name, ['Administrador', 'Gerencia', 'Mantenimiento']))
                                    <div class="py-1">
                                        <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase">Movimientos</div>
                                        
                                        @if(in_array(Auth::user()->role?->name, ['Administrador', 'Gerencia']))
                                            <a href="{{ route('material-receptions.index') }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-green-50 dark:hover:bg-gray-700 {{ request()->routeIs('material-receptions.*') ? 'bg-green-100 dark:bg-gray-700 font-semibold' : '' }}">
                                                <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                                                Entradas de Material
                                            </a>
                                        @endif

                                        <a href="{{ route('material-outputs.index') }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-red-50 dark:hover:bg-gray-700 {{ request()->routeIs('material-outputs.*') ? 'bg-red-100 dark:bg-gray-700 font-semibold' : '' }}">
                                            <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16V4m0 0l4 4m-4-4l-4 4M7 20v-12m0 0L3 12m4-4l4 4"></path></svg>
                                            Salidas de Material
                                        </a>
                                    </div>
                                @endif

                            </div>
                        </div>
                    @endif

                    <!-- DROPDOWN HAZMAT -->
                    @if(in_array(Auth::user()->role?->name, ['Administrador', 'Seguridad y Salud']))
                        <div class="relative" @mouseenter="hazmatOpen = true" @mouseleave="hazmatOpen = false">
                            <button class="inline-flex items-center px-1 pt-1 pb-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700 h-16 {{ request()->is('hazmat*') ? 'border-indigo-400 dark:border-indigo-600 text-gray-900 dark:text-gray-100' : '' }}">
                                ⚠️ Hazmat
                                <svg class="ml-2 -mr-0.5 h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': hazmatOpen }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="hazmatOpen"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute left-0 mt-2 w-56 origin-top-left rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-50"
                                 style="display: none;">
                                
                                <a href="{{ route('hazmat.index') }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-yellow-50 dark:hover:bg-gray-700 {{ request()->routeIs('hazmat.*') ? 'bg-yellow-100 dark:bg-gray-700 font-semibold' : '' }}">
                                    <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    Listado Maestro
                                </a>
                                <a href="{{ route('hazmat-requests.index') }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-yellow-50 dark:hover:bg-gray-700 {{ request()->routeIs('hazmat-requests.*') ? 'bg-yellow-100 dark:bg-gray-700 font-semibold' : '' }}">
                                    <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    Solicitudes de Autorización
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-300 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-400 hover:text-gray-500 dark:hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden" x-data="{ almacenMobileOpen: false, hazmatMobileOpen: false }">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @if(Auth::user()->role?->name === 'Administrador')
                <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                    {{ __('Usuarios') }}
                </x-responsive-nav-link>
            @endif

            <!-- ALMACÉN COLLAPSIBLE MOBILE -->
            @if(in_array(Auth::user()->role?->name, ['Administrador', 'Gerencia', 'Mantenimiento', 'Almacenista']))
                <div class="border-t border-gray-200 dark:border-gray-700">
                    <button @click="almacenMobileOpen = !almacenMobileOpen" class="w-full flex items-center justify-between px-4 py-2 text-left text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition {{ request()->is('material-*') || request()->is('consumables*') ? 'bg-indigo-50 dark:bg-gray-700 text-indigo-700 dark:text-indigo-300 border-l-4 border-indigo-500' : '' }}">
                        <span>📦 Almacén</span>
                        <svg class="h-5 w-5 transition-transform duration-200" :class="{ 'rotate-180': almacenMobileOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="almacenMobileOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-y-95"
                         x-transition:enter-end="opacity-100 scale-y-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-y-100"
                         x-transition:leave-end="opacity-0 scale-y-95"
                         class="bg-gray-50 dark:bg-gray-900 border-l-2 border-gray-100 dark:border-gray-600"
                         style="display: none;">
                        
                        @if(in_array(Auth::user()->role?->name, ['Administrador', 'Almacenista']))
                            <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase">Inventario</div>
                            <x-responsive-nav-link :href="route('consumables.index')" :active="request()->routeIs('consumables.*')">
                                📦 Catálogo de Productos
                            </x-responsive-nav-link>
                            <x-responsive-nav-link :href="route('inventory-locations.index')" :active="request()->routeIs('inventory-locations.*')">
                                📍 Ubicaciones
                            </x-responsive-nav-link>
                        @endif

                        @if(in_array(Auth::user()->role?->name, ['Administrador', 'Gerencia', 'Mantenimiento']))
                            <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase mt-2">Movimientos</div>
                            
                            @if(in_array(Auth::user()->role?->name, ['Administrador', 'Gerencia']))
                                <x-responsive-nav-link :href="route('material-receptions.index')" :active="request()->routeIs('material-receptions.*')">
                                    📥 Entradas de Material
                                </x-responsive-nav-link>
                            @endif

                            <x-responsive-nav-link :href="route('material-outputs.index')" :active="request()->routeIs('material-outputs.*')">
                                📤 Salidas de Material
                            </x-responsive-nav-link>
                        @endif
                    </div>
                </div>
            @endif

            <!-- HAZMAT COLLAPSIBLE MOBILE -->
            @if(in_array(Auth::user()->role->name, ['Administrador', 'Seguridad y Salud']))
                <div class="border-t border-gray-200 dark:border-gray-700">
                    <button @click="hazmatMobileOpen = !hazmatMobileOpen" class="w-full flex items-center justify-between px-4 py-2 text-left text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition {{ request()->is('hazmat*') ? 'bg-yellow-50 dark:bg-gray-700 text-yellow-700 dark:text-yellow-300 border-l-4 border-yellow-500' : '' }}">
                        <span>⚠️ Hazmat</span>
                        <svg class="h-5 w-5 transition-transform duration-200" :class="{ 'rotate-180': hazmatMobileOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="hazmatMobileOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-y-95"
                         x-transition:enter-end="opacity-100 scale-y-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-y-100"
                         x-transition:leave-end="opacity-0 scale-y-95"
                         class="bg-gray-50 dark:bg-gray-900 border-l-2 border-gray-100 dark:border-gray-600"
                         style="display: none;">
                        
                        <x-responsive-nav-link :href="route('hazmat.index')" :active="request()->routeIs('hazmat.*')">
                            ⚠️ Listado Maestro
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('hazmat-requests.index')" :active="request()->routeIs('hazmat-requests.*')">
                            📄 Solicitudes de Autorización
                        </x-responsive-nav-link>
                    </div>
                </div>
            @endif
        </div>

        <!-- Responsive Settings -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>