<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductoResource extends JsonResource
{
    public function toArray($request)
    {
        $variantes = $this->whenLoaded('variantes', function () {
            return $this->variantes;
        }, collect());

        // Imagen principal = primera imagen de la primera variante con imágenes
        $imagenPrincipal = null;
        foreach ($variantes as $variante) {
            $primera = $variante->imagenes->first() ?? null;
            if ($primera) {
                $imagenPrincipal = url('/api/imagen/' . $primera->imagen);
                break;
            }
        }

        // Galería = todas las imágenes de todas las variantes
        $galeria = [];
        foreach ($variantes as $variante) {
            foreach ($variante->imagenes as $img) {
                $galeria[] = url('/api/imagen/' . $img->imagen);
            }
        }

        // Stock total
        $stockTotal = $variantes->sum('stock');

        // Tallas únicas
        $tallas = $variantes->pluck('talla')->unique()->filter()->values()->toArray();

        // Colores únicos
        $colores = $variantes->pluck('color')->unique()->filter()->values()->toArray();

        // SKU principal
        $skuPrincipal = $variantes->isNotEmpty() ? $variantes->first()->sku : null;

        return [
            'id' => $this->id_producto,
            'titulo' => $this->nombre_producto,
            'descripcion' => '',
            'precio' => (float) $this->precio,
            'precio_oferta' => $this->precio_oferta ? (float) $this->precio_oferta : null,
            'descuento' => $this->descuento,
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
            'en_oferta' => $this->precio_oferta !== null,
            'variantes' => VarianteResource::collection($variantes),
            'fecha_creacion' => $this->created_at?->format('Y-m-d H:i:s'),
            'fecha_actualizacion' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}