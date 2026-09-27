<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class PedidoUsuarioController extends Controller
{
    public function index()
    {
        $pedidos = auth()->user()
            ->pedidos()
            ->with([
                'detalles.variante.producto',
                'detalles.variante.imagenes',
                'tipoEntrega',
                'departamento',
            ])
            ->orderBy('fecha_pedido', 'desc')
            ->paginate(5);

        return view('pedidos.index', compact('pedidos'));
    }
}