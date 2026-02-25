<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryImageText extends Model
{
    use HasFactory;

    protected $fillable = [
        'gallery_image_id',
        'idioma_id',
        'titulo',
        'descripcion',
        'alt_text'
    ];

    /**
     * Relación con la imagen de galería
     */
    public function galleryImage(): BelongsTo
    {
        return $this->belongsTo(GalleryImage::class);
    }

    /**
     * Relación con el idioma
     */
    public function idioma(): BelongsTo
    {
        return $this->belongsTo(Idioma::class);
    }

    /**
     * Scope para obtener texto por idioma específico
     */
    public function scopeForLanguage($query, $idiomaId)
    {
        return $query->where('idioma_id', $idiomaId);
    }

    /**
     * Obtener el texto alternativo con fallback
     */
    public function getAltTextWithFallbackAttribute(): string
    {
        if ($this->alt_text) {
            return $this->alt_text;
        }
        
        if ($this->titulo) {
            return $this->titulo;
        }
        
        return $this->galleryImage->gallery->nombre . ' - Imagen ' . $this->galleryImage->id;
    }
}