<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Banner;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::check() && (int) Auth::user()->id_rol === 1) {
            return redirect()->route('admin.dashboard');
        }

        $hayFiltros = $request->filled('buscar')
                   || ($request->filled('categoria') && $request->categoria != 'Todo')
                   || $request->has('promocion')
                   || $request->has('nuevo');

        // ✅ Cargamos la relación con las imágenes de variantes
        $query = Producto::query()
            ->where('estado_producto', 1)
            ->with(['variantes.imagenes']);

        if ($request->filled('categoria') && $request->categoria != 'Todo') {
            $query->whereHas('categoria', function ($q) use ($request) {
                $q->where('nombre_categoria', $request->categoria);
            });
        }

        // Filtro por promoción activa
        if ($request->has('promocion')) {
            $query->where(function ($q) {
                $q->whereNotNull('precio_oferta')
                    ->where('precio_oferta', '>', 0)
                    ->whereColumn('precio_oferta', '<', 'precio');
            });
        }

        // ✅ Filtro "Lo nuevo" → 8 últimos productos creados
        if ($request->has('nuevo')) {
            $query->orderBy('created_at', 'desc')->limit(8);
        }

        // ✅ Filtro por búsqueda de texto (mejorado)
        if ($request->filled('buscar')) {
            $buscar = trim($request->buscar);

            $palabras = array_filter(
                array_map(fn($p) => trim($p), explode(' ', $buscar)),
                fn($p) => strlen($p) >= 2
            );

            $query->where(function ($q) use ($palabras) {
                foreach ($palabras as $palabra) {
                    $q->where(function ($subQ) use ($palabra) {
                        $subQ->where('nombre_producto', 'like', "%{$palabra}%")
                             ->orWhere('marca', 'like', "%{$palabra}%");
                    });
                }
            });
        }

        // ✅ Si hay filtros → un solo array
        if ($hayFiltros) {
            // Si es "lo nuevo", ya tiene su orderBy y limit aplicado
            if (!$request->has('nuevo')) {
                $query->orderBy('created_at', 'desc');
            }
            $productos = $query->get();
            $categorias = collect();
        }
        // ✅ Si NO hay filtros → agrupar por categoría
        else {
            $categorias = Categoria::where('estado_categoria', 1)
                ->with(['productos' => function ($q) {
                    $q->where('estado_producto', 1)
                      ->with(['variantes.imagenes'])
                      ->orderBy('created_at', 'desc');
                }])
                ->orderBy('id_categoria', 'asc')
                ->get()
                ->filter(function ($cat) {
                    return $cat->productos->count() > 0;
                });

            $productos = collect();
        }

        $banners = Banner::where('estado', 1)
            ->orderBy('orden', 'asc')
            ->get();

        return view('home.index', compact('productos', 'categorias', 'banners'));
    }
}