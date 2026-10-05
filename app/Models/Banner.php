<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table = 'banners';
    protected $primaryKey = 'id_banner';

    public $timestamps = true;

    protected $fillable = [
        'titulo',
        'url_boton',
        'orden',
        'estado',
        'imagen',
    ];

    protected $casts = [
        'orden'  => 'integer',
        'estado' => 'integer',
    ];

    // ── Scopes

    public function scopeActivos($query)
    {
        return $query->where('estado', 1)->orderBy('orden');
    }
}