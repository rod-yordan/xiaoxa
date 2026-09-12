@extends('layouts.app')

@section('title', 'Xiaoxa')

@section('content')

{{-- ===== CARRUSEL ===== --}}
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
                <img
                    src="{{ asset('banners/' . $banner->imagen) }}"
                    alt="{{ $banner->titulo }}"
                    class="w-full h-full object-cover object-center"
                    loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                >
            </div>
            @endforeach
        </div>

        {{-- INDICADORES RECTANGULARES DEL MISMO TAMAÑO --}}
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
{{-- Banner por defecto --}}
<section class="w-full bg-white">
    <div class="relative w-full" style="height: 750px; overflow: hidden;">
        <img src="{{ asset('images/banner-home.jpg') }}" alt="Banner" class="w-full h-full object-cover object-center">
    </div>
</section>
@endif

{{-- ===== SECCIÓN DE PRODUCTOS ===== --}}
<div class="max-w-7xl mx-auto px-4 sm:px-8 py-10">

    <div class="flex items-center justify-between mb-7">
        <div>
            <h2 class="text-base font-bold uppercase tracking-[0.15em] text-black">
                @if(request('categoria')) {{ request('categoria') }}
                @elseif(request('promocion')) Promociones
                @else Todos los Productos
                @endif
            </h2>
        </div>
        @if($productos->count() > 4)
        <a href="#" class="text-xs font-medium text-gray-400 hover:text-black transition-colors flex items-center gap-1">
            Ver todo
            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
        @endif
    </div>

    @if($productos->isEmpty())
        <div class="text-center py-24">
            <svg class="w-10 h-10 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0H4"/>
            </svg>
            <p class="text-sm font-medium text-gray-400">No hay productos disponibles</p>
        </div>
    @else
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
        @foreach($productos as $item)
            <a href="{{ url('/producto/' . $item->id_producto) }}"
                class="group cursor-pointer block bg-white border border-gray-100 rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1">

                <div class="relative aspect-[3/4] bg-gray-50 overflow-hidden">
                    @if($item->precio_oferta)
                        <span class="absolute top-2.5 left-2.5 bg-red-500 text-white text-[9px] font-bold px-2.5 py-1 z-10 uppercase tracking-wider rounded-full shadow-sm">
                            Oferta
                        </span>
                    @endif
                    <img
                        src="{{ asset('productos/' . $item->imagen) }}"
                        alt="{{ $item->nombre_producto }}"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                        loading="lazy"
                    >
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition-colors duration-300"></div>
                </div>

                <div class="p-3.5 space-y-1">
                    <p class="text-[9px] text-gray-400 uppercase font-bold tracking-widest">{{ $item->marca }}</p>
                    <h3 class="text-[13px] font-medium text-gray-800 leading-snug line-clamp-2">{{ $item->nombre_producto }}</h3>
                    <div class="h-px bg-gray-100 my-2"></div>
                    <div class="flex items-center gap-2 flex-wrap">
                        @if($item->precio_oferta)
                            <span class="text-sm font-bold text-red-500">S/ {{ number_format($item->precio_oferta, 2) }}</span>
                            <span class="text-[11px] text-gray-400 line-through">S/ {{ number_format($item->precio, 2) }}</span>
                            <span class="ml-auto text-[9px] font-bold text-red-500 bg-red-50 px-1.5 py-0.5 rounded">
                                -{{ round((1 - $item->precio_oferta / $item->precio) * 100) }}%
                            </span>
                        @else
                            <span class="text-sm font-bold text-gray-900">S/ {{ number_format($item->precio, 2) }}</span>
                        @endif
                    </div>
                </div>
            </a>
        @endforeach
    </div>
    @endif

</div>

@endsection