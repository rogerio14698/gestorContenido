<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Menu;

class MenusPieComposerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('web.partials.footer', function ($view) {
            $menusPie = Menu::menuPie()->where('visible', true)->get();
            $idiomaRuta = app()->getLocale();
            $view->with('menusPie', $menusPie)->with('idiomaRuta', $idiomaRuta);
        });
    }
}
