@extends('layouts.app')

@section('title', 'Compra finalizada - Xiaoxa')

@section('content')

{{-- ===== BREADCRUMB (mismo que en checkout) ===== --}}
<div class="max-w-7xl mx-auto px-4 sm:px-8 pt-6 pb-2">
    <div class="flex items-center justify-center gap-2 flex-wrap">
        <a href="{{ route('carrito.index') }}" class="flex items-center gap-1">
            <span class="text-[20px] font-normal text-gray-800">Mi bolsa</span>
        </a>
        <x-heroicon-o-chevron-right class="w-5 h-5 text-gray-400" />
        <span class="text-[20px] font-normal text-gray-800">Datos de compra</span>
        <x-heroicon-o-chevron-right class="w-5 h-5 text-gray-400" />
        <span class="text-[20px] font-bold text-gray-800">Compra finalizada</span>
    </div>
</div>

{{-- ===== CONTENIDO ===== --}}
<div class="max-w-7xl mx-auto px-4 sm:px-8 py-12">

    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-10 text-center max-w-lg mx-auto">

        {{-- Icono fallo --}}
        <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-3">
            El pago no se pudo procesar
        </h1>

        <p class="text-gray-500 text-sm leading-relaxed mb-8">
            Tu pago fue rechazado. Puedes intentar nuevamente desde tu
            <span class="font-semibold text-gray-700">"Mi bolsa"</span>
            o contactar a tu banco si el problema persiste.
        </p>

        <div class="space-y-3">
            <a href="{{ route('carrito.index') }}"
               class="block w-full bg-gray-900 text-white rounded-full py-3.5 text-sm font-semibold hover:bg-gray-800 transition-colors">
                Volver a intentar
            </a>

            <a href="{{ route('perfil.index') }}?seccion=pedidos"
               class="block w-full border border-gray-200 rounded-full py-3.5 text-sm font-semibold hover:bg-gray-50 transition-colors">
                Ver mis pedidos
            </a>
        </div>

    </div>
</div>

@endsection