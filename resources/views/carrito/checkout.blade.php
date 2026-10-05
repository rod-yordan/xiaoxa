{{-- resources/views/carrito/checkout.blade.php --}}
@extends('layouts.app')

@section('title', 'Datos de compra - Xiaoxa')

@section('content')

{{-- ===== BREADCRUMB ===== --}}
<div class="max-w-7xl mx-auto px-4 sm:px-8 pt-6 pb-2">
    <div class="flex items-center justify-center gap-2 flex-wrap">
        <a href="{{ route('carrito.index') }}" class="flex items-center gap-1">
            <span class="text-[20px] font-normal text-gray-800">Mi bolsa</span>
        </a>
        <x-heroicon-o-chevron-right class="w-5 h-5 text-gray-400" />
        <span class="text-[20px] font-bold text-gray-800">Datos de compra</span>
        <x-heroicon-o-chevron-right class="w-5 h-5 text-gray-400" />
        <span class="text-[20px] font-normal text-gray-800">Compra finalizada</span>
    </div>
</div>

{{-- CALCULAR TOTAL Y PRODUCTOS --}}
@php
    $total = 0;
    $resumenProductos = [];
    if ($carrito && $carrito->detalles->count()) {
        foreach ($carrito->detalles as $detalle) {
            $variante = $detalle->variante ?? null;
            $producto = $variante?->producto;
            if (!$producto) continue;
            $precio = $producto->precio_oferta ?? $producto->precio;
            $subtotalItem = $precio * $detalle->cantidad;
            $total += $subtotalItem;

            $resumenProductos[] = [
                'nombre'   => $producto->nombre_producto,
                'cantidad' => $detalle->cantidad,
                'subtotal' => $subtotalItem,
            ];
        }
    }

    $tipoDocumentoTextoInicial = 'Tipo de documento';
    if (auth()->user()->id_tipo_documento) {
        $td = $tiposDocumento->firstWhere('id_tipo_documento', auth()->user()->id_tipo_documento);
        if ($td) {
            $tipoDocumentoTextoInicial = $td->nombre_tipo_documento;
        }
    }
@endphp

<div x-data="checkoutData({{ $total }})" x-cloak
    class="max-w-7xl mx-auto px-4 sm:px-8 py-8 grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">

    {{-- ===== IZQUIERDA: FORMULARIO ===== --}}
    <div class="lg:col-span-8 space-y-8">

        {{-- ───── DATOS PERSONALES ───── --}}
        <div>
            <h2 class="text-base font-normal text-gray-700 mb-5">Datos personales</h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                {{-- Tipo de documento --}}
                <div>
                    <label class="block text-sm font-normal text-gray-700 mb-2">Tipo de documento</label>

                    <div x-data="{ open: false }" class="relative">
                        <input type="hidden" name="id_tipo_documento" :value="idTipoDocumento">

                        <button type="button" @click="open = !open"
                            class="w-full flex items-center justify-between gap-2 pl-4 pr-4 py-2.5 border border-gray-200 rounded-full bg-white text-sm text-gray-700 cursor-pointer focus:outline-none focus:border-gray-400 transition-colors">
                            <span x-text="idTipoDocumentoTexto"></span>
                            <x-heroicon-o-chevron-down class="w-4 h-4 shrink-0" />
                        </button>

                        <div x-show="open" x-cloak @click.outside="open = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="absolute z-50 mt-2 w-full bg-white border border-gray-200 rounded-2xl shadow-lg overflow-hidden">
                            <div class="max-h-64 overflow-y-auto py-1">
                                @foreach($tiposDocumento as $td)
                                    <button type="button"
                                        @click="idTipoDocumento = '{{ $td->id_tipo_documento }}'; idTipoDocumentoTexto = '{{ addslashes($td->nombre_tipo_documento) }}'; open = false"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 transition">
                                        {{ $td->nombre_tipo_documento }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Número de documento --}}
                <div>
                    <label class="block text-sm font-normal text-gray-700 mb-2">Número de documento</label>
                    <input type="text" name="numero_documento"
                        x-model="numeroDocumento"
                        placeholder="Ingresar documento"
                        class="w-full border border-gray-200 rounded-full px-4 py-2.5 text-sm text-gray-700 placeholder-gray-400 bg-white focus:outline-none focus:border-gray-400 transition-colors">
                </div>

                {{-- Teléfono --}}
                <div>
                    <label class="block text-sm font-normal text-gray-700 mb-2">Teléfono</label>
                    <input type="text" name="telefono"
                        x-model="telefono"
                        placeholder="Ingresar teléfono"
                        class="w-full border border-gray-200 rounded-full px-4 py-2.5 text-sm text-gray-700 placeholder-gray-400 bg-white focus:outline-none focus:border-gray-400 transition-colors">
                </div>

            </div>
        </div>

        {{-- ───── DATOS DE ENTREGA ───── --}}
        <div>
            <h2 class="text-base font-normal text-gray-700 mb-5">Datos de entrega</h2>

            {{-- Radio --}}
            <div class="flex items-center gap-8 mb-6 text-sm text-gray-700">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="tipo_entrega" value="2"
                        x-model.number="tipoEntrega"
                        class="w-4 h-4 cursor-pointer"
                        style="accent-color: #111827;">
                    <span>Envío a provincia</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="tipo_entrega" value="1"
                        x-model.number="tipoEntrega"
                        class="w-4 h-4 cursor-pointer"
                        style="accent-color: #111827;">
                    <span>Retiro en tienda</span>
                </label>
            </div>

            {{-- Campos de ubicación (solo envío a provincia) --}}
            <div x-show="tipoEntrega == 2" class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                <div>
                    <label class="block text-sm font-normal text-gray-700 mb-2">Departamento</label>
                    <input type="text"
                        x-model="departamento"
                        placeholder="Ingresar departamento"
                        class="w-full border border-gray-200 rounded-full px-4 py-2.5 text-sm text-gray-700 placeholder-gray-400 bg-white focus:outline-none focus:border-gray-400 transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-normal text-gray-700 mb-2">Provincia</label>
                    <input type="text"
                        x-model="provincia"
                        placeholder="Ingresar provincia"
                        class="w-full border border-gray-200 rounded-full px-4 py-2.5 text-sm text-gray-700 placeholder-gray-400 bg-white focus:outline-none focus:border-gray-400 transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-normal text-gray-700 mb-2">Distrito</label>
                    <input type="text"
                        x-model="distrito"
                        placeholder="Ingresar distrito"
                        class="w-full border border-gray-200 rounded-full px-4 py-2.5 text-sm text-gray-700 placeholder-gray-400 bg-white focus:outline-none focus:border-gray-400 transition-colors">
                </div>

            </div>

            {{-- ══════════════════════════════════════════════════════ --}}
            {{-- MENSAJES DINÁMICOS                                     --}}
            {{-- ══════════════════════════════════════════════════════ --}}
            <div class="mt-6">

                {{-- ── ENVÍO A PROVINCIA ── --}}
                <div x-show="tipoEntrega == 2" class="space-y-4">

                    <p class="text-sm text-gray-800">
                        Tu pedido llegará en un plazo de 3 a 5 días hábiles.
                    </p>

                    <div class="relative bg-gray-900 rounded-2xl p-4 overflow-hidden">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 bg-white/10 rounded-xl flex items-center justify-center shrink-0">
                                <x-heroicon-o-exclamation-circle class="w-5 h-5 text-white" />
                            </div>
                            <p class="text-gray-200 text-sm leading-relaxed pt-1">
                                Cuando su pedido haya sido revisado, podrá ver la fecha y dirección donde deberá recoger su pedido en la lista de sus pedidos, ubicada en el apartado de su perfil.
                            </p>
                        </div>
                    </div>

                </div>

                {{-- ── RETIRO EN TIENDA ── --}}
                <div x-show="tipoEntrega != 2" class="space-y-4">

                    <div class="relative bg-gray-900 rounded-2xl p-4 overflow-hidden">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 bg-white/10 rounded-xl flex items-center justify-center shrink-0">
                                <x-heroicon-o-exclamation-circle class="w-5 h-5 text-white" />
                            </div>
                            <p class="text-gray-200 text-sm leading-relaxed pt-1">
                                Cuando su pedido haya sido revisado, podrá ver la fecha en que deberá recogerlo en la lista de sus pedidos, ubicada en el apartado de su perfil.
                            </p>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        {{-- Botón pagar mobile --}}
        <div class="lg:hidden">
            <button @click="intentarPagar"
                class="w-full bg-gray-900 text-white rounded-full py-4 text-sm font-semibold tracking-wide hover:bg-gray-800 transition-all">
                <span x-show="!procesando">Continuar con la compra</span>
                <span x-show="procesando" class="flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>
                    Procesando…
                </span>
            </button>
        </div>

    </div>

    {{-- ===== DERECHA: RESUMEN DEL PEDIDO ===== --}}
    <div class="lg:col-span-4">
        <div class="bg-[#f1f1f1] rounded-2xl p-7 lg:sticky lg:top-24">

            <h2 class="font-bold text-lg mb-6 text-gray-900">Resumen del pedido</h2>

            {{-- Lista de productos --}}
            <div class="space-y-3 mb-5">
                @foreach($resumenProductos as $item)
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm text-gray-800 leading-tight">
                                {{ ucwords(strtolower($item['nombre'])) }} x {{ $item['cantidad'] }}
                            </p>
                        </div>
                        <span class="text-sm text-gray-800 shrink-0">
                            S/ {{ number_format($item['subtotal'], 2) }}
                        </span>
                    </div>
                @endforeach
            </div>

            <div class="flex justify-between items-center">
                <span class="font-bold text-gray-800 text-[16px]">Total</span>
                <span class="font-bold text-gray-800 text-[16px]">S/ {{ number_format($total, 2) }}</span>
            </div>

            <button @click="intentarPagar"
                class="hidden lg:flex mt-7 items-center justify-center w-full bg-gray-900 text-white rounded-full py-4 text-xs font-bold tracking-widest hover:bg-gray-800 active:scale-[0.99] transition-all uppercase">
                <span x-show="!procesando">Continuar con la compra</span>
                <span x-show="procesando" class="flex items-center gap-2">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>
                    Procesando…
                </span>
            </button>

        </div>
    </div>

    {{-- Formulario oculto --}}
    <form id="form-pedido" method="POST" action="{{ route('checkout.confirmar') }}">
        @csrf
        <input type="hidden" name="id_tipo_entrega"    x-bind:value="tipoEntrega">
        <input type="hidden" name="departamento"       x-bind:value="departamento">
        <input type="hidden" name="provincia"          x-bind:value="provincia">
        <input type="hidden" name="distrito"           x-bind:value="distrito">
        <input type="hidden" name="id_tipo_documento"  x-bind:value="idTipoDocumento">
        <input type="hidden" name="numero_documento"   x-bind:value="numeroDocumento">
        <input type="hidden" name="telefono"           x-bind:value="telefono">
    </form>

</div>

@endsection

@push('scripts')
<script>
    function checkoutData(totalProductos) {
        return {
            tipoEntrega:         {{ $tiposEntrega->first()->id_tipo_entrega ?? 1 }},
            departamento:        '',
            provincia:           '',
            distrito:            '',
            totalProductos:      totalProductos,
            procesando:          false,

            idTipoDocumento:      '{{ auth()->user()->id_tipo_documento ?? '' }}',
            idTipoDocumentoTexto: '{{ addslashes($tipoDocumentoTextoInicial) }}',
            numeroDocumento:      '{{ auth()->user()->numero_documento ?? '' }}',
            telefono:             '{{ auth()->user()->telefono ?? '' }}',

            intentarPagar() {
                if (!this.idTipoDocumento) {
                    alert('Selecciona un tipo de documento.');
                    return;
                }
                if (!this.numeroDocumento) {
                    alert('Ingresa tu número de documento.');
                    return;
                }
                if (!this.telefono) {
                    alert('Ingresa tu teléfono.');
                    return;
                }
                if (this.tipoEntrega == 2 && !this.departamento) {
                    alert('Ingresa el departamento de envío.');
                    return;
                }
                if (this.tipoEntrega == 2 && !this.provincia) {
                    alert('Ingresa la provincia de envío.');
                    return;
                }
                if (this.tipoEntrega == 2 && !this.distrito) {
                    alert('Ingresa el distrito de envío.');
                    return;
                }
                this.procesando = true;
                document.getElementById('form-pedido').submit();
            },
        }
    }
</script>
@endpush