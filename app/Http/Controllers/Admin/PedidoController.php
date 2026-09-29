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

        // Filtro por tipo de entrega (1 = Retiro en tienda, 2 = Envío a provincia)
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
            'estado_pedido'    => 'required|in:Pendiente,En camino,Listo para recoger,Entregado',
            'fecha_entrega'    => 'nullable|date',
            'direccion_entrega'=> 'nullable|string|max:500',
            'accion'           => 'nullable|in:guardar,estado',
        ]);

        $pedido = Pedido::findOrFail($id);

        $data = ['estado_pedido' => $request->estado_pedido];

        // Actualizar fecha de entrega si viene con valor
        if ($request->filled('fecha_entrega')) {
            $data['fecha_entrega'] = $request->fecha_entrega;
        }

        // Actualizar dirección de entrega (incluso si viene vacío, para permitir borrarlo)
        if ($request->has('direccion_entrega')) {
            $data['direccion_entrega'] = $request->input('direccion_entrega');
        }

        $pedido->update($data);

        // Mensaje dinámico según la acción (sin #)
        if ($request->input('accion') === 'estado') {
            $mensaje = "Pedido {$pedido->numero_pedido} actualizado a '{$request->estado_pedido}'.";
        } else {
            $mensaje = "Pedido {$pedido->numero_pedido} actualizado correctamente.";
        }

        return back()->with('success', $mensaje);
    }
}