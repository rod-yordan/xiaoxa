<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductoResource extends JsonResource
{
    public function toArray($request)
    {
        // Obtener variantes (cargadas o colección vacía)
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

        // Precio final (solo oferta, sin promociones)
        $precioOriginal = (float) $this->precio;
        $precioFinal = $precioOriginal;
        $descuento = 0;

        if ($this->precio_oferta) {
            $precioFinal = (float) $this->precio_oferta;
            $descuento = $precioOriginal > 0
                ? round((($precioOriginal - $precioFinal) / $precioOriginal) * 100, 0)
                : 0;
        }

        return [
            'id' => $this->id_producto,
            'titulo' => $this->nombre_producto,
            'descripcion' => '',
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
            'en_oferta' => $this->precio_oferta !== null,
            'variantes' => VarianteResource::collection($variantes),
            'fecha_creacion' => $this->created_at?->format('Y-m-d H:i:s'),
            'fecha_actualizacion' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}