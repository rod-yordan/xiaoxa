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

<div class="max-w-7xl mx-auto px-4 sm:px-8 py-8" x-data="carritoData()" x-cloak>

    @if(count($items) > 0)

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">

        {{-- ===== COLUMNA IZQUIERDA: LISTA DE PRODUCTOS ===== --}}
        <div class="lg:col-span-8 min-w-0">

            <div class="hidden sm:grid grid-cols-12 gap-4 pb-4 mb-2 border-b border-gray-200 text-sm text-gray-800">
                <div class="col-span-7">Producto</div>
                <div class="col-span-2 text-center">Precio</div>
                <div class="col-span-1 text-center">Cantidad</div>
                <div class="col-span-2 text-center">Subtotal</div>
            </div>

            <div class="divide-y divide-gray-100">
                @foreach($items as $id => $detalles)
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center py-6">

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

                    <div class="col-span-1 flex flex-col items-center gap-1">
                        <span class="sm:hidden text-xs text-gray-400 mb-1">Cantidad:</span>

                        <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden">
                            <a href="{{ route('carrito.disminuir', $id) }}"
                               class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition">
                                <x-heroicon-o-minus class="w-3.5 h-3.5" />
                            </a>

                            <span class="w-8 text-center text-sm font-semibold text-gray-800">
                                {{ $detalles['cantidad'] }}
                            </span>

                            <a href="{{ route('carrito.aumentar', $id) }}"
                               class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition
                                      {{ ($detalles['cantidad'] ?? 0) >= ($detalles['stock'] ?? 0) ? 'pointer-events-none opacity-40' : '' }}">
                                <x-heroicon-o-plus class="w-3.5 h-3.5" />
                            </a>
                        </div>
                    </div>

                    <div class="col-span-2 text-center">
                        <span class="sm:hidden text-xs text-gray-400 mr-1">Subtotal:</span>
                        <span class="text-sm text-gray-800">
                            S/ {{ number_format($detalles['precio'] * $detalles['cantidad'], 2) }}
                        </span>
                    </div>

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
    <div class="flex flex-col items-center justify-center text-center py-24">
        <x-heroicon-o-shopping-bag class="w-16 h-16 mx-auto mb-4 text-gray-200" />
        <h3 class="text-lg font-bold text-gray-800 mb-2">Tu bolsa está vacía</h3>
        <p class="text-sm text-gray-500 max-w-xs mx-auto">
            Aún no has agregado ningún producto. Descubre nuestra colección y encuentra algo que te guste.
        </p>
    </div>

    @endif

</div>

@endsection

@push('scripts')
<script>
    function carritoData() {
        return {}
    }
</script>
@endpush