@extends('admin.layout')

@section('content')
<div x-data="{ createModal: {{ $errors->any() ? 'true' : 'false' }} }">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
        <div>
            <p class="text-xs font-black uppercase tracking-widest text-indigo-500 mb-2">Marketing</p>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">Banners</h1>
            <p class="text-gray-500 mt-2 text-base sm:text-lg font-medium">Gestiona los banners del carrusel principal.</p>
        </div>
        <button @click="createModal = true"
            class="inline-flex items-center justify-center gap-3 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3.5 sm:px-7 sm:py-4 rounded-2xl font-bold shadow-xl shadow-indigo-200 transition-all hover:-translate-y-1 active:scale-95 w-full sm:w-auto">
            <x-heroicon-o-plus class="w-5 h-5" />
            Nuevo Banner
        </button>
    </div>

    <hr class="border-gray-100 mb-8">

    {{-- GRID DE BANNERS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($banners as $banner)
            <div x-data="{ confirmDelete: false }"
                class="group relative bg-white border border-gray-100 rounded-[2rem] overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500">

                {{-- Imagen --}}
                <div class="relative h-44 sm:h-52 bg-gray-100 overflow-hidden">

                    <img src="{{ url('/api/imagen/' . $banner->imagen) }}"
                         alt="{{ $banner->titulo }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                         onerror="this.src='https://placehold.co/800x400/e5e7eb/9ca3af?text=Sin+imagen'">

                    {{-- Badge Orden --}}
                    <div class="absolute top-3 left-3 flex gap-2 flex-wrap">
                        <span class="bg-black/60 backdrop-blur-sm text-white text-xs font-black px-3 py-1.5 rounded-full">
                            # {{ $banner->orden }}
                        </span>
                    </div>

                    {{-- Estado --}}
                    <div class="absolute top-3 right-3">
                        <span class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-full text-xs font-black uppercase tracking-wider
                            {{ $banner->estado ? 'bg-emerald-500/90 text-white' : 'bg-gray-400/80 text-white' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $banner->estado ? 'bg-white' : 'bg-gray-200' }}"></span>
                            {{ $banner->estado ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                </div>

                {{-- Contenido --}}
                <div class="p-5 sm:p-6">
                    <h3 class="text-lg sm:text-xl font-black text-gray-900 leading-tight">{{ $banner->titulo }}</h3>

                    @if($banner->url_boton)
                    <div class="mt-3">
                        <span class="inline-flex items-center gap-2 text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-xl truncate max-w-full">
                            <x-heroicon-o-link class="w-3.5 h-3.5 flex-shrink-0" />
                            <span class="truncate">{{ $banner->url_boton }}</span>
                        </span>
                    </div>
                    @endif

                    {{-- Acciones --}}
                    <div class="pt-4 mt-4 border-t border-gray-50 flex flex-wrap items-center justify-between gap-3">
                        <button @click="$dispatch('open-edit-banner', {{ $banner->toJson() }})"
                            class="font-bold text-gray-600 hover:text-indigo-600 flex items-center gap-2 transition-colors text-sm">
                            <x-heroicon-o-pencil-square class="w-4 h-4" />
                            Editar
                        </button>
                        <div class="flex items-center gap-4">
                            <form action="{{ route('admin.banners.toggle', $banner->id_banner) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="font-bold text-sm transition-colors {{ $banner->estado ? 'text-amber-500 hover:text-amber-600' : 'text-emerald-500 hover:text-emerald-600' }}">
                                    {{ $banner->estado ? 'Desactivar' : 'Activar' }}
                                </button>
                            </form>
                            <button @click="confirmDelete = true"
                                class="font-bold text-sm text-rose-500 hover:text-rose-600 transition-colors">
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Modal Confirmar Eliminar --}}
                <template x-if="confirmDelete">
                    <div class="fixed inset-0 z-[110] flex items-center justify-center p-4">
                        <div @click="confirmDelete = false" class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm"></div>
                        <div class="relative bg-white rounded-[2rem] p-6 sm:p-8 max-w-sm w-full shadow-2xl text-center mx-4">
                            <div class="w-16 h-16 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-4">
                                <x-heroicon-o-trash class="w-8 h-8" />
                            </div>
                            <h3 class="text-xl font-black text-gray-900 mb-2">¿Eliminar banner?</h3>
                            <p class="text-gray-500 font-medium mb-6 text-sm">
                                Estás a punto de eliminar <span class="font-bold text-gray-800">"{{ $banner->titulo }}"</span>.
                                Esta acción no se puede deshacer.
                            </p>
                            <form action="{{ route('admin.banners.destroy', $banner->id_banner) }}" method="POST" class="flex gap-3">
                                @csrf @method('DELETE')
                                <button type="button" @click="confirmDelete = false"
                                    class="flex-1 py-3 bg-gray-100 text-gray-500 font-bold rounded-xl text-sm">Cancelar</button>
                                <button type="submit"
                                    class="flex-1 py-3 bg-rose-500 text-white font-bold rounded-xl shadow-lg text-sm">Eliminar</button>
                            </form>
                        </div>
                    </div>
                </template>
            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-gray-50 rounded-[2.5rem] border-2 border-dashed border-gray-200">
                <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <x-heroicon-o-photo class="w-8 h-8 text-gray-300" />
                </div>
                <p class="text-gray-400 font-bold">No hay banners registrados.</p>
                <p class="text-gray-300 font-medium mt-1 text-sm">Crea tu primer banner para el carrusel.</p>
            </div>
        @endforelse
    </div>

    {{-- ===================== MODAL CREAR BANNER ===================== --}}
    <template x-if="createModal">
        <div class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center">
            <div @click="createModal = false" class="absolute inset-0 bg-gray-900/60 backdrop-blur-xl"></div>
            <div class="relative bg-white rounded-t-[2.5rem] sm:rounded-[2.5rem] p-6 sm:p-10 w-full sm:max-w-2xl shadow-2xl max-h-[92vh] overflow-y-auto">

                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-black text-gray-900">Nuevo Banner</h2>
                        <p class="text-gray-400 mt-1 font-medium italic text-sm">Configura el banner para el carrusel.</p>
                    </div>
                    <button @click="createModal = false"
                        class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-50 text-gray-400 hover:bg-rose-50 hover:text-rose-500 transition ml-4 flex-shrink-0">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>

                <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Título --}}
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">
                                Título * <span class="font-medium normal-case text-gray-300">(solo uso interno)</span>
                            </label>
                            <input type="text" name="titulo" required value="{{ old('titulo') }}" placeholder="Ej: Banner Verano 2026"
                                class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none font-bold transition-all text-sm sm:text-base">
                        </div>

                        {{-- URL del Botón --}}
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">
                                URL del Banner <span class="font-medium normal-case text-gray-300">(a dónde redirige al hacer clic)</span>
                            </label>
                            <input type="text" name="url_boton" value="{{ old('url_boton') }}" placeholder="Ej: https://xiaoxa.com/?categoria=Blusas o https://xiaoxa.com/producto/15"
                                class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none font-semibold transition-all text-sm">
                            <p class="text-xs text-gray-400 mt-1 ml-1">💡 Copia la URL desde el navegador y pégala aquí. El banner completo será clickeable.</p>
                        </div>

                        {{-- Orden --}}
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Orden</label>
                            <input type="number" name="orden" value="{{ old('orden', 0) }}" min="0"
                                class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none font-bold transition-all text-sm">
                        </div>

                        {{-- Estado --}}
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Estado</label>
                            <select name="estado"
                                class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none font-semibold transition-all text-sm">
                                <option value="1" {{ old('estado', 1) == 1 ? 'selected' : '' }}>Activo</option>
                                <option value="0" {{ old('estado') === '0' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>

                        {{-- Imagen --}}
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Imagen *</label>
                            <label x-data="{ fileName: '' }"
                                class="flex flex-col items-center justify-center w-full h-28 bg-gray-50 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/40 transition-all group">
                                <x-heroicon-o-cloud-arrow-up class="w-8 h-8 text-gray-300 group-hover:text-indigo-400 transition-colors mb-2" />
                                <span class="text-sm font-bold text-gray-400 group-hover:text-indigo-500 px-4 text-center"
                                    x-text="fileName || 'Haz clic para subir imagen'"></span>
                                <span class="text-xs text-gray-300 mt-1">JPG, PNG, WEBP — Max 2MB</span>
                                <input type="file" name="imagen" required accept="image/jpeg,image/png,image/webp" class="hidden"
                                    @change="fileName = $event.target.files[0]?.name || ''">
                            </label>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="createModal = false"
                            class="flex-1 py-4 bg-gray-100 text-gray-500 font-bold rounded-2xl hover:bg-gray-200 transition text-sm">Cancelar</button>
                        <button type="submit"
                            class="flex-[2] py-4 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 shadow-lg transition-all active:scale-95 text-sm">Crear Banner</button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- ===================== MODAL EDITAR BANNER ===================== --}}
    <div x-data="{ editModal: false, banner: {} }"
         @open-edit-banner.window="banner = $event.detail; editModal = true">
        <template x-if="editModal">
            <div class="fixed inset-0 z-[110] flex items-end sm:items-center justify-center">
                <div @click="editModal = false" class="absolute inset-0 bg-gray-900/60 backdrop-blur-xl"></div>
                <div class="relative bg-white rounded-t-[2.5rem] sm:rounded-[2.5rem] p-6 sm:p-10 w-full sm:max-w-2xl shadow-2xl max-h-[92vh] overflow-y-auto">

                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h2 class="text-2xl sm:text-3xl font-black text-gray-900">Editar Banner</h2>
                            <p class="text-gray-400 mt-1 font-medium italic text-sm">Modifica los datos del banner.</p>
                        </div>
                        <button @click="editModal = false"
                            class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-50 text-gray-400 hover:bg-rose-50 hover:text-rose-500 transition ml-4 flex-shrink-0">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                        </button>
                    </div>

                    <form :action="`/admin/banners/${banner.id_banner}`" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Título --}}
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">
                                    Título * <span class="font-medium normal-case text-gray-300">(solo uso interno)</span>
                                </label>
                                <input type="text" name="titulo" :value="banner.titulo" required
                                    class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none font-bold transition-all text-sm sm:text-base">
                            </div>

                            {{-- URL del Botón --}}
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">
                                    URL del Banner <span class="font-medium normal-case text-gray-300">(a dónde redirige al hacer clic)</span>
                                </label>
                                <input type="text" name="url_boton" :value="banner.url_boton"
                                    class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none font-semibold transition-all text-sm">
                            </div>

                            {{-- Orden --}}
                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Orden</label>
                                <input type="number" name="orden" :value="banner.orden" min="0"
                                    class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none font-bold transition-all text-sm">
                            </div>

                            {{-- Estado --}}
                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Estado</label>
                                <select name="estado"
                                    class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none font-semibold transition-all text-sm"
                                    x-init="$el.value = banner.estado">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>

                            {{-- Preview imagen actual --}}
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Imagen actual</label>
                                <div class="relative w-full h-32 rounded-2xl overflow-hidden bg-gray-100">
                                    <img :src="`/api/imagen/${banner.imagen}`"
                                         :alt="banner.titulo"
                                         class="w-full h-full object-cover">
                                </div>
                            </div>

                            {{-- Reemplazar imagen --}}
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">
                                    Reemplazar Imagen <span class="font-medium normal-case text-gray-300">(opcional)</span>
                                </label>
                                <label x-data="{ fileName: '' }"
                                    class="flex flex-col items-center justify-center w-full h-24 bg-gray-50 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/40 transition-all group">
                                    <x-heroicon-o-arrow-path class="w-7 h-7 text-gray-300 group-hover:text-indigo-400 transition-colors mb-1" />
                                    <span class="text-sm font-bold text-gray-400 group-hover:text-indigo-500 px-4 text-center"
                                        x-text="fileName || 'Haz clic para cambiar imagen'"></span>
                                    <input type="file" name="imagen" accept="image/jpeg,image/png,image/webp" class="hidden"
                                        @change="fileName = $event.target.files[0]?.name || ''">
                                </label>
                            </div>
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button type="button" @click="editModal = false"
                                class="flex-1 py-4 bg-gray-100 text-gray-500 font-bold rounded-2xl hover:bg-gray-200 transition text-sm">Cancelar</button>
                            <button type="submit"
                                class="flex-[2] py-4 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 shadow-lg transition-all active:scale-95 text-sm">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>

</div>
@endsection