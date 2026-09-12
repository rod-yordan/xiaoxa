@extends('layouts.app')

@section('title', 'Mi cuenta - Xiaoxa')

@section('content')
<div 
    x-data="{ editando: {{ $errors->any() ? 'true' : 'false' }} }"
    class="min-h-[calc(100vh-150px)] bg-[#fbfaf8] py-8 px-4"
>

    <div class="max-w-5xl mx-auto">
        
        {{-- TÍTULO --}}
        <h1 class="text-2xl font-normal text-black text-center mb-8">Mi cuenta</h1>

        {{-- GRID: 1/3 IZQUIERDA + 2/3 DERECHA --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-16">
            
            {{-- CARD IZQUIERDA --}}
            <div class="md:col-span-1 bg-[#f1f1f1] border border-gray-300 rounded-lg p-4 min-h-[400px] flex flex-col w-full max-w-[260px]">
                
                {{-- Avatar + Nombre --}}
                <div class="flex items-center gap-3 pb-4 mb-3 border-b border-gray-300">
                    <div class="w-10 h-10 rounded-full bg-black flex items-center justify-center text-white text-sm font-bold shrink-0">
                        {{ strtoupper(substr(auth()->user()->nombres, 0, 1)) }}
                    </div>
                    <span class="text-sm text-black font-normal truncate">
                        {{ auth()->user()->nombres }} {{ auth()->user()->apellidos }}
                    </span>
                </div>

                {{-- Menú --}}
                <nav class="space-y-1">
                    
                    <a href="#" 
                        @click.prevent="editando = false"
                        :class="!editando ? 'bg-gray-300 text-black' : 'text-black hover:bg-gray-200'"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-md transition">
                        <x-heroicon-o-user class="w-5 h-5 shrink-0" />
                        <span class="text-sm">Información de cuenta</span>
                    </a>

                    <a href="#" 
                        @click.prevent="editando = true"
                        :class="editando ? 'bg-gray-300 text-black' : 'text-black hover:bg-gray-200'"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-md transition">
                        <x-heroicon-o-pencil-square class="w-5 h-5 shrink-0" />
                        <span class="text-sm">Editar mis datos</span>
                    </a>

                    <a href="{{ route('perfil.pedidos.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-black hover:bg-gray-200 rounded-md transition">
                        <x-heroicon-o-shopping-bag class="w-5 h-5 shrink-0" />
                        <span class="text-sm">Mis compras</span>
                    </a>

                </nav>

                {{-- Cerrar sesión --}}
                <form method="POST" action="{{ route('logout') }}" class="mt-auto pt-4">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 px-3 py-2 text-black hover:bg-gray-200 rounded-md transition w-full">
                        <x-heroicon-o-arrow-left-on-rectangle class="w-5 h-5 shrink-0" />
                        <span class="text-xs uppercase tracking-wider">Cerrar sesión</span>
                    </button>
                </form>

            </div>

            {{-- CARD DERECHA --}}
            <div class="md:col-span-2 bg-[#f1f1f1] border border-gray-300 rounded-lg p-6 min-h-[400px] flex flex-col">

                {{-- ============ VISTA: INFORMACIÓN DE CUENTA ============ --}}
                <div x-show="!editando" class="w-full">
                    
                    {{-- TÍTULO FIJO --}}
                    <h2 class="text-xl font-normal text-black text-center mb-4 mt-4">
                        Información de cuenta
                    </h2>

                    {{-- CONTENIDO PEGADO AL TÍTULO --}}
                    <div class="w-full mt-12">
                        <div class="grid grid-cols-2 gap-x-10 gap-y-8 w-fit mx-auto">

                            <div>
                                <p class="text-sm text-gray-700">Nombres:</p>
                                <p class="text-base text-black mt-1 ml-6">{{ auth()->user()->nombres }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-700">Apellidos:</p>
                                <p class="text-base text-black mt-1 ml-6">{{ auth()->user()->apellidos }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-700">Teléfono:</p>
                                <p class="text-base {{ auth()->user()->telefono ? 'text-black' : 'text-gray-400' }} mt-1 ml-6">
                                    {{ auth()->user()->telefono ?? 'No registrado' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-700">Correo electrónico:</p>
                                <p class="text-base text-black mt-1 ml-6">{{ auth()->user()->correo }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-700">Tipo de documento:</p>
                                <p class="text-base {{ auth()->user()->tipoDocumento ? 'text-black' : 'text-gray-400' }} mt-1 ml-6">
                                    {{ auth()->user()->tipoDocumento->nombre_tipo_documento ?? 'No registrado' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-700">Número de documento:</p>
                                <p class="text-base {{ auth()->user()->numero_documento ? 'text-black' : 'text-gray-400' }} mt-1 ml-6">
                                    {{ auth()->user()->numero_documento ?? 'No registrado' }}
                                </p>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ============ VISTA: EDITAR DATOS ============ --}}
                <div x-show="editando" x-cloak class="w-full">
                    
                    {{-- TÍTULO FIJO --}}
                    <h2 class="text-xl font-normal text-black text-center mb-4 mt-4">
                        Editar mis datos
                    </h2>

                    {{-- CONTENIDO PEGADO AL TÍTULO --}}
                    <div class="w-full">
                        <form method="POST" action="{{ route('perfil.update') }}" class="space-y-6 w-full">
                            @csrf
                            @method('PUT')

                            {{-- GRID 2 COLUMNAS --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">

                                {{-- Nombres (editable) --}}
                                <div>
                                    <label class="text-xs font-semibold uppercase tracking-widest text-gray-600 block mb-2">Nombres</label>
                                    <div class="relative">
                                        <input type="text" name="nombres" value="{{ old('nombres', auth()->user()->nombres) }}"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 pr-10 text-sm text-black focus:outline-none focus:border-black transition">
                                        <x-heroicon-o-pencil class="w-4 h-4 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" />
                                    </div>
                                    @error('nombres')
                                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Apellidos (editable) --}}
                                <div>
                                    <label class="text-xs font-semibold uppercase tracking-widest text-gray-600 block mb-2">Apellidos</label>
                                    <div class="relative">
                                        <input type="text" name="apellidos" value="{{ old('apellidos', auth()->user()->apellidos) }}"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 pr-10 text-sm text-black focus:outline-none focus:border-black transition">
                                        <x-heroicon-o-pencil class="w-4 h-4 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" />
                                    </div>
                                    @error('apellidos')
                                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Teléfono (editable) --}}
                                <div>
                                    <label class="text-xs font-semibold uppercase tracking-widest text-gray-600 block mb-2">Teléfono móvil</label>
                                    <div class="relative">
                                        <input type="text" name="telefono"
                                            value="{{ old('telefono', auth()->user()->telefono) }}"
                                            maxlength="9"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                            placeholder="Ej. 912345678"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 pr-10 text-sm text-black focus:outline-none focus:border-black transition">
                                        <x-heroicon-o-pencil class="w-4 h-4 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" />
                                    </div>
                                    @error('telefono')
                                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Correo (NO editable) --}}
                                <div>
                                    <label class="text-xs font-semibold uppercase tracking-widest text-gray-600 block mb-2">Correo electrónico</label>
                                    <input type="email" value="{{ auth()->user()->correo }}"
                                        disabled
                                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-500 cursor-not-allowed">
                                </div>

                                {{-- Tipo de documento (NO editable) --}}
                                <div>
                                    <label class="text-xs font-semibold uppercase tracking-widest text-gray-600 block mb-2">Tipo de documento</label>
                                    <input type="text" value="{{ auth()->user()->tipoDocumento->nombre_tipo_documento ?? 'No registrado' }}"
                                        disabled
                                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-500 cursor-not-allowed">
                                </div>

                                {{-- Número de documento (NO editable) --}}
                                <div>
                                    <label class="text-xs font-semibold uppercase tracking-widest text-gray-600 block mb-2">Número de documento</label>
                                    <input type="text" value="{{ auth()->user()->numero_documento ?? 'No registrado' }}"
                                        disabled
                                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-500 cursor-not-allowed">
                                </div>

                            </div>

                            {{-- BOTÓN GUARDAR (CENTRADO) --}}
                            <div class="pt-2 flex justify-center">
                                <button type="submit"
                                    class="bg-black text-white px-12 py-3 rounded-lg font-bold uppercase tracking-widest text-xs hover:bg-gray-800 transition-all">
                                    Guardar
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>
@endsection