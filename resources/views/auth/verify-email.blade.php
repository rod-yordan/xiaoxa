@extends('layouts.app')

@section('title', 'Verificar email - C Lucky')

@section('content')
<div class="min-h-[calc(100vh-200px)] flex items-center justify-center py-8 px-4 bg-[#fbfaf8]">
    <div class="w-full max-w-md">

        <div class="p-8 sm:p-10">

            <h1 class="text-2xl font-bold text-center text-black mb-8">Verifica tu correo</h1>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-green-700 text-sm text-center font-medium">
                        Se ha enviado un nuevo enlace de verificación a tu correo electrónico.
                    </p>
                </div>
            @endif

            <div class="text-center space-y-3">
                <p class="text-gray-700 text-sm leading-relaxed">
                    Tu cuenta ha sido creada. Sin embargo, la cuenta requiere activación.
                </p>
                <p class="text-gray-700 text-sm leading-relaxed">
                    La clave de activación será enviada a tu correo electrónico en un plazo de 5 a 10 minutos.
                    Por favor, revisa tu correo para más información.
                </p>
                <p class="text-gray-700 text-sm leading-relaxed">
                    Si el correo no llega después de un tiempo prolongado, tiene sentido revisar la carpeta de "Spam".
                </p>
            </div>

        </div>
    </div>
</div>
@endsection