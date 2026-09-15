<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categoria;

class CategoriaController extends Controller
{
    /**
     * Mostrar lista de categorías
     */
    public function index()
    {
        $categorias = Categoria::withCount('productos')->get();

        return view('admin.categorias.index', compact('categorias'));
    }

    /**
     * Guardar nueva categoría
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre_categoria' => 'required|string|max:50|unique:categoria,nombre_categoria',
        ]);

        Categoria::create([
            'nombre_categoria' => $request->nombre_categoria,
            'estado_categoria' => 1
        ]);

        return redirect()
            ->route('admin.categorias.index')
            ->with('success', 'Categoría creada correctamente');
    }

    /**
     * Actualizar categoría
     */
    public function update(Request $request, Categoria $categoria)
    {
        $request->validate([
            'nombre_categoria' => 'required|string|max:50|unique:categoria,nombre_categoria,' . $categoria->id_categoria . ',id_categoria',
        ]);

        $categoria->update([
            'nombre_categoria' => $request->nombre_categoria,
        ]);

        return redirect()
            ->route('admin.categorias.index')
            ->with('success', 'Categoría actualizada correctamente');
    }

    /**
     * Activar/Desactivar categoría
     */
    public function toggle($id)
    {
        $categoria = Categoria::with('productos')->findOrFail($id);

        $tieneStock = $categoria->productos()
            ->whereHas('variantes', function ($q) {
                $q->where('stock', '>', 0);
            })
            ->exists();

        if ($tieneStock && $categoria->estado_categoria) {
            return redirect()->back()->with(
                'error',
                'No se puede desactivar porque tiene productos con stock.'
            );
        }

        $categoria->estado_categoria = !$categoria->estado_categoria;
        $categoria->save();

        return redirect()->back()->with('success', 'Estado actualizado correctamente.');
    }
}