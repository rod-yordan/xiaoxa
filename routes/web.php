<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\UbicacionController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\Admin\ProductoController;
use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\PedidoController;
use App\Http\Controllers\Admin\AgenciaController;
use App\Http\Controllers\Admin\DashboardController;
use App\Models\Producto;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CuponController;
use App\Http\Controllers\PedidoUsuarioController;
use App\Http\Controllers\Api\ImageController;

// ── PÚBLICAS

Route::get('/', [HomeController::class, 'index'])->name('home');

// ✅ Ruta para servir imágenes desde storage
Route::get('/api/imagen/{filename}', [ImageController::class, 'show'])
    ->where('filename', '.*')
    ->name('imagen.show');

// ✅ Detalle del producto - con variantes + imágenes + categoría
Route::get('/producto/{id}', function ($id) {
    $producto = Producto::with(['variantes.imagenes', 'categoria'])
        ->findOrFail($id);
    return view('producto.detalle', compact('producto'));
})->name('producto.show');

// CARRITO
Route::get('/carrito',                   [CarritoController::class, 'index'])->name('carrito.index');
Route::post('/carrito/add/{id}',         [CarritoController::class, 'add'])->name('carrito.add');
Route::get('/carrito/aumentar/{id}',     [CarritoController::class, 'aumentar'])->name('carrito.aumentar');
Route::get('/carrito/disminuir/{id}',    [CarritoController::class, 'disminuir'])->name('carrito.disminuir');
Route::get('/carrito/eliminar/{id}',     [CarritoController::class, 'eliminar'])->name('carrito.eliminar');

// UBICACIÓN
Route::prefix('ubicacion')->name('ubicacion.')->group(function () {
    Route::get('/provincias/{id}', [UbicacionController::class, 'provincias'])->name('provincias');
    Route::get('/distritos/{id}',  [UbicacionController::class, 'distritos'])->name('distritos');
    Route::get('/agencias/{id}',   [UbicacionController::class, 'agencias'])->name('agencias');
});

// ── AUTENTICADOS
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/finalizar-compra',            [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/finalizar-compra/confirmar', [CheckoutController::class, 'confirmar'])->name('checkout.confirmar');
    Route::get('/pedido-confirmado',            fn() => view('pedido.confirmado'))->name('pedido.confirmado');

    Route::put('/usuario/actualizar', [UsuarioController::class, 'actualizar'])->name('usuario.actualizar');

    Route::get('/perfil',        [PerfilController::class, 'index'])->name('perfil.index');
    Route::get('/perfil/editar', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil',        [PerfilController::class, 'update'])->name('perfil.update');

    //Mercado pago
    Route::post('/pago/crear',    [PagoController::class, 'crearPreferencia'])->name('pago.crear');
    Route::get('/pago/exito',     [PagoController::class, 'exito'])->name('pago.exito');
    Route::get('/pago/fallo',     [PagoController::class, 'fallo'])->name('pago.fallo');
    Route::get('/pago/pendiente', [PagoController::class, 'pendiente'])->name('pago.pendiente');
});

// ── ADMINISTRADOR

Route::middleware(['auth', 'verified', 'role:1'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Productos
        Route::resource('productos', ProductoController::class)->except(['show']);

        // Categorías
        Route::resource('categorias', CategoriaController::class)->except(['show']);
        Route::patch('categorias/{id}/toggle', [CategoriaController::class, 'toggle'])->name('categorias.toggle');

        // Pedidos
        Route::get('pedidos',      [PedidoController::class, 'index'])->name('pedidos.index');
        Route::get('pedidos/{id}', [PedidoController::class, 'show'])->name('pedidos.show');
        Route::put('pedidos/{id}', [PedidoController::class, 'update'])->name('pedidos.update');

        // Agencias
        Route::resource('agencias', AgenciaController::class)->except(['show']);
        Route::patch('agencias/{agencia}/toggle', [AgenciaController::class, 'toggleEstado'])->name('agencias.toggle');

        // selects encadenados (departamento → provincia → distrito)
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('provincias/{id}', [AgenciaController::class, 'provincias'])->name('provincias');
            Route::get('distritos/{id}',  [AgenciaController::class, 'distritos'])->name('distritos');
        });

        //Banners y cupones
        Route::resource('banners', BannerController::class)->except(['show']);
        Route::patch('banners/{banner}/toggle', [BannerController::class, 'toggle'])->name('banners.toggle');
        Route::resource('cupones', CuponController::class)->except(['show']);
        Route::patch('cupones/{cupon}/toggle',  [CuponController::class,  'toggle'])->name('cupones.toggle');
    });

// ── PERFIL

Route::middleware('auth')->prefix('perfil')->name('perfil.')->group(function () {
    Route::get('/',        [PerfilController::class, 'index'])->name('index');
    Route::get('/editar',  [PerfilController::class, 'edit'])->name('edit');
    Route::put('/update',  [PerfilController::class, 'update'])->name('update');
    Route::put('/email',   [PerfilController::class, 'updateEmail'])->name('update-email');
    Route::put('/password', [PerfilController::class, 'updatePassword'])->name('update-password');
    Route::delete('/',     [PerfilController::class, 'destroy'])->name('destroy');
    Route::get('/mis-pedidos', [PedidoUsuarioController::class, 'index'])
        ->name('pedidos.index');
});

require __DIR__ . '/auth.php';