<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VarianteResource extends JsonResource
{
    public function toArray($request)
    {
        // 👇 NUEVO: colección de imágenes de esta variante (puede estar vacía)
        $imagenes = $this->relationLoaded('imagenes')
            ? $this->imagenes->map(fn($img) => [
                'id'     => $img->id_imagen,
                'url'    => url('/api/imagen/' . $img->imagen),
                'orden'  => $img->orden,
            ])->values()->toArray()
            : [];

        // Imagen principal de la variante (primera) o null
        $imagenPrincipal = $imagenes[0]['url'] ?? null;

        return [
            'id' => $this->id_variante,
            'talla' => $this->talla,
            'color' => $this->color,
            'color_hex' => $this->color_hex,
            'stock' => $this->stock,
            'sku' => $this->sku,
            'disponible' => $this->stock > 0,
            'imagen_principal' => $imagenPrincipal,
            'imagenes' => $imagenes,
        ];
    }
}