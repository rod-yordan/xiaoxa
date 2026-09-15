<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    /**
     * Obtener todas las categorías activas
     */
    public function index(Request $request)
    {
        $categorias = Categoria::where('estado_categoria', true)
            ->orderBy('nombre_categoria', 'asc')
            ->get();

        $categorias = $categorias->map(function ($categoria) {
            return [
                'id_categoria'     => $categoria->id_categoria,
                'nombre_categoria' => $categoria->nombre_categoria,
                'estado_categoria' => $categoria->estado_categoria,
                'created_at'       => $categoria->created_at,
                'updated_at'       => $categoria->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $categorias
        ]);
    }

    /**
     * Obtener productos por categoría
     */
    public function productos($id, Request $request)
    {
        $categoria = Categoria::with(['productos' => function ($query) use ($request) {
            $query->where('estado_producto', true)
                  ->with('variantes');
        }])->find($id);

        if (!$categoria) {
            return response()->json([
                'success' => false,
                'message' => 'Categoría no encontrada'
            ], 404);
        }

        $productos = $categoria->productos->map(function ($producto) use ($categoria) {
            $imagenPrincipal = $producto->imagen
                ? url('/api/imagen/' . $producto->imagen)
                : null;

            $galeria = [];
            if ($producto->galeria) {
                $imagenesGaleria = is_array($producto->galeria)
                    ? $producto->galeria
                    : json_decode($producto->galeria, true) ?? [];

                $galeria = collect($imagenesGaleria)->map(function ($img) {
                    return url('/api/imagen/' . $img);
                })->toArray();
            }

            $precioOriginal = (float) $producto->precio;
            $precioFinal    = $precioOriginal;
            $descuento      = 0;

            if ($producto->precio_oferta) {
                $precioFinal = (float) $producto->precio_oferta;
                $descuento   = round((($precioOriginal - $precioFinal) / $precioOriginal) * 100, 0);
            }

            return [
                'id'             => $producto->id_producto,
                'titulo'         => $producto->nombre_producto,
                'descripcion'    => $producto->descripcion ?? '',
                'precio'         => $precioFinal,
                'precio_antes'   => $precioFinal < $precioOriginal ? $precioOriginal : null,
                'descuento'      => $descuento > 0 ? $descuento : null,
                'imagen_principal' => $imagenPrincipal,
                'imagenes'       => $galeria,
                'categoria'      => $categoria->nombre_categoria,
                'categoria_id'   => $categoria->id_categoria,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $productos
        ]);
    }
}