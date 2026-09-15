<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoVarianteImagen extends Model
{
    protected $table = 'producto_variante_imagen';
    protected $primaryKey = 'id_imagen';

    public $timestamps = true;

    protected $fillable = [
        'id_variante', 'imagen', 'orden'
    ];

    protected $casts = [
        'orden' => 'integer',
    ];

    public function variante()
    {
        return $this->belongsTo(ProductoVariante::class, 'id_variante', 'id_variante');
    }
}