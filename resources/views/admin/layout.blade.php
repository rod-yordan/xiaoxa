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

    <div class="flex min-h-screen relative">

        {{-- ============== SIDEBAR ============== --}}
        <aside class="w-64 bg-white border-r border-gray-200 flex flex-col z-40">

            {{-- Logo: misma distancia arriba (pt-14) y a la izquierda (pl-14) --}}
            <div class="pt-14 pb-8 pl-14 border-b border-gray-100">
                <a href="{{ route('admin.dashboard') }}" class="inline-block">
                    <img src="{{ asset('images/logo.jpeg') }}" alt="Xiaoxa" class="h-12 w-auto">
                </a>
            </div>

            {{-- Menú --}}
            <nav class="flex-1 py-6 space-y-2 overflow-y-auto">
                @php
                    $links = [
                        ['route' => 'admin.dashboard',          'icon' => 'o-chart-bar',              'label' => 'Dashboard'],
                        ['route' => 'admin.productos.index',    'icon' => 'o-cube',                   'label' => 'Productos'],
                        ['route' => 'admin.categorias.index',   'icon' => 'o-tag',                    'label' => 'Categorías'],
                        ['route' => 'admin.pedidos.index',      'icon' => 'o-clipboard-document-list','label' => 'Pedidos'],
                    ];

                    $otrosRoutes = ['admin.banners.index', 'admin.cupones.index'];
                    $otrosActive = request()->routeIs($otrosRoutes);
                @endphp

                @foreach($links as $link)
                @php $activo = request()->routeIs($link['route']); @endphp
                <a href="{{ route($link['route']) }}"
                   class="flex items-center gap-3 py-3.5 pl-14 pr-5 mr-4 font-medium transition-all
                   {{ $activo
                        ? 'bg-pink-600 text-white rounded-r-full'
                        : 'text-gray-700 hover:bg-gray-100 rounded-full' }}">
                    <x-dynamic-component :component="'heroicon-' . $link['icon']" class="w-5 h-5 flex-shrink-0" />
                    <span>{{ $link['label'] }}</span>
                </a>
                @endforeach

                {{-- DROPDOWN OTROS --}}
                <div x-data="{ otrosOpen: {{ $otrosActive ? 'true' : 'false' }} }" class="mr-4">
                    <button @click="otrosOpen = !otrosOpen"
                        class="w-full flex items-center gap-3 py-3.5 pl-14 pr-5 font-medium transition-all
                        {{ $otrosActive ? 'bg-pink-600 text-white rounded-r-full' : 'text-gray-700 hover:bg-gray-100 rounded-full' }}">
                        <x-heroicon-o-squares-2x2 class="w-5 h-5 flex-shrink-0" />
                        <span class="flex-1 text-left">Otros</span>
                        <x-heroicon-o-chevron-down
                            :class="otrosOpen ? 'rotate-180' : ''"
                            class="w-4 h-4 transition-transform duration-200" />
                    </button>

                    <div x-show="otrosOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="mt-1 ml-4 pl-4 border-l border-gray-200 space-y-1">

                        <a href="{{ route('admin.banners.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-full font-medium transition-all
                           {{ request()->routeIs('admin.banners.index') ? 'bg-pink-100 text-pink-700' : 'text-gray-600 hover:bg-gray-100' }}">
                            <x-heroicon-o-photo class="w-5 h-5 flex-shrink-0" />
                            <span>Banners</span>
                        </a>

                        <a href="{{ route('admin.cupones.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-full font-medium transition-all
                           {{ request()->routeIs('admin.cupones.index') ? 'bg-pink-100 text-pink-700' : 'text-gray-600 hover:bg-gray-100' }}">
                            <x-heroicon-o-ticket class="w-5 h-5 flex-shrink-0" />
                            <span>Cupones</span>
                        </a>
                    </div>
                </div>
            </nav>

            {{-- Cerrar sesión --}}
            <div class="p-4 border-t border-gray-100">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 pl-14 pr-5 py-3.5 rounded-full font-semibold text-gray-700 hover:bg-gray-100 transition-all text-left">
                        <x-heroicon-o-arrow-left-on-rectangle class="w-5 h-5 flex-shrink-0" />
                        <span class="text-sm">CERRAR SESION</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- ============== TOASTS ============== --}}
        <div class="fixed top-6 right-6 z-[999] flex flex-col gap-3 pointer-events-none">

            {{-- ✅ ÉXITO --}}
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
                    <div class="flex-1">
                        <p class="font-black text-gray-900 text-sm leading-none">¡Éxito!</p>
                        <p class="text-gray-500 text-xs font-medium mt-1">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="text-gray-300 hover:text-gray-500 transition">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>
            @endif

            {{-- ❌ ERRORES DE VALIDACIÓN --}}
            @if($errors->any())
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 8000)"
                     x-transition:enter="transform ease-out duration-300 transition"
                     x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-10"
                     x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="pointer-events-auto flex items-start gap-4 p-5 bg-white shadow-2xl rounded-[2rem] border-l-8 border-rose-500 min-w-[320px] max-w-md">
                    <div class="flex-shrink-0 bg-rose-100 text-rose-600 p-2 rounded-xl">
                        <x-heroicon-s-exclamation-circle class="w-7 h-7" />
                    </div>
                    <div class="flex-1">
                        <p class="font-black text-gray-900 text-sm leading-none">No se pudo guardar</p>
                        <ul class="text-gray-500 text-xs font-medium mt-2 space-y-1 list-disc pl-4">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button @click="show = false" class="text-gray-300 hover:text-gray-500 transition flex-shrink-0">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>
            @endif

            {{-- ❌ ERROR --}}
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
                    <div class="flex-1">
                        <p class="font-black text-gray-900 text-sm leading-none">Error</p>
                        <p class="text-gray-500 text-xs font-medium mt-1">{{ session('error') }}</p>
                    </div>
                    <button @click="show = false" class="text-gray-300 hover:text-gray-500 transition">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>
            @endif
        </div>

        {{-- ============== CONTENIDO ============== --}}
        <main class="flex-1 overflow-y-auto max-h-screen">
            <div class="px-10 py-8">
                @yield('content')
            </div>
        </main>
    </div>

</body>
</html>