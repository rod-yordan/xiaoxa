<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VentaDetalle extends Model
{
    protected $table = 'venta_detalles';
    protected $primaryKey = 'id_venta_detalle';
    protected $fillable = ['id_venta', 'id_variante', 'cantidad', 'precio_unitario', 'subtotal'];
}