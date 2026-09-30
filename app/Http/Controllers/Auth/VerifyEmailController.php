<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    /**
     * Marca el correo como verificado (a través del link firmado del correo).
     */
    public function __invoke(Request $request, $id, $hash): RedirectResponse
    {
        // 1. Buscamos al usuario por ID
        $user = User::findOrFail($id);

        // 2. Verificamos que el hash coincida con su correo
        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Enlace de verificación inválido.');
        }

        // 3. Marcamos como verificado (si aún no lo estaba)
        if (! $user->hasVerifiedEmail()) {
            if ($user->markEmailAsVerified()) {
                event(new Verified($user));
            }
        }

        // 4. Redirigimos a la vista de verificación con ?verificado=1
        return redirect()->route('verification.notice', ['verificado' => 1]);
    }
}