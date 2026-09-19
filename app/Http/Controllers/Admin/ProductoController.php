<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\ProductoVariante;
use App\Models\ProductoVarianteImagen;
use App\Models\Categoria;
use App\Models\Promocion;
use Illuminate\Support\Facades\File;
use App\Services\PusherBeamsService;

class ProductoController extends Controller
{
    protected $pusherBeams;

    public function __construct(PusherBeamsService $pusherBeams)
    {
        $this->pusherBeams = $pusherBeams;
    }

    public function index(Request $request)
    {
        $buscar = $request->get('buscar');
        $perPage = $request->get('perPage', 10);

        $query = Producto::with(['variantes.imagenes'])
            ->where('nombre_producto', 'LIKE', '%' . $buscar . '%');

        // Filtro por categoría
        if ($request->filled('categoria')) {
            $query->where('id_categoria', $request->categoria);
        }

        // Filtro por marca
        if ($request->filled('marca')) {
            $query->where('marca', $request->marca);
        }

        // Filtro por stock
        if ($request->filled('stock')) {
            switch ($request->stock) {
                case 'agotado':
                    $query->whereDoesntHave('variantes', fn($q) => $q->where('stock', '>', 0));
                    break;
                case 'bajo':
                    $query->whereHas('variantes', fn($q) => $q->where('stock', '>', 0))
                          ->withSum('variantes as stock_total', 'stock')
                          ->having('stock_total', '<=', 10);
                    break;
                case 'disponible':
                    $query->whereHas('variantes', fn($q) => $q->where('stock', '>', 0))
                          ->withSum('variantes as stock_total', 'stock')
                          ->having('stock_total', '>', 10);
                    break;
            }
        }

        $productos = $query->paginate($perPage)->withQueryString();

        $categoriasFiltro = Categoria::orderBy('nombre_categoria')->get();
        $marcasFiltro = Producto::whereNotNull('marca')
            ->where('marca', '!=', '')
            ->distinct()
            ->orderBy('marca')
            ->pluck('marca');

        return view('admin.productos.index', compact('productos', 'categoriasFiltro', 'marcasFiltro'));
    }

    public function create()
    {
        $categorias = Categoria::all();
        return view('admin.productos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_producto' => 'required|string|max:150',
            'precio' => 'required|numeric|min:0',
            'precio_oferta' => 'nullable|numeric|min:0|lt:precio',
            'variantes' => 'required|array|min:1',
            'variantes.*.talla' => 'required|string|max:50',
            'variantes.*.stock' => 'required|integer|min:0',
            'variantes.*.sku' => 'nullable|string|max:50|distinct|unique:producto_variante,sku',
            'variantes.*.imagenes' => 'nullable|array',
            'variantes.*.imagenes.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        // Validar combinaciones duplicadas
        $combinaciones = collect($request->variantes)->map(fn($v) =>
            strtolower(trim($v['talla'])) . '-' . strtolower(trim($v['color'] ?? ''))
        );
        if ($combinaciones->duplicates()->isNotEmpty()) {
            return back()->withErrors(['variantes' => 'No puedes repetir la misma combinación de Talla y Color.'])->withInput();
        }

        // Detalles: filtrar vacíos
        $detalles = collect($request->detalles ?? [])
            ->filter(fn($d) => !empty(trim($d)))
            ->values()
            ->toArray();

        // Crear producto (SIN descripcion)
        $producto = Producto::create(array_merge($request->only([
            'nombre_producto', 'precio', 'precio_oferta', 'marca', 'id_categoria'
        ]), [
            'detalles' => $detalles
        ]));

        // Crear variantes + imágenes (imágenes opcionales)
        foreach ($request->variantes as $index => $v) {
            $variante = $producto->variantes()->create([
                'talla' => $v['talla'],
                'color' => $v['color'] ?? null,
                'color_hex' => $v['color_hex'] ?? null,
                'stock' => $v['stock'],
                'sku' => $v['sku'] ?? strtoupper(substr($producto->nombre_producto, 0, 3)) . '-' . uniqid(),
            ]);

            // Guardar imágenes de la variante (solo si las hay)
            if (isset($v['imagenes']) && is_array($v['imagenes'])) {
                foreach ($v['imagenes'] as $orden => $foto) {
                    if (!$foto instanceof \Illuminate\Http\UploadedFile) {
                        continue;
                    }
                    $nombre = $this->cargarArchivo($foto, 'variantes');
                    ProductoVarianteImagen::create([
                        'id_variante' => $variante->id_variante,
                        'imagen' => $nombre,
                        'orden' => $orden
                    ]);
                }
            }
        }

        try {
            $categoriaNombre = $producto->categoria->nombre_categoria ?? '';
            $this->pusherBeams->enviarLanzamiento($producto->nombre_producto, $categoriaNombre);
        } catch (\Exception $e) {}

        return redirect()->route('admin.productos.index')->with('success', 'Producto creado exitosamente.');
    }

    public function edit($id)
    {
        $producto = Producto::with(['variantes.imagenes'])->findOrFail($id);
        $categorias = Categoria::all();
        $promociones = Promocion::where('estado_promocion', 1)->get();

        return view('admin.productos.edit', compact('producto', 'categorias', 'promociones'));
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::with(['variantes.imagenes'])->findOrFail($id);

        $teniaOfertaAntes = !is_null($producto->precio_oferta) && $producto->precio_oferta > 0;
        $precioOfertaAntes = $producto->precio_oferta;

        $request->validate([
            'nombre_producto' => 'required|string|max:150',
            'precio' => 'required|numeric|min:0',
            'precio_oferta' => 'nullable|numeric|min:0|lt:precio',
            'variantes' => 'required|array|min:1',
            'variantes.*.talla' => 'required|string|max:50',
            'variantes.*.stock' => 'required|integer|min:0',
            'variantes.*.sku' => 'required|string|max:50|distinct',
            'variantes.*.imagenes' => 'nullable|array',
            'variantes.*.imagenes.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $combinaciones = collect($request->variantes)->map(fn($v) =>
            strtolower(trim($v['talla'])) . '-' . strtolower(trim($v['color'] ?? ''))
        );
        if ($combinaciones->duplicates()->isNotEmpty()) {
            return back()->withErrors(['variantes' => 'Hay combinaciones de Talla y Color duplicadas.'])->withInput();
        }

        foreach ($request->variantes as $index => $v) {
            $exists = ProductoVariante::where('sku', $v['sku'])
                ->where('id_producto', '!=', $producto->id_producto)
                ->exists();
            if ($exists) {
                return back()->withErrors(["variantes.$index.sku" => "El SKU '{$v['sku']}' ya está en uso."])->withInput();
            }
        }

        // Detalles
        $detalles = collect($request->detalles ?? [])
            ->filter(fn($d) => !empty(trim($d)))
            ->values()
            ->toArray();

        // SIN descripcion
        $datos = $request->only(['nombre_producto', 'precio', 'precio_oferta', 'marca', 'estado_producto', 'id_categoria', 'id_promocion']);
        $datos['detalles'] = $detalles;

        $producto->update($datos);

        $idsEnviados = [];

        foreach ($request->variantes as $index => $v) {
            $variante = $producto->variantes()->updateOrCreate(
                ['id_variante' => $v['id_variante'] ?? null],
                [
                    'talla' => $v['talla'],
                    'color' => $v['color'] ?? null,
                    'color_hex' => $v['color_hex'] ?? null,
                    'stock' => $v['stock'],
                    'sku'   => $v['sku'],
                ]
            );
            $idsEnviados[] = $variante->id_variante;

            // Eliminar imágenes marcadas
            if (isset($v['imagenes_eliminar']) && is_array($v['imagenes_eliminar'])) {
                foreach ($v['imagenes_eliminar'] as $idImagen) {
                    $img = ProductoVarianteImagen::find($idImagen);
                    if ($img && $img->id_variante == $variante->id_variante) {
                        $ruta = storage_path('app/public/variantes/' . $img->imagen);
                        if (File::exists($ruta)) File::delete($ruta);
                        $img->delete();
                    }
                }
            }

            // Agregar nuevas imágenes (opcional)
            if (isset($v['imagenes']) && is_array($v['imagenes'])) {
                $ordenMax = $variante->imagenes()->max('orden') ?? -1;
                foreach ($v['imagenes'] as $foto) {
                    if ($foto instanceof \Illuminate\Http\UploadedFile) {
                        $ordenMax++;
                        $nombre = $this->cargarArchivo($foto, 'variantes');
                        ProductoVarianteImagen::create([
                            'id_variante' => $variante->id_variante,
                            'imagen' => $nombre,
                            'orden' => $ordenMax
                        ]);
                    }
                }
            }
        }

        // Eliminar variantes que ya no están
        $variantesAEliminar = $producto->variantes()->whereNotIn('id_variante', $idsEnviados)->get();
        foreach ($variantesAEliminar as $vElim) {
            foreach ($vElim->imagenes as $img) {
                $ruta = storage_path('app/public/variantes/' . $img->imagen);
                if (File::exists($ruta)) File::delete($ruta);
                $img->delete();
            }
            $vElim->delete();
        }

        // Pusher Beams
        $tieneOfertaAhora = !is_null($request->precio_oferta) && $request->precio_oferta > 0;
        if ($tieneOfertaAhora && (!$teniaOfertaAntes || $precioOfertaAntes != $request->precio_oferta)) {
            try {
                $categoriaNombre = $producto->categoria->nombre_categoria ?? '';
                $this->pusherBeams->enviarOferta($producto->nombre_producto, $request->precio_oferta, $categoriaNombre);
            } catch (\Exception $e) {}
        }

        return redirect()->route('admin.productos.index')->with('success', 'Producto actualizado correctamente');
    }

    private function cargarArchivo($file, $carpeta = 'productos')
    {
        $nombre = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
        $file->move(storage_path('app/public/' . $carpeta), $nombre);
        return $nombre;
    }

    public function destroy($id)
    {
        $producto = Producto::with(['variantes.imagenes'])->findOrFail($id);

        if ($producto->variantes()->where('stock', '>', 0)->exists()) {
            return redirect()->back()->with('error', 'No se puede eliminar un producto con stock.');
        }

        foreach ($producto->variantes as $variante) {
            foreach ($variante->imagenes as $img) {
                $ruta = storage_path('app/public/variantes/' . $img->imagen);
                if (File::exists($ruta)) File::delete($ruta);
            }
        }

        $idCat = $producto->id_categoria;
        $producto->delete();

        $this->actualizarEstadoCategoria($idCat);

        return redirect()->route('admin.productos.index')->with('success', 'Producto eliminado.');
    }

    private function actualizarEstadoCategoria($id_categoria)
    {
        $categoria = Categoria::find($id_categoria);
        if ($categoria) {
            $tieneStock = $categoria->productos()
                ->whereHas('variantes', fn($q) => $q->where('stock', '>', 0))
                ->exists();
            $categoria->estado_categoria = $tieneStock ? 1 : 0;
            $categoria->save();
        }
    }
}