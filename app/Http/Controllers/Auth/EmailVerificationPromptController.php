<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Muestra la vista de "verifica tu correo".
     * No requiere usuario autenticado.
     */
    public function __invoke(Request $request): View
    {
        return view('auth.verify-email');
    }
}