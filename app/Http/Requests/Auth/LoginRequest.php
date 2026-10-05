<?php

namespace App\Http\Requests\Auth;

use App\Models\Usuario;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'correo'     => ['required', 'string', 'email'],
            'contrasena' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // 1. Verificamos si el correo existe
        $usuario = Usuario::where('correo', $this->correo)->first();

        if (! $usuario) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'correo' => 'El correo electrónico es incorrecto.',
            ]);
        }

        // 2. Verificamos si la contraseña es correcta
        if (! Hash::check($this->contrasena, $usuario->contrasena)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'contrasena' => 'La contraseña es incorrecta.',
            ]);
        }

        // 3. Credenciales correctas → iniciamos sesión
        Auth::login($usuario, $this->boolean('remember'));

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'correo' => "Demasiados intentos. Inténtalo de nuevo en {$seconds} segundos.",
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('correo')).'|'.$this->ip());
    }
}