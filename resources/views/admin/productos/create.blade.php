@extends('admin.layout')

@section('content')
<div x-data="productoForm({{ json_encode(old('variantes', [['talla' => '', 'color' => '', 'color_hex' => '#000000', 'stock' => '', 'sku' => '']])) }}, {{ json_encode(old('detalles', [''])) }})">

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-12">
        <a href="{{ route('admin.productos.index') }}" class="p-3 bg-white border border-gray-100 rounded-2xl text-gray-400 hover:text-indigo-600 transition-all shadow-sm">
            <x-heroicon-o-arrow-left class="w-6 h-6" />
        </a>
        <div>
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Nuevo Producto</h1>
            <p class="text-gray-500 font-medium">Completa la información para el catálogo.</p>
        </div>
    </div>

    {{-- Errores --}}
    @if ($errors->any())
        <div class="mb-8 p-6 bg-rose-50 border-l-4 border-rose-500 rounded-2xl flex gap-4 items-start">
            <x-heroicon-s-x-circle class="w-6 h-6 text-rose-500 flex-shrink-0" />
            <div>
                <h3 class="font-bold text-rose-800">Hay errores en el formulario:</h3>
                <ul class="text-rose-600 text-sm mt-1 list-disc pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.productos.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        @csrf

        {{-- COLUMNA IZQUIERDA --}}
        <div class="lg:col-span-2 space-y-8">

            {{-- Información General --}}
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm space-y-6">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                        <x-heroicon-o-document-text class="w-5 h-5" />
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Información General</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Nombre del producto</label>
                        <input name="nombre_producto" value="{{ old('nombre_producto') }}" placeholder="Ej: Blusa Skinny Denim"
                               class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 transition-all outline-none font-bold" required>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Marca</label>
                        <input name="marca" value="{{ old('marca') }}" placeholder="Ej: Zara"
                               class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 transition-all outline-none font-bold">
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Precio Principal (S/)</label>
                        <input name="precio" type="number" step="0.01" value="{{ old('precio') }}" placeholder="0.00"
                               class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 transition-all outline-none font-bold" required>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Precio Oferta (Opcional)</label>
                        <input name="precio_oferta" type="number" step="0.01" value="{{ old('precio_oferta') }}" placeholder="0.00"
                               class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 transition-all outline-none font-bold">
                    </div>

                    <div class="space-y-2 md:col-span-2">
                        <label class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Categoría</label>
                        <select name="id_categoria" class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 transition-all outline-none font-bold appearance-none">
                            @foreach($categorias as $c)
                                <option value="{{ $c->id_categoria }}" {{ old('id_categoria') == $c->id_categoria ? 'selected' : '' }}>{{ $c->nombre_categoria }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Detalles (viñetas dinámicas) --}}
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                            <x-heroicon-o-list-bullet class="w-5 h-5" />
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Detalles</h2>
                    </div>
                    <button type="button" @click="addDetalle"
                            class="flex items-center gap-2 text-sm font-black text-indigo-600 hover:text-indigo-700 transition">
                        <x-heroicon-o-plus-circle class="w-5 h-5" />
                        Añadir Detalle
                    </button>
                </div>

                <p class="text-xs text-gray-400 mb-4">Cada detalle aparecerá como una viñeta con punto en la ficha del producto.</p>

                <div class="space-y-3">
                    <template x-for="(detalle, index) in detalles" :key="index">
                        <div class="flex items-center gap-3 bg-gray-50 rounded-2xl p-3">
                            <span class="w-2 h-2 rounded-full bg-gray-900 flex-shrink-0"></span>
                            <input type="text"
                                   :name="`detalles[${index}]`"
                                   x-model="detalles[index]"
                                   placeholder="Ej: Cuello redondo ideal para un look casual"
                                   class="flex-1 px-4 py-3 bg-white border-2 border-transparent rounded-xl focus:border-indigo-500 outline-none font-medium text-sm transition-all">
                            <button type="button" @click="removeDetalle(index)"
                                    x-show="detalles.length > 1"
                                    class="p-2 text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition flex-shrink-0">
                                <x-heroicon-o-trash class="w-5 h-5" />
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Variantes con imágenes --}}
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-amber-50 rounded-lg text-amber-600">
                            <x-heroicon-o-swatch class="w-5 h-5" />
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Tallas, Colores e Imágenes</h2>
                    </div>
                    <button type="button" @click="addVariante"
                            class="flex items-center gap-2 text-sm font-black text-indigo-600 hover:text-indigo-700 transition">
                        <x-heroicon-o-plus-circle class="w-5 h-5" />
                        Añadir Variante
                    </button>
                </div>

                {{-- 👇 CAMBIO: se eliminó el aviso "Cada variante debe tener al menos 1 imagen" --}}
                <p class="text-xs text-gray-400 bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 mb-6">
                    💡 Las imágenes de cada variante son <strong>opcionales</strong>. Puedes agregarlas ahora o después.
                </p>

                <div class="space-y-6">
                    <template x-for="(variante, index) in variantes" :key="index">
                        <div class="p-5 bg-gray-50 rounded-3xl space-y-4">

                            {{-- Fila 1: Talla, Color, HEX, Stock, SKU --}}
                            <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                                <input type="text" :name="`variantes[${index}][talla]`" x-model="variante.talla" placeholder="Talla"
                                       class="bg-white px-4 py-3 rounded-xl border-none font-bold text-sm shadow-sm" required>

                                <input type="text" :name="`variantes[${index}][color]`" x-model="variante.color" placeholder="Nombre color"
                                       class="bg-white px-4 py-3 rounded-xl border-none font-bold text-sm shadow-sm">

                                <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-xl shadow-sm">
                                    <input type="color" :name="`variantes[${index}][color_hex]`" x-model="variante.color_hex"
                                           class="w-9 h-9 rounded-lg border-0 cursor-pointer p-0"
                                           style="background: transparent;">
                                    <span class="text-[10px] font-mono font-bold text-gray-500" x-text="variante.color_hex"></span>
                                </div>

                                <input type="number" :name="`variantes[${index}][stock]`" x-model="variante.stock" placeholder="Stock"
                                       class="bg-white px-4 py-3 rounded-xl border-none font-bold text-sm shadow-sm" min="0" required>

                                <input type="text" :name="`variantes[${index}][sku]`" x-model="variante.sku" placeholder="SKU"
                                       class="bg-white px-4 py-3 rounded-xl border-none font-bold text-sm shadow-sm">
                            </div>

                            {{-- Fila 2: Imágenes (ahora OPCIONAL) --}}
                            <div class="flex items-start gap-3">
                                <label class="flex-1 flex flex-col items-center justify-center py-4 bg-white border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/30 transition-all">
                                    <x-heroicon-o-cloud-arrow-up class="w-7 h-7 text-gray-300 mb-1" />

                                    {{-- 👇 CAMBIO: texto "mínimo 1" → "opcional" --}}
                                    <span class="text-xs font-bold text-gray-500"
                                          x-text="variante.imagenesNombres && variante.imagenesNombres.length > 0
                                                    ? variante.imagenesNombres.join(', ')
                                                    : 'Seleccionar imágenes (opcional)'"></span>
                                    <span class="text-[10px] text-gray-400 mt-0.5">JPG, PNG, WEBP — Max 4MB c/u</span>

                                    {{-- 👇 CAMBIO: se eliminó el atributo 'required' --}}
                                    <input type="file"
                                           :name="`variantes[${index}][imagenes][]`"
                                           accept="image/*"
                                           multiple
                                           class="hidden"
                                           @change="variante.imagenesNombres = Array.from($event.target.files).map(f => f.name)">
                                </label>

                                <button type="button" @click="removeVariante(index)" x-show="variantes.length > 1"
                                        class="p-4 text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-2xl transition flex-shrink-0">
                                    <x-heroicon-o-trash class="w-5 h-5" />
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- COLUMNA DERECHA: INFORMACIÓN + BOTONES --}}
        <div class="space-y-8">
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm space-y-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-50 rounded-lg text-emerald-600">
                        <x-heroicon-o-information-circle class="w-5 h-5" />
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Instrucciones</h2>
                </div>
                <ul class="text-sm text-gray-500 space-y-2 leading-relaxed">
                    <li>• Ingresa la información general del producto.</li>
                    <li>• Añade los <strong>detalles</strong> (cada uno será una viñeta).</li>
                    <li>• Agrega al menos una <strong>variante</strong> (talla/color).</li>
                    {{-- 👇 CAMBIO: se eliminó la línea "Cada variante debe tener al menos 1 imagen" --}}
                    <li>• Las <strong>imágenes</strong> por variante son opcionales.</li>
                    <li>• Las imágenes de la primera variante se mostrarán en el catálogo.</li>
                </ul>
            </div>

            <div class="flex flex-col gap-4">
                <button type="submit" class="w-full py-5 bg-indigo-600 text-white font-black rounded-3xl shadow-xl shadow-indigo-100 hover:bg-indigo-700 hover:-translate-y-1 transition-all active:scale-95">
                    Guardar Producto
                </button>
                <a href="{{ route('admin.productos.index') }}" class="w-full py-5 bg-white text-gray-400 font-bold rounded-3xl text-center border border-gray-100 hover:bg-gray-50 transition">
                    Cancelar
                </a>
            </div>
        </div>
    </form>

    <script>
        function productoForm(initialVariantes, initialDetalles) {
            return {
                variantes: (initialVariantes || []).map(v => ({
                    talla: v.talla ?? '',
                    color: v.color ?? '',
                    color_hex: v.color_hex ?? '#000000',
                    stock: v.stock ?? '',
                    sku: v.sku ?? '',
                    imagenesNombres: []
                })),
                detalles: (initialDetalles && initialDetalles.length > 0) ? initialDetalles : [''],
                addVariante() {
                    this.variantes.push({ talla: '', color: '', color_hex: '#000000', stock: '', sku: '', imagenesNombres: [] })
                },
                removeVariante(index) {
                    this.variantes.splice(index, 1)
                },
                addDetalle() {
                    this.detalles.push('')
                },
                removeDetalle(index) {
                    if (this.detalles.length > 1) this.detalles.splice(index, 1)
                    else this.detalles[0] = ''
                }
            }
        }
    </script>
</div>
@endsection