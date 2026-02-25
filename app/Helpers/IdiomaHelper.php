<?php

namespace App\Helpers;

class IdiomaHelper
{
    /**
     * Normaliza la etiqueta de idioma (por ejemplo, 'es-ES' a 'es').
     */
    public static function normalizarEtiqueta($etiqueta)
    {
        if (!$etiqueta) {
            return 'es';
        }
        $etiqueta = strtolower(trim($etiqueta));
        if (in_array($etiqueta, ['es', 'es-es', 'español', 'spanish'])) {
            return 'es';
        }
        if (in_array($etiqueta, ['ast', 'asturianu', 'asturiano', 'as'])) {
            return 'ast';
        }
        if (in_array($etiqueta, ['en', 'en-us', 'en-gb', 'english'])) {
            return 'en';
        }
        if (in_array($etiqueta, ['fr', 'fr-fr', 'francés', 'french'])) {
            return 'fr';
        }
        return $etiqueta;
    }

    /**
     * Convierte una etiqueta de idioma a la forma usada en la URL (por ejemplo, 'es-ES' a 'es', 'asturiano' a 'ast').
     */
    public static function etiquetaParaRuta($etiqueta)
    {
        // Normalizar primero
        $etiqueta = self::normalizarEtiqueta($etiqueta);
        // Si es español, asturiano, inglés, francés, etc, devolver el código corto
        if (in_array($etiqueta, ['es', 'ast', 'en', 'fr'])) {
            return $etiqueta;
        }
        // Fallback: devolver la etiqueta tal cual
        return $etiqueta;
    }
}
