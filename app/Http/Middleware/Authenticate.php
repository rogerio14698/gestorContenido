<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (!$request->expectsJson()) {
            // Si la ruta actual es del admin, redirigir a admin.login
            if ($request->is('admin/*') || $request->is('admin')) {
                return route('admin.login');
            }
            
            // Por defecto, redirigir a la ruta login global
            return route('login');
        }
        
        return null;
    }
}
