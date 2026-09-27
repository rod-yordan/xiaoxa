<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\DetallePedido;
use App\Models\Pedido;
use App\Models\ProductoVariante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;

class PagoController extends Controller
{
    /**
     * MP redirige aquí cuando el pago fue aprobado.
     */
    public function exito(Request $request)
    {
        $pedido = null;

        if ($request->status === 'approved'
            && $request->payment_id
            && $request->external_reference) {

            $pedido = $this->crearPedidoSiNoExiste(
                $request->external_reference,
                (string) $request->payment_id
            );
        }

        return view('pagos.exito', compact('pedido'));
    }

    /**
     * MP redirige aquí cuando el pago fue rechazado.
     */
    public function fallo(Request $request)
    {
        if ($request->external_reference) {
            Cache::forget("checkout:{$request->external_reference}");
        }

        return view('pagos.fallo');
    }

    /**
     * MP redirige aquí cuando el pago quedó pendiente.
     */
    public function pendiente(Request $request)
    {
        return view('pagos.pendiente');
    }

    /**
     * Webhook: MP llama a esta URL desde sus servidores.
     */
    public function webhook(Request $request)
    {
        $paymentId = $request->input('data.id') ?? $request->input('id');

        if (!$paymentId) {
            return response()->json(['status' => 'ignored'], 200);
        }

        try {
            MercadoPagoConfig::setAccessToken(env('MP_ACCESS_TOKEN'));
            $client  = new PaymentClient();
            $payment = $client->get($paymentId);

            \Log::info('Webhook MP recibido', [
                'payment_id' => $paymentId,
                'status'     => $payment->status,
                'external'   => $payment->external_reference ?? null,
            ]);

            if ($payment->status === 'approved' && $payment->external_reference) {
                $this->crearPedidoSiNoExiste(
                    $payment->external_reference,
                    (string) $payment->id
                );
            }

        } catch (\Throwable $e) {
            \Log::error('Webhook MP error', [
                'msg'        => $e->getMessage(),
                'payment_id' => $paymentId,
            ]);
        }

        return response()->json(['status' => 'ok'], 200);
    }


    // ── Helper: crear pedido una sola vez (idempotente) ─────────────────────

    private function crearPedidoSiNoExiste(string $referencia, string $paymentId): ?Pedido
    {
        // 🔍 LOG 1: entró al método
        \Log::info('DEBUG crearPedidoSiNoExiste', [
            'paso'       => 1,
            'referencia' => $referencia,
            'payment_id' => $paymentId,
        ]);

        // 🔒 Si ya existe un pedido con ese payment_id, devolverlo
        $existente = Pedido::with('detalles.variante.producto')
            ->where('payment_id', $paymentId)
            ->first();

        if ($existente) {
            \Log::info('DEBUG crearPedidoSiNoExiste', [
                'paso'      => 2,
                'mensaje'   => 'Ya existe pedido con ese payment_id',
                'pedido_id' => $existente->id_pedido,
            ]);
            return $existente;
        }

        // Buscar los datos del checkout en caché
        $data = Cache::get("checkout:{$referencia}");

        \Log::info('DEBUG crearPedidoSiNoExiste', [
            'paso'       => 3,
            'en_cache'   => $data ? 'SÍ' : 'NO',
            'cache_data' => $data ? array_keys($data) : null,
        ]);

        if (!$data) {
            \Log::warning('Checkout no encontrado en caché', [
                'referencia' => $referencia,
                'payment_id' => $paymentId,
            ]);
            return null;
        }

        try {
            $pedido = DB::transaction(function () use ($data, $paymentId, $referencia) {

                // 🔒 Re-verificar dentro de la transacción
                $existe = Pedido::where('payment_id', $paymentId)->first();
                if ($existe) {
                    return $existe;
                }

                // 🔒 Validar stock
                foreach ($data['items'] as $item) {
                    $variante = ProductoVariante::find($item['id_variante']);
                    if (!$variante || $variante->stock < $item['cantidad']) {
                        throw new \Exception(
                            "Stock insuficiente para variante {$item['id_variante']}"
                        );
                    }
                }

                // ✅ Determinar tipo de entrega
                $esRetiro = (int) $data['id_tipo_entrega'] === 1;

                \Log::info('DEBUG crearPedidoSiNoExiste', [
                    'paso'            => 4,
                    'mensaje'         => 'Antes de crear pedido',
                    'esRetiro'        => $esRetiro,
                    'id_tipo_entrega' => $data['id_tipo_entrega'],
                    'total'           => $data['total'],
                ]);

                // Crear el pedido
                $pedido = Pedido::create([
                    'numero_pedido'           => $this->generarNumeroPedido(),
                    'fecha_pedido'            => now(),
                    'total_pedido'            => $data['total'],
                    'estado_pedido'           => $esRetiro ? 'Listo para recoger' : 'Pendiente',
                    'payment_id'              => $paymentId,
                    'id_usuario'              => $data['id_usuario'],
                    'id_tipo_entrega'         => $data['id_tipo_entrega'],
                    'id_departamento'         => $data['id_departamento'],
                    'provincia'               => $data['provincia'],
                    'distrito'                => $data['distrito'],
                    'lugar_recojo'            => $data['lugar_recojo'],
                    'fecha_entrega_estimada'  => $esRetiro ? null : now()->addDays(5),
                ]);

                \Log::info('DEBUG crearPedidoSiNoExiste', [
                    'paso'           => 5,
                    'mensaje'        => 'Pedido creado',
                    'id_pedido'      => $pedido->id_pedido,
                    'numero_pedido'  => $pedido->numero_pedido,
                    'estado_pedido'  => $pedido->estado_pedido,
                ]);

                // Detalles + descuento de stock
                foreach ($data['items'] as $item) {
                    DetallePedido::create([
                        'id_pedido'       => $pedido->id_pedido,
                        'id_variante'     => $item['id_variante'],
                        'cantidad'        => $item['cantidad'],
                        'precio_unitario' => $item['precio_unitario'],
                        'subtotal'        => $item['precio_unitario'] * $item['cantidad'],
                    ]);

                    ProductoVariante::where('id_variante', $item['id_variante'])
                        ->decrement('stock', $item['cantidad']);
                }

                // Vaciar carrito
                $carrito = Carrito::where('id_usuario', $data['id_usuario'])->first();
                if ($carrito) {
                    $carrito->detalles()->delete();
                }

                // Limpiar caché
                Cache::forget("checkout:{$referencia}");

                return $pedido;
            });

            return $pedido->load('detalles.variante.producto');

        } catch (\Throwable $e) {
            \Log::error('Error creando pedido post-pago', [
                'msg'        => $e->getMessage(),
                'linea'      => $e->getLine(),
                'archivo'    => $e->getFile(),
                'referencia' => $referencia,
                'payment_id' => $paymentId,
            ]);
            return null;
        }
    }


    // ── Helper ──────────────────────────────────────────────────────────────

    private function generarNumeroPedido(): string
    {
        $fecha       = now()->format('Ymd');
        $cantidad    = Pedido::whereDate('created_at', today())->count() + 1;
        $correlativo = str_pad($cantidad, 3, '0', STR_PAD_LEFT);

        return "{$fecha}-{$correlativo}";
    }
}