<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Carrito;
use App\Models\DetalleCarrito;
use App\Models\ProductoVariante;

class CarritoController extends Controller
{
    /**
     * Devuelve el nombre de archivo de la primera imagen
     * de la variante; si no tiene, cae al producto.
     */
    private function obtenerImagen($variante)
    {
        $primeraImagen = $variante->imagenes->first();
        if ($primeraImagen && !empty($primeraImagen->imagen)) {
            return $primeraImagen->imagen;
        }
        return $variante->producto->imagen ?? null;
    }

    /**
     * Devuelve el porcentaje de descuento calculado,
     * o null si el producto no está en oferta.
     */
    private function obtenerDescuento($producto)
    {
        if (!$producto->precio_oferta || $producto->precio_oferta <= 0) {
            return null;
        }
        if (!$producto->precio || $producto->precio <= 0) {
            return null;
        }
        return round((1 - ($producto->precio_oferta / $producto->precio)) * 100);
    }

    // AGREGAR
    public function add(Request $request, $id)
    {
        $id_variante = $request->id_variante;
        $cantidad    = max(1, (int) ($request->cantidad ?? 1));

        $variante = ProductoVariante::with(['producto', 'imagenes'])->findOrFail($id_variante);

        if ($variante->stock < $cantidad) {
            return redirect()->back()->with('error', 'Stock no disponible');
        }

        $imagen = $this->obtenerImagen($variante);

        // USUARIO LOGUEADO → BD
        if (Auth::check()) {

            $carrito = Carrito::firstOrCreate([
                'id_usuario' => Auth::id()
            ]);

            $detalle = DetalleCarrito::where('id_carrito', $carrito->id_carrito)
                ->where('id_variante', $id_variante)
                ->first();

            if ($detalle) {
                if ($detalle->cantidad + $cantidad > $variante->stock) {
                    return redirect()->back()->with('error', 'Stock no disponible');
                }
                $detalle->cantidad += $cantidad;
                $detalle->save();
            } else {
                DetalleCarrito::create([
                    'id_carrito'  => $carrito->id_carrito,
                    'id_variante' => $id_variante,
                    'cantidad'    => $cantidad
                ]);
            }
        }
        // INVITADO → SESSION
        else {

            $carrito = session()->get('carrito', []);

            if (isset($carrito[$id_variante])) {
                $carrito[$id_variante]['cantidad'] += $cantidad;
            } else {
                $carrito[$id_variante] = [
                    "id_variante"   => $id_variante,
                    "id_producto"   => $variante->producto->id_producto,
                    "nombre"        => $variante->producto->nombre_producto,
                    "cantidad"      => $cantidad,
                    "precio"        => $variante->producto->precio_oferta ?? $variante->producto->precio,
                    "precio_normal" => $variante->producto->precio, // ✅ NUEVO
                    "precio_oferta" => $variante->producto->precio_oferta,
                    "descuento"     => $this->obtenerDescuento($variante->producto),
                    "imagen"        => $imagen,
                    "talla"         => $variante->talla,
                    "color"         => $variante->color
                ];
            }

            session()->put('carrito', $carrito);
        }

        return redirect()->route('carrito.index');
    }

    // MOSTRAR
    public function index()
    {
        $items = [];
        $total = 0;

        if (Auth::check()) {

            $carrito = Carrito::with(['detalles.variante.producto', 'detalles.variante.imagenes'])
                ->where('id_usuario', Auth::id())
                ->first();

            if ($carrito) {
                foreach ($carrito->detalles as $detalle) {

                    $variante = $detalle->variante;
                    $producto = $variante->producto;

                    $items[$detalle->id_variante] = [
                        "id_variante"   => $detalle->id_variante,
                        "id_producto"   => $producto->id_producto,
                        "nombre"        => $producto->nombre_producto,
                        "cantidad"      => $detalle->cantidad,
                        "precio"        => $producto->precio_oferta ?? $producto->precio,
                        "precio_normal" => $producto->precio, // ✅ NUEVO
                        "precio_oferta" => $producto->precio_oferta,
                        "descuento"     => $this->obtenerDescuento($producto),
                        "imagen"        => $this->obtenerImagen($variante),
                        "talla"         => $variante->talla,
                        "color"         => $variante->color
                    ];

                    $total += $items[$detalle->id_variante]['precio'] * $detalle->cantidad;
                }
            }
        } else {

            $items = session()->get('carrito', []);

            foreach ($items as $item) {
                $total += $item['precio'] * $item['cantidad'];
            }
        }

        return view('carrito.index', compact('items', 'total'));
    }

    // AUMENTAR
    public function aumentar($id_variante)
    {
        if (Auth::check()) {
            $variante = ProductoVariante::findOrFail($id_variante);
            $carrito  = Carrito::where('id_usuario', Auth::id())->first();

            if ($carrito) {
                $detalle = DetalleCarrito::where('id_carrito', $carrito->id_carrito)
                    ->where('id_variante', $id_variante)
                    ->first();

                if ($detalle && $detalle->cantidad < $variante->stock) {
                    $detalle->cantidad++;
                    $detalle->save();
                }
            }
        } else {
            $carrito = session()->get('carrito', []);

            if (isset($carrito[$id_variante])) {
                $carrito[$id_variante]['cantidad']++;
                session()->put('carrito', $carrito);
            }
        }

        return redirect()->route('carrito.index');
    }

    // DISMINUIR
    public function disminuir($id_variante)
    {
        if (Auth::check()) {
            $carrito = Carrito::where('id_usuario', Auth::id())->first();

            if ($carrito) {
                $detalle = DetalleCarrito::where('id_carrito', $carrito->id_carrito)
                    ->where('id_variante', $id_variante)
                    ->first();

                if ($detalle) {
                    if ($detalle->cantidad > 1) {
                        $detalle->cantidad--;
                        $detalle->save();
                    } else {
                        $detalle->delete();
                    }
                }
            }
        } else {
            $carrito = session()->get('carrito', []);

            if (isset($carrito[$id_variante])) {
                if ($carrito[$id_variante]['cantidad'] > 1) {
                    $carrito[$id_variante]['cantidad']--;
                } else {
                    unset($carrito[$id_variante]);
                }
                session()->put('carrito', $carrito);
            }
        }

        return redirect()->route('carrito.index');
    }

    // ELIMINAR
    public function eliminar($id_variante)
    {
        if (Auth::check()) {
            $carrito = Carrito::where('id_usuario', Auth::id())->first();

            if ($carrito) {
                DetalleCarrito::where('id_carrito', $carrito->id_carrito)
                    ->where('id_variante', $id_variante)
                    ->delete();
            }
        } else {
            $carrito = session()->get('carrito', []);
            unset($carrito[$id_variante]);
            session()->put('carrito', $carrito);
        }

        return redirect()->route('carrito.index');
    }
}