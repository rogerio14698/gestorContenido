<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    protected $table = 'contents';

    protected $fillable = [
        'lugar',
        'fecha',
        'fecha_publicacion',
        'tipo_contenido',
        'imagen',
        'imagen_alt',
        'imagen_portada',
        'imagen_portada_alt',
        'pagina_estatica',
        'columnas',
        'fb_pixel',
        'portada',
        'galeria_id',
        'actions',
        'orden',
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_publicacion' => 'date',
        'pagina_estatica' => 'boolean',
        'portada' => 'boolean',
    ];

    /**
     * Relación: Un contenido pertenece a una galería
     */
    public function galeria()
    {
        return $this->belongsTo(Gallery::class, 'galeria_id');
    }

    /**
     * Relación: Un contenido puede tener muchos textos en diferentes idiomas
     */
    public function textos()
    {
        return $this->hasMany(TextoIdioma::class, 'contenido_id');
    }

    /**
     * Relación: Un contenido puede tener muchos menús
     */
    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    /**
     * Scope para contenidos de portada
     */
    public function scopePortada($query)
    {
        return $query->where('portada', true);
    }

    /**
     * Scope para páginas estáticas
     */
    public function scopePaginaEstatica($query)
    {
        return $query->where('pagina_estatica', true);
    }

    /**
     * Scope para noticias
     */
    public function scopeNoticias($query)
    {
        return $query->where('tipo_contenido', 'noticia');
    }

    /**
     * Obtener el texto en un idioma específico
     */
    public function getTextoEnIdioma($idiomaId)
    {
        return $this->textos()->where('idioma_id', $idiomaId)->first();
    }

    /**
     * Accessor para obtener el título del contenido
     */
    public function getTituloAttribute()
    {
        // Intentar obtener el texto en el idioma actual de la sesión
        $idiomaActual = session('idioma_actual', 'es');
        
        $texto = $this->textos()
            ->whereHas('idioma', function($query) use ($idiomaActual) {
                $query->where('etiqueta', $idiomaActual);
            })
            ->first();
        
        if ($texto && $texto->titulo) {
            return $texto->titulo;
        }
        
        // Fallback al idioma principal
        $idiomaPrincipal = \App\Models\Idioma::where('es_principal', true)->first();
        if ($idiomaPrincipal) {
            $textoFallback = $this->textos()->where('idioma_id', $idiomaPrincipal->id)->first();
            if ($textoFallback && $textoFallback->titulo) {
                return $textoFallback->titulo;
            }
        }
        
        // Si no hay título, usar el primer texto disponible
        $primerTexto = $this->textos()->first();
        if ($primerTexto && $primerTexto->titulo) {
            return $primerTexto->titulo;
        }
        
        return 'Contenido sin título';
    }

    /**
     * Relación con tipo de contenido
     */
    public function tipoContenido()
    {
        return $this->belongsTo(TipoContenido::class, 'tipo_contenido_id');
    }
}