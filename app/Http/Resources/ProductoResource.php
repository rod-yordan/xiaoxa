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

        // 👇 NUEVO: imagen principal = primera imagen de la primera variante con imágenes
        $imagenPrincipal = null;
        foreach ($variantes as $variante) {
            $primera = $variante->imagenes->first() ?? null;
            if ($primera) {
                $imagenPrincipal = url('/api/imagen/' . $primera->imagen);
                break;
            }
        }

        // 👇 NUEVO: galería = todas las imágenes de todas las variantes
        $galeria = [];
        foreach ($variantes as $variante) {
            foreach ($variante->imagenes as $img) {
                $galeria[] = url('/api/imagen/' . $img->imagen);
            }
        }

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
            $descuento = $precioOriginal > 0
                ? round((($precioOriginal - $precioFinal) / $precioOriginal) * 100, 0)
                : 0;
        } elseif ($this->id_promocion && $this->promocion && $this->promocion->estado_promocion) {
            $precioFinal = $precioOriginal - $this->promocion->descuento;
            $descuento = $precioOriginal > 0
                ? round(($this->promocion->descuento / $precioOriginal) * 100, 0)
                : 0;
        }

        return [
            'id' => $this->id_producto,
            'titulo' => $this->nombre_producto,
            'descripcion' => '',   // 👈 ya no existe la columna
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
            'promocion' => $this->when($this->id_promocion && $this->promocion, function () {
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