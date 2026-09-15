<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoVariante extends Model
{
    protected $table = 'producto_variante';
    protected $primaryKey = 'id_variante';
    
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'id_producto', 'talla', 'color', 'color_hex', 'stock', 'sku'
    ];

    protected $casts = [
        'stock' => 'integer',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    // ✅ NUEVO: Relación con las imágenes
    public function imagenes()
    {
        return $this->hasMany(ProductoVarianteImagen::class, 'id_variante', 'id_variante')
                    ->orderBy('orden', 'asc');
    }

    public function detallesPedido()
    {
        return $this->hasMany(DetallePedido::class, 'id_variante', 'id_variante');
    }

    public function detallesCarrito()
    {
        return $this->hasMany(DetalleCarrito::class, 'id_variante', 'id_variante');
    }

    public function getDisponibleAttribute()
    {
        return $this->stock > 0;
    }
}