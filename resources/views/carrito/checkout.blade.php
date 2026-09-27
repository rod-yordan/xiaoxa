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

{{-- CALCULAR TOTAL --}}
@php
    $total = 0;
    if ($carrito && $carrito->detalles->count()) {
        foreach ($carrito->detalles as $detalle) {
            $variante = $detalle->variante ?? null;
            $producto = $variante?->producto;
            if (!$producto) continue;
            $precio = $producto->precio_oferta ?? $producto->precio;
            $total += $precio * $detalle->cantidad;
        }
    }
@endphp

<div x-data="checkoutData()" x-cloak
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
                    <div class="relative">
                        <select name="id_tipo_documento"
                            x-model="idTipoDocumento"
                            class="w-full border border-gray-200 rounded-full px-4 py-2.5 text-sm text-gray-700 bg-white focus:outline-none focus:border-gray-400 transition-colors appearance-none pr-10">
                            <option value="">Seleccionar</option>
                            @foreach($tiposDocumento as $td)
                                <option value="{{ $td->id_tipo_documento ?? $td->id }}"
                                    {{ (auth()->user()->id_tipo_documento ?? null) == ($td->id_tipo_documento ?? $td->id) ? 'selected' : '' }}>
                                    {{ $td->nombre_tipo_documento ?? $td->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <x-heroicon-o-chevron-down class="w-4 h-4 text-gray-700 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" />
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

            {{-- Radio: envío a provincia / retiro en tienda --}}
            <div class="flex items-center gap-8 mb-6 text-sm text-gray-700">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="tipo_entrega" value="2"
                        x-model.number="tipoEntrega"
                        @change="calcularEnvioPorTipo()"
                        class="w-4 h-4 cursor-pointer"
                        style="accent-color: #111827;">
                    <span>Envío a provincia</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="tipo_entrega" value="1"
                        x-model.number="tipoEntrega"
                        @change="calcularEnvioPorTipo()"
                        class="w-4 h-4 cursor-pointer"
                        style="accent-color: #111827;">
                    <span>Retiro en tienda</span>
                </label>
            </div>

            {{-- Selects: Departamento / Provincia / Distrito (solo envío a provincia) --}}
            <div x-show="tipoEntrega == 2" class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                {{-- Departamento --}}
                <div>
                    <label class="block text-sm font-normal text-gray-700 mb-2">Departamento</label>
                    <div class="relative">
                        <select x-model="departamento"
                            class="w-full border border-gray-200 rounded-full px-4 py-2.5 text-sm text-gray-700 bg-white focus:outline-none focus:border-gray-400 transition-colors appearance-none pr-10">
                            <option value="">Seleccionar</option>
                            @foreach($departamentos as $dep)
                                <option value="{{ $dep->id_departamento }}">{{ $dep->nombre_departamento }}</option>
                            @endforeach
                        </select>
                        <x-heroicon-o-chevron-down class="w-4 h-4 text-gray-700 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" />
                    </div>
                </div>

                {{-- Provincia --}}
                <div>
                    <label class="block text-sm font-normal text-gray-700 mb-2">Provincia</label>
                    <input type="text"
                        x-model="provincia"
                        placeholder="Ingresar provincia"
                        class="w-full border border-gray-200 rounded-full px-4 py-2.5 text-sm text-gray-700 placeholder-gray-400 bg-white focus:outline-none focus:border-gray-400 transition-colors">
                </div>

                {{-- Distrito --}}
                <div>
                    <label class="block text-sm font-normal text-gray-700 mb-2">Distrito</label>
                    <input type="text"
                        x-model="distrito"
                        placeholder="Ingresar distrito"
                        class="w-full border border-gray-200 rounded-full px-4 py-2.5 text-sm text-gray-700 placeholder-gray-400 bg-white focus:outline-none focus:border-gray-400 transition-colors">
                </div>

            </div>

            {{-- Mensaje dinámico según tipo de entrega --}}
            <div class="mt-6">

                {{-- Envío a provincia --}}
                <div x-show="tipoEntrega == 2" class="space-y-3">
                    <p class="text-sm text-gray-800">
                        Su pedido llegará dentro de 2 a 5 días hábiles.
                    </p>

                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <p class="text-sm text-gray-700 leading-relaxed">
                            Una vez recibido, podrá ver en <span class="font-semibold">"Mis pedidos"</span>
                            la ubicación exacta donde deberá recoger su pedido.
                        </p>
                    </div>
                </div>

                {{-- Retiro en tienda --}}
                <div x-show="tipoEntrega != 2">
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <p class="text-sm text-gray-700 leading-relaxed">
                            Puede acercarse en cualquier momento a recoger su pedido a la tienda ubicada en
                            <span class="font-semibold">"Jr. Cajamarca N° 396 - Huancayo"</span>.
                        </p>
                    </div>
                </div>

            </div>
        </div>

        {{-- Botón pagar mobile --}}
        <div class="lg:hidden">
            <button @click="intentarPagar"
                class="w-full bg-gray-900 text-white rounded-full py-4 text-sm font-semibold tracking-wide hover:bg-gray-800 transition-all">
                <span x-show="!procesando">
                    Continuar con la compra
                </span>
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

            <div class="space-y-3 text-sm text-gray-800">
                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span>S/ {{ number_format($total, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Envío</span>
                    <span class="text-gray-500 italic text-xs">A calcular</span>
                </div>
            </div>

            <div class="mt-5 pt-5 flex justify-between items-center">
                <span class="font-bold text-gray-800 text-base">Total</span>
                <span class="font-bold text-gray-800 text-lg">
                    S/ {{ number_format($total, 2) }}
                </span>
            </div>

            {{-- Botón pagar desktop --}}
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

    {{-- Formulario oculto para enviar el pedido --}}
    <form id="form-pedido" method="POST" action="{{ route('checkout.confirmar') }}">
        @csrf
        <input type="hidden" name="id_tipo_entrega"    x-bind:value="tipoEntrega">
        <input type="hidden" name="id_departamento"    x-bind:value="departamento">
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
    function checkoutData() {
        return {
            tipoEntrega:         {{ $tiposEntrega->first()->id_tipo_entrega ?? 1 }},
            departamento:        '',
            provincia:           '',
            distrito:            '',
            totalProductos:      {{ $total }},
            procesando:          false,

            // ── Datos personales precargados desde el usuario autenticado
            idTipoDocumento:  '{{ auth()->user()->id_tipo_documento ?? '' }}',
            numeroDocumento:  '{{ auth()->user()->numero_documento ?? '' }}',
            telefono:         '{{ auth()->user()->telefono ?? '' }}',

            // ── Resetear envío si cambia a recojo en tienda
            calcularEnvioPorTipo() {
                if (this.tipoEntrega != 2) {
                    this.departamento = '';
                    this.provincia    = '';
                    this.distrito     = '';
                }
            },

            // ── Enviar pedido
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
                if (this.tipoEntrega == 2 && !this.distrito) {
                    alert('Ingresa un distrito de envío.');
                    return;
                }
                this.procesando = true;
                document.getElementById('form-pedido').submit();
            },
        }
    }
</script>
@endpush