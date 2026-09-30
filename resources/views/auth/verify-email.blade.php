@extends('layouts.app')

@section('title', 'Verificar cuenta - Xiaoxa')

@section('content')
<div class="flex-1 flex justify-center py-8 px-4"
     style="min-height: calc(100vh - 160px);">
    <div class="w-full max-w-2xl">

        <div class="p-8 sm:p-10">

            @if (request()->has('verificado'))
                {{-- ✅ Caso: usuario acaba de verificar su correo --}}
                <h1 class="text-2xl font-bold text-center text-black mb-8">¡Cuenta verificada!</h1>

                <div class="text-center space-y-3">
                    <p class="text-gray-700 text-sm leading-relaxed">
                        Tu correo electrónico ha sido verificado correctamente.
                    </p>
                    <p class="text-gray-700 text-sm leading-relaxed">
                        Ya puedes iniciar sesión con tu correo y contraseña.
                    </p>
                </div>
            @else
                {{-- ⏳ Caso: usuario recién registrado, debe verificar --}}
                <h1 class="text-2xl font-bold text-center text-black mb-8">Verifica tu cuenta</h1>

                <div class="text-center space-y-3">
                    <p class="text-gray-700 text-sm leading-relaxed">
                        Tu cuenta ha sido creada. Sin embargo, es necesario activarla.
                    </p>
                    <p class="text-gray-700 text-sm leading-relaxed">
                        La clave de activación se enviará a tu correo electrónico en un plazo de 5 a 10 minutos.
                        Por favor, revisa tu correo para obtener más información.
                    </p>
                    <p class="text-gray-700 text-sm leading-relaxed">
                        Si el correo no llega después de un tiempo, te recomendamos revisar la carpeta de "Spam".
                    </p>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection