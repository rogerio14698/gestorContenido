<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TextoIdioma extends Model
{
    use HasFactory;

    protected $table = 'textos_idiomas';

    protected $fillable = [
        'idioma_id',
        'contenido_id',
        'tipo_contenido_id', // Puede ser null para empresa
        'titulo',
        'subtitulo',
        'resumen',
        'contenido',
        'metadescripcion',
        'metatitulo',
        'slug',
        'visible',
        'imagen_alt',
        'imagen_portada_alt',
        'objeto_type',
        'objeto_id',
        'campo',
        'texto',
        'activo',
    ];

    protected $casts = [
        'visible' => 'boolean',
        'activo' => 'boolean',
    ];

    /**
     * Relación: Un texto pertenece a un idioma
     */
    public function idioma()
    {
        return $this->belongsTo(Idioma::class);
    }

    /**
     * Relación: Un texto pertenece a un contenido
     */
    public function contenidoModel()
    {
        return $this->belongsTo(Content::class, 'contenido_id');
    }

    /**
     * Relación: Un texto pertenece a un tipo de contenido
     */
    public function tipoContenido()
    {
        return $this->belongsTo(TipoContenido::class);
    }

    /**
     * Relación polimórfica: Un texto puede pertenecer a diferentes modelos
     */
    public function objeto()
    {
        return $this->morphTo();
    }

    /**
     * Scope para textos visibles
     */
    public function scopeVisible($query)
    {
        return $query->where('visible', true);
    }

    /**
     * Scope para obtener texto por idioma
     */
    public function scopeByIdioma($query, $codigoIdioma)
    {
        // Permitir coincidencia flexible: solo la parte base del idioma, compatible con SQLite y MySQL
        $codigoBase = explode('-', strtolower($codigoIdioma))[0];
        return $query->whereHas('idioma', function ($q) use ($codigoBase) {
            $q->whereRaw('(LOWER(etiqueta) = ? OR LOWER(etiqueta) LIKE ?)', [$codigoBase, $codigoBase . '-%']);
        });
    }

    /**
     * Scope para obtener texto por slug
     */
    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }
}