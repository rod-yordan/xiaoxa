<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedido';
    protected $primaryKey = 'id_pedido';

    public $timestamps = true;

    protected $casts = [
        'fecha_pedido'           => 'datetime',
        'fecha_envio'            => 'datetime',
        'fecha_entrega_estimada' => 'date',
        'fecha_entrega_real'     => 'datetime',
        'total_pedido'           => 'decimal:2',
    ];

    protected $fillable = [
        'numero_pedido',
        'fecha_pedido',
        'total_pedido',
        'estado_pedido',
        'payment_id',
        'id_departamento',
        'provincia',
        'distrito',
        'lugar_recojo',
        'fecha_envio',
        'fecha_entrega_estimada',
        'fecha_entrega_real',
        'id_usuario',
        'id_cupon',
        'id_tipo_entrega',
    ];

    // ── Relaciones ──────────────────────────────────

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    public function detalles()
    {
        return $this->hasMany(DetallePedido::class, 'id_pedido', 'id_pedido');
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento', 'id_departamento');
    }

    public function tipoEntrega()
    {
        return $this->belongsTo(TipoEntrega::class, 'id_tipo_entrega', 'id_tipo_entrega');
    }

    public function cupon()
    {
        return $this->belongsTo(Cupon::class, 'id_cupon', 'id_cupon');
    }

    // ── Helpers ─────────────────────────────────────

    /**
     * Devuelve la dirección de envío formateada.
     */
    public function getDireccionCompletaAttribute(): ?string
    {
        $partes = array_filter([
            $this->distrito,
            $this->provincia,
            $this->departamento?->nombre_departamento,
        ]);

        return $partes ? implode(', ', $partes) : null;
    }
}