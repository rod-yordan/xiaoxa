<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'producto';
    protected $primaryKey = 'id_producto';
    
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'nombre_producto', 'detalles', 'precio', 'precio_oferta',
        'marca', 'estado_producto',
        'id_categoria', 'id_promocion'
    ];

    protected $casts = [
        'detalles' => 'array',
        'precio' => 'decimal:2',
        'precio_oferta' => 'decimal:2',
        'estado_producto' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function variantes()
    {
        return $this->hasMany(ProductoVariante::class, 'id_producto', 'id_producto');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria', 'id_categoria');
    }

    public function promocion()
    {
        return $this->belongsTo(Promocion::class, 'id_promocion', 'id_promocion');
    }

    public function detallesPedido()
    {
        return $this->hasManyThrough(
            DetallePedido::class,
            ProductoVariante::class,
            'id_producto',
            'id_variante',
            'id_producto',
            'id_variante'
        );
    }

    public function detallesCarrito()
    {
        return $this->hasManyThrough(
            DetalleCarrito::class,
            ProductoVariante::class,
            'id_producto',
            'id_variante',
            'id_producto',
            'id_variante'
        );
    }

    // ============ ACCESORES ============

    public function getImagenPrincipalAttribute()
    {
        foreach ($this->variantes as $variante) {
            if ($variante->imagenes->count() > 0) {
                return $variante->imagenes->first()->imagen;
            }
        }
        return null;
    }

    public function getPrecioFinalAttribute()
    {
        if ($this->precio_oferta) {
            return (float) $this->precio_oferta;
        }
        
        if ($this->promocion && $this->promocion->estado_promocion) {
            $fechaActual = now();
            if ($fechaActual >= $this->promocion->fecha_inicio && 
                $fechaActual <= $this->promocion->fecha_fin) {
                return (float) ($this->precio - $this->promocion->descuento);
            }
        }
        
        return (float) $this->precio;
    }

    public function getPrecioAnteriorAttribute()
    {
        if ($this->precio_oferta) {
            return (float) $this->precio;
        }
        
        if ($this->promocion && $this->promocion->estado_promocion) {
            $fechaActual = now();
            if ($fechaActual >= $this->promocion->fecha_inicio && 
                $fechaActual <= $this->promocion->fecha_fin) {
                return (float) $this->precio;
            }
        }
        
        return null;
    }

    public function getDescuentoAttribute()
    {
        if ($this->precio_oferta && $this->precio > 0) {
            return round((($this->precio - $this->precio_oferta) / $this->precio) * 100);
        }
        
        if ($this->promocion && $this->promocion->estado_promocion) {
            $fechaActual = now();
            if ($fechaActual >= $this->promocion->fecha_inicio && 
                $fechaActual <= $this->promocion->fecha_fin) {
                return round(($this->promocion->descuento / $this->precio) * 100);
            }
        }
        
        return null;
    }

    public function getStockAttribute()
    {
        return $this->variantes()->sum('stock');
    }

    public function getTallasListAttribute()
    {
        return $this->variantes()
            ->where('stock', '>', 0)
            ->pluck('talla')
            ->unique()
            ->filter()
            ->values()
            ->toArray();
    }

    public function getColoresListAttribute()
    {
        return $this->variantes()
            ->where('stock', '>', 0)
            ->pluck('color')
            ->unique()
            ->filter()
            ->values()
            ->toArray();
    }

    public function getSkuAttribute()
    {
        $primeraVariante = $this->variantes()->first();
        return $primeraVariante ? $primeraVariante->sku : null;
    }

    public function getDisponibleAttribute()
    {
        return $this->stock > 0 && $this->estado_producto == 1;
    }

    // ============ SCOPES ============

    public function scopeActivos($query)
    {
        return $query->where('estado_producto', 1);
    }

    public function scopeConStock($query)
    {
        return $query->whereHas('variantes', function($q) {
            $q->where('stock', '>', 0);
        });
    }

    public function scopeDisponibles($query)
    {
        return $query->activos()->conStock();
    }

    public function scopeEnOferta($query)
    {
        return $query->where(function($q) {
            $q->whereNotNull('precio_oferta')
              ->orWhereHas('promocion', function($q2) {
                  $q2->where('estado_promocion', 1)
                    ->where('fecha_inicio', '<=', now())
                    ->where('fecha_fin', '>=', now());
              });
        });
    }

    public function scopePorCategoria($query, $categoriaId)
    {
        return $query->where('id_categoria', $categoriaId);
    }

    public function scopeBuscar($query, $termino)
    {
        return $query->where('nombre_producto', 'LIKE', "%{$termino}%")
                     ->orWhere('marca', 'LIKE', "%{$termino}%");
    }

    public function scopeOrderByPrecio($query, $direccion = 'asc')
    {
        return $query->orderBy('precio', $direccion);
    }
}