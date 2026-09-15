<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::where('estado', true)
            ->orderBy('orden', 'asc')
            ->get();

        $banners = $banners->map(function ($banner) {
            return [
                'id'        => $banner->id_banner,
                'titulo'    => $banner->titulo,       // solo uso interno
                'url_boton' => $banner->url_boton,    // link de destino
                'imagen'    => $banner->imagen
                    ? url('/api/imagen/' . $banner->imagen)
                    : null,
                'orden'     => $banner->orden,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $banners
        ]);
    }
}