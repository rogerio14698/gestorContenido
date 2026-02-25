<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsurePermission
{
    /**
     * Verifica que el usuario autenticado posea el permiso solicitado.
     */
    public function handle(Request $request, Closure $next, string $module, string $action = 'mostrar')
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Acceso no autorizado.');
        }

        // Los administradores tienen acceso completo
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return $next($request);
        }

        if (!$user->hasPermissionTo($module, $action)) {
            \Log::warning('Permiso denegado', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'module' => $module,
                'action' => $action,
            ]);

            abort(403, 'No tienes permisos suficientes para acceder a esta sección.');
        }

        return $next($request);
    }
}
