<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TipoContenido extends Model
{
    use HasFactory;

    protected $table = 'tipo_contenidos';

    protected $fillable = [
        'tipo_contenido',
        'icono',
        'descripcion',
    ];

    /**
     * Accessor para nombre (compatibilidad)
     */
    public function getNombreAttribute()
    {
        return $this->tipo_contenido;
    }

    /**
     * Obtener solo tipos de contenido que deben aparecer en menús públicos
     */
    public static function tiposParaMenu()
    {
        $permitidos = [
            'pagina',
            'paginas',
            'contenido',
            'noticia',
            'noticias',
            'entrevista',
            'entrevistas',
            'galeria',
            'galerias',
            'portada',
            'multimedia'
        ];

        return self::all()
            ->filter(function ($tipo) use ($permitidos) {
                $slug = Str::slug($tipo->tipo_contenido);
                return in_array($slug, $permitidos, true);
            })
            ->sortBy('tipo_contenido')
            ->values();
    }

    /**
     * Relación: Un tipo de contenido puede tener muchos textos
     */
    public function textos()
    {
        return $this->hasMany(TextoIdioma::class);
    }
}