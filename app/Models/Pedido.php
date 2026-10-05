<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedido';
    protected $primaryKey = 'id_pedido';

    public $timestamps = true;

    protected $casts = [
        'fecha_pedido'  => 'datetime',
        'total_pedido'  => 'decimal:2',
        'subtotal'      => 'decimal:2',
        'descuento'     => 'decimal:2',
    ];

    protected $fillable = [
        'numero_pedido',
        'fecha_pedido',
        'subtotal',
        'descuento',
        'total_pedido',
        'estado_pedido',
        'payment_id',
        'departamento',
        'provincia',
        'distrito',
        'direccion_entrega',
        'tiempo_entrega',
        'archivo_adjunto',
        'id_usuario',
        'id_cupon',
        'id_tipo_entrega',
    ];

    // ── Relaciones ──────────────────────────────────

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function detalles()
    {
        return $this->hasMany(DetallePedido::class, 'id_pedido', 'id_pedido');
    }

    public function tipoEntrega()
    {
        return $this->belongsTo(TipoEntrega::class, 'id_tipo_entrega', 'id_tipo_entrega');
    }

    public function cupon()
    {
        return $this->belongsTo(Cupon::class, 'id_cupon', 'id_cupon');
    }

    // 🆕 Registro de uso del cupón (quién lo usó, cuándo, en qué pedido)
    public function cuponUsado()
    {
        return $this->hasOne(CuponUsado::class, 'id_pedido', 'id_pedido');
    }

    // ── Helpers ─────────────────────────────────────

    /**
     * Devuelve la ubicación del cliente formateada (departamento, provincia, distrito).
     */
    public function getUbicacionCompletaAttribute(): ?string
    {
        $partes = array_filter([
            $this->distrito,
            $this->provincia,
            $this->departamento,
        ]);

        return $partes ? implode(', ', $partes) : null;
    }

    /**
     * Verifica si el pedido tiene archivo adjunto.
     */
    public function tieneArchivo(): bool
    {
        return !empty($this->archivo_adjunto);
    }
}