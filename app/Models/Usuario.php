<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';

    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'nombres',
        'apellidos',
        'correo',
        'contrasena',
        'telefono',
        'numero_documento',
        'id_tipo_documento',
        'id_rol',
        'email_verified_at',
        'remember_token',
    ];

    protected $hidden = [
        'contrasena',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
        'id_rol'            => 'integer',
        'id_tipo_documento' => 'integer',
    ];

    // ============ RELACIONES ============

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }

    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class, 'id_tipo_documento', 'id_tipo_documento');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'id_usuario', 'id_usuario');
    }

    public function carrito()
    {
        return $this->hasOne(Carrito::class, 'id_usuario', 'id_usuario');
    }

    public function favoritos()
    {
        return $this->belongsToMany(Producto::class, 'favoritos', 'id_usuario', 'id_producto');
    }

    // 🆕 Cupones que este usuario ha usado
    public function cuponesUsados()
    {
        return $this->hasMany(CuponUsado::class, 'id_usuario', 'id_usuario');
    }

    // ============ AUTENTICACIÓN ============

    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    public function getAuthIdentifierName()
    {
        return 'id_usuario';
    }

    public function getEmailForVerification()
    {
        return $this->correo;
    }

    public function routeNotificationForMail($notification)
    {
        return $this->correo;
    }

    // ============ ACCESORES ============

    public function getNombreCompletoAttribute()
    {
        return trim($this->nombres . ' ' . $this->apellidos);
    }

    public function getDocumentoCompletoAttribute()
    {
        if ($this->tipoDocumento && $this->numero_documento) {
            return $this->tipoDocumento->nombre_tipo_documento . ': ' . $this->numero_documento;
        }
        return null;
    }

    // ============ SCOPES ============

    public function scopeActivos($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    public function scopePorRol($query, $rolId)
    {
        return $query->where('id_rol', $rolId);
    }
}