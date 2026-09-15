<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductoResource;
use App\Http\Resources\VarianteResource;
use App\Models\Producto;
use App\Models\ProductoVariante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductoController extends Controller
{
    /**
     * Listar productos con paginación
     */
    public function index(Request $request)
    {
        $query = Producto::activos()
            ->with(['categoria', 'promocion', 'variantes.imagenes']);

        // Filtros
        if ($request->has('categoria')) {
            $query->where('id_categoria', $request->categoria);
        }

        if ($request->has('talla') && !empty($request->talla)) {
            $query->whereHas('variantes', function($q) use ($request) {
                $q->where('talla', $request->talla)
                  ->where('stock', '>', 0);
            });
        }

        if ($request->has('color') && !empty($request->color)) {
            $query->whereHas('variantes', function($q) use ($request) {
                $q->where('color', $request->color)
                  ->where('stock', '>', 0);
            });
        }

        if ($request->has('precio_min') && $request->has('precio_max')) {
            $query->whereBetween('precio', [$request->precio_min, $request->precio_max]);
        }

        if ($request->has('busqueda') && !empty($request->busqueda)) {
            $query->buscar($request->busqueda);
        }

        // Solo productos con stock
        if ($request->boolean('con_stock', true)) {
            $query->conStock();
        }

        // Ordenamiento
        $orden = $request->get('orden', 'created_at');
        $direccion = $request->get('direccion', 'desc');

        $ordenPermitido = ['created_at', 'nombre_producto', 'precio', 'id_producto'];
        if (!in_array($orden, $ordenPermitido)) {
            $orden = 'created_at';
        }

        $query->orderBy($orden, $direccion);

        // Paginación
        $productos = $query->paginate($request->get('limit', 10));

        return response()->json([
            'success' => true,
            'data' => ProductoResource::collection($productos),
            'current_page' => $productos->currentPage(),
            'last_page' => $productos->lastPage(),
            'total' => $productos->total(),
        ]);
    }

    /**
     * Obtener un producto por ID
     */
    public function show($id)
    {
        $producto = Producto::activos()
            ->with(['categoria', 'promocion', 'variantes.imagenes'])
            ->find($id);

        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new ProductoResource($producto)
        ]);
    }

    /**
     * Obtener variantes de un producto
     */
    public function variantes($id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }

        $variantes = $producto->variantes()
            ->with('imagenes')            // 👈 NUEVO: eager loading
            ->where('stock', '>', 0)
            ->get();

        return response()->json([
            'success' => true,
            'data' => VarianteResource::collection($variantes)
        ]);
    }

    /**
     * Obtener productos recomendados
     */
    public function recomendados(Request $request)
    {
        try {
            $query = Producto::activos()
                ->with(['categoria', 'promocion', 'variantes.imagenes']);

            // Filtrar por oferta/promociones
            if ($request->has('en_oferta') && $request->en_oferta == 'true') {
                $query->enOferta();
            }

            // Filtrar por categoría si es necesario
            if ($request->has('categoria_id')) {
                $query->where('id_categoria', $request->categoria_id);
            }

            // Límite de resultados
            $limit = $request->get('limit', 10);

            // Orden aleatorio para "recomendados"
            $productos = $query->inRandomOrder()->limit($limit)->get();

            return response()->json([
                'success' => true,
                'data' => ProductoResource::collection($productos)
            ]);
        } catch (\Exception $e) {
            Log::error('Error en recomendados: ' . $e->getMessage());
            Log::error('Archivo: ' . $e->getFile() . ' Línea: ' . $e->getLine());

            return response()->json([
                'success' => false,
                'message' => 'Error al cargar productos recomendados: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener productos populares
     */
    public function populares(Request $request)
    {
        try {
            $query = Producto::activos()
                ->with(['categoria', 'promocion', 'variantes.imagenes']);

            // Filtrar por oferta/promociones
            if ($request->has('en_oferta') && $request->en_oferta == 'true') {
                $query->enOferta();
            }

            // Filtrar por categoría si es necesario
            if ($request->has('categoria_id')) {
                $query->where('id_categoria', $request->categoria_id);
            }

            // Límite de resultados
            $limit = $request->get('limit', 10);

            // Orden aleatorio para "populares"
            $productos = $query->inRandomOrder()->limit($limit)->get();

            return response()->json([
                'success' => true,
                'data' => ProductoResource::collection($productos)
            ]);

        } catch (\Exception $e) {
            Log::error('Error en populares: ' . $e->getMessage());
            Log::error('Archivo: ' . $e->getFile() . ' Línea: ' . $e->getLine());

            return response()->json([
                'success' => false,
                'message' => 'Error al cargar productos populares: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener productos en oferta
     */
    public function ofertas(Request $request)
    {
        try {
            $query = Producto::activos()
                ->enOferta()
                ->with(['categoria', 'promocion', 'variantes.imagenes'])
                ->conStock();

            $productos = $query->paginate($request->get('limit', 10));

            return response()->json([
                'success' => true,
                'data' => ProductoResource::collection($productos),
                'current_page' => $productos->currentPage(),
                'last_page' => $productos->lastPage(),
                'total' => $productos->total(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error en ofertas: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al cargar productos en oferta'
            ], 500);
        }
    }

    /**
     * Buscar productos
     */
    public function buscar(Request $request)
    {
        try {
            $request->validate([
                'q' => 'required|string|min:2'
            ]);

            $query = Producto::activos()
                ->buscar($request->q)
                ->with(['categoria', 'promocion', 'variantes.imagenes'])
                ->conStock();

            $productos = $query->paginate($request->get('limit', 10));

            return response()->json([
                'success' => true,
                'data' => ProductoResource::collection($productos),
                'current_page' => $productos->currentPage(),
                'last_page' => $productos->lastPage(),
                'total' => $productos->total(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error en buscar: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda'
            ], 500);
        }
    }

    /**
     * Filtrar productos por talla
     */
    public function porTalla(Request $request, $talla)
    {
        try {
            $query = Producto::activos()
                ->whereHas('variantes', function($q) use ($talla) {
                    $q->where('talla', $talla)
                      ->where('stock', '>', 0);
                })
                ->with(['categoria', 'promocion', 'variantes.imagenes']);

            $productos = $query->paginate($request->get('limit', 10));

            return response()->json([
                'success' => true,
                'data' => ProductoResource::collection($productos),
                'current_page' => $productos->currentPage(),
                'last_page' => $productos->lastPage(),
                'total' => $productos->total(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error en porTalla: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al filtrar por talla'
            ], 500);
        }
    }

    /**
     * Filtrar productos por color
     */
    public function porColor(Request $request, $color)
    {
        try {
            $query = Producto::activos()
                ->whereHas('variantes', function($q) use ($color) {
                    $q->where('color', $color)
                      ->where('stock', '>', 0);
                })
                ->with(['categoria', 'promocion', 'variantes.imagenes']);

            $productos = $query->paginate($request->get('limit', 10));

            return response()->json([
                'success' => true,
                'data' => ProductoResource::collection($productos),
                'current_page' => $productos->currentPage(),
                'last_page' => $productos->lastPage(),
                'total' => $productos->total(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error en porColor: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al filtrar por color'
            ], 500);
        }
    }

    /**
     * Filtrar productos por rango de precio
     */
    public function porRangoPrecio(Request $request)
    {
        try {
            $request->validate([
                'min' => 'required|numeric|min:0',
                'max' => 'required|numeric|min:0|gt:min',
            ]);

            $query = Producto::activos()
                ->whereBetween('precio', [$request->min, $request->max])
                ->with(['categoria', 'promocion', 'variantes.imagenes'])
                ->conStock();

            $productos = $query->paginate($request->get('limit', 10));

            return response()->json([
                'success' => true,
                'data' => ProductoResource::collection($productos),
                'current_page' => $productos->currentPage(),
                'last_page' => $productos->lastPage(),
                'total' => $productos->total(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error en porRangoPrecio: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al filtrar por rango de precio'
            ], 500);
        }
    }
}