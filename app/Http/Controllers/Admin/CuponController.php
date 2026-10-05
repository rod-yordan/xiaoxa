<?php

namespace App\Http\Controllers\Admin;

use App\Models\Cupon;
use App\Models\CuponUsado;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CuponController extends Controller
{
    // ── LISTAR ──────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Cupon::query();

        // Filtro: buscar por código
        if ($request->filled('buscar')) {
            $query->where('codigo_cupon', 'like', '%' . $request->buscar . '%');
        }

        // Filtro: estado
        if ($request->filled('estado')) {
            $query->where('estado_cupon', $request->estado);
        }

        // Filtro: fecha de vencimiento
        if ($request->filled('fecha')) {
            $query->whereDate('fecha_vencimiento', $request->fecha);
        }

        $cupones = $query->orderBy('fecha_vencimiento', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.cupones.index', compact('cupones'));
    }

    // ── CREAR ───────────────────────────────────────────────
    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo_cupon'        => 'required|unique:cupones,codigo_cupon|max:50',
            'monto_cupon'         => 'required|numeric|min:0',
            'monto_compra_minima' => 'required|numeric|min:0',
            'fecha_vencimiento'   => 'required|date|after:today',
            'estado_cupon'        => 'nullable|boolean',
        ]);

        $data['codigo_cupon'] = strtoupper(trim($data['codigo_cupon']));
        $data['estado_cupon'] = $request->input('estado_cupon', 1);

        Cupon::create($data);

        return redirect()->route('admin.cupones.index')
            ->with('success', 'Cupón creado correctamente.');
    }

    // ── EDITAR (vista) ──────────────────────────────────────
    public function edit($id)
    {
        $cupon = Cupon::findOrFail($id);
        return view('admin.cupones.edit', compact('cupon'));
    }

    // ── ACTUALIZAR ──────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $cupon = Cupon::findOrFail($id);

        $data = $request->validate([
            'codigo_cupon'        => 'required|unique:cupones,codigo_cupon,' . $id . ',id_cupon|max:50',
            'monto_cupon'         => 'required|numeric|min:0',
            'monto_compra_minima' => 'required|numeric|min:0',
            'fecha_vencimiento'   => 'required|date',
            'estado_cupon'        => 'nullable|boolean',
        ]);

        $data['codigo_cupon'] = strtoupper(trim($data['codigo_cupon']));

        if ($request->has('estado_cupon')) {
            $data['estado_cupon'] = $request->estado_cupon;
        }

        $cupon->update($data);

        return back()->with('success', 'Cupón actualizado correctamente.');
    }

    // ── ACTIVAR / DESACTIVAR ────────────────────────────────
    public function toggle($id)
    {
        $cupon = Cupon::findOrFail($id);

        $cupon->estado_cupon = !$cupon->estado_cupon;
        $cupon->save();

        return back()->with('success', $cupon->estado_cupon
            ? 'Cupón activado.'
            : 'Cupón desactivado.');
    }

    // ── ELIMINAR ────────────────────────────────────────────
    public function destroy($id)
    {
        $cupon = Cupon::findOrFail($id);

        // Eliminar registros de uso asociados
        CuponUsado::where('id_cupon', $id)->delete();

        $cupon->delete();

        return back()->with('success', 'Cupón eliminado correctamente.');
    }
}