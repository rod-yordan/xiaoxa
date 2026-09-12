{{-- resources/views/components/navbar.blade.php --}}
<nav class="border-b border-gray-200 bg-white"
    x-data="{
        searchOpen: false,
        query: '{{ addslashes(request('buscar', '')) }}',
        submit() {
            const q = this.query.trim();
            if (q.length > 0) {
                window.location.href = '{{ route('home') }}?buscar=' + encodeURIComponent(q);
            }
        },
        clear() {
            this.query = '';
            window.location.href = '{{ route('home') }}';
        }
    }"
>
    {{-- FILA SUPERIOR: BUSCADOR | LOGO | ICONOS --}}
    <div class="bg-white">
        <div class="w-full px-5 sm:px-8">
            {{-- Grid de 3 columnas exactas --}}
            <div class="grid grid-cols-3 items-center h-20">
                
                {{-- COLUMNA 1: BUSCADOR (izquierda) --}}
                <div class="flex items-center justify-start">
                    <div class="hidden lg:flex items-center bg-[#f1f1f1] rounded-full px-3 py-1">
                        <x-heroicon-o-magnifying-glass class="w-5 h-5 text-black mr-2" />
                        <input
                            x-ref="searchInput"
                            x-model="query"
                            @keydown.enter="submit()"
                            type="text"
                            placeholder="Buscar..."
                            class="w-48 bg-transparent focus:outline-none text-black placeholder-black text-sm py-1"
                        >
                    </div>
                </div>

                {{-- COLUMNA 2: LOGO (centrado) --}}
                <div class="flex items-center justify-center">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('images/logo.jpeg') }}" alt="Xiaoxa" class="h-14 w-auto">
                    </a>
                </div>

                {{-- COLUMNA 3: ICONOS (derecha) --}}
                <div class="flex items-center justify-end gap-1">

                    {{-- LUPA MÓVIL --}}
                    <button @click="searchOpen = !searchOpen" class="lg:hidden p-1" aria-label="Buscar">
                        <x-heroicon-o-magnifying-glass x-show="!searchOpen" class="h-7 w-7 text-black" />
                        <x-heroicon-o-x-mark x-show="searchOpen" x-cloak class="h-7 w-7 text-black" />
                    </button>

                    {{-- USUARIO --}}
                    @auth
                        <a href="{{ route('perfil.index') }}" class="p-1" title="Mi cuenta">
                            <x-heroicon-o-user class="h-7 w-7 text-black" />
                        </a>
                    @endauth
                    @guest
                        <a href="{{ route('login') }}" class="p-1" title="Iniciar sesión">
                            <x-heroicon-o-user class="h-7 w-7 text-black" />
                        </a>
                    @endguest

                    {{-- CARRITO --}}
                    <a href="{{ route('carrito.index') }}" class="relative p-1" title="Carrito">
                        <x-heroicon-o-shopping-bag class="h-7 w-7 text-black" />
                        @php
                            $totalItems = session('carrito') ? count(session('carrito')) : 0;
                        @endphp
                        @if($totalItems > 0)
                            <span class="absolute -top-1 -right-0 bg-red-500 text-white text-[9px] rounded-full h-4 w-4 flex items-center justify-center font-bold">
                                {{ $totalItems }}
                            </span>
                        @endif
                    </a>

                    {{-- ADMIN --}}
                    @auth
                        @if(auth()->user()->id_rol == 1)
                            <a href="{{ route('admin.dashboard') }}" class="hidden xl:block text-xs font-bold text-black border border-black px-3 py-1.5 rounded-full hover:bg-black hover:text-white transition-all duration-200 ml-1">
                                Admin
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>

    {{-- LÍNEA SEPARADORA --}}
    <div class="border-t border-gray-200"></div>

    {{-- FILA DE CATEGORÍAS CON FONDO #f1f1f1 --}}
    <div class="bg-[#f1f1f1]">
        <div class="w-full px-5 sm:px-8">
            <div class="hidden md:flex items-center justify-center space-x-8 lg:space-x-10 py-3">
                <a href="{{ route('home') }}" 
                    class="text-base font-normal text-black">
                    Lo nuevo
                </a>
                <a href="{{ route('home', ['categoria' => 'Mujer']) }}"
                    class="text-base font-normal text-black">
                    Categorías
                </a>
                <a href="{{ route('home', ['categoria' => 'Hombre']) }}"
                    class="text-base font-normal text-black">
                    Accesorios
                </a>
                <a href="{{ route('home', ['promocion' => 1]) }}"
                    class="text-base font-normal text-black">
                    Promociones
                </a>
            </div>

            {{-- MENÚ MÓVIL --}}
            <div class="md:hidden flex items-center justify-center py-2">
                <div class="flex space-x-4 text-xs font-normal">
                    <a href="{{ route('home') }}" class="text-black">
                        Inicio
                    </a>
                    <a href="{{ route('home', ['categoria' => 'Mujer']) }}" class="text-black">
                        Mujer
                    </a>
                    <a href="{{ route('home', ['categoria' => 'Hombre']) }}" class="text-black">
                        Hombre
                    </a>
                    <a href="{{ route('home', ['promocion' => 1]) }}" class="text-black">
                        Ofertas
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- BUSCADOR MÓVIL --}}
    <div
        x-show="searchOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0 -translate-y-1"
        class="lg:hidden border-t border-gray-100 px-4 py-3 bg-white shadow-md"
    >
        <div class="relative max-w-md mx-auto">
            <div class="flex items-center bg-[#f1f1f1] rounded-full px-3 py-1">
                <x-heroicon-o-magnifying-glass class="w-5 h-5 text-black mr-2" />
                <input
                    x-model="query"
                    @keydown.enter="submit()"
                    type="text"
                    placeholder="Buscar productos..."
                    class="w-full bg-transparent focus:outline-none text-black placeholder-black text-sm py-1"
                >
            </div>
        </div>
    </div>
</nav>