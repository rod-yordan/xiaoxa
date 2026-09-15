<?php

namespace App\Http\Controllers\Admin;

use App\Models\Banner;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('orden')->paginate(10);
        return view('admin.banners.index', compact('banners'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo'    => 'required|max:100',
            'url_boton' => 'nullable|max:500',
            'orden'     => 'integer',
            'estado'    => 'boolean',
            'imagen'    => 'required|image|max:2048',
        ]);

        // ✅ Guardar en storage/app/public/banners/
        $archivo = time() . '_' . $request->file('imagen')->getClientOriginalName();
        $request->file('imagen')->move(storage_path('app/public/banners'), $archivo);
        $data['imagen'] = $archivo;

        Banner::create($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner creado correctamente');
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $data = $request->validate([
            'titulo'    => 'required|max:100',
            'url_boton' => 'nullable|max:500',
            'orden'     => 'integer',
            'estado'    => 'boolean',
            'imagen'    => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('imagen')) {
            // ✅ Eliminar imagen anterior de storage
            $rutaAnterior = storage_path('app/public/banners/' . $banner->imagen);
            if (file_exists($rutaAnterior)) {
                unlink($rutaAnterior);
            }

            $archivo = time() . '_' . $request->file('imagen')->getClientOriginalName();
            $request->file('imagen')->move(storage_path('app/public/banners'), $archivo);
            $data['imagen'] = $archivo;
        }

        $banner->update($data);

        return back()->with('success', 'Banner actualizado correctamente');
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

        // ✅ Eliminar de storage
        $ruta = storage_path('app/public/banners/' . $banner->imagen);
        if (file_exists($ruta)) {
            unlink($ruta);
        }

        $banner->delete();
        return back()->with('success', 'Banner eliminado');
    }

    public function toggle(Banner $banner)
    {
        $banner->update(['estado' => !$banner->estado]);
        return back()->with('success', 'Estado del banner actualizado.');
    }
}