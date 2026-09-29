<?php

namespace App\Http\Controllers\Admin;

use App\Models\Departamento;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EnvioController extends Controller
{
    public function index()
    {
        $departamentos = Departamento::orderBy('nombre_departamento')->get();
        return view('admin.envios.index', compact('departamentos'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'costo_envio' => 'required|numeric|min:0',
        ], [
            'costo_envio.required' => 'El costo de envío es obligatorio.',
            'costo_envio.numeric'  => 'El costo de envío debe ser un número.',
            'costo_envio.min'      => 'El costo de envío no puede ser negativo.',
        ]);

        $departamento = Departamento::findOrFail($id);
        $departamento->costo_envio = $request->costo_envio;
        $departamento->save();

        return back()->with('success', 'Costo de envío actualizado correctamente.');
    }
}