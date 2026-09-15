@extends('admin.layout')

@section('content')
    <div x-data="editProductoForm(
        @js(old('variantes') ?? $producto->variantes ?? []),
        @js(old('detalles') ?? $producto->detalles ?? [''])
    )">

        {{-- Header --}}
        <div class="flex items-center gap-4 mb-12">
            <a href="{{ route('admin.productos.index') }}"
                class="p-3 bg-white border border-gray-100 rounded-2xl text-gray-400 hover:text-indigo-600 transition-all shadow-sm">
                <x-heroicon-o-arrow-left class="w-6 h-6" />
            </a>
            <div>
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Editar Producto</h1>
            </div>
        </div>

        {{-- Errores --}}
        @if ($errors->any())
            <div class="mb-8 p-6 bg-rose-50 border-l-4 border-rose-500 rounded-2xl flex gap-4 items-start">
                <x-heroicon-s-x-circle class="w-6 h-6 text-rose-500 flex-shrink-0" />
                <div>
                    <h3 class="font-bold text-rose-800 text-sm">Hay errores que debes corregir:</h3>
                    <ul class="text-rose-600 text-xs mt-1 list-disc pl-4 font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.productos.update', $producto->id_producto) }}" method="POST"
            enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            @csrf
            @method('PUT')

            {{-- COLUMNA IZQUIERDA --}}
            <div class="lg:col-span-2 space-y-8">

                {{-- Información General --}}
                <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                            <x-heroicon-o-pencil-square class="w-5 h-5" />
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Información del Producto</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase text-gray-400 ml-1">Nombre</label>
                            <input name="nombre_producto" value="{{ old('nombre_producto', $producto->nombre_producto) }}"
                                required
                                class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase text-gray-400 ml-1">Marca</label>
                            <input name="marca" value="{{ old('marca', $producto->marca) }}"
                                class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase text-gray-400 ml-1">Precio (S/)</label>
                            <input name="precio" type="number" step="0.01" value="{{ old('precio', $producto->precio) }}"
                                required
                                class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl font-bold text-sm text-indigo-600 focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase text-gray-400 ml-1">Precio Oferta</label>
                            <input name="precio_oferta" type="number" step="0.01"
                                value="{{ old('precio_oferta', $producto->precio_oferta) }}"
                                class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl font-bold text-sm text-rose-500 focus:ring-2 focus:ring-indigo-500 outline-none">
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

                    <div class="space-y-1 mb-6">
                        <label class="text-[10px] font-black uppercase text-gray-400 ml-1">Estado del producto</label>
                        <select name="estado_producto"
                            class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                            <option value="1" {{ $producto->estado_producto ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ !$producto->estado_producto ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>

                    {{-- 👇 CAMBIO: se eliminó el aviso "Cada variante debe tener al menos 1 imagen" --}}
                    <p class="text-xs text-gray-500 bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 mb-6">
                        💡 Las imágenes por variante son <strong>opcionales</strong>. Puedes dejar una variante sin imágenes.
                    </p>

                    <div class="space-y-6">
                        <template x-for="(variante, index) in variantes" :key="variante.uid">
                            <div class="p-5 rounded-3xl transition-all border-2 space-y-4"
                                 :class="isDuplicated(index) ? 'bg-rose-50 border-rose-200' : 'bg-gray-50 border-transparent'">

                                <input type="hidden" :name="`variantes[${index}][id_variante]`" x-model="variante.id_variante">

                                {{-- Fila 1: Talla, Color, HEX, Stock, SKU --}}
                                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-black uppercase text-gray-400 ml-1">Talla</label>
                                        <input type="text" :name="`variantes[${index}][talla]`" x-model="variante.talla" required
                                            class="w-full px-4 py-3 rounded-xl border-2 font-bold text-sm outline-none transition-all bg-white border-transparent focus:border-indigo-500">
                                    </div>

                                    <div class="space-y-1">
                                        <label class="text-[10px] font-black uppercase text-gray-400 ml-1">Color</label>
                                        <input type="text" :name="`variantes[${index}][color]`" x-model="variante.color"
                                            class="w-full px-4 py-3 rounded-xl border-2 font-bold text-sm outline-none transition-all bg-white border-transparent focus:border-indigo-500">
                                    </div>

                                    <div class="space-y-1">
                                        <label class="text-[10px] font-black uppercase text-gray-400 ml-1">HEX</label>
                                        <div class="flex items-center gap-2 bg-white px-2 py-2 rounded-xl shadow-sm">
                                            <input type="color" :name="`variantes[${index}][color_hex]`" x-model="variante.color_hex"
                                                   class="w-8 h-8 rounded-lg border-0 cursor-pointer p-0"
                                                   style="background: transparent;">
                                            <span class="text-[10px] font-mono font-bold text-gray-500" x-text="variante.color_hex"></span>
                                        </div>
                                    </div>

                                    <div class="space-y-1">
                                        <label class="text-[10px] font-black uppercase text-gray-400 ml-1">Stock</label>
                                        <input type="number" :name="`variantes[${index}][stock]`" x-model="variante.stock" required
                                            class="w-full bg-white px-4 py-3 rounded-xl border-none font-bold text-sm shadow-sm" min="0">
                                    </div>

                                    <div class="space-y-1">
                                        <label class="text-[10px] font-black uppercase text-gray-400 ml-1">SKU</label>
                                        <input type="text" :name="`variantes[${index}][sku]`" x-model="variante.sku" required
                                            class="w-full px-4 py-3 rounded-xl border-2 font-bold text-[10px] shadow-sm outline-none bg-white border-transparent focus:border-indigo-500">
                                    </div>
                                </div>

                                {{-- Fila 2: Imágenes --}}
                                <div class="space-y-3">
                                    <label class="text-[10px] font-black uppercase text-gray-400 ml-1">Imágenes de la variante</label>

                                    {{-- Imágenes existentes --}}
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="(img, imgIndex) in variante.imagenes_existentes" :key="img.id_imagen">
                                            <div class="relative w-20 h-20 rounded-xl overflow-hidden group border border-gray-200">
                                                <img :src="'/api/imagen/' + img.imagen" class="w-full h-full object-cover">

                                                <label class="absolute inset-0 bg-rose-500/80 opacity-0 group-hover:opacity-100 transition-all cursor-pointer flex flex-col items-center justify-center text-white text-center">
                                                    <input type="checkbox"
                                                           :name="`variantes[${index}][imagenes_eliminar][]`"
                                                           :value="img.id_imagen"
                                                           class="hidden peer">
                                                    <x-heroicon-o-trash class="w-5 h-5 mb-0.5" />
                                                    <span class="text-[7px] font-black uppercase peer-checked:hidden">Eliminar</span>
                                                    <span class="hidden peer-checked:block text-[7px] font-black uppercase">¡Marcado!</span>
                                                </label>
                                            </div>
                                        </template>
                                    </div>

                                    {{-- Input para agregar nuevas imágenes (OPCIONAL) --}}
                                    <label class="flex items-center justify-center gap-3 py-4 bg-white border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/30 transition-all">
                                        <x-heroicon-o-cloud-arrow-up class="w-6 h-6 text-gray-300" />

                                        {{-- 👇 CAMBIO: texto "Añadir más imágenes" → "Añadir imágenes (opcional)" --}}
                                        <span class="text-xs font-bold text-gray-500"
                                              x-text="variante.imagenesNuevasNombres && variante.imagenesNuevasNombres.length > 0
                                                        ? variante.imagenesNuevasNombres.join(', ')
                                                        : 'Añadir imágenes (opcional)'"></span>

                                        <input type="file"
                                               :name="`variantes[${index}][imagenes][]`"
                                               accept="image/*"
                                               multiple
                                               class="hidden"
                                               @change="variante.imagenesNuevasNombres = Array.from($event.target.files).map(f => f.name)">
                                    </label>
                                </div>

                                {{-- Botón eliminar variante --}}
                                <div class="flex justify-end">
                                    <button type="button" @click="removeVariante(index)"
                                        x-show="variantes.length > 1"
                                        class="flex items-center gap-2 text-xs font-bold text-rose-500 hover:text-rose-600 hover:bg-rose-50 px-3 py-2 rounded-xl transition">
                                        <x-heroicon-o-trash class="w-4 h-4" />
                                        Eliminar variante
                                    </button>
                                </div>

                                <template x-if="isDuplicated(index)">
                                    <div class="flex items-center gap-1 text-[10px] font-black text-rose-600 uppercase ml-1">
                                        <x-heroicon-s-exclamation-triangle class="w-4 h-4" />
                                        <span>Talla y Color repetidos</span>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- COLUMNA DERECHA --}}
            <div class="space-y-8">
                <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-emerald-50 rounded-lg text-emerald-600">
                            <x-heroicon-o-information-circle class="w-5 h-5" />
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Instrucciones</h2>
                    </div>
                    <ul class="text-sm text-gray-500 space-y-2 leading-relaxed">
                        <li>• Cada <strong>detalle</strong> será una viñeta en la ficha.</li>
                        {{-- 👇 CAMBIO: se eliminó "Cada variante debe tener al menos 1 imagen" --}}
                        <li>• Las <strong>imágenes</strong> por variante son opcionales.</li>
                        <li>• Puedes eliminar imágenes marcando el checkbox.</li>
                        <li>• Al agregar nuevas imágenes, se suman a las existentes.</li>
                    </ul>
                </div>

                <div class="flex flex-col gap-4">
                    <button type="submit" :disabled="hasErrors()"
                        :class="hasErrors() ? 'bg-gray-300 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700'"
                        class="w-full py-5 text-white font-black rounded-3xl shadow-xl transition-all active:scale-95">
                        <span x-text="hasErrors() ? 'Corrige los errores' : 'Guardar Cambios'"></span>
                    </button>
                    <a href="{{ route('admin.productos.index') }}"
                        class="w-full py-5 bg-white text-gray-400 font-bold rounded-3xl text-center border border-gray-100 hover:bg-gray-50 transition-all">
                        Descartar Cambios
                    </a>
                </div>
            </div>
        </form>

        <script>
            function editProductoForm(initialVariantes = [], initialDetalles = ['']) {
                return {
                    variantes: [],
                    detalles: (initialDetalles && initialDetalles.length > 0) ? initialDetalles : [''],

                    init() {
                        if (Array.isArray(initialVariantes) && initialVariantes.length > 0) {
                            this.variantes = initialVariantes.map(v => ({
                                uid: crypto.randomUUID(),
                                id_variante: v.id_variante ?? null,
                                talla: v.talla ?? '',
                                color: v.color ?? '',
                                color_hex: v.color_hex ?? '#000000',
                                stock: v.stock ?? 0,
                                sku: v.sku ?? '',
                                imagenes_existentes: v.imagenes ?? [],
                                imagenesNuevasNombres: []
                            }));
                        } else {
                            this.addVariante();
                        }
                    },

                    addVariante() {
                        this.variantes.push({
                            uid: crypto.randomUUID(),
                            id_variante: null,
                            talla: '',
                            color: '',
                            color_hex: '#000000',
                            stock: 0,
                            sku: '',
                            imagenes_existentes: [],
                            imagenesNuevasNombres: []
                        });
                    },

                    removeVariante(index) {
                        if (this.variantes.length > 1) this.variantes.splice(index, 1);
                    },

                    addDetalle() {
                        this.detalles.push('');
                    },
                    removeDetalle(index) {
                        if (this.detalles.length > 1) this.detalles.splice(index, 1);
                        else this.detalles[0] = '';
                    },

                    isDuplicated(index) {
                        const current = this.variantes[index];
                        if (!current.talla.trim()) return false;
                        return this.variantes.some((v, i) => i !== index &&
                            v.talla.toLowerCase().trim() === current.talla.toLowerCase().trim() &&
                            v.color.toLowerCase().trim() === current.color.toLowerCase().trim());
                    },
                    hasErrors() {
                        return this.variantes.some((_, i) => this.isDuplicated(i));
                    }
                }
            }
        </script>
    </div>
@endsection