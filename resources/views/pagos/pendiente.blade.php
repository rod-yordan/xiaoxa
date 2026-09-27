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

        {{-- Icono pendiente --}}
        <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-3">
            Pago pendiente
        </h1>

        <p class="text-gray-500 text-sm leading-relaxed mb-8">
            Tu pago está siendo procesado. Te avisaremos cuando se confirme.
            Puedes revisar el estado en cualquier momento desde
            <span class="font-semibold text-gray-700">"Mis pedidos"</span>.
        </p>

        <div class="space-y-3">
            <a href="{{ route('perfil.index') }}?seccion=pedidos"
               class="block w-full bg-gray-900 text-white rounded-full py-3.5 text-sm font-semibold hover:bg-gray-800 transition-colors">
                Ver mis pedidos
            </a>

            <a href="{{ url('/') }}"
               class="block w-full border border-gray-200 rounded-full py-3.5 text-sm font-semibold hover:bg-gray-50 transition-colors">
                Ir al inicio
            </a>
        </div>

    </div>
</div>

@endsection