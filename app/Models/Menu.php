<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menus';

    protected $fillable = [
        'parent_id',
        'tipo_enlace',
        'tipo_contenido_id', 
        'content_id',
        'url',
        'url_externa',
        'icon',
        'visible',
        'abrir_nueva_ventana',
        'menu_pie',
        'orden',
    ];

    protected $casts = [
        'menu_pie' => 'boolean',
        'visible' => 'boolean',
        'abrir_nueva_ventana' => 'boolean',
    ];

    /**
     * Relación: Un menú puede tener un padre (menú padre)
     */
    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    /**
     * Relación: Un menú puede tener muchos hijos (submenús)
     */
    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('orden');
    }

    /**
     * Relación: Un menú puede estar asociado a un contenido
     */
    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    /**
     * Relación: Un menú puede estar asociado a un tipo de contenido
     */
    public function tipoContenido()
    {
        return $this->belongsTo(TipoContenido::class);
    }

    /**
     * Relación: Un menú tiene textos en diferentes idiomas
     */
    public function textos()
    {
        return $this->morphMany(TextoIdioma::class, 'objeto');
    }

    /**
     * Obtener el título del menú en un idioma específico con fallback
     */
    public function getTituloAttribute()
    {
        $idiomaEtiqueta = session('idioma_actual', 'es');

        // Buscar el idioma actual
        $idiomaActual = \App\Models\Idioma::where('etiqueta', $idiomaEtiqueta)->first();
        
        if ($idiomaActual) {
            $texto = \App\Models\TextoIdioma::where('objeto_type', 'App\Models\Menu')
                ->where('objeto_id', $this->id)
                ->where('idioma_id', $idiomaActual->id)
                ->where('activo', true)
                ->first();

            if ($texto && !empty($texto->titulo)) {
                return $texto->titulo;
            }
        }

        // Fallback al idioma principal si no existe
        $idiomaPrincipal = \App\Models\Idioma::where('es_principal', true)->first();
        if ($idiomaPrincipal) {
            $textoFallback = \App\Models\TextoIdioma::where('objeto_type', 'App\Models\Menu')
                ->where('objeto_id', $this->id)
                ->where('idioma_id', $idiomaPrincipal->id)
                ->where('activo', true)
                ->first();

            if ($textoFallback && !empty($textoFallback->titulo)) {
                return $textoFallback->titulo;
            }
        }

        // Si no hay textos, retornar un valor por defecto
        return 'Menú sin título';
    }

    /**
     * Método para obtener el título en un idioma específico
     */
    public function getTituloEnIdioma($idiomaEtiqueta)
    {
        return $this->getTituloAttribute($idiomaEtiqueta);
    }

    /**
     * Método para obtener la URL completa del menú
     */
    public function getUrlCompletaAttribute()
    {
        switch ($this->tipo_enlace) {
            case 'contenido':
                if ($this->content) {
                    // Generar URL basada en el tipo de contenido
                    $tipoContenido = $this->tipoContenido;
                    if ($tipoContenido) {
                        switch ($tipoContenido->nombre) {
                            case 'página':
                            case 'pagina':
                                return route('pagina.show', $this->content->slug);
                            case 'noticia':
                                return route('noticias.show', $this->content->slug);
                            case 'entrevista':
                                return route('entrevistas.show', $this->content->slug);
                            default:
                                return route('contenido.show', $this->content->slug);
                        }
                    }
                    return route('contenido.show', $this->content->slug);
                }
                return '#';
            case 'url_externa':
                return $this->url ?? '#';
            default:
                return '#';
        }
    }

    /**
     * Scope para menús principales (sin padre)
     */
    public function scopePrincipal($query)
    {
        return $query->whereNull('parent_id')->orderBy('orden');
    }

    /**
     * Scope para menús visibles
     */
    public function scopeVisible($query)
    {
        return $query->where('visible', true);
    }

    /**
     * Scope para menús de pie
     */
    public function scopeMenuPie($query)
    {
        return $query->where('menu_pie', true)->orderBy('orden');
    }

    /**
     * Scope para menús principales visibles con sus hijos visibles
     */
    public function scopeMenuCompleto($query)
    {
        return $query->whereNull('parent_id')
                    ->where('visible', true)
                    ->with(['children' => function ($query) {
                        $query->where('visible', true)->orderBy('orden');
                    }])
                    ->orderBy('orden');
    }
}