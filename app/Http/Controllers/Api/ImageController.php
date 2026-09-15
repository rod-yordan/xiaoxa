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

        // ✅ Buscar en productos Y en banners (rutas absolutas)
        $pathProducto = storage_path('app/public/productos/' . $filename);
        $pathBanner   = storage_path('app/public/banners/' . $filename);

        // Debug en logs
        \Log::info('Productos: ' . $pathProducto . ' → ' . (file_exists($pathProducto) ? 'SI' : 'NO'));
        \Log::info('Banners: ' . $pathBanner . ' → ' . (file_exists($pathBanner) ? 'SI' : 'NO'));

        if (file_exists($pathProducto)) {
            $path = $pathProducto;
        } elseif (file_exists($pathBanner)) {
            $path = $pathBanner;
        } else {
            abort(404, 'Imagen no encontrada');
        }

        return response()->file($path, [
            'Content-Type' => mime_content_type($path),
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }
}