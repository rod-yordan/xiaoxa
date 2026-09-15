<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Carrito;
use App\Models\DetalleCarrito;
use App\Models\ProductoVariante;

class CarritoController extends Controller
{

    // AGREGAR
    public function add(Request $request, $id)
    {
        $id_variante = $request->id_variante;
        $cantidad    = max(1, (int) ($request->cantidad ?? 1)); // ✅ NUEVO: cantidad mínima 1

        $variante = ProductoVariante::with('producto')->findOrFail($id_variante);

        if ($variante->stock < $cantidad) {
            return redirect()->back()->with('error', 'Stock no disponible');
        }

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

                $detalle->cantidad += $cantidad; // ✅ Sumar la cantidad enviada
                $detalle->save();

            } else {

                DetalleCarrito::create([
                    'id_carrito' => $carrito->id_carrito,
                    'id_variante' => $id_variante,
                    'cantidad' => $cantidad // ✅ Usar la cantidad enviada
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
                    "id_variante" => $id_variante,
                    "nombre"      => $variante->producto->nombre_producto,
                    "cantidad"    => $cantidad,
                    "precio"      => $variante->producto->precio_oferta ?? $variante->producto->precio,
                    "imagen"      => $variante->producto->imagen,
                    "talla"       => $variante->talla,
                    "color"       => $variante->color
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

            $carrito = Carrito::with('detalles.variante.producto')
                ->where('id_usuario', Auth::id())
                ->first();

            if ($carrito) {

                foreach ($carrito->detalles as $detalle) {

                    $variante = $detalle->variante;
                    $producto = $variante->producto;

                    $items[$detalle->id_variante] = [
                        "id_variante" => $detalle->id_variante,
                        "nombre"      => $producto->nombre_producto,
                        "cantidad"    => $detalle->cantidad,
                        "precio"      => $producto->precio_oferta ?? $producto->precio,
                        "imagen"      => $producto->imagen,
                        "talla"       => $variante->talla,
                        "color"       => $variante->color
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
            $carrito = Carrito::where('id_usuario', Auth::id())->first();

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