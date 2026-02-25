<?php
// Este provider registra un pattern dinámico para el parámetro {idioma} según los idiomas activos en la base de datos
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Models\Idioma;

class DynamicLocaleRouteProvider extends ServiceProvider
{
    public function boot()
    {
        // Limitar el pattern dinámico SOLO a rutas públicas (no admin)
        // Usar un grupo para evitar afectar rutas admin que usan {idioma} como id
        if (\Schema::hasTable('idiomas')) {
            $etiquetas = Idioma::where('activo', true)->pluck('etiqueta')->toArray();
            if (count($etiquetas) > 0) {
                $pattern = implode('|', array_map('preg_quote', $etiquetas));
                // Solo afecta a rutas que empiezan por /{idioma} (no /admin)
                Route::pattern('idioma', $pattern);
            }
        }
    }
}
