@extends('admin.layout')

@section('content')
    <div x-data="editProductoForm(@js(old('variantes') ?? $producto->variantes ?? []))">

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

        {{-- Errores de Validación --}}
        @if ($errors->any())
            <div class="mb-8 p-6 bg-rose-50 border-l-4 border-rose-500 rounded-2xl flex gap-4 items-start animate-pulse">
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

                {{-- Card de Información General --}}
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

                {{-- Card de Variantes --}}
                <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-amber-50 rounded-lg text-amber-600">
                                <x-heroicon-o-swatch class="w-5 h-5" />
                            </div>
                            <h2 class="text-xl font-bold text-gray-800">Variantes de Stock</h2>
                        </div>
                        <button type="button" @click="addVariante"
                            class="flex items-center gap-2 text-xs font-black text-indigo-600 hover:underline">
                            <x-heroicon-o-plus-circle class="w-5 h-5" />
                            Añadir Variante
                        </button>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase text-gray-400 ml-1">Estado</label>
                        <select name="estado_producto"
                            class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">

                            <option value="1" {{ $producto->estado_producto ? 'selected' : '' }}>
                                Activo
                            </option>

                            <option value="0" {{ !$producto->estado_producto ? 'selected' : '' }}>
                                Inactivo
                            </option>
                        </select>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(variante, index) in variantes" :key="variante.uid">
                            <div class="relative grid grid-cols-1 md:grid-cols-5 gap-3 p-5 rounded-3xl transition-all border-2"
                                :class="isDuplicated(index) ? 'bg-rose-50 border-rose-200' : 'bg-gray-50 border-transparent'">

                                <input type="hidden" :name="`variantes[${index}][id_variante]`"
                                    x-model="variante.id_variante">

                                <div class="space-y-1">
                                    <label class="text-[10px] font-black uppercase text-gray-400 ml-1">Talla</label>
                                    <input type="text" :name="`variantes[${index}][talla]`" x-model="variante.talla"
                                        required
                                        class="w-full px-4 py-3 rounded-xl border-2 font-bold text-sm outline-none transition-all"
                                        :class="isDuplicated(index) ? 'border-rose-300 text-rose-600' : 'border-transparent focus:border-indigo-500 bg-white'">
                                </div>

                                <div class="space-y-1">
                                    <label class="text-[10px] font-black uppercase text-gray-400 ml-1">Color</label>
                                    <input type="text" :name="`variantes[${index}][color]`" x-model="variante.color"
                                        class="w-full px-4 py-3 rounded-xl border-2 font-bold text-sm outline-none transition-all"
                                        :class="isDuplicated(index) ? 'border-rose-300 text-rose-600' : 'border-transparent focus:border-indigo-500 bg-white'">
                                </div>

                                <div class="space-y-1">
                                    <label class="text-[10px] font-black uppercase text-gray-400 ml-1">Stock</label>
                                    <input type="number" :name="`variantes[${index}][stock]`" x-model="variante.stock"
                                        required
                                        class="w-full bg-white px-4 py-3 rounded-xl border-none font-bold text-sm shadow-sm"
                                        min="0">
                                </div>

                                <div class="space-y-1">
                                    <label class="text-[10px] font-black uppercase text-gray-400 ml-1">SKU</label>
                                    <input type="text" :name="`variantes[${index}][sku]`" x-model="variante.sku" required
                                        class="w-full px-4 py-3 rounded-xl border-2 font-bold text-[10px] shadow-sm outline-none"
                                        :class="isDuplicated(index) ? 'border-rose-500 bg-rose-100 text-rose-700' : 'border-transparent focus:border-indigo-500 bg-white'">
                                </div>

                                <div class="flex items-center justify-center pt-5">
                                    <button type="button" @click="removeVariante(index)"
                                        class="p-3 text-rose-400 hover:text-rose-600 hover:bg-rose-100 rounded-xl transition-all">
                                        <x-heroicon-o-trash class="w-5 h-5" />
                                    </button>
                                </div>

                                <template x-if="isDuplicated(index)">
                                    <div
                                        class="col-span-full flex items-center gap-1 text-[10px] font-black text-rose-600 uppercase mt-1 ml-1">
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
                {{-- Imagen Principal --}}
                <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm space-y-4">
                    <label class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Imagen Principal</label>
                    <div
                        class="relative group aspect-square rounded-3xl overflow-hidden bg-gray-100 border-2 border-dashed border-gray-200 hover:border-indigo-500 transition-all">

                        <template x-if="imgPrincipalPreview">
                            <img :src="imgPrincipalPreview" class="w-full h-full object-cover">
                        </template>

                        <template x-if="!imgPrincipalPreview">
                            @if($producto->imagen)
                                <img src="{{ url('/api/imagen/' . $producto->imagen) }}"
                                    class="w-full h-full object-cover group-hover:opacity-50 transition-all">
                            @endif
                        </template>

                        <div
                            class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all bg-black/20">
                            <x-heroicon-o-camera class="w-10 h-10 text-white" />
                        </div>

                        <input type="file" name="imagen" @change="previewPrincipal"
                            class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*">
                    </div>
                    <p class="text-[9px] text-gray-400 text-center font-bold uppercase">Click para cambiar imagen</p>
                </div>

                {{-- Galería de Imágenes --}}
                <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm space-y-6">
                    <label class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Galería</label>

                    <div class="grid grid-cols-3 gap-2">

                        {{-- Fotos actuales en la DB --}}
                        @forelse($producto->galeria ?? [] as $img)
                            <div class="relative aspect-square rounded-xl overflow-hidden group border border-gray-50">
                                <img src="{{ url('/api/imagen/' . $img) }}" class="w-full h-full object-cover">
                                <label
                                    class="absolute inset-0 bg-rose-500/80 opacity-0 group-hover:opacity-100 transition-all cursor-pointer flex flex-col items-center justify-center text-white text-center">
                                    <input type="checkbox" name="galeria_eliminar[]" value="{{ $img }}" class="hidden peer">
                                    <x-heroicon-o-trash class="w-5 h-5 mb-1" />
                                    <span class="text-[7px] font-black uppercase peer-checked:hidden">Eliminar</span>
                                    <span class="hidden peer-checked:block text-[7px] font-black uppercase">¡Marcado!</span>
                                </label>
                            </div>
                        @empty
                            <div class="col-span-3 py-6 border-2 border-dashed border-gray-200 rounded-2xl text-center">
                                <span class="text-[9px] font-bold text-gray-300 uppercase">Sin fotos previas</span>
                            </div>
                        @endforelse

                        {{-- Previa Nuevas Fotos --}}
                        <template x-for="url in galeriaPreviews">
                            <div class="relative aspect-square rounded-xl overflow-hidden border-2 border-indigo-500">
                                <img :src="url" class="w-full h-full object-cover">
                                <div class="absolute top-1 right-1">
                                    <span class="bg-indigo-600 text-white text-[7px] px-1 rounded font-bold">NUEVA</span>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Input para añadir más --}}
                    <div
                        class="relative w-full py-4 border-2 border-dashed border-gray-200 rounded-2xl hover:bg-indigo-50 hover:border-indigo-300 transition-all text-center">
                        <x-heroicon-o-plus class="w-6 h-6 text-gray-400 mx-auto mb-1" />
                        <span class="text-[10px] font-black text-gray-400 uppercase">Añadir nuevas fotos</span>
                        <input type="file" name="galeria[]" multiple @change="previewGaleria"
                            class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*">
                    </div>
                </div>

                {{-- Botones de Accion --}}
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
            function editProductoForm(initialVariantes = []) {
                return {
                    variantes: [],
                    imgPrincipalPreview: null,
                    galeriaPreviews: [],

                    init() {
                        if (Array.isArray(initialVariantes) && initialVariantes.length > 0) {
                            this.variantes = initialVariantes.map(v => ({
                                uid: crypto.randomUUID(),
                                id_variante: v.id_variante ?? null,
                                talla: v.talla ?? '',
                                color: v.color ?? '',
                                stock: v.stock ?? 0,
                                sku: v.sku ?? ''
                            }));
                        } else {
                            this.addVariante();
                        }
                    },

                    // funcion para ver la imagen principal antes de subirla

                    previewPrincipal(event) {
                        const file = event.target.files[0];
                        if (file) this.imgPrincipalPreview = URL.createObjectURL(file);
                    },


                    // función para ver las fotos nuevas de la galeria

                    previewGaleria(event) {
                        const files = event.target.files;
                        this.galeriaPreviews = [];
                        Array.from(files).forEach(file => {
                            this.galeriaPreviews.push(URL.createObjectURL(file));
                        });
                    },
                    addVariante() {
                        this.variantes.push({ uid: crypto.randomUUID(), id_variante: null, talla: '', color: '', stock: 0, sku: '' });
                    },
                    removeVariante(index) {
                        if (this.variantes.length > 1) this.variantes.splice(index, 1);
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