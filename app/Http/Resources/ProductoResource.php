<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductoResource extends JsonResource
{
    public function toArray($request)
    {
        // Imagen principal
        $imagenPrincipal = null;
        if ($this->imagen) {
            $imagenPrincipal = filter_var($this->imagen, FILTER_VALIDATE_URL)
                ? $this->imagen
                : url('/api/imagen/' . $this->imagen);
        }

        // Procesar la galería de imágenes si existe
        $galeria = [];
        if ($this->galeria) {
            // Decodificar JSON si es string
            $imagenesGaleria = [];
            if (is_array($this->galeria)) {
                $imagenesGaleria = $this->galeria;
            } elseif (is_string($this->galeria)) {
                $imagenesGaleria = json_decode($this->galeria, true) ?? [];
            }

            $galeria = collect($imagenesGaleria)->map(function($img) {
                return filter_var($img, FILTER_VALIDATE_URL)
                    ? $img
                    : url('/api/imagen/' . $img);
            })->toArray();
        }

        // Obtener variantes del producto
        $variantes = $this->whenLoaded('variantes', function() {
            return $this->variantes;
        }, collect());

        // Calcular stock total
        $stockTotal = $variantes->sum('stock');

        // Obtener tallas únicas
        $tallas = $variantes->pluck('talla')->unique()->filter()->values()->toArray();

        // Obtener colores únicos
        $colores = $variantes->pluck('color')->unique()->filter()->values()->toArray();

        // Calcular SKU principal
        $skuPrincipal = $variantes->isNotEmpty() ? $variantes->first()->sku : null;

        // Calcular precio final considerando oferta y promoción
        $precioOriginal = (float) $this->precio;
        $precioFinal = $precioOriginal;
        $descuento = 0;

        if ($this->precio_oferta) {
            $precioFinal = (float) $this->precio_oferta;
            $descuento = round((($precioOriginal - $precioFinal) / $precioOriginal) * 100, 0);
        } elseif ($this->id_promocion && $this->promocion && $this->promocion->estado_promocion) {
            $precioFinal = $precioOriginal - $this->promocion->descuento;
            $descuento = round(($this->promocion->descuento / $precioOriginal) * 100, 0);
        }

        return [
            'id' => $this->id_producto,
            'titulo' => $this->nombre_producto,
            'descripcion' => $this->descripcion ?? '',
            'precio' => $precioFinal,
            'precio_antes' => $precioFinal < $precioOriginal ? $precioOriginal : null,
            'descuento' => $descuento > 0 ? $descuento : null,
            'imagenes' => $galeria,
            'imagen_principal' => $imagenPrincipal,
            'categoria' => $this->categoria?->nombre_categoria,
            'categoria_id' => $this->id_categoria,
            'tallas' => $tallas,
            'colores' => $colores,
            'marca' => $this->marca,
            'stock' => $stockTotal,
            'sku' => $skuPrincipal,
            'disponible' => $stockTotal > 0 && $this->estado_producto == 1,
            'en_oferta' => $this->precio_oferta !== null || $this->id_promocion !== null,
            'variantes' => VarianteResource::collection($variantes),
            'promocion' => $this->when($this->id_promocion && $this->promocion, function() {
                return [
                    'id' => $this->promocion->id_promocion,
                    'nombre' => $this->promocion->nombre_promocion,
                    'descuento' => (float) $this->promocion->descuento,
                    'fecha_fin' => $this->promocion->fecha_fin,
                ];
            }),
            'fecha_creacion' => $this->created_at?->format('Y-m-d H:i:s'),
            'fecha_actualizacion' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}