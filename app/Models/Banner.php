<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $primaryKey = 'id_banner';

    protected $fillable = [
        'titulo',
        'subtitulo',
        'descripcion',
        'etiqueta',
        'texto_boton',
        'url_boton',
        'imagen',
        'orden',
        'estado'
    ];

    protected $casts = [
        'estado' => 'integer',  // ← ✅ cambiado de 'boolean' a 'integer'
    ];

    public function scopeActivos($query)
    {
        return $query->where('estado', 1)->orderBy('orden');
    }
}