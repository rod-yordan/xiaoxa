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

    public function fallo(Request $request)
    {
        if ($request->external_reference) {
            Cache::forget("checkout:{$request->external_reference}");
        }

        return view('pagos.fallo');
    }

    public function pendiente(Request $request)
    {
        return view('pagos.pendiente');
    }

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

    private function crearPedidoSiNoExiste(string $referencia, string $paymentId): ?Pedido
    {
        $existente = Pedido::with('detalles.variante.producto')
            ->where('payment_id', $paymentId)
            ->first();

        if ($existente) {
            return $existente;
        }

        $data = Cache::get("checkout:{$referencia}");

        if (!$data) {
            \Log::warning('Checkout no encontrado en caché', [
                'referencia' => $referencia,
                'payment_id' => $paymentId,
            ]);
            return null;
        }

        try {
            $pedido = DB::transaction(function () use ($data, $paymentId, $referencia) {

                $existe = Pedido::where('payment_id', $paymentId)->first();
                if ($existe) {
                    return $existe;
                }

                foreach ($data['items'] as $item) {
                    $variante = ProductoVariante::find($item['id_variante']);
                    if (!$variante || $variante->stock < $item['cantidad']) {
                        throw new \Exception(
                            "Stock insuficiente para variante {$item['id_variante']}"
                        );
                    }
                }

                $esRetiro = (int) $data['id_tipo_entrega'] === 1;

                $pedido = Pedido::create([
                    'numero_pedido'      => $this->generarNumeroPedido(),
                    'fecha_pedido'       => now(),
                    'subtotal'           => $data['subtotal'] ?? $data['total'],
                    'costo_envio'        => $data['costo_envio'] ?? 0,
                    'total_pedido'       => $data['total'],
                    'estado_pedido'      => 'Pendiente', // ← Todos nacen en Pendiente
                    'payment_id'         => $paymentId,
                    'id_usuario'         => $data['id_usuario'],
                    'id_tipo_entrega'    => $data['id_tipo_entrega'],
                    'id_departamento'    => $data['id_departamento'],
                    'provincia'          => $data['provincia'],
                    'distrito'           => $data['distrito'],
                    'direccion_entrega'  => $esRetiro
                        ? 'Jr. Cajamarca N° 396 - Huancayo'
                        : null,
                    'fecha_entrega'      => null,
                ]);

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

                $carrito = Carrito::where('id_usuario', $data['id_usuario'])->first();
                if ($carrito) {
                    $carrito->detalles()->delete();
                }

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

    private function generarNumeroPedido(): string
    {
        $prefijo = 'XIA-';

        $ultimo = Pedido::where('numero_pedido', 'like', $prefijo . '%')
            ->orderByDesc('id_pedido')
            ->lockForUpdate()
            ->value('numero_pedido');

        $siguiente = $ultimo
            ? ((int) str_replace($prefijo, '', $ultimo)) + 1
            : 10001;

        return $prefijo . $siguiente;
    }
}