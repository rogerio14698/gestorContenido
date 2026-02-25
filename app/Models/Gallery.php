<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Gallery extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'activa'
    ];

    protected $casts = [
        'activa' => 'boolean',
    ];

    /**
     * Relación con las imágenes de la galería
     */
    public function images()
    {
        return $this->hasMany(GalleryImage::class)->orderBy('orden')->orderBy('id');
    }

    /**
     * Obtener la primera imagen como portada
     */
    public function getImagenPortadaAttribute()
    {
        $firstImage = $this->images()->first();
        return $firstImage ? $firstImage->imagen : null;
    }

    /**
     * Obtener URL de la imagen de portada responsive
     */
    public function getPortadaDesktopUrlAttribute()
    {
        if ($this->imagen_portada) {
            // desktop = false
            return responsive_image('storage/' . $this->imagen_portada, false);
        }
        return null;
    }

    public function getPortadaMobileUrlAttribute()
    {
        if ($this->imagen_portada) {
            // mobile = true
            return responsive_image('storage/' . $this->imagen_portada, true);
        }
        return null;
    }

    /**
     * Scope para galerías activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    /**
     * Generar un slug simple basado en el nombre para URLs
     */
    public function getSlugAttribute()
    {
        return \Str::slug($this->nombre);
    }
}
