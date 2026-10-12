<?php
// app/Http/Resources/CarritoProductoResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CarritoProductoResource extends JsonResource
{
    public function toArray($request)
    {
        // ── Imágenes del producto: sacar de sus variantes
        $imagenes = [];
        if ($this->relationLoaded('variantes')) {
            foreach ($this->variantes as $variante) {
                if ($variante->imagenes) {
                    foreach ($variante->imagenes as $img) {
                        $url = filter_var($img->imagen, FILTER_VALIDATE_URL)
                            ? $img->imagen
                            : url('/api/imagen/' . $img->imagen);

                        if (!in_array($url, $imagenes)) {
                            $imagenes[] = $url;
                        }
                    }
                }
            }
        }

        $imagenPrincipal = $imagenes[0] ?? null;

        // ── Detalles: array de strings
        $detalles = $this->detalles ?? [];
        if (!is_array($detalles)) {
            $detalles = $detalles ? [$detalles] : [];
        }

        return [
            'id_producto'      => $this->id_producto,
            'titulo'           => $this->nombre_producto,
            'detalles'         => $detalles,   // 👈 antes: descripcion

            'precio'           => (float) $this->precio,
            'precio_oferta'    => $this->precio_oferta !== null
                ? (float) $this->precio_oferta
                : null,

            'marca'            => $this->marca,
            'imagen_principal' => $imagenPrincipal,
            'imagenes'         => $imagenes,
            'categoria'        => $this->categoria->nombre_categoria ?? null,
            'id_categoria'     => $this->id_categoria,
            'stock'            => (int) ($this->stock ?? 0),
            'disponible'       => (bool) $this->estado_producto,
            'en_oferta'        => $this->precio_oferta !== null,
        ];
    }
}