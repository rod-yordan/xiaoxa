<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TipoDocumento;

class TipoDocumentoController extends Controller
{
    public function index()
    {
        $tipos = TipoDocumento::select(
            'id_tipo_documento as id',
            'nombre_tipo_documento as nombre'
        )->get();

        return response()->json([
            'success' => true,
            'data' => $tipos,
        ]);
    }
}