<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SlideTranslation extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'slide_translations';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'slide_id',
        'idioma_id',
        'titulo',
        'descripcion', 
        'alt_text',
        'url'
    ];

    /**
     * Get the slide that owns the translation.
     */
    public function slide(): BelongsTo
    {
        return $this->belongsTo(Slide::class);
    }

    /**
     * Get the language that owns the translation.
     */
    public function idioma(): BelongsTo
    {
        return $this->belongsTo(Idioma::class);
    }

    /**
     * Set the URL attribute with proper formatting.
     */
    public function setUrlAttribute($value): void
    {
        if (empty($value)) {
            $this->attributes['url'] = null;
            return;
        }

        // Agregar http:// si no tiene protocolo
        if (!preg_match('/^https?:\/\//', $value)) {
            $value = 'http://' . $value;
        }

        $this->attributes['url'] = $value;
    }
}
