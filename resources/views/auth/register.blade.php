@extends('layouts.app')

@section('title', 'Registrarse - Xiaoxa')

@section('content')
<div class="min-h-[calc(100vh-200px)] flex items-center justify-center py-8 px-4 bg-[#fbfaf8]">
    <div class="w-full max-w-md">
        
        {{-- CONTENEDOR --}}
        <div class="p-8 sm:p-10">
            
            {{-- TÍTULO --}}
            <h1 class="text-2xl font-bold text-center text-black mb-8">Registrarse</h1>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                {{-- NOMBRES --}}
                <div class="mb-5">
                    <label for="nombres" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombres
                    </label>
                    <input 
                        type="text" 
                        name="nombres" 
                        id="nombres"
                        value="{{ old('nombres') }}" 
                        placeholder="Ingresar nombres"
                        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm text-black placeholder-gray-400 focus:outline-none focus:border-gray-400 transition @error('nombres') border-red-500 @enderror" 
                        required 
                        autofocus
                    >
                    @error('nombres')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- APELLIDOS --}}
                <div class="mb-5">
                    <label for="apellidos" class="block text-sm font-medium text-gray-700 mb-2">
                        Apellidos
                    </label>
                    <input 
                        type="text" 
                        name="apellidos" 
                        id="apellidos"
                        value="{{ old('apellidos') }}" 
                        placeholder="Ingresar apellidos"
                        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm text-black placeholder-gray-400 focus:outline-none focus:border-gray-400 transition @error('apellidos') border-red-500 @enderror" 
                        required
                    >
                    @error('apellidos')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

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
                    >
                    @error('correo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- CONTRASEÑA CON ALPINE.JS --}}
                <div class="mb-5" x-data="{ verPassword: false }">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Contraseña
                    </label>
                    <div class="relative">
                        <input 
                            :type="verPassword ? 'text' : 'password'"
                            name="password" 
                            id="password"
                            placeholder="Ingresar contraseña"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 pr-10 text-sm text-black placeholder-gray-400 focus:outline-none focus:border-gray-400 transition @error('password') border-red-500 @enderror" 
                            required
                        >
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
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- CONFIRMAR CONTRASEÑA CON ALPINE.JS --}}
                <div class="mb-6" x-data="{ verConfirmar: false }">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirmar contraseña
                    </label>
                    <div class="relative">
                        <input 
                            :type="verConfirmar ? 'text' : 'password'"
                            name="password_confirmation" 
                            id="password_confirmation"
                            placeholder="Ingresar contraseña nuevamente"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 pr-10 text-sm text-black placeholder-gray-400 focus:outline-none focus:border-gray-400 transition" 
                            required
                        >
                        <button 
                            type="button" 
                            @click="verConfirmar = !verConfirmar" 
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition"
                            aria-label="Mostrar contraseña"
                        >
                            <x-heroicon-o-eye x-show="!verConfirmar" class="w-5 h-5" />
                            <x-heroicon-o-eye-slash x-show="verConfirmar" x-cloak class="w-5 h-5" />
                        </button>
                    </div>
                </div>

                {{-- BOTÓN CREAR CUENTA --}}
                <button 
                    type="submit" 
                    class="w-full bg-black text-white text-sm font-bold uppercase tracking-wider py-3 rounded-lg hover:bg-gray-800 transition-colors duration-200"
                >
                    Crear cuenta
                </button>
            </form>

            {{-- LINK INICIAR SESIÓN --}}
            <p class="text-center text-sm text-gray-500 mt-6">
                ¿Ya tienes una cuenta? 
                <a href="{{ route('login') }}" class="text-black font-semibold hover:underline">
                    Iniciar sesión
                </a>
            </p>

        </div>
    </div>
</div>
@endsection