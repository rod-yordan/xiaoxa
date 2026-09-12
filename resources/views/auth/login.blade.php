@extends('layouts.app')

@section('title', 'Iniciar sesión - Xiaoxa')

@section('content')
<div class="min-h-[calc(100vh-200px)] flex items-center justify-center py-16 px-4 bg-[#fbfaf8]">
    <div class="w-full max-w-md">
        
        {{-- CONTENEDOR --}}
        <div class="p-8 sm:p-10">
            
            {{-- TÍTULO --}}
            <h1 class="text-2xl font-bold text-center text-black mb-8">Iniciar sesión</h1>

            {{-- Mensaje de error general --}}
            @if ($errors->has('correo'))
                <div class="bg-red-50 text-red-600 text-sm px-4 py-3 rounded-lg mb-6">
                    {{ $errors->first('correo') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- CORREO ELECTRÓNICO --}}
                <div class="mb-5">
                    <label for="correo" class="block text-sm font-medium text-gray-700 mb-2">
                        Correo electrónico
                    </label>
                    <input 
                        type="email" 
                        name="correo" 
                        id="correo"
                        value="{{ old('correo') }}" 
                        placeholder="Ingresar correo"
                        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm text-black placeholder-gray-400 focus:outline-none focus:border-gray-400 transition @error('correo') border-red-500 @enderror" 
                        required 
                        autofocus
                    >
                </div>

                {{-- CONTRASEÑA CON ALPINE.JS --}}
                <div class="mb-3" x-data="{ verPassword: false }">
                    <label for="contrasena" class="block text-sm font-medium text-gray-700 mb-2">
                        Contraseña
                    </label>
                    <div class="relative">
                        <input 
                            :type="verPassword ? 'text' : 'password'"
                            name="contrasena" 
                            id="contrasena"
                            placeholder="Ingresar contraseña"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 pr-10 text-sm text-black placeholder-gray-400 focus:outline-none focus:border-gray-400 transition @error('correo') border-red-500 @enderror" 
                            required
                        >
                        {{-- Botón con iconos Heroicons alternados --}}
                        <button 
                            type="button" 
                            @click="verPassword = !verPassword" 
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition"
                            aria-label="Mostrar contraseña"
                        >
                            <x-heroicon-o-eye x-show="!verPassword" class="w-5 h-5" />
                            <x-heroicon-o-eye-slash x-show="verPassword" x-cloak class="w-5 h-5" />
                        </button>
                    </div>
                </div>

                {{-- OLVIDÉ MI CONTRASEÑA --}}
                <div class="text-right mb-6">
                    <a href="#" class="text-sm text-gray-500 hover:text-black transition">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>

                {{-- BOTÓN INICIAR SESIÓN --}}
                <button 
                    type="submit" 
                    class="w-full bg-black text-white text-sm font-bold uppercase tracking-wider py-3 rounded-lg hover:bg-gray-800 transition-colors duration-200"
                >
                    Iniciar sesión
                </button>
            </form>

            {{-- LINK REGISTRARSE --}}
            <p class="text-center text-sm text-gray-500 mt-6">
                ¿No tienes una cuenta? 
                <a href="{{ route('register') }}" class="text-black font-semibold hover:underline">
                    Registrarse
                </a>
            </p>

        </div>
    </div>
</div>
@endsection