@extends('admin.layout')

@section('content')
<div x-data="variantesForm({{ json_encode(old('variantes', [['talla' => '', 'color' => '', 'stock' => '', 'sku' => '']])) }})">

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

    {{-- Errores con estilo --}}
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

        {{-- COLUMNA IZQUIERDA: DATOS --}}
        <div class="lg:col-span-2 space-y-8">

            {{-- Card de Información General --}}
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
                        <input name="nombre_producto" value="{{ old('nombre_producto') }}" placeholder="Ej: Zapatillas Urban X"
                               class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 transition-all outline-none font-bold" required>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Marca</label>
                        <input name="marca" value="{{ old('marca') }}" placeholder="Ej: Nike"
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

                <div class="space-y-2">
                    <label class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Descripción</label>
                    <textarea name="descripcion" rows="4" placeholder="Detalles del producto..."
                              class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 transition-all outline-none font-medium">{{ old('descripcion') }}</textarea>
                </div>
            </div>

            {{-- Card de Variantes --}}
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-amber-50 rounded-lg text-amber-600">
                            <x-heroicon-o-swatch class="w-5 h-5" />
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Tallas y Colores</h2>
                    </div>
                    <button type="button" @click="addVariante" class="flex items-center gap-2 text-sm font-black text-indigo-600 hover:text-indigo-700 transition">
                        <x-heroicon-o-plus-circle class="w-5 h-5" />
                        Añadir Variante
                    </button>
                </div>

                <div class="space-y-4">
                    <template x-for="(variante, index) in variantes" :key="index">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 p-5 bg-gray-50 rounded-3xl relative group">
                            <input type="text" :name="`variantes[${index}][talla]`" x-model="variante.talla" placeholder="Talla" class="bg-white px-4 py-3 rounded-xl border-none font-bold text-sm shadow-sm" required>
                            <input type="text" :name="`variantes[${index}][color]`" x-model="variante.color" placeholder="Color" class="bg-white px-4 py-3 rounded-xl border-none font-bold text-sm shadow-sm">
                            <input type="number" :name="`variantes[${index}][stock]`" x-model="variante.stock" placeholder="Stock" class="bg-white px-4 py-3 rounded-xl border-none font-bold text-sm shadow-sm" min="0" required>
                            <input type="text" :name="`variantes[${index}][sku]`" x-model="variante.sku" placeholder="SKU" class="bg-white px-4 py-3 rounded-xl border-none font-bold text-sm shadow-sm">

                            <div class="flex items-center justify-center">
                                <button type="button" @click="removeVariante(index)" x-show="variantes.length > 1"
                                        class="p-2 text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition">
                                    <x-heroicon-o-trash class="w-5 h-5" />
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- COLUMNA DERECHA: IMÁGENES --}}
        <div class="space-y-8">
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm space-y-6">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                        <x-heroicon-o-photo class="w-5 h-5" />
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Multimedia</h2>
                </div>

                {{-- Imagen Principal --}}
                <div class="space-y-4">
                    <label class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Imagen Principal</label>
                    <div class="relative group cursor-pointer">
                        <input type="file" name="imagen" accept="image/*" x-on:change="previewImage"
                               class="absolute inset-0 w-full h-full opacity-0 z-10 cursor-pointer" required>
                        <div class="w-full h-64 bg-gray-50 border-4 border-dashed border-gray-100 rounded-[2rem] flex flex-col items-center justify-center overflow-hidden transition-all group-hover:border-indigo-200 group-hover:bg-indigo-50/30">
                            <template x-if="!imagePreview">
                                <div class="text-center">
                                    <x-heroicon-o-arrow-up-tray class="w-10 h-10 text-gray-300 mx-auto mb-2" />
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-tighter">Subir imagen</p>
                                </div>
                            </template>
                            <template x-if="imagePreview">
                                <img :src="imagePreview" class="w-full h-full object-cover">
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Galería --}}
                <div class="space-y-4 pt-4">
                    <label class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Galería de fotos</label>
                    <input type="file" name="galeria[]" multiple accept="image/*"
                           class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 transition">
                </div>
            </div>

            {{-- Botones de Acción --}}
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
        function variantesForm(initialVariantes) {
            return {
                variantes: initialVariantes,
                imagePreview: null,
                addVariante() {
                    this.variantes.push({ talla: '', color: '', stock: '', sku: '' })
                },
                removeVariante(index) {
                    this.variantes.splice(index, 1)
                },
                previewImage(e) {
                    const file = e.target.files[0]
                    if (!file) return
                    this.imagePreview = URL.createObjectURL(file)
                }
            }
        }
    </script>
</div>
@endsection