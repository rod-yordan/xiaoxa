<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ImageController extends Controller
{
    public function show($filename)
    {
        // Validar extensión
        if (!preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $filename)) {
            abort(404);
        }

        // Carpetas donde se guardan imágenes (en orden de búsqueda)
        $carpetas = ['variantes', 'productos', 'banners', 'categorias', 'cupones'];

        $path = null;
        foreach ($carpetas as $carpeta) {
            $candidato = storage_path('app/public/' . $carpeta . '/' . $filename);
            if (file_exists($candidato)) {
                $path = $candidato;
                break;
            }
        }

        if (!$path) {
            abort(404, 'Imagen no encontrada');
        }

        return response()->file($path, [
            'Content-Type'  => mime_content_type($path),
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }
}