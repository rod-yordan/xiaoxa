<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cupon extends Model
{
    protected $table = 'cupones';
    protected $primaryKey = 'id_cupon';

    public $timestamps = false;

    protected $fillable = [
        'codigo_cupon',
        'monto_cupon',
        'monto_compra_minima',
        'fecha_vencimiento',
        'estado_cupon',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'estado_cupon'      => 'boolean',
        'monto_cupon'       => 'decimal:2',
        'monto_compra_minima' => 'decimal:2',
    ];

    // ============ RELACIONES ============

    /**
     * Pedidos que usaron este cupón.
     */
    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'id_cupon', 'id_cupon');
    }

    /**
     * Registros de uso de este cupón (quién lo usó, cuándo).
     */
    public function usos()
    {
        return $this->hasMany(CuponUsado::class, 'id_cupon', 'id_cupon');
    }

    // ============ VALIDACIONES ============

    /**
     * Validación básica: activo, no vencido, monto mínimo alcanzado.
     */
    public function esValido($total)
    {
        return $this->estado_cupon
            && $this->fecha_vencimiento >= now()
            && $total >= $this->monto_compra_minima;
    }

    /**
     * 🆕 Validación completa para un usuario específico.
     * Verifica que el usuario no lo haya usado antes.
     */
    public function esValidoPara($idUsuario, $subtotal): bool
    {
        return $this->estado_cupon
            && $this->fecha_vencimiento >= now()
            && $subtotal >= $this->monto_compra_minima
            && !$this->yaFueUsadoPor($idUsuario);
    }

    /**
     * 🆕 Verifica si un usuario ya usó este cupón.
     */
    public function yaFueUsadoPor($idUsuario): bool
    {
        return $this->usos()
            ->where('id_usuario', $idUsuario)
            ->exists();
    }

    // ============ SCOPES ============

    /**
     * Solo cupones activos y vigentes.
     */
    public function scopeVigentes($query)
    {
        return $query->where('estado_cupon', 1)
                     ->where('fecha_vencimiento', '>=', now());
    }

    /**
     * Solo cupones disponibles para un usuario específico
     * (activos, vigentes, y que él no haya usado).
     */
    public function scopeDisponiblesPara($query, $idUsuario)
    {
        return $query->where('estado_cupon', 1)
                     ->where('fecha_vencimiento', '>=', now())
                     ->whereDoesntHave('usos', function ($q) use ($idUsuario) {
                         $q->where('id_usuario', $idUsuario);
                     });
    }
}