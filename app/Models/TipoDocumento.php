<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    protected $table = 'tipo_documento';
    protected $primaryKey = 'id_tipo_documento';

    public $timestamps = false;

    protected $fillable = [
        'nombre_tipo_documento',
    ];

    // ── Relaciones

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_tipo_documento', 'id_tipo_documento');
    }
}