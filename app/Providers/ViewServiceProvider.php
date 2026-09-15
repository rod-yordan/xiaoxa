<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Categoria;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Compartir las categorías activas con el navbar
        View::composer('components.navbar', function ($view) {
            $categoriasMenu = Categoria::where('estado_categoria', 1)
                ->orderBy('nombre_categoria', 'asc')
                ->get(['id_categoria', 'nombre_categoria']);

            $view->with('categoriasMenu', $categoriasMenu);
        });
    }
}