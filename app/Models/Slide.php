<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;

class Slide extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'imagen',
        'imagen_miniatura', 
        'metadatos',
        'nueva_ventana',
        'visible',
        'orden',
        'activo'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'metadatos' => 'array',
        'nueva_ventana' => 'boolean',
        'visible' => 'boolean',
        'activo' => 'boolean',
        'orden' => 'integer'
    ];

    /**
     * The attributes that should have default values.
     */
    protected $attributes = [
        'nueva_ventana' => false,
        'visible' => true,
        'activo' => true,
        'metadatos' => '[]'
    ];

    /**
     * Boot method to handle automatic ordering.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-assign order on create
        static::creating(function ($slide) {
            if (is_null($slide->orden)) {
                $slide->orden = static::max('orden') + 1;
            }
        });
    }

    /**
     * Get the translations for the slide.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(SlideTranslation::class);
    }

    /**
     * Get translation for specific language.
     */
    public function translation($idioma_id = null)
    {
        $idioma_id = $idioma_id ?? $this->getCurrentLanguageId();
        
        return $this->translations()
                   ->where('language_id', $idioma_id)
                   ->first();
    }

    /**
     * Get current translation based on app locale.
     */
    public function currentTranslation()
    {
        return $this->translation();
    }

    /**
     * Get translated title.
     */
    public function getTituloAttribute()
    {
        $translation = $this->currentTranslation();
        return $translation ? $translation->titulo : '';
    }

    /**
     * Get translated description.
     */
    public function getDescripcionAttribute()
    {
        $translation = $this->currentTranslation();
        return $translation ? $translation->descripcion : '';
    }

    /**
     * Get translated alt text.
     */
    public function getAltTextAttribute()
    {
        $translation = $this->currentTranslation();
        return $translation ? $translation->alt_text : '';
    }

    /**
     * Get translated URL.
     */
    public function getUrlAttribute()
    {
        $translation = $this->currentTranslation();
        return $translation ? $translation->url : '';
    }

    /**
     * Accessor para obtener la URL completa de la imagen
     */
    public function getImagenUrlAttribute(): ?string
    {
        return $this->imagen ? Storage::url($this->imagen) : null;
    }

    /**
     * Accessor para obtener la URL completa de la miniatura
     */
    public function getMiniaturaUrlAttribute(): ?string
    {
        return $this->imagen_miniatura ? Storage::url($this->imagen_miniatura) : null;
    }

    /**
     * Scope para obtener slides visibles
     */
    public function scopeVisible($query)
    {
        return $query->where('visible', true)->where('activo', true);
    }

    /**
     * Scope para obtener slides ordenados
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('orden', 'asc')->orderBy('created_at', 'asc');
    }

    /**
     * Scope para slides activos
     */
    public function scopeActive($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Obtener el siguiente orden disponible
     */
    public static function getNextOrder(): int
    {
        return (static::max('orden') ?? 0) + 1;
    }

    /**
     * Reordenar slides
     */
    public static function reorder(array $slidesOrder): bool
    {
        try {
            foreach ($slidesOrder as $orden => $slideId) {
                static::where('id', $slideId)->update(['orden' => $orden + 1]);
            }
            return true;
        } catch (\Exception $e) {
            \Log::error('Error reordenando slides: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si tiene imagen
     */
    public function hasImage(): bool
    {
        return !empty($this->imagen) && Storage::disk('public')->exists($this->imagen);
    }

    /**
     * Obtener slides para mostrar en frontend
     */
    public static function getForDisplay()
    {
        return static::visible()
            ->ordered()
            ->get();
    }

    /**
     * Get current language ID.
     */
    private function getCurrentLanguageId(): int
    {
        $currentLocale = \App\Helpers\IdiomaHelper::normalizarEtiqueta(App::getLocale());
        $etiqueta = $currentLocale ?: 'es';
        $idioma = \App\Models\Idioma::where('etiqueta', $etiqueta)->first();
        
        // Si no encuentra por etiqueta, usar el idioma principal
        if (!$idioma) {
            $idioma = \App\Models\Idioma::where('es_principal', true)->first();
        }
        
        // Si aún no encuentra, usar el primero
        if (!$idioma) {
            $idioma = \App\Models\Idioma::first();
        }
        
        return $idioma ? $idioma->id : 3; // Fallback al ID 3 (español)
    }

    /**
     * Get all translations for the slide keyed by language code
     */
    public function getTranslationsByLanguage()
    {
        $translations = $this->translations()->with('idioma')->get();
        
        return $translations->mapWithKeys(function ($translation) {
            return [$translation->idioma->codigo => $translation];
        });
    }

    /**
     * Save translations for multiple languages
     */
    public function saveTranslations(array $translations)
    {
        foreach ($translations as $idioma_id => $data) {
            $this->translations()->updateOrCreate(
                ['language_id' => $idioma_id],
                [
                    'titulo' => $data['titulo'] ?? '',
                    'descripcion' => $data['descripcion'] ?? '',
                    'alt_text' => $data['alt_text'] ?? '',
                    'url' => $data['url'] ?? '',
                ]
            );
        }
    }
}
