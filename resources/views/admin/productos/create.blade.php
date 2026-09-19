@extends('admin.layout')

@section('content')
<div x-data="productoForm({{ json_encode(old('variantes', [['talla' => '', 'color' => '', 'color_hex' => '#000000', 'stock' => '', 'sku' => '']])) }}, {{ json_encode(old('detalles', [''])) }})">

    <div class="flex items-center gap-4 mb-12">
        <a href="{{ route('admin.productos.index') }}" class="p-3 bg-white border border-gray-100 rounded-2xl text-gray-400 hover:text-indigo-600 transition-all shadow-sm">
            <x-heroicon-o-arrow-left class="w-6 h-6" />
        </a>
        <div>
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Nuevo Producto</h1>
            <p class="text-gray-500 font-medium">Completa la información para el catálogo.</p>
        </div>
    </div>

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

    <form action="{{ route('admin.productos.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        @csrf

        {{-- COLUMNA 1: PRODUCTO --}}
        <div class="space-y-8">

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
                        <input name="precio" type="text" inputmode="decimal" value="{{ old('precio') }}" placeholder="0.00"
                               oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
                               class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 transition-all outline-none font-bold" required>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Precio Oferta</label>
                        <input name="precio_oferta" type="text" inputmode="decimal" value="{{ old('precio_oferta') }}" placeholder="0.00"
                               oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
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

            {{-- Detalles --}}
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

            {{-- Botones --}}
            <div class="flex flex-col gap-4">
                <button type="submit" class="w-full py-5 bg-indigo-600 text-white font-black rounded-3xl shadow-xl shadow-indigo-100 hover:bg-indigo-700 hover:-translate-y-1 transition-all active:scale-95">
                    Guardar Producto
                </button>
                <a href="{{ route('admin.productos.index') }}" class="w-full py-5 bg-white text-gray-400 font-bold rounded-3xl text-center border border-gray-100 hover:bg-gray-50 transition">
                    Cancelar
                </a>
            </div>
        </div>

        {{-- COLUMNA 2: VARIANTES --}}
        <div class="space-y-8">
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

                <div class="space-y-6">
                    <template x-for="(variante, index) in variantes" :key="index">
                        <div class="p-5 bg-gray-50 rounded-3xl space-y-4">

                            {{-- Fila 1: Talla, Color, HEX, Stock, SKU --}}
                            <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                                <input type="text" :name="`variantes[${index}][talla]`" x-model="variante.talla" placeholder="Talla"
                                       class="bg-white px-4 py-3 rounded-xl border-none font-bold text-sm shadow-sm" required>

                                <input type="text" :name="`variantes[${index}][color]`" x-model="variante.color" placeholder="Color"
                                       class="bg-white px-4 py-3 rounded-xl border-none font-bold text-sm shadow-sm">

                                <div class="flex items-center gap-2 bg-white px-2 py-2 rounded-xl shadow-sm">
                                    <input type="color" :name="`variantes[${index}][color_hex]`" x-model="variante.color_hex"
                                           class="w-5 h-6 rounded-md border-0 cursor-pointer p-0 flex-shrink-0"
                                           style="background: transparent;">
                                    <input type="text"
                                           x-model="variante.color_hex"
                                           @input="variante.color_hex = variante.color_hex.toUpperCase()"
                                           maxlength="7"
                                           placeholder="#000000"
                                           class="w-full min-w-0 bg-transparent border-none outline-none font-mono font-bold text-[11px] text-gray-600 uppercase">
                                </div>

                                <input type="text" inputmode="numeric" :name="`variantes[${index}][stock]`" x-model="variante.stock" placeholder="Stock"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                       class="bg-white px-4 py-3 rounded-xl border-none font-bold text-sm shadow-sm" required>

                                <input type="text" :name="`variantes[${index}][sku]`" x-model="variante.sku" placeholder="SKU"
                                       class="bg-white px-4 py-3 rounded-xl border-none font-bold text-sm shadow-sm">
                            </div>

                            {{-- Fila 2: Miniaturas + botón cuadrado --}}
                            <div class="flex items-start gap-3">
                                <div class="flex-1">
                                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">

                                        <template x-for="(file, imgIndex) in variante.files" :key="imgIndex">
                                            <div class="relative group aspect-square">
                                                <img :src="variante.previewUrls[imgIndex]"
                                                     @click="pickColorFromImage($event, index, imgIndex)"
                                                     class="w-full h-full object-cover rounded-xl cursor-crosshair border-2 border-transparent hover:border-indigo-400 transition"
                                                     title="Clic para tomar color">
                                                <button type="button"
                                                        @click="removeImagen(index, imgIndex)"
                                                        class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-rose-500 text-white rounded-full text-[10px] font-bold opacity-0 group-hover:opacity-100 transition shadow-lg">
                                                    ✕
                                                </button>
                                            </div>
                                        </template>

                                        <label class="aspect-square flex flex-col items-center justify-center bg-white border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/30 transition-all">
                                            <x-heroicon-o-cloud-arrow-up class="w-5 h-5 text-gray-300" />
                                            <span class="text-[9px] font-bold text-gray-400 mt-1">Añadir imagen</span>

                                            <input type="file"
                                                   :name="`variantes[${index}][imagenes][]`"
                                                   accept="image/*"
                                                   multiple
                                                   class="hidden"
                                                   @change="handleImagenes($event, index)">
                                        </label>
                                    </div>
                                </div>

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
    </form>

    <script>
        function productoForm(initialVariantes, initialDetalles) {
            return {
                variantes: (initialVariantes || []).map(v => ({
                    talla: v.talla ?? '',
                    color: v.color ?? '',
                    color_hex: (v.color_hex ?? '#000000').toUpperCase(),
                    stock: v.stock ?? '',
                    sku: v.sku ?? '',
                    files: [],
                    previewUrls: []
                })),
                detalles: (initialDetalles && initialDetalles.length > 0) ? initialDetalles : [''],

                addVariante() {
                    this.variantes.push({
                        talla: '', color: '', color_hex: '#000000', stock: '', sku: '',
                        files: [], previewUrls: []
                    })
                },
                removeVariante(index) {
                    (this.variantes[index].previewUrls || []).forEach(u => URL.revokeObjectURL(u))
                    this.variantes.splice(index, 1)
                },
                addDetalle() {
                    this.detalles.push('')
                },
                removeDetalle(index) {
                    if (this.detalles.length > 1) this.detalles.splice(index, 1)
                    else this.detalles[0] = ''
                },

                handleImagenes(event, index) {
                    const nuevos = Array.from(event.target.files)
                    if (nuevos.length === 0) return

                    const variante = this.variantes[index]

                    variante.files = [...variante.files, ...nuevos]

                    variante.previewUrls.forEach(u => URL.revokeObjectURL(u))
                    variante.previewUrls = variante.files.map(f => URL.createObjectURL(f))

                    this.syncInputFiles(index, event.target)
                },

                removeImagen(index, imgIndex) {
                    const variante = this.variantes[index]

                    URL.revokeObjectURL(variante.previewUrls[imgIndex])

                    variante.files.splice(imgIndex, 1)
                    variante.previewUrls.splice(imgIndex, 1)

                    const input = this.$el.querySelector(`input[name="variantes[${index}][imagenes][]"]`)
                    if (input) this.syncInputFiles(index, input)
                },

                syncInputFiles(index, input) {
                    const dt = new DataTransfer()
                    this.variantes[index].files.forEach(f => dt.items.add(f))
                    input.files = dt.files
                },

                pickColorFromImage(event, index, imgIndex) {
                    const img = event.target
                    const rect = img.getBoundingClientRect()

                    const x = Math.floor((event.clientX - rect.left) * (img.naturalWidth / rect.width))
                    const y = Math.floor((event.clientY - rect.top) * (img.naturalHeight / rect.height))

                    const canvas = document.createElement('canvas')
                    canvas.width = img.naturalWidth
                    canvas.height = img.naturalHeight
                    const ctx = canvas.getContext('2d')
                    ctx.drawImage(img, 0, 0)

                    try {
                        const pixel = ctx.getImageData(x, y, 1, 1).data
                        const hex = '#' + [pixel[0], pixel[1], pixel[2]]
                            .map(c => c.toString(16).padStart(2, '0')).join('')
                            .toUpperCase()

                        this.variantes[index].color_hex = hex
                    } catch (e) {
                        console.warn('No se pudo leer el píxel (posible CORS):', e)
                    }
                }
            }
        }
    </script>
</div>
@endsection