<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $producto->nombre_producto }} – C'Lucky</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['DM Sans', 'sans-serif'],
                        display: ['Playfair Display', 'serif'],
                    },
                    colors: {
                        gold: '#C9A84C',
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        #view-principal { transition: opacity 0.25s ease-in-out; }

        .thumbnail-btn { transition: all 0.2s ease; }
        .thumbnail-active { border-color: #111 !important; border-width: 2px !important; }

        /* Scrollbar galería */
        .scroll-gallery::-webkit-scrollbar { height: 3px; }
        .scroll-gallery::-webkit-scrollbar-track { background: #f1f1f1; }
        .scroll-gallery::-webkit-scrollbar-thumb { background: #ccc; border-radius: 99px; }
    </style>
</head>

<body class="bg-stone-50 font-sans text-gray-900 antialiased">


{{-- NAVBAR  --}}
<nav class="border-b sticky top-0 bg-white z-50">
    <div class="max-w-full mx-auto px-4 sm:px-8">
        <div class="flex justify-between h-20 items-center">
            <div class="flex-shrink-0 flex items-center">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Logo C'Lucky"
                        class="h-14 w-auto transition-transform hover:scale-105">
                </a>
            </div>

            <div class="flex items-center space-x-5">
                <button class="hover:scale-110 transition text-gray-600 hover:text-gray-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
                <a href="{{ route('login') }}" class="hover:scale-110 transition text-gray-600 hover:text-gray-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </a>
                <a href="{{ route('carrito.index') }}" class="hover:scale-110 transition relative text-gray-600 hover:text-gray-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 11-8 0m-4 8h16l-1.244 6.323A2 2 0 0114.805 23H9.195a2 2 0 01-1.951-1.677L6 11z"/>
                    </svg>
                    <span class="absolute -top-1 -right-1 bg-gray-900 text-white text-[10px] rounded-full h-4 w-4 flex items-center justify-center font-bold">
                        {{ session('carrito') ? count(session('carrito')) : 0 }}
                    </span>
                </a>
            </div>
        </div>
    </div>
</nav>


{{--CONTENIDO PRINCIPAL--}}
<main class="max-w-7xl mx-auto px-4 sm:px-8 py-8">
    <div class="flex flex-col lg:flex-row gap-10 items-start">


        {{-- GALERÍA  --}}
        <div class="w-full lg:w-[58%] space-y-3">

            {{-- Imagen principal --}}
            <div class="aspect-[4/5] bg-white overflow-hidden rounded-2xl border border-gray-100 shadow-sm group relative">
                <img id="view-principal"
                    src="{{ url('/api/imagen/' . $producto->imagen) }}"
                    class="w-full h-full object-contain p-6 transition-all duration-500 group-hover:scale-105"
                    alt="{{ $producto->nombre_producto }}">

                @if($producto->precio_oferta)
                <div class="absolute top-4 left-4">
                    <span class="bg-gray-900 text-white text-xs font-bold px-3 py-1.5 rounded-full">
                        -{{ round((($producto->precio - $producto->precio_oferta) / $producto->precio) * 100) }}% OFF
                    </span>
                </div>
                @endif
            </div>

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


        {{-- INFO PRODUCTO  --}}
        <div class="w-full lg:w-[42%] lg:sticky lg:top-28 space-y-0">

            {{-- Marca + Nombre --}}
            <div class="pb-5 border-b border-gray-100">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-[0.2em] mb-2">{{ $producto->marca }}</p>
                <h1 class="font-display text-3xl font-semibold leading-snug text-gray-900">
                    {{ $producto->nombre_producto }}
                </h1>
                <p id="sku-text" class="text-xs text-gray-400 mt-3 tracking-wider">SKU: Selecciona color y talla</p>
            </div>

            {{-- Precio --}}
            <div class="py-5 border-b border-gray-100">
                @if($producto->precio_oferta)
                    <div class="flex items-center gap-3 flex-wrap">
                        <span class="text-3xl font-display font-bold text-gray-900">
                            S/ {{ number_format($producto->precio_oferta, 2) }}
                        </span>
                        <span class="text-lg text-gray-400 line-through font-medium">
                            S/ {{ number_format($producto->precio, 2) }}
                        </span>
                        <span class="bg-emerald-50 text-emerald-700 text-xs font-bold px-2.5 py-1 rounded-full border border-emerald-100">
                            Ahorra S/ {{ number_format($producto->precio - $producto->precio_oferta, 2) }}
                        </span>
                    </div>
                @else
                    <span class="text-3xl font-display font-bold text-gray-900">
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
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">Talla</p>
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
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Debes seleccionar color y talla antes de añadir.</span>
                    </div>

                    @if(session('success'))
                    <button type="button"
                            class="w-full bg-emerald-600 text-white rounded-2xl py-4 text-sm font-semibold tracking-wide flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        ¡Añadido a tu bolsa!
                    </button>
                    @else
                    <button type="submit"
                            class="w-full bg-gray-900 text-white rounded-2xl py-4 text-sm font-semibold tracking-wide
                                   hover:bg-gray-800 active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 11-8 0m-4 8h16l-1.244 6.323A2 2 0 0114.805 23H9.195a2 2 0 01-1.951-1.677L6 11z"/>
                        </svg>
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
                        <svg class="w-4 h-4 text-gray-400 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </summary>
                    <div class="px-5 pb-5 border-t border-gray-100">
                        <p class="mt-4 text-sm text-gray-500 leading-relaxed">{{ $producto->descripcion }}</p>
                    </div>
                </details>
            </div>

        </div>
    </div>
</main>


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

</body>
</html>
