{{-- resources/views/carrito/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Mi bolsa - Xiaoxa')

@section('content')

{{-- ===== BREADCRUMB ===== --}}
<div class="max-w-7xl mx-auto px-4 sm:px-8 pt-6 pb-2">
    <div class="flex items-center justify-center gap-2 flex-wrap">
        <a href="{{ route('carrito.index') }}" class="flex items-center gap-1">
            <span class="text-[20px] font-bold text-gray-800">Mi bolsa</span>
        </a>
        <x-heroicon-o-chevron-right class="w-5 h-5 text-gray-400" />
        <span class="text-[20px] font-normal text-gray-800">Datos de compra</span>
        <x-heroicon-o-chevron-right class="w-5 h-5 text-gray-400" />
        <span class="text-[20px] font-normal text-gray-800">Compra finalizada</span>
    </div>
</div>

<main class="max-w-7xl mx-auto px-4 sm:px-8 py-8" x-data="carritoData()" x-cloak>

    @if(count($items) > 0)

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">

        {{-- ===== COLUMNA IZQUIERDA: LISTA DE PRODUCTOS ===== --}}
        <div class="lg:col-span-8 min-w-0">

            {{-- Cabecera de columnas (solo desktop) --}}
            <div class="hidden sm:grid grid-cols-12 gap-4 pb-4 mb-2 border-b border-gray-200 text-sm text-gray-800">
                <div class="col-span-7">Producto</div>
                <div class="col-span-2 text-center">Precio</div>
                <div class="col-span-1 text-center">Cantidad</div>
                <div class="col-span-2 text-center">Subtotal</div>
            </div>

            {{-- Items --}}
            <div class="divide-y divide-gray-100">
                @foreach($items as $id => $detalles)
                <div class="relative grid grid-cols-1 sm:grid-cols-12 gap-4 items-center py-6 pb-12">

                    {{-- PRODUCTO: toda la columna es un enlace al detalle --}}
                    <a href="{{ route('producto.show', $detalles['id_producto'] ?? '') }}"
                       class="col-span-7 flex items-center gap-4 min-w-0">
                        <div class="relative shrink-0">
                            @if(!empty($detalles['imagen']))
                                <img src="{{ url('/api/imagen/' . $detalles['imagen']) }}"
                                     alt="{{ $detalles['nombre'] }}"
                                     class="w-24 h-32 object-cover bg-gray-100">
                            @else
                                <div class="w-24 h-32 bg-gray-100 flex items-center justify-center">
                                    <x-heroicon-o-photo class="w-7 h-7 text-gray-300" />
                                </div>
                            @endif
                        </div>

                        <div class="flex flex-col justify-center min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="text-sm text-gray-800 leading-tight">
                                    {{ ucwords(strtolower($detalles['nombre'])) }}
                                </h3>

                                @if(!empty($detalles['precio_oferta']))
                                    <span class="bg-red-500 text-white text-[11px] font-semibold leading-none min-w-[1px] text-center px-1 py-1 rounded-md shrink-0">
                                        -{{ abs($detalles['descuento'] ?? 50) }}%
                                    </span>
                                @endif
                            </div>

                            <p class="text-xs text-gray-800 mt-1">
                                Talla: {{ $detalles['talla'] ?? '—' }}
                            </p>
                            <p class="text-xs text-gray-800">
                                Color: {{ $detalles['color'] ?? '—' }}
                            </p>
                        </div>
                    </a>

                    {{-- PRECIO UNITARIO --}}
                    <div class="col-span-2 text-center">
                        <span class="sm:hidden text-xs text-gray-400 mr-1">Precio:</span>

                        @if(!empty($detalles['precio_oferta']))
                            <div class="flex flex-col items-center leading-tight">
                                <span class="text-sm text-gray-800">S/ {{ number_format($detalles['precio'], 2) }}</span>
                                <span class="text-xs text-gray-400 line-through">S/ {{ number_format($detalles['precio_normal'], 2) }}</span>
                            </div>
                        @else
                            <span class="text-sm text-gray-800">S/ {{ number_format($detalles['precio'], 2) }}</span>
                        @endif
                    </div>

                    {{-- CANTIDAD --}}
                    <div class="col-span-1 text-center">
                        <span class="sm:hidden text-xs text-gray-400 mr-1">Cantidad:</span>
                        <span class="text-sm text-gray-800">{{ $detalles['cantidad'] }}</span>
                    </div>

                    {{-- SUBTOTAL --}}
                    <div class="col-span-2 text-center">
                        <span class="sm:hidden text-xs text-gray-400 mr-1">Subtotal:</span>
                        <span class="text-sm text-gray-800">
                            S/ {{ number_format($detalles['precio'] * $detalles['cantidad'], 2) }}
                        </span>
                    </div>

                    {{-- BOTÓN ELIMINAR (abajo a la derecha, rojo fijo) --}}
                    <a href="{{ route('carrito.eliminar', $id) }}"
                       class="absolute bottom-3 right-0 text-xs text-rose-600 font-medium">
                        Eliminar
                    </a>

                </div>
                @endforeach
            </div>

        </div>

        {{-- ===== COLUMNA DERECHA: RESUMEN DEL PEDIDO ===== --}}
        <div class="lg:col-span-4">
            <div class="bg-[#f1f1f1] rounded-2xl p-7 lg:sticky lg:top-24">

                <h2 class="font-bold text-lg mb-6 text-gray-800">Resumen del pedido</h2>

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

                <a href="{{ route('checkout.index') }}"
                   class="mt-7 flex items-center justify-center w-full bg-gray-900 text-white rounded-full py-4 text-xs font-bold tracking-widest hover:bg-gray-800 active:scale-[0.99] transition-all uppercase">
                    Continuar con la compra
                </a>

            </div>
        </div>

    </div>

    @else

    {{-- ===== CARRITO VACÍO ===== --}}
    <div class="bg-gray-50 rounded-2xl border border-gray-100 py-24 flex flex-col items-center text-center">
        <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mb-5 border border-gray-200 shadow-sm">
            <x-heroicon-o-shopping-bag class="w-7 h-7 text-gray-300" />
        </div>
        <h2 class="font-bold text-xl mb-2 text-gray-800">Tu bolsa está vacía</h2>
        <p class="text-sm text-gray-400 mb-8 max-w-xs">
            Aún no has agregado ningún producto. Descubre nuestra colección y encuentra algo que te guste.
        </p>
        <a href="{{ route('home') }}"
           class="inline-flex items-center gap-2 bg-gray-900 text-white rounded-full px-8 py-3 text-sm font-semibold hover:bg-gray-800 transition-all">
            <x-heroicon-o-arrow-left class="w-4 h-4" />
            Volver a la tienda
        </a>
    </div>

    @endif

</main>

@endsection

@push('scripts')
<script>
    function carritoData() {
        return {}
    }
</script>
@endpush