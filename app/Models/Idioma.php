<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class Idioma extends Model
{
    use HasFactory;

    protected $table = 'idiomas';

    protected $fillable = [
        'nombre',
        'etiqueta', 
        'imagen',
        'activo',
        'es_principal',
        'orden'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'es_principal' => 'boolean',
    ];

    /**
     * Boot del modelo para manejar eventos
     */
    protected static function boot()
    {
        parent::boot();

        // Evento antes de crear/actualizar para manejar idioma principal único
        static::saving(function ($idioma) {
            if ($idioma->es_principal) {
                // Si este idioma se marca como principal, quitar principal de todos los demás
                static::where('es_principal', true)
                    ->where('id', '!=', $idioma->id)
                    ->update(['es_principal' => false]);
            }
        });

        // Validar que siempre haya al menos un idioma principal
        static::deleting(function ($idioma) {
            if ($idioma->es_principal) {
                $otrosIdiomas = static::where('id', '!=', $idioma->id)
                    ->where('activo', true)
                    ->count();

                if ($otrosIdiomas === 0) {
                    throw new \Exception('No se puede eliminar el único idioma principal activo.');
                }

                // Asignar principal al primer idioma activo disponible
                static::where('id', '!=', $idioma->id)
                    ->where('activo', true)
                    ->orderBy('orden')
                    ->first()
                    ?->update(['es_principal' => true]);
            }
        });
    }

    /**
     * Relación: Un idioma puede tener muchos textos
     */
    public function textos()
    {
        return $this->hasMany(TextoIdioma::class);
    }

    /**
     * Scope para obtener solo idiomas activos
     */
    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para obtener idiomas ordenados
     */
    public function scopeOrdenados(Builder $query): Builder
    {
        return $query->orderBy('orden')->orderBy('nombre');
    }

    /**
     * Scope para obtener el idioma principal (compatible con versión anterior)
     */
    public function scopePrincipal($query)
    {
        return $query->where('es_principal', true);
    }

    /**
     * Scope para obtener idiomas activos (compatible con versión anterior)
     */
    public function scopeActivado($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Obtener el idioma principal del sistema
     */
    public static function principal(): ?static
    {
        return static::where('es_principal', true)
            ->where('activo', true)
            ->first();
    }

    /**
     * Obtener todos los idiomas activos para el frontend
     */
    public static function activosParaFrontend(): \Illuminate\Database\Eloquent\Collection
    {
        return static::activos()
            ->ordenados()
            ->get();
    }

    /**
     * Verificar si este idioma es el principal
     */
    public function esPrincipal(): bool
    {
        return $this->es_principal;
    }

    /**
     * Obtener la URL completa de la imagen
     */
    public function getImagenUrlAttribute(): ?string
    {
        if (!$this->imagen) {
            return null;
        }

        // Si la imagen ya es una URL completa, devolverla tal como está
        if (filter_var($this->imagen, FILTER_VALIDATE_URL)) {
            return $this->imagen;
        }

        // Si existe en storage, generar URL
        if (Storage::disk('public')->exists($this->imagen)) {
            return Storage::url($this->imagen);
        }

        // Verificar si existe en public directamente
        $publicPath = public_path($this->imagen);
        if (file_exists($publicPath)) {
            return asset($this->imagen);
        }

        return null;
    }

    /**
     * Eliminar imagen asociada al idioma
     */
    public function eliminarImagen(): bool
    {
        if (!$this->imagen) {
            return true;
        }

        try {
            // Intentar eliminar de storage
            if (Storage::disk('public')->exists($this->imagen)) {
                Storage::disk('public')->delete($this->imagen);
            }
            
            // Actualizar el modelo
            $this->update(['imagen' => null]);
            
            return true;
        } catch (\Exception $e) {
            logger()->error('Error eliminando imagen de idioma: ' . $e->getMessage(), [
                'idioma_id' => $this->id,
                'imagen' => $this->imagen
            ]);
            return false;
        }
    }

    /**
     * Obtener el código de idioma para HTML lang attribute
     */
    public function getCodigoHtmlAttribute(): string
    {
        return strtolower($this->etiqueta);
    }

    /**
     * Representación string del modelo
     */
    public function __toString(): string
    {
        return $this->nombre;
    }
}