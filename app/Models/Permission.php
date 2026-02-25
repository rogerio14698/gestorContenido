<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Permission extends Model
{
    protected $fillable = [
        'nombre',
        'slug',
        'modulo',
        'tipo_permiso',
        'descripcion',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Constantes para módulos y tipos de permisos
    public const TIPOS_PERMISO = ['crear', 'mostrar', 'editar', 'eliminar'];
    
    public const MODULOS = [
        'usuarios' => 'Usuarios',
        'roles' => 'Roles',
        'permisos' => 'Permisos',
        'idiomas' => 'Idiomas',
        'menus' => 'Menús',
        'slides' => 'Slides',
        'contenidos' => 'Contenidos',
        'galerias' => 'Galerías',
        'imagenes' => 'Configuración de Imágenes'
    ];

    // Relaciones
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    // Mutadores
    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = $value;
        if (empty($this->attributes['slug'])) {
            $this->attributes['slug'] = Str::slug($value);
        }
    }

    // Accessors
    public function getModuloNombreAttribute(): string
    {
        return self::MODULOS[$this->modulo] ?? ucfirst($this->modulo);
    }

    public function getTipoPermisoNombreAttribute(): string
    {
        return ucfirst($this->tipo_permiso);
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->tipo_permiso_nombre} {$this->modulo_nombre}";
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeByModulo($query, $modulo)
    {
        return $query->where('modulo', $modulo);
    }

    public function scopeByTipo($query, $tipo)
    {
        return $query->where('tipo_permiso', $tipo);
    }

    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }

    // Métodos estáticos
    public static function getModulosOptions(): array
    {
        return self::MODULOS;
    }

    public static function getTiposPermisoOptions(): array
    {
        return array_combine(self::TIPOS_PERMISO, array_map('ucfirst', self::TIPOS_PERMISO));
    }
}
