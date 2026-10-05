<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'ventas';
    protected $primaryKey = 'id_venta';

    public $timestamps = true;

    protected $fillable = [
        'total',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    // ── Relaciones

    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class, 'id_venta', 'id_venta');
    }
}