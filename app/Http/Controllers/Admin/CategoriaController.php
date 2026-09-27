<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categoria;
use Illuminate\Support\Facades\File;

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
            'estado_categoria' => 'nullable|in:0,1',
        ]);

        $datos = [
            'nombre_categoria' => $request->nombre_categoria,
        ];

        // Solo actualizamos el estado si se envía en el formulario
        if ($request->has('estado_categoria')) {
            $datos['estado_categoria'] = $request->estado_categoria;
        }

        $categoria->update($datos);

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

    /**
     * ✅ Eliminar categoría
     *
     * Orden:
     * 1. Eliminar imágenes físicas + registros de las variantes
     * 2. Eliminar variantes
     * 3. Eliminar productos de la categoría
     * 4. Eliminar la categoría
     */
    public function destroy($id)
    {
        $categoria = Categoria::with(['productos.variantes.imagenes'])->findOrFail($id);

        // 1-3. Borrar cada producto con sus variantes e imágenes
        foreach ($categoria->productos as $producto) {
            foreach ($producto->variantes as $variante) {
                // Borrar imágenes físicas + registros
                foreach ($variante->imagenes as $img) {
                    $ruta = storage_path('app/public/variantes/' . $img->imagen);
                    if (File::exists($ruta)) {
                        File::delete($ruta);
                    }
                    $img->delete();
                }
                // Borrar variante
                $variante->delete();
            }
            // Borrar producto
            $producto->delete();
        }

        // 4. Borrar categoría
        $categoria->delete();

        return redirect()
            ->route('admin.categorias.index')
            ->with('success', 'Categoría eliminada correctamente');
    }
}