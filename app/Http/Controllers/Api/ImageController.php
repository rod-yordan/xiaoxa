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

        // Ruta absoluta usando storage_path()
        $path = storage_path('app/public/productos/' . $filename);

        // Debug: verificar en logs
        \Log::info('Buscando imagen en: ' . $path);
        \Log::info('¿Existe el archivo? ' . (file_exists($path) ? 'SI' : 'NO'));

        if (!file_exists($path)) {
            abort(404, 'Imagen no encontrada');
        }

        $mime = mime_content_type($path);

        return response()->file($path, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }
}