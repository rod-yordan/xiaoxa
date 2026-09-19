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
            'titulo'    => 'required|string|max:100|unique:banners,titulo',
            'url_boton' => 'nullable|string|max:500',
            'orden'     => 'nullable|integer|min:0',
            'estado'    => 'nullable|boolean',
            'imagen'    => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'titulo.required' => 'El título es obligatorio.',
            'titulo.unique'   => 'Ya existe un banner con ese título.',
            'imagen.required' => 'La imagen es obligatoria.',
            'imagen.image'    => 'El archivo debe ser una imagen válida.',
            'imagen.mimes'    => 'Solo se permiten JPG, JPEG, PNG o WEBP.',
            'imagen.max'      => 'La imagen no debe pesar más de 2MB.',
        ]);

        $carpeta = storage_path('app/public/banners');
        if (!file_exists($carpeta)) {
            mkdir($carpeta, 0755, true);
        }

        $archivo = time() . '_' . uniqid() . '_' . $request->file('imagen')->getClientOriginalName();
        $request->file('imagen')->move($carpeta, $archivo);

        $data['imagen'] = $archivo;
        $data['orden']  = $data['orden'] ?? 0;
        $data['estado'] = $data['estado'] ?? 1;

        Banner::create($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner creado correctamente');
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $data = $request->validate([
            'titulo'    => 'required|string|max:100|unique:banners,titulo,' . $id . ',id_banner',
            'url_boton' => 'nullable|string|max:500',
            'orden'     => 'nullable|integer|min:0',
            'estado'    => 'nullable|boolean',
            'imagen'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'titulo.required' => 'El título es obligatorio.',
            'titulo.unique'   => 'Ya existe un banner con ese título.',
            'imagen.image'    => 'El archivo debe ser una imagen válida.',
            'imagen.mimes'    => 'Solo se permiten JPG, JPEG, PNG o WEBP.',
            'imagen.max'      => 'La imagen no debe pesar más de 2MB.',
        ]);

        if ($request->hasFile('imagen')) {
            $rutaAnterior = storage_path('app/public/banners/' . $banner->imagen);
            if (file_exists($rutaAnterior)) {
                unlink($rutaAnterior);
            }

            $archivo = time() . '_' . uniqid() . '_' . $request->file('imagen')->getClientOriginalName();
            $request->file('imagen')->move(storage_path('app/public/banners'), $archivo);
            $data['imagen'] = $archivo;
        }

        $banner->update($data);

        return back()->with('success', 'Banner actualizado correctamente');
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

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