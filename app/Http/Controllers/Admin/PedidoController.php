<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function index(Request $request)
    {
        $query = Pedido::with(['usuario', 'tipoEntrega', 'departamento']);

        // Buscador por N° pedido, nombre, apellido o correo del cliente
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('numero_pedido', 'like', "%{$buscar}%")
                  ->orWhereHas('usuario', function ($q2) use ($buscar) {
                      $q2->where('nombres', 'like', "%{$buscar}%")
                         ->orWhere('apellidos', 'like', "%{$buscar}%")
                         ->orWhere('correo', 'like', "%{$buscar}%");
                  });
            });
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado_pedido', $request->estado);
        }

        // ✅ NUEVO: Filtro por tipo de entrega (1 = Retiro en tienda, 2 = Envío a provincia)
        if ($request->filled('tipo_entrega')) {
            $query->where('id_tipo_entrega', $request->tipo_entrega);
        }

        // Filtro por fecha específica
        if ($request->filled('fecha')) {
            $query->whereDate('created_at', $request->fecha);
        }

        $pedidos = $query->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.pedidos.index', compact('pedidos'));
    }

    public function show($id)
    {
        $pedido = Pedido::with([
            'usuario',
            'tipoEntrega',
            'departamento',
            'detalles.variante.producto',
            'detalles.variante.imagenes',
        ])->findOrFail($id);

        return view('admin.pedidos.show', compact('pedido'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'estado_pedido' => 'required|in:Pendiente,En camino,Listo para recoger,Entregado',
        ]);

        $pedido = Pedido::findOrFail($id);

        $data = ['estado_pedido' => $request->estado_pedido];

        // Solo actualizar fecha estimada si viene con valor
        if ($request->filled('fecha_entrega_estimada')) {
            $data['fecha_entrega_estimada'] = $request->fecha_entrega_estimada;
        }

        // Auto-registrar fecha de envío
        if ($request->estado_pedido === 'En camino' && !$pedido->fecha_envio) {
            $data['fecha_envio'] = now();
        }

        // Auto-registrar fecha real de entrega
        if ($request->estado_pedido === 'Entregado' && !$pedido->fecha_entrega_real) {
            $data['fecha_entrega_real'] = now();
        }

        $pedido->update($data);

        return back()->with('success', "Pedido #{$pedido->numero_pedido} actualizado a '{$request->estado_pedido}'.");
    }
}