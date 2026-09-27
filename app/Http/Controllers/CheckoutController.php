<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\Departamento;
use App\Models\Pedido;
use App\Models\ProductoVariante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\TipoEntrega;
use App\Models\TipoDocumento;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;

class CheckoutController extends Controller
{
    // ── Vista del checkout ───────────────────────────────────────────────────

    public function index()
    {
        if (session()->has('carrito')) {
            $carrito = Carrito::firstOrCreate(['id_usuario' => Auth::id()]);

            foreach (session('carrito') as $idVariante => $item) {
                \App\Models\DetalleCarrito::updateOrCreate(
                    ['id_carrito' => $carrito->id_carrito, 'id_variante' => $idVariante],
                    ['cantidad'   => $item['cantidad']]
                );
            }

            session()->forget('carrito');
        }

        $carrito        = Carrito::with('detalles.variante.producto')
            ->where('id_usuario', Auth::id())
            ->first();
        $departamentos  = Departamento::all();
        $tiposEntrega   = TipoEntrega::where('estado', 1)->get();
        $tiposDocumento = TipoDocumento::all();

        return view('carrito.checkout', compact(
            'carrito',
            'departamentos',
            'tiposEntrega',
            'tiposDocumento'
        ));
    }


    // ── Confirmar: guarda datos en caché y redirige a MP ────────────────────

    public function confirmar(Request $request)
    {
        $request->validate([
            'id_tipo_entrega'    => 'required|integer|in:1,2',
            'id_tipo_documento'  => 'required|integer|exists:tipo_documento,id_tipo_documento',
            'numero_documento'   => 'required|string|max:20',
            'telefono'           => 'required|string|max:20',
            'id_departamento'    => 'nullable|integer|exists:departamento,id_departamento',
            'provincia'          => 'nullable|string|max:100',
            'distrito'           => 'nullable|string|max:100',
        ]);

        // ✅ Guardar datos personales del usuario (permanente)
        $user = Auth::user();
        $user->id_tipo_documento = $request->id_tipo_documento;
        $user->numero_documento  = $request->numero_documento;
        $user->telefono          = $request->telefono;
        $user->save();

        $carrito = Carrito::with('detalles.variante.producto')
            ->where('id_usuario', Auth::id())
            ->firstOrFail();

        if ($carrito->detalles->isEmpty()) {
            return back()->with('error', 'Tu carrito está vacío.');
        }

        // ✅ Validar stock ANTES de mandar a pagar
        foreach ($carrito->detalles as $detalle) {
            if ($detalle->variante->stock < $detalle->cantidad) {
                return back()->with('error',
                    'Stock insuficiente para: ' . $detalle->variante->producto->nombre_producto
                );
            }
        }

        // ── Calcular total e items para MP ──────────────────────────────────
        $total = 0;
        $items = [];

        foreach ($carrito->detalles as $detalle) {
            $producto = $detalle->variante->producto;
            $precio   = $producto->precio_oferta ?? $producto->precio;
            $total   += $precio * $detalle->cantidad;

            $items[] = [
                "title"       => $producto->nombre_producto
                                 . ' (' . $detalle->variante->color
                                 . ' - Talla ' . $detalle->variante->talla . ')',
                "quantity"    => (int) $detalle->cantidad,
                "unit_price"  => (float) $precio,
                "currency_id" => "PEN",
            ];
        }

        $esEnvio = (int) $request->id_tipo_entrega === 2;

        // ── Guardar TODO el checkout en caché (2 horas) ─────────────────────
        $referencia = 'CHK-' . Auth::id() . '-' . uniqid();

        $checkoutData = [
            'id_usuario'       => Auth::id(),
            'total'            => $total,
            'id_tipo_entrega'  => (int) $request->id_tipo_entrega,
            'id_departamento'  => $esEnvio ? (int) $request->id_departamento : null,
            'provincia'        => $esEnvio ? $request->provincia : null,
            'distrito'         => $esEnvio ? $request->distrito : null,
            'lugar_recojo'     => $esEnvio ? null : 'Jr. Cajamarca N° 396 - Huancayo',
            'items'            => collect($carrito->detalles)->map(fn($d) => [
                'id_variante'     => $d->id_variante,
                'cantidad'        => $d->cantidad,
                'precio_unitario' => $d->variante->producto->precio_oferta
                                     ?? $d->variante->producto->precio,
            ])->toArray(),
        ];

        Cache::put("checkout:{$referencia}", $checkoutData, now()->addHours(2));

        // 🔍 TEMPORAL: log para debug (ver la referencia en el log)
        \Log::info('Checkout guardado', [
            'referencia'      => $referencia,
            'id_tipo_entrega' => $checkoutData['id_tipo_entrega'],   // ← AGREGAR
            'lugar_recojo'    => $checkoutData['lugar_recojo'],       // ← AGREGAR
            'total'           => $total,
            'items_count'     => count($checkoutData['items']),
            'id_usuario'      => Auth::id(),
        ]);

        // ── Crear preferencia en MP ─────────────────────────────────────────
        MercadoPagoConfig::setAccessToken(env('MP_ACCESS_TOKEN'));
        $client = new PreferenceClient();

        $preferenceData = [
            "items"              => $items,
            "payer"              => [
                "name"  => Auth::user()->nombres,
                "email" => Auth::user()->correo,
            ],
            "back_urls"          => [
                "success" => route('pago.exito'),
                "failure" => route('pago.fallo'),
                "pending" => route('pago.pendiente'),
            ],
            "notification_url"   => route('pago.webhook'),
            "external_reference" => $referencia,
            "payment_methods"    => [
                "installments"         => 1,
                "default_installments" => 1,
            ],
        ];

        // auto_return solo en producción con HTTPS (MP lo rechaza en local)
        if (app()->environment('production') && str_starts_with(config('app.url'), 'https://')) {
            $preferenceData['auto_return'] = 'approved';
        }

        try {
            $preference = $client->create($preferenceData);
        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            dd([
                'mensaje'   => $e->getMessage(),
                'respuesta' => $e->getApiResponse()->getContent(),
            ]);
        }

        // URL según entorno
        $url = app()->environment('production')
            ? $preference->init_point
            : $preference->sandbox_init_point;

        return redirect($url);
    }
}