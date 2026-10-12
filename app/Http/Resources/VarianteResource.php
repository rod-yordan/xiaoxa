<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VarianteResource extends JsonResource
{
    public function toArray($request)
    {
        // Imágenes como lista de strings (URL completa)
        $imagenes = $this->relationLoaded('imagenes')
            ? $this->imagenes
                ->map(fn($img) => url('/api/imagen/' . $img->imagen))
                ->values()
                ->toArray()
            : [];

        // Imagen principal = primera imagen
        $imagenPrincipal = $imagenes[0] ?? null;

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