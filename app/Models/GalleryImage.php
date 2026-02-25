<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GalleryImage extends Model
{
    protected $fillable = [
        'gallery_id',
        'titulo',
        'descripcion', 
        'imagen',
        'imagen_miniatura',
        'alt_text',
        'orden',
        'activa',
        'metadatos'
    ];

    protected $casts = [
        'activa' => 'boolean',
        'metadatos' => 'array'
    ];

    /**
     * Relación con la galería
     */
    public function gallery(): BelongsTo
    {
        return $this->belongsTo(Gallery::class);
    }

    /**
     * Relación con los textos multiidioma
     */
    public function texts(): HasMany
    {
        return $this->hasMany(GalleryImageText::class);
    }

    /**
     * Obtener texto para un idioma específico
     */
    public function getTextForLanguage($idiomaId): ?GalleryImageText
    {
        return $this->texts()->where('idioma_id', $idiomaId)->first();
    }

    /**
     * Obtener texto alternativo multiidioma con fallback
     */
    public function getMultilingualAltText($idiomaId = null): string
    {
        if (!$idiomaId) {
            $idiomaId = app()->getLocale() === 'es' ? 
                \App\Models\Idioma::where('es_principal', true)->first()?->id : 
                \App\Models\Idioma::where('etiqueta', app()->getLocale())->first()?->id;
        }

        $text = $this->getTextForLanguage($idiomaId);
        
        if ($text && $text->alt_text) {
            return $text->alt_text;
        }
        
        if ($text && $text->titulo) {
            return $text->titulo;
        }
        
        // Fallback al alt_text base
        return $this->alt_text ?: $this->titulo ?: 'Imagen de galería ' . $this->gallery->nombre;
    }

    /**
     * Scope para imágenes activas
     */
    public function scopeActive($query)
    {
        return $query->where('activa', true);
    }

    /**
     * Obtener URL de la imagen con sistema responsive
     */
    public function getImagenUrlAttribute(): string
    {
        return responsive_image($this->imagen, false); // Versión desktop
    }

    /**
     * Obtener URL de la imagen móvil con sistema responsive
     */
    public function getImagenMobileUrlAttribute(): string
    {
        return responsive_image($this->imagen, true); // Versión móvil
    }

    /**
     * Obtener URL de la miniatura
     */
    public function getMiniaturaUrlAttribute(): ?string
    {
        if ($this->imagen_miniatura) {
            return responsive_image($this->imagen_miniatura);
        }
        
        // Fallback a la imagen principal
        return $this->imagen_url;
    }

    /**
     * Generar HTML responsive para la imagen
     */
    public function getResponsiveHtmlAttribute(): string
    {
        return responsive_image_html(
            $this->imagen,
            $this->alt_text ?: $this->titulo ?: 'Imagen de galería',
            'gallery-image',
            ''
        );
    }

    /**
     * Obtener texto alt apropiado
     */
    public function getAltTextAttribute($value): string
    {
        return $value ?: $this->titulo ?: 'Imagen de galería ' . $this->gallery->nombre;
    }
}
