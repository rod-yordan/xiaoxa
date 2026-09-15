<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\ProductoVariante;
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

        $productos = Producto::where('nombre_producto', 'LIKE', '%' . $buscar . '%')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.productos.index', compact('productos'));
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
            'imagen' => 'required|image|mimes:jpg,jpeg,png,webp',
            'variantes' => 'required|array|min:1',
            'variantes.*.talla' => 'required|string|max:50',
            'variantes.*.stock' => 'required|integer|min:0',
            'variantes.*.sku' => 'nullable|string|max:50|distinct|unique:producto_variante,sku',
        ]);

        $combinaciones = collect($request->variantes)->map(function ($v) {
            return strtolower(trim($v['talla'])) . '-' . strtolower(trim($v['color'] ?? ''));
        });

        if ($combinaciones->duplicates()->isNotEmpty()) {
            return back()->withErrors(['variantes' => 'No puedes repetir la misma combinación de Talla y Color.'])->withInput();
        }

        $producto = Producto::create($request->only([
            'nombre_producto',
            'descripcion',
            'precio',
            'precio_oferta',
            'marca',
            'id_categoria'
        ]));

        // ✅ Imagen principal guardada en storage/app/public/productos/
        if ($request->hasFile('imagen')) {
            $archivo = $request->file('imagen');
            $nombre = uniqid() . '.' . $archivo->getClientOriginalExtension();
            $archivo->move(storage_path('app/public/productos'), $nombre);
            $producto->update(['imagen' => $nombre]);
        }

        // ✅ Galería guardada en storage/app/public/productos/
        if ($request->hasFile('galeria')) {
            $galeria = [];
            foreach ($request->file('galeria') as $foto) {
                $galeria[] = $this->cargarArchivo($foto);
            }
            $producto->update(['galeria' => $galeria]);
        }

        foreach ($request->variantes as $v) {
            $producto->variantes()->create([
                'talla' => $v['talla'],
                'color' => $v['color'] ?? null,
                'stock' => $v['stock'],
                'sku' => $v['sku'] ?? strtoupper(substr($producto->nombre_producto, 0, 3)) . '-' . uniqid(),
            ]);
        }

        try {
            $categoriaNombre = $producto->categoria->nombre_categoria ?? '';
            $this->pusherBeams->enviarLanzamiento(
                $producto->nombre_producto,
                $categoriaNombre
            );
        } catch (\Exception $e) {
            // Silencioso
        }

        return redirect()->route('admin.productos.index')->with('success', 'Producto creado exitosamente.');
    }

    public function edit($id)
    {
        $producto = Producto::with('variantes')->findOrFail($id);

        $categorias = Categoria::all();
        $promociones = Promocion::where('estado_promocion', 1)->get();

        return view('admin.productos.edit', compact('producto', 'categorias', 'promociones'));
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $teniaOfertaAntes = !is_null($producto->precio_oferta) && $producto->precio_oferta > 0;
        $precioOfertaAntes = $producto->precio_oferta;

        $request->validate([
            'nombre_producto' => 'required|string|max:150',
            'precio' => 'required|numeric|min:0',
            'precio_oferta' => 'nullable|numeric|min:0|lt:precio',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'galeria.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'variantes' => 'required|array|min:1',
            'variantes.*.talla' => 'required|string|max:50',
            'variantes.*.stock' => 'required|integer|min:0',
            'variantes.*.sku' => 'required|string|max:50|distinct',
        ]);

        $combinaciones = collect($request->variantes)->map(fn($v) => strtolower(trim($v['talla'])) . '-' . strtolower(trim($v['color'] ?? '')));
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

        $datos = $request->only(['nombre_producto', 'descripcion', 'precio', 'precio_oferta', 'marca', 'estado_producto', 'id_categoria', 'id_promocion']);

        // ✅ Guardar nueva imagen principal
        if ($request->hasFile('imagen')) {
            // Eliminar la imagen anterior
            if ($producto->imagen && File::exists(storage_path('app/public/productos/' . $producto->imagen))) {
                File::delete(storage_path('app/public/productos/' . $producto->imagen));
            }
            $datos['imagen'] = $this->cargarArchivo($request->file('imagen'));
        }

        $galeriaActual = $producto->galeria ?? [];

        // ✅ Eliminar fotos de la galería
        if ($request->has('galeria_eliminar')) {
            foreach ($request->galeria_eliminar as $fotoEliminar) {
                File::delete(storage_path('app/public/productos/' . $fotoEliminar));
            }
            $galeriaActual = array_diff($galeriaActual, $request->galeria_eliminar);
        }

        // ✅ Agregar nuevas fotos a la galería
        if ($request->hasFile('galeria')) {
            foreach ($request->file('galeria') as $foto) {
                $galeriaActual[] = $this->cargarArchivo($foto);
            }
        }
        $datos['galeria'] = array_values($galeriaActual);

        $producto->update($datos);

        $idsEnviados = [];
        foreach ($request->variantes as $v) {
            $variante = $producto->variantes()->updateOrCreate(
                ['id_variante' => $v['id_variante'] ?? null],
                [
                    'talla' => $v['talla'],
                    'color' => $v['color'] ?? null,
                    'stock' => $v['stock'],
                    'sku'   => $v['sku'],
                ]
            );
            $idsEnviados[] = $variante->id_variante;
        }

        $producto->variantes()->whereNotIn('id_variante', $idsEnviados)->delete();

        $tieneOfertaAhora = !is_null($request->precio_oferta) && $request->precio_oferta > 0;

        if ($tieneOfertaAhora) {
            if (!$teniaOfertaAntes || $precioOfertaAntes != $request->precio_oferta) {
                try {
                    $categoriaNombre = $producto->categoria->nombre_categoria ?? '';

                    $this->pusherBeams->enviarOferta(
                        $producto->nombre_producto,
                        $request->precio_oferta,
                        $categoriaNombre
                    );
                } catch (\Exception $e) {
                }
            }
        }

        return redirect()->route('admin.productos.index')->with('success', 'Producto actualizado correctamente');
    }

    // ✅ Guardar archivo en storage/app/public/productos/
    private function cargarArchivo($file)
    {
        $nombre = time() . '_' . $file->getClientOriginalName();
        $file->move(storage_path('app/public/productos'), $nombre);
        return $nombre;
    }

    public function destroy($id)
    {
        $producto = Producto::with('variantes')->findOrFail($id);

        if ($producto->variantes()->where('stock', '>', 0)->exists()) {
            return redirect()->back()->with('error', 'No se puede eliminar un producto con stock.');
        }

        // ✅ Eliminar archivos desde storage
        if ($producto->imagen && File::exists(storage_path('app/public/productos/' . $producto->imagen))) {
            File::delete(storage_path('app/public/productos/' . $producto->imagen));
        }
        if ($producto->galeria) {
            foreach ($producto->galeria as $img) {
                if (File::exists(storage_path('app/public/productos/' . $img))) {
                    File::delete(storage_path('app/public/productos/' . $img));
                }
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