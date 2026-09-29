<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'ventas';
    protected $primaryKey = 'id_venta';
    protected $fillable = ['total'];

    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class, 'id_venta', 'id_venta');
    }
}