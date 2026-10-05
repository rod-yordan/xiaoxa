<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuponUsado extends Model
{
    protected $table = 'cupones_usados';
    protected $primaryKey = 'id_cupon_usado';

    public $timestamps = true;

    protected $fillable = [
        'id_cupon',
        'id_usuario',
        'id_pedido',
        'usado_en',
    ];

    protected $casts = [
        'usado_en' => 'datetime',
    ];

    // ── Relaciones

    public function cupon()
    {
        return $this->belongsTo(Cupon::class, 'id_cupon', 'id_cupon');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido', 'id_pedido');
    }
}