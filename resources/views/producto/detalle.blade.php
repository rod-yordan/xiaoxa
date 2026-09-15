@extends('layouts.app')

@section('title', $producto->nombre_producto . ' - Xiaoxa')

@section('content')

<main class="max-w-7xl mx-auto px-4 sm:px-8 py-8">
    <div class="flex flex-col lg:flex-row gap-10 items-start">

        {{-- ===== GALERÍA ===== --}}
        <div class="w-full lg:w-[58%] space-y-3">

            {{-- Imagen principal --}}
            <div class="aspect-[4/5] bg-white overflow-hidden rounded-2xl border border-gray-100 shadow-sm group relative">
                <img id="view-principal"
                    src="{{ url('/api/imagen/' . $producto->imagen) }}"
                    class="w-full h-full object-contain p-6 transition-all duration-500 group-hover:scale-105"
                    alt="{{ $producto->nombre_producto }}">

                @if($producto->precio_oferta)
                <div class="absolute top-4 left-4">
                    <span class="bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-full">
                        -{{ round((($producto->precio - $producto->precio_oferta) / $producto->precio) * 100) }}% OFF
                    </span>
                </div>
                @endif
            </div>

            {{-- Miniaturas --}}
            <div class="flex gap-2.5 overflow-x-auto pb-1 scroll-gallery">
                <button type="button"
                        onclick="cambiarImagen(this, '{{ url('/api/imagen/' . $producto->imagen) }}')"
                        class="thumbnail-btn thumbnail-active w-[72px] h-[82px] flex-shrink-0 rounded-xl border-2 overflow-hidden bg-white p-1">
                    <img src="{{ url('/api/imagen/' . $producto->imagen) }}" class="w-full h-full object-cover rounded-lg">
                </button>

                @if($producto->galeria)
                    @foreach($producto->galeria as $foto)
                    <button type="button"
                            onclick="cambiarImagen(this, '{{ url('/api/imagen/' . $foto) }}')"
                            class="thumbnail-btn w-[72px] h-[82px] flex-shrink-0 rounded-xl border border-gray-200 overflow-hidden bg-white p-1 hover:border-gray-400 transition-all">
                        <img src="{{ url('/api/imagen/' . $foto) }}" class="w-full h-full object-cover rounded-lg">
                    </button>
                    @endforeach
                @endif
            </div>
        </div>


        {{-- ===== INFO PRODUCTO ===== --}}
        <div class="w-full lg:w-[42%] lg:sticky lg:top-28 space-y-0">

            {{-- Marca + Nombre --}}
            <div class="pb-5 border-b border-gray-100">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-[0.2em] mb-2">{{ $producto->marca }}</p>
                <h1 class="text-3xl font-semibold leading-snug text-gray-900">
                    {{ ucwords(strtolower($producto->nombre_producto)) }}
                </h1>
                <p id="sku-text" class="text-xs text-gray-400 mt-3 tracking-wider">SKU: Selecciona color y talla</p>
            </div>

            {{-- Precio --}}
            <div class="py-5 border-b border-gray-100">
                @if($producto->precio_oferta)
                    <div class="flex items-center gap-3 flex-wrap">
                        <span class="text-3xl font-bold text-red-500">
                            S/ {{ number_format($producto->precio_oferta, 2) }}
                        </span>
                        <span class="text-lg text-gray-400 line-through font-medium">
                            S/ {{ number_format($producto->precio, 2) }}
                        </span>
                        <span class="bg-red-50 text-red-600 text-xs font-bold px-2.5 py-1 rounded-full border border-red-100">
                            -{{ round((1 - $producto->precio_oferta / $producto->precio) * 100) }}%
                        </span>
                    </div>
                @else
                    <span class="text-3xl font-bold text-gray-900">
                        S/ {{ number_format($producto->precio, 2) }}
                    </span>
                @endif
            </div>

            @php
                $variantesPorColor = $producto->variantes->groupBy('color');
                $colores = $variantesPorColor->keys()->filter();
                $tallas  = $producto->variantes->pluck('talla')->unique();
            @endphp

            {{-- Selector de Color --}}
            <div class="py-5 border-b border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">Color</p>
                    <span id="color-seleccionado" class="text-xs text-gray-400 italic">Selecciona uno</span>
                </div>

                <div class="flex flex-wrap gap-2">
                    @foreach($colores as $color)
                    <button type="button"
                            onclick="seleccionarColor(this, '{{ $color }}')"
                            class="color-btn px-4 py-2 border border-gray-200 rounded-xl text-sm font-medium hover:border-gray-900 transition-all bg-white"
                            data-color="{{ $color }}">
                        {{ $color }}
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Selector de Talla --}}
            <div class="py-5 border-b border-gray-100">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest mb-3">Talla</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($tallas as $talla)
                    <button type="button"
                            onclick="selectTalla(this)"
                            class="talla-btn w-12 h-12 border border-gray-200 rounded-xl text-sm font-semibold hover:border-gray-900 transition-all bg-white"
                            data-talla="{{ $talla }}">
                        {{ $talla }}
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Formulario + Botón --}}
            <div class="pt-5">
                <form id="form-carrito" action="{{ route('carrito.add', $producto->id_producto) }}" method="POST">
                    @csrf
                    <input type="hidden" name="color"       id="input-color">
                    <input type="hidden" name="talla"       id="input-talla">
                    <input type="hidden" name="id_variante" id="input-variante">

                    {{-- Error --}}
                    <div id="error-msg" class="hidden bg-red-50 border border-red-100 text-red-600 text-sm rounded-xl px-4 py-3 mb-4 flex items-center gap-2">
                        <x-heroicon-o-exclamation-circle class="w-4 h-4 shrink-0" />
                        <span>Debes seleccionar color y talla antes de añadir.</span>
                    </div>

                    @if(session('success'))
                    <button type="button"
                            class="w-full bg-emerald-600 text-white rounded-2xl py-4 text-sm font-semibold tracking-wide flex items-center justify-center gap-2">
                        <x-heroicon-o-check-circle class="w-5 h-5" />
                        ¡Añadido a tu bolsa!
                    </button>
                    @else
                    <button type="submit"
                            class="w-full bg-gray-900 text-white rounded-2xl py-4 text-sm font-semibold tracking-wide
                                   hover:bg-gray-800 active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                        <x-heroicon-o-shopping-bag class="w-4 h-4" />
                        Añadir a la bolsa
                    </button>
                    @endif
                </form>
            </div>

            {{-- Descripción --}}
            <div class="mt-5 bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm">
                <details class="group">
                    <summary class="flex items-center justify-between px-5 py-4 cursor-pointer list-none select-none">
                        <span class="text-xs font-semibold uppercase tracking-widest text-gray-700">Descripción del producto</span>
                        <x-heroicon-o-chevron-down class="w-4 h-4 text-gray-400 transition-transform group-open:rotate-180" />
                    </summary>
                    <div class="px-5 pb-5 border-t border-gray-100">
                        <p class="mt-4 text-sm text-gray-500 leading-relaxed whitespace-pre-line">{{ $producto->descripcion }}</p>
                    </div>
                </details>
            </div>

        </div>
    </div>
</main>

@endsection

@push('styles')
<style>
    #view-principal { transition: opacity 0.25s ease-in-out; }

    .thumbnail-btn { transition: all 0.2s ease; }
    .thumbnail-active { border-color: #111 !important; border-width: 2px !important; }

    /* Scrollbar galería */
    .scroll-gallery::-webkit-scrollbar { height: 3px; }
    .scroll-gallery::-webkit-scrollbar-track { background: #f1f1f1; }
    .scroll-gallery::-webkit-scrollbar-thumb { background: #ccc; border-radius: 99px; }
</style>
@endpush

@push('scripts')
<script>
    const variantes = @json($producto->variantes);
    let colorSeleccionado = "";
    let tallaSeleccionada = "";

    function cambiarImagen(elemento, ruta) {
        const mainImg = document.getElementById('view-principal');
        mainImg.style.opacity = '0';
        setTimeout(() => {
            mainImg.src = ruta;
            mainImg.style.opacity = '1';
        }, 250);
        document.querySelectorAll('.thumbnail-btn').forEach(btn => {
            btn.classList.remove('thumbnail-active', 'border-2', 'border-gray-900');
            btn.classList.add('border', 'border-gray-200');
        });
        elemento.classList.add('thumbnail-active');
    }

    function seleccionarColor(elemento, nombre) {
        colorSeleccionado = nombre;
        document.getElementById('color-seleccionado').innerText = nombre;
        document.getElementById('input-color').value = nombre;

        document.querySelectorAll('.color-btn').forEach(btn => {
            btn.classList.remove('border-gray-900', 'bg-gray-900', 'text-white', 'ring-2', 'ring-gray-900');
            btn.classList.add('border-gray-200', 'bg-white', 'text-gray-900');
        });
        elemento.classList.remove('border-gray-200', 'bg-white', 'text-gray-900');
        elemento.classList.add('border-gray-900', 'bg-gray-900', 'text-white');

        bloquearTallasPorColor();
        actualizarVarianteID();
    }

    function selectTalla(elemento) {
        if (elemento.disabled) return;
        tallaSeleccionada = elemento.dataset.talla;
        document.getElementById('input-talla').value = tallaSeleccionada;

        document.querySelectorAll('.talla-btn').forEach(btn => {
            btn.classList.remove('border-gray-900', 'bg-gray-900', 'text-white');
            btn.classList.add('border-gray-200', 'bg-white', 'text-gray-900');
        });
        elemento.classList.remove('border-gray-200', 'bg-white', 'text-gray-900');
        elemento.classList.add('border-gray-900', 'bg-gray-900', 'text-white');

        actualizarVarianteID();
    }

    function actualizarVarianteID() {
        if (colorSeleccionado && tallaSeleccionada) {
            const variante = variantes.find(v =>
                v.color.trim().toLowerCase() === colorSeleccionado.trim().toLowerCase() &&
                v.talla.trim().toLowerCase() === tallaSeleccionada.trim().toLowerCase()
            );
            if (variante) {
                document.getElementById('input-variante').value = variante.id_variante;
                document.getElementById('sku-text').innerText = 'SKU: ' + variante.sku;
            }
        }
    }

    function bloquearTallasPorColor() {
        document.querySelectorAll('.talla-btn').forEach(btn => {
            const talla = btn.dataset.talla;
            const disponible = variantes.find(v =>
                v.color?.trim().toLowerCase() === colorSeleccionado.trim().toLowerCase() &&
                v.talla?.trim().toLowerCase() === talla.trim().toLowerCase() &&
                v.stock > 0
            );
            if (!disponible) {
                btn.disabled = true;
                btn.classList.add('opacity-35', 'cursor-not-allowed', 'line-through');
                btn.classList.remove('hover:border-gray-900');
            } else {
                btn.disabled = false;
                btn.classList.remove('opacity-35', 'cursor-not-allowed', 'line-through');
            }
        });
    }

    document.getElementById('form-carrito').addEventListener('submit', function(e) {
        const color = document.getElementById('input-color').value;
        const talla = document.getElementById('input-talla').value;
        const errorEl = document.getElementById('error-msg');
        if (!color || !talla) {
            e.preventDefault();
            errorEl.classList.remove('hidden');
            errorEl.classList.add('flex');
        } else {
            errorEl.classList.add('hidden');
            errorEl.classList.remove('flex');
        }
    });
</script>
@endpush