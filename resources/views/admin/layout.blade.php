<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>

<body class="bg-gray-50 min-h-screen text-gray-900 overflow-x-hidden">

    <div x-data="{ open: true }" class="flex min-h-screen relative">

        <aside :class="open ? 'w-72' : 'w-24'"
            class="bg-black text-gray-400 transition-all duration-300 flex flex-col shadow-2xl z-40">

            <div class="flex items-center justify-between p-6 border-b border-gray-800/50">
                <span x-show="open" x-transition.opacity class="text-xl font-black text-white tracking-tighter">
                    C´LUCKY
                </span>
                <button @click="open = !open" class="p-2 rounded-xl bg-gray-800 text-white hover:bg-black transition-colors">
                    <x-heroicon-o-bars-3-bottom-left class="w-6 h-6" />
                </button>
            </div>

            <nav class="flex-1 p-4 space-y-2 mt-4">
                @php
                    $links = [
                        ['route' => 'admin.dashboard',          'icon' => 'o-chart-bar',             'label' => 'Dashboard'],
                        ['route' => 'admin.productos.index',    'icon' => 'o-shopping-bag',          'label' => 'Productos'],
                        ['route' => 'admin.categorias.index',   'icon' => 'o-tag',                   'label' => 'Categorías'],
                        ['route' => 'admin.pedidos.index',      'icon' => 'o-clipboard-document-list','label' => 'Pedidos'],
                        ['route' => 'admin.agencias.index',     'icon' => 'o-building-office',       'label' => 'Agencias'],
                    ];

                    $otrosRoutes = ['admin.banners.index', 'admin.cupones.index'];
                    $otrosActive = request()->routeIs($otrosRoutes);
                @endphp

                @foreach($links as $link)
                <a href="{{ route($link['route']) }}"
                   class="flex items-center gap-4 p-4 rounded-2xl font-semibold transition-all group
                   {{ request()->routeIs($link['route']) ? 'bg-white/10 text-white shadow-lg' : 'hover:bg-white/10 hover:text-white' }}">
                    <x-dynamic-component :component="'heroicon-' . $link['icon']" class="w-6 h-6 transition-transform group-hover:scale-110 flex-shrink-0" />
                    <span x-show="open" x-transition.opacity>{{ $link['label'] }}</span>
                </a>
                @endforeach

                {{-- DROPDOWN OTROS --}}
                <div x-data="{ otrosOpen: {{ $otrosActive ? 'true' : 'false' }} }">
                    <button @click="otrosOpen = !otrosOpen"
                        class="w-full flex items-center gap-4 p-4 rounded-2xl font-semibold transition-all group
                        {{ $otrosActive ? 'bg-white/10 text-white' : 'hover:bg-white/10 hover:text-white' }}">
                        <x-heroicon-o-squares-2x2 class="w-6 h-6 transition-transform group-hover:scale-110 flex-shrink-0" />
                        <span x-show="open" x-transition.opacity class="flex-1 text-left">Otros</span>
                        <x-heroicon-o-chevron-down
                            x-show="open"
                            :class="otrosOpen ? 'rotate-180' : ''"
                            class="w-4 h-4 transition-transform duration-200" />
                    </button>

                    <div x-show="otrosOpen && open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="mt-1 ml-4 pl-4 border-l border-gray-700/60 space-y-1">

                        <a href="{{ route('admin.banners.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-semibold transition-all group
                           {{ request()->routeIs('admin.banners.index') ? 'bg-white/10 text-white' : 'text-gray-500 hover:bg-white/10 hover:text-white' }}">
                            <x-heroicon-o-photo class="w-5 h-5 transition-transform group-hover:scale-110 flex-shrink-0" />
                            <span>Banners</span>
                        </a>

                        <a href="{{ route('admin.cupones.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-semibold transition-all group
                           {{ request()->routeIs('admin.cupones.index') ? 'bg-white/10 text-white' : 'text-gray-500 hover:bg-white/10 hover:text-white' }}">
                            <x-heroicon-o-ticket class="w-5 h-5 transition-transform group-hover:scale-110 flex-shrink-0" />
                            <span>Cupones</span>
                        </a>
                    </div>

                    {{-- Íconos en modo colapsado --}}
                    <div x-show="otrosOpen && !open" class="mt-1 ml-1 space-y-1">
                        <a href="{{ route('admin.banners.index') }}"
                           class="flex items-center justify-center p-3 rounded-xl transition-all
                           {{ request()->routeIs('admin.banners.index') ? 'bg-white/10 text-white' : 'text-gray-500 hover:bg-white/10 hover:text-white' }}">
                            <x-heroicon-o-photo class="w-5 h-5" />
                        </a>
                        <a href="{{ route('admin.cupones.index') }}"
                           class="flex items-center justify-center p-3 rounded-xl transition-all
                           {{ request()->routeIs('admin.cupones.index') ? 'bg-white/10 text-white' : 'text-gray-500 hover:bg-white/10 hover:text-white' }}">
                            <x-heroicon-o-ticket class="w-5 h-5" />
                        </a>
                    </div>
                </div>
            </nav>

            <div class="p-4 border-t border-gray-800/50">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-4 p-4 rounded-2xl font-semibold text-rose-400 hover:bg-rose-500/10 transition-all group text-left">
                        <x-heroicon-o-arrow-left-on-rectangle class="w-6 h-6 group-hover:-translate-x-1 transition-transform flex-shrink-0" />
                        <span x-show="open" x-transition.opacity>Cerrar sesión</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- TOASTS --}}
        <div class="fixed top-6 right-6 z-[100] flex flex-col gap-3 pointer-events-none">

            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                     x-transition:enter="transform ease-out duration-300 transition"
                     x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-10"
                     x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="pointer-events-auto flex items-center gap-4 p-5 bg-white shadow-2xl rounded-[2rem] border-l-8 border-emerald-500 min-w-[320px]">
                    <div class="flex-shrink-0 bg-emerald-100 text-emerald-600 p-2 rounded-xl">
                        <x-heroicon-s-check-circle class="w-7 h-7" />
                    </div>
                    <div class="flex-1 text-center">
                        <p class="font-black text-gray-900 text-sm leading-none">¡Éxito!</p>
                        <p class="text-gray-500 text-xs font-medium mt-1">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="text-gray-300 hover:text-gray-500 transition">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
                     x-transition:enter="transform ease-out duration-300 transition"
                     x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-10"
                     x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="pointer-events-auto flex items-center gap-4 p-5 bg-white shadow-2xl rounded-[2rem] border-l-8 border-rose-500 min-w-[320px]">
                    <div class="flex-shrink-0 bg-rose-100 text-rose-600 p-2 rounded-xl">
                        <x-heroicon-s-exclamation-circle class="w-7 h-7" />
                    </div>
                    <div class="flex-1 text-center">
                        <p class="font-black text-gray-900 text-sm leading-none">Error</p>
                        <p class="text-gray-500 text-xs font-medium mt-1">{{ session('error') }}</p>
                    </div>
                    <button @click="show = false" class="text-gray-300 hover:text-gray-500 transition">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>
            @endif
        </div>

        <main class="flex-1 p-8 lg:p-12 overflow-y-auto max-h-screen">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>
    </div>

</body>
</html>