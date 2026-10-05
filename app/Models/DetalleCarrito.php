<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleCarrito extends Model
{
    protected $table = 'detalle_carrito';
    protected $primaryKey = 'id_detalle_carrito';

    public $timestamps = true;

    protected $fillable = [
        'id_carrito',
        'id_variante',
        'cantidad',
    ];

    protected $casts = [
        'cantidad' => 'integer',
    ];

    // ── Relaciones

    public function carrito()
    {
        return $this->belongsTo(Carrito::class, 'id_carrito', 'id_carrito');
    }

    public function variante()
    {
        return $this->belongsTo(ProductoVariante::class, 'id_variante', 'id_variante');
    }
}