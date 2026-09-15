<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        //principal
        $totalVentas = DB::table('pedido')
            ->whereNotIn('estado_pedido', ['Cancelado'])
            ->sum('total_pedido') ?? 0;

        $ventasMesActual = DB::table('pedido')
            ->whereNotIn('estado_pedido', ['Cancelado'])
            ->whereMonth('fecha_pedido', now()->month)
            ->whereYear('fecha_pedido', now()->year)
            ->sum('total_pedido') ?? 0;

        $ventasMesAnterior = DB::table('pedido')
            ->whereNotIn('estado_pedido', ['Cancelado'])
            ->whereMonth('fecha_pedido', now()->subMonth()->month)
            ->whereYear('fecha_pedido', now()->subMonth()->year)
            ->sum('total_pedido') ?? 0;

        $crecimientoVentas = $ventasMesAnterior > 0
            ? round((($ventasMesActual - $ventasMesAnterior) / $ventasMesAnterior) * 100, 1)
            : 0;

        $pedidosPendientes = DB::table('pedido')->where('estado_pedido', 'Pendiente')->count();
        $pedidosHoy        = DB::table('pedido')->whereDate('fecha_pedido', today())->count();
        $totalClientes     = DB::table('usuario')->where('id_rol', 2)->count();
        $totalProductos    = DB::table('producto')->where('estado_producto', 1)->count();

        $stockBajo = DB::table('producto')
            ->join('producto_variante', 'producto.id_producto', '=', 'producto_variante.id_producto')
            ->select('producto.id_producto')
            ->groupBy('producto.id_producto')
            ->havingRaw('SUM(producto_variante.stock) > 0 AND SUM(producto_variante.stock) <= 10')
            ->get()
            ->count();

        $sinStock = DB::table('producto')
            ->join('producto_variante', 'producto.id_producto', '=', 'producto_variante.id_producto')
            ->select('producto.id_producto')
            ->groupBy('producto.id_producto')
            ->havingRaw('SUM(producto_variante.stock) = 0')
            ->get()
            ->count();

        // ─── Ventas últimos 30 días
        $ventasRaw = DB::table('pedido')
            ->selectRaw('DATE(fecha_pedido) as dia, SUM(total_pedido) as total, COUNT(*) as cantidad')
            ->whereNotIn('estado_pedido', ['Cancelado'])
            ->whereBetween('fecha_pedido', [now()->subDays(29)->startOfDay(), now()->endOfDay()])
            ->groupBy('dia')
            ->orderBy('dia')
            ->get()
            ->keyBy('dia');

        $diasCompletos = collect();
        for ($i = 29; $i >= 0; $i--) {
            $fecha = now()->subDays($i)->format('Y-m-d');
            $reg = $ventasRaw->get($fecha);
            $diasCompletos->push([
                'dia'      => Carbon::parse($fecha)->format('d M'),
                'total'    => $reg ? (float) $reg->total : 0,
                'cantidad' => $reg ? (int) $reg->cantidad : 0,
            ]);
        }

        // ─── Pedidos por estado
        $pedidosPorEstado = DB::table('pedido')
            ->selectRaw('estado_pedido, COUNT(*) as total')
            ->groupBy('estado_pedido')
            ->pluck('total', 'estado_pedido');

        // ─── Ventas por categoría
        $ventasPorCategoria = DB::table('detalle_pedido as dp')
            ->join('pedido as p',             'p.id_pedido',    '=', 'dp.id_pedido')
            ->join('producto_variante as pv', 'pv.id_variante', '=', 'dp.id_variante')
            ->join('producto as pr',          'pr.id_producto', '=', 'pv.id_producto')
            ->join('categoria as c',          'c.id_categoria', '=', 'pr.id_categoria')
            ->whereNotIn('p.estado_pedido', ['Cancelado'])
            ->selectRaw('c.nombre_categoria, SUM(dp.subtotal) as total')
            ->groupBy('c.id_categoria', 'c.nombre_categoria')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        // ─── Top 5 productos más vendidos
        $topProductos = DB::table('detalle_pedido as dp')
            ->join('pedido as p',             'p.id_pedido',    '=', 'dp.id_pedido')
            ->join('producto_variante as pv', 'pv.id_variante', '=', 'dp.id_variante')
            ->join('producto as pr',          'pr.id_producto', '=', 'pv.id_producto')
            ->leftJoin('producto_variante_imagen as pvi', 'pvi.id_variante', '=', 'pv.id_variante')
            ->whereNotIn('p.estado_pedido', ['Cancelado'])
            ->selectRaw('
                pr.id_producto,
                pr.nombre_producto,
                MIN(pvi.imagen) as imagen,
                SUM(dp.cantidad) as unidades,
                SUM(dp.subtotal) as ingresos
            ')
            ->groupBy('pr.id_producto', 'pr.nombre_producto')
            ->orderByDesc('unidades')
            ->limit(5)
            ->get();

        // ─── Últimos 8 pedidos
        $ultimosPedidos = DB::table('pedido as p')
            ->leftJoin('usuario as u', 'u.id_usuario', '=', 'p.id_usuario')
            ->select(
                'p.id_pedido',
                'p.numero_pedido',
                'p.total_pedido',
                'p.estado_pedido',
                'p.fecha_pedido',
                'p.created_at',
                'u.nombres',
                'u.apellidos'
            )
            ->orderByDesc('p.created_at')
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact(
            'totalVentas',
            'ventasMesActual',
            'ventasMesAnterior',
            'crecimientoVentas',
            'pedidosPendientes',
            'pedidosHoy',
            'totalClientes',
            'totalProductos',
            'stockBajo',
            'sinStock',
            'diasCompletos',
            'pedidosPorEstado',
            'ventasPorCategoria',
            'topProductos',
            'ultimosPedidos'
        ));
    }
}