<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PerfilController extends Controller
{
    public function index()
    {
        $pedidos = auth()->user()
            ->pedidos()
            ->with(['detalles.variante.producto', 'detalles.variante.imagenes', 'tipoEntrega', 'cupon'])
            ->orderBy('fecha_pedido', 'desc')
            ->paginate(5);

        return view('perfil.index', compact('pedidos'));
    }

    public function edit()
    {
        return view('perfil.edit');
    }

    public function update(Request $request)
    {
        $request->validate([
            'nombres'          => 'required|string|max:255',
            'apellidos'        => 'required|string|max:255',
            'telefono'         => 'nullable|numeric|digits:9',
            'numero_documento' => 'nullable|numeric|digits_between:8,12',
        ]);

        Auth::user()->update([
            'nombres'          => $request->nombres,
            'apellidos'        => $request->apellidos,
            'telefono'         => $request->telefono,
            'numero_documento' => $request->numero_documento,
        ]);

        return redirect()->route('perfil.index')->with('success', 'Perfil actualizado');
    }

    public function updateEmail(Request $request)
    {
        $request->validate([
            'nuevo_correo'              => 'required|email|unique:usuario,correo',
            'nuevo_correo_confirmation' => 'required|same:nuevo_correo',
            'contrasena_actual'         => 'required',
        ]);

        if (!Hash::check($request->contrasena_actual, Auth::user()->contrasena)) {
            return back()->withErrors(['contrasena_actual' => 'Contraseña incorrecta']);
        }

        Auth::user()->update(['correo' => $request->nuevo_correo]);

        return redirect()->route('perfil.index')->with('success', 'Correo actualizado');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'contrasena_actual' => 'required',
            'nueva_contrasena'  => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->contrasena_actual, Auth::user()->contrasena)) {
            return back()->withErrors(['contrasena_actual' => 'Contraseña incorrecta']);
        }

        Auth::user()->update(['contrasena' => Hash::make($request->nueva_contrasena)]);

        return redirect()->route('perfil.index')->with('success', 'Contraseña actualizada');
    }

    public function destroy(Request $request)
    {
        $request->validate(['contrasena' => 'required']);

        if (!Hash::check($request->contrasena, Auth::user()->contrasena)) {
            return back()->withErrors(['contrasena' => 'Contraseña incorrecta']);
        }

        $usuario = Auth::user();
        Auth::logout();
        $usuario->delete();

        return redirect()->route('home')->with('success', 'Cuenta eliminada');
    }
}