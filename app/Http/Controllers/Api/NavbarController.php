<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categoria;

class NavbarController extends Controller
{
    public function index()
    {
        // Categorías reales de la BD
        $categorias = Categoria::where('estado_categoria', 1)
            ->orderBy('id_categoria', 'asc')
            ->get()
            ->map(fn($c) => [
                'nombre' => $c->nombre_categoria,
                'valor'  => $c->nombre_categoria,
            ]);

        // Items fijos + categorías dinámicas
        $items = [
            [
                'tipo'   => 'nuevo',
                'nombre' => 'Lo nuevo',
                'valor'  => 'nuevo',
            ],
            [
                'tipo'     => 'categorias_dropdown',
                'nombre'   => 'Categorías',
                'valor'    => 'categorias',
                'subitems' => $categorias,
            ],
            [
                'tipo'   => 'categoria',
                'nombre' => 'Accesorios',
                'valor'  => 'Accesorios',
            ],
            [
                'tipo'   => 'promocion',
                'nombre' => 'Promociones',
                'valor'  => 'promocion',
            ],
        ];

        return response()->json([
            'success' => true,
            'data'    => $items,
        ]);
    }
}