<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\ConfiguracionEmpresa;
use App\Http\Controllers\Traits\RedesSocialesTrait;

class FooterComposerServiceProvider extends ServiceProvider
{
    use RedesSocialesTrait;

    public function boot()
    {
        View::composer('web.partials.footer', function ($view) {
            $configEmpresa = ConfiguracionEmpresa::first();
            $redesSociales = $this->obtenerRedesSociales($configEmpresa);
            $view->with('configEmpresa', $configEmpresa)
                 ->with('redesSociales', $redesSociales);
        });
    }
}
