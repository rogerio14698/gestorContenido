<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Models\Menu;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar ImageService
        $this->app->singleton(\App\Services\ImageService::class, function ($app) {
            return new \App\Services\ImageService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // View composer para datos de empresa en todas las vistas
        View::composer('*', function ($view) {
            $configEmpresa = \App\Models\ConfiguracionEmpresa::first();
            $view->with('configEmpresa', $configEmpresa);
        });

        // Configurar rutas de redirección para autenticación
        $this->app['router']->middleware('auth')->group(function () {
            // Configuración manejada por middleware
        });
        // View composer para el menú del footer
        View::composer('layouts.app', function ($view) {
            $idioma = app()->getLocale();
            $menuPrincipal = Menu::where('visible', true)
                ->where('menu_pie', true)
                ->orderBy('orden')
                ->get();
            $view->with('menuPrincipal', $menuPrincipal);
        });
    }
}
