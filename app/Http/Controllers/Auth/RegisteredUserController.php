<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombres'   => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'correo'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:usuario,correo'],
            'password'  => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            // Mensajes personalizados que aparecerán debajo de cada input
            'nombres.required'    => 'Los nombres son obligatorios.',
            'nombres.string'      => 'Los nombres deben ser texto válido.',
            'nombres.max'         => 'Los nombres no pueden tener más de 255 caracteres.',

            'apellidos.required'  => 'Los apellidos son obligatorios.',
            'apellidos.string'    => 'Los apellidos deben ser texto válido.',
            'apellidos.max'       => 'Los apellidos no pueden tener más de 255 caracteres.',

            'correo.required'     => 'El correo electrónico es obligatorio.',
            'correo.email'        => 'Ingresa un correo electrónico válido.',
            'correo.unique'       => 'Este correo ya está registrado.',
            'correo.max'          => 'El correo no puede tener más de 255 caracteres.',

            'password.required'   => 'La contraseña es obligatoria.',
            'password.confirmed'  => 'Las contraseñas no coinciden.',
            'password.min'        => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $user = User::create([
            'nombres'    => $request->nombres,
            'apellidos'  => $request->apellidos,
            'correo'     => $request->correo,
            'contrasena' => Hash::make($request->password),
            'id_rol'     => 2,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('verification.notice');
    }
}