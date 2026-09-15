@extends('layouts.app')

@section('title', 'Xiaoxa')

@section('content')

{{-- ===== CARRUSEL (SOLO EN HOME SIN FILTROS) ===== --}}
@if(!request('categoria') && !request('promocion') && !request('buscar'))
    @if(isset($banners) && $banners->count() > 0)
    <section class="w-full bg-white">
        <div
            x-data="{
                current: 0,
                total: {{ $banners->count() }},
                autoplay: null,
                init() { this.startAutoplay(); },
                startAutoplay() { this.autoplay = setInterval(() => this.next(), 5500); },
                stopAutoplay() { clearInterval(this.autoplay); },
                next() { this.current = (this.current + 1) % this.total; },
                prev() { this.current = (this.current - 1 + this.total) % this.total; },
                goTo(i) { this.current = i; this.stopAutoplay(); this.startAutoplay(); }
            }"
            @mouseenter="stopAutoplay()"
            @mouseleave="startAutoplay()"
            class="relative w-full"
        >
            <div class="relative w-full" style="height: 750px; overflow: hidden;">
                @foreach($banners as $index => $banner)
                <div
                    x-show="current === {{ $index }}"
                    x-transition:enter="transition-opacity duration-700 ease-in-out"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity duration-500 ease-in-out"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="absolute inset-0 flex items-center justify-center"
                >
                    @if($banner->url_boton)
                        <a href="{{ $banner->url_boton }}" class="block w-full h-full">
                            <img
                                src="{{ url('/api/imagen/' . $banner->imagen) }}"
                                alt="{{ $banner->titulo }}"
                                class="w-full h-full object-cover object-center"
                                loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                            >
                        </a>
                    @else
                        <img
                            src="{{ url('/api/imagen/' . $banner->imagen) }}"
                            alt="{{ $banner->titulo }}"
                            class="w-full h-full object-cover object-center"
                            loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                        >
                    @endif
                </div>
                @endforeach
            </div>

            @if($banners->count() > 1)
            <div class="flex items-center justify-center gap-2 mt-3 pb-3">
                @foreach($banners as $index => $banner)
                <button
                    @click="goTo({{ $index }})"
                    :class="current === {{ $index }} ? 'w-12 h-1.5 bg-gray-600' : 'w-12 h-1.5 bg-gray-300 hover:bg-gray-400'"
                    class="rounded-sm transition-all duration-300"
                    aria-label="Slide {{ $index + 1 }}"
                ></button>
                @endforeach
            </div>
            @endif
        </div>
    </section>
    @else
    <section class="w-full bg-white">
        <div class="relative w-full" style="height: 750px; overflow: hidden;">
            <img src="{{ asset('images/banner-home.jpg') }}" alt="Banner" class="w-full h-full object-cover object-center">
        </div>
    </section>
    @endif
@endif


{{-- ===== SECCIONES POR CATEGORÍA (SOLO CUANDO NO HAY FILTROS) ===== --}}
@if(isset($categorias) && $categorias->count() > 0)

    @foreach($categorias as $categoria)
    <div class="max-w-7xl mx-auto px-4 sm:px-8 py-10">

        {{-- TÍTULO CENTRADO + VER TODO A LA DERECHA --}}
        <div class="flex items-center justify-between mb-7">
            <div class="w-20"></div>
            <h2 class="text-3xl font-normal text-black text-center flex-1">
                {{ $categoria->nombre_categoria }}
            </h2>
            <div class="w-20 flex justify-end">
                <a href="{{ route('home', ['categoria' => $categoria->nombre_categoria]) }}"
                    class="text-xs font-medium hover:text-black transition-colors flex items-center gap-1 mt-4">
                    Ver todo
                    <x-heroicon-o-chevron-right class="w-3 h-3" />
                </a>
            </div>
        </div>

        {{-- GRID DE PRODUCTOS (4 COLUMNAS) --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
            @foreach($categoria->productos->take(4) as $item)
                <a href="{{ url('/producto/' . $item->id_producto) }}"
                    class="group cursor-pointer block transition-all duration-300">

                    {{-- IMAGEN --}}
                    <div class="relative aspect-[3/4] bg-[#f5f5f5] overflow-hidden rounded-xl">
                        @if($item->precio_oferta)
                            <span class="absolute top-2.5 right-2.5 bg-red-500 text-white text-[15px] font-semibold leading-none min-w-[46px] text-center px-1 py-1.5 z-10 rounded-md shadow-sm">
                                -{{ round((1 - $item->precio_oferta / $item->precio) * 100) }}%
                            </span>
                        @endif
                        <img
                            src="{{ url('/api/imagen/' . $item->imagen) }}"
                            alt="{{ $item->nombre_producto }}"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                            loading="lazy"
                        >
                    </div>

                    {{-- INFO --}}
                    <div class="pt-3 space-y-1">
                        <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">
                            {{ $item->marca }}
                        </p>
                        <h3 class="text-[15px] font-normal text-gray-800 leading-snug line-clamp-2">
                            {{ ucwords(strtolower($item->nombre_producto)) }}
                        </h3>
                        <div class="flex items-center gap-2 flex-wrap pt-1">
                            @if($item->precio_oferta)
                                <span class="text-xl font-normal text-red-500">
                                    S/ {{ number_format($item->precio_oferta, 2) }}
                                </span>
                                <span class="text-sm text-gray-500 line-through">
                                    S/ {{ number_format($item->precio, 2) }}
                                </span>
                            @else
                                <span class="text-xl font-normal text-gray-900">
                                    S/ {{ number_format($item->precio, 2) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

    </div>
    @endforeach

@endif


{{-- ===== GRID ÚNICO (CUANDO HAY FILTROS O BÚSQUEDA) ===== --}}
@if(isset($productos) && $productos->count() > 0)

<div class="max-w-7xl mx-auto px-4 sm:px-8 py-10">

    {{-- ICONO HOME + CHEVRON + TÍTULO --}}
    <div class="flex items-center justify-between mb-7">
        <div class="flex items-center gap-2">
            <a href="{{ route('home') }}"
                class="flex items-center"
                title="Volver al inicio">
                <x-heroicon-o-home class="w-5 h-5 text-black" />
                <x-heroicon-o-chevron-right class="w-3 h-3 text-black" />
            </a>
            <h2 class="text-base font-normal text-black">
                @if(request('categoria')) {{ ucwords(strtolower(request('categoria'))) }}
                @elseif(request('promocion')) Promociones
                @elseif(request('buscar')) Resultados: "{{ request('buscar') }}"
                @else Todos los Productos
                @endif
            </h2>
        </div>
    </div>

    {{-- GRID --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
        @foreach($productos as $item)
            <a href="{{ url('/producto/' . $item->id_producto) }}"
                class="group cursor-pointer block transition-all duration-300">

                {{-- IMAGEN --}}
                <div class="relative aspect-[3/4] bg-[#f5f5f5] overflow-hidden rounded-xl">
                    @if($item->precio_oferta)
                        <span class="absolute top-2.5 right-2.5 bg-red-500 text-white text-[15px] font-semibold leading-none min-w-[46px] text-center px-1 py-1.5 z-10 rounded-md shadow-sm">
                            -{{ round((1 - $item->precio_oferta / $item->precio) * 100) }}%
                        </span>
                    @endif
                    <img
                        src="{{ url('/api/imagen/' . $item->imagen) }}"
                        alt="{{ $item->nombre_producto }}"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                        loading="lazy"
                    >
                </div>

                {{-- INFO --}}
                <div class="pt-3 space-y-1">
                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">
                        {{ $item->marca }}
                    </p>
                    <h3 class="text-[15px] font-normal text-gray-800 leading-snug line-clamp-2">
                        {{ ucwords(strtolower($item->nombre_producto)) }}
                    </h3>
                    <div class="flex items-center gap-2 flex-wrap pt-1">
                        @if($item->precio_oferta)
                            <span class="text-xl font-normal text-red-500">
                                S/ {{ number_format($item->precio_oferta, 2) }}
                            </span>
                            <span class="text-sm text-gray-500 line-through">
                                S/ {{ number_format($item->precio, 2) }}
                            </span>
                        @else
                            <span class="text-xl font-normal text-gray-900">
                                S/ {{ number_format($item->precio, 2) }}
                            </span>
                        @endif
                    </div>
                </div>
            </a>
        @endforeach
    </div>

</div>

{{-- SIN RESULTADOS --}}
@elseif(isset($productos) && $productos->isEmpty() && (request('buscar') || request('categoria') || request('promocion')))
<div class="max-w-7xl mx-auto px-4 sm:px-8 py-24 text-center">
    <x-heroicon-o-shopping-bag class="w-12 h-12 mx-auto mb-3 text-gray-200" />
    <p class="text-sm font-medium text-gray-400">No hay productos disponibles</p>
</div>
@endif

@endsection