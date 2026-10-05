<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;

class UsuarioController extends Controller
{
    public function actualizar(Request $request)
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        $request->validate([
            'nombres'           => 'required',
            'apellidos'         => 'required',
            'telefono'          => 'nullable',
            'numero_documento'  => 'nullable',
            'id_tipo_documento' => 'nullable',
        ]);

        $usuario->update([
            'nombres'           => $request->nombres,
            'apellidos'         => $request->apellidos,
            'telefono'          => $request->telefono,
            'numero_documento'  => $request->numero_documento,
            'id_tipo_documento' => $request->id_tipo_documento,
        ]);

        return back()->with('success', 'Datos actualizados correctamente');
    }
}