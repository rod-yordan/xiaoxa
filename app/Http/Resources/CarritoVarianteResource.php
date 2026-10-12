<?php
// app/Http/Resources/CarritoVarianteResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CarritoVarianteResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id_variante' => $this->id_variante,
            'talla'       => $this->talla,
            'color'       => $this->color,
            'color_hex'   => $this->color_hex,
            'sku'         => $this->sku,
            'stock'       => $this->stock,
            'producto'    => new CarritoProductoResource(
                $this->whenLoaded('producto')
            ),
        ];
    }
}