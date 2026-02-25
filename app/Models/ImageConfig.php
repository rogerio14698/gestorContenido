<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo_contenido',
        'tipo_imagen',
        'ancho',
        'alto',
        'ancho_movil',
        'alto_movil',
        'mantener_aspecto',
        'mantener_aspecto_movil',
        'formato',
        'calidad',
        'calidad_movil',
        'redimensionar',
        'generar_version_movil',
        'activo',
        'descripcion',
    ];

    protected $casts = [
        'mantener_aspecto' => 'boolean',
        'mantener_aspecto_movil' => 'boolean',
        'redimensionar' => 'boolean',
        'generar_version_movil' => 'boolean',
        'activo' => 'boolean',
        'ancho' => 'integer',
        'alto' => 'integer',
        'ancho_movil' => 'integer',
        'alto_movil' => 'integer',
        'calidad' => 'integer',
        'calidad_movil' => 'integer',
    ];

    /**
     * Obtener configuración para un tipo específico de contenido e imagen
     */
    public static function getConfig($tipoContenido, $tipoImagen)
    {
        return self::where('tipo_contenido', $tipoContenido)
                   ->where('tipo_imagen', $tipoImagen)
                   ->where('activo', true)
                   ->first();
    }

    /**
     * Obtener todas las configuraciones activas
     */
    public static function getActiveConfigs()
    {
        return self::where('activo', true)
                   ->orderBy('tipo_contenido')
                   ->orderBy('tipo_imagen')
                   ->get()
                   ->groupBy('tipo_contenido');
    }
}