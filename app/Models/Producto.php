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
        'nombre_producto', 'descripcion', 'precio', 'precio_oferta',
        'imagen', 'galeria', 'marca', 'estado_producto',
        'id_categoria', 'id_promocion'
    ];

    protected $casts = [
        'galeria' => 'array',
        'precio' => 'decimal:2',
        'precio_oferta' => 'decimal:2',
        'estado_producto' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con variantes (tallas y colores)
     */
    public function variantes()
    {
        return $this->hasMany(ProductoVariante::class, 'id_producto', 'id_producto');
    }

    /**
     * Relación con categoría
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria', 'id_categoria');
    }

    /**
     * Relación con promoción
     */
    public function promocion()
    {
        return $this->belongsTo(Promocion::class, 'id_promocion', 'id_promocion');
    }

    /**
     * Relación con detalles de pedido
     */
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

    /**
     * Relación con carritos
     */
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

    /**
     * Obtener todas las imágenes del producto (principal + galería)
     */
    public function getImagenesAttribute()
    {
        $imagenes = [];
        
        if ($this->galeria && is_array($this->galeria)) {
            $imagenes = $this->galeria;
        }
        
        if ($this->imagen) {
            array_unshift($imagenes, $this->imagen);
        }
        
        if (empty($imagenes)) {
            $imagenes = ['default-product.jpg'];
        }
        
        return $imagenes;
    }

    /**
     * Obtener el precio final (considerando oferta)
     */
    public function getPrecioFinalAttribute()
    {
        // Si hay precio de oferta, usar ese
        if ($this->precio_oferta) {
            return (float) $this->precio_oferta;
        }
        
        // Si hay promoción activa, aplicar descuento
        if ($this->promocion && $this->promocion->estado_promocion) {
            $fechaActual = now();
            if ($fechaActual >= $this->promocion->fecha_inicio && 
                $fechaActual <= $this->promocion->fecha_fin) {
                return (float) ($this->precio - $this->promocion->descuento);
            }
        }
        
        return (float) $this->precio;
    }

    /**
     * Obtener el precio anterior (si está en oferta)
     */
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

    /**
     * Obtener el porcentaje de descuento
     */
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

    /**
     * Obtener stock total sumando todas las variantes
     */
    public function getStockAttribute()
    {
        return $this->variantes()->sum('stock');
    }

    /**
     * Obtener todas las tallas disponibles
     */
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

    /**
     * Obtener todos los colores disponibles
     */
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

    /**
     * Obtener SKU principal (primera variante)
     */
    public function getSkuAttribute()
    {
        $primeraVariante = $this->variantes()->first();
        return $primeraVariante ? $primeraVariante->sku : null;
    }

    /**
     * Verificar si el producto está disponible
     */
    public function getDisponibleAttribute()
    {
        return $this->stock > 0 && $this->estado_producto == 1;
    }

    // ============ SCOPES ============

    /**
     * Scope para productos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estado_producto', 1);
    }

    /**
     * Scope para productos con stock
     */
    public function scopeConStock($query)
    {
        return $query->whereHas('variantes', function($q) {
            $q->where('stock', '>', 0);
        });
    }

    /**
     * Scope para productos disponibles (activos y con stock)
     */
    public function scopeDisponibles($query)
    {
        return $query->activos()->conStock();
    }

    /**
     * Scope para productos en oferta
     */
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

    /**
     * Scope para productos por categoría
     */
    public function scopePorCategoria($query, $categoriaId)
    {
        return $query->where('id_categoria', $categoriaId);
    }

    /**
     * Scope para búsqueda
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where('nombre_producto', 'LIKE', "%{$termino}%")
                     ->orWhere('descripcion', 'LIKE', "%{$termino}%")
                     ->orWhere('marca', 'LIKE', "%{$termino}%");
    }

    /**
     * Scope para ordenar por precio
     */
    public function scopeOrderByPrecio($query, $direccion = 'asc')
    {
        return $query->orderBy('precio', $direccion);
    }
}