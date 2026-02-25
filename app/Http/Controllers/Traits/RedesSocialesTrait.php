<?php

namespace App\Http\Controllers\Traits;

trait RedesSocialesTrait
{
    /**
     * Devuelve un array de redes sociales válidas para la vista
     * Cada elemento tiene: icono, url, alt
     */
    public function obtenerRedesSociales($configEmpresa)
    {
        if (!$configEmpresa || !is_array($configEmpresa->redes_sociales)) {
            return [];
        }
        return collect($configEmpresa->redes_sociales)
            ->filter(function($red) {
                return !empty($red['url']) && !empty($red['icono']);
            })
            ->map(function($red) {
                return [
                    'icono' => $red['icono'],
                    'url'   => $red['url'],
                    'alt'   => $red['alt'] ?? '',
                ];
            })->values()->toArray();
    }
}
