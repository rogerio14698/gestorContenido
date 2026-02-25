<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relaciones
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Métodos de utilidad para permisos
    public function hasRole($role): bool
    {
        if (!$this->role) {
            return false;
        }
        
        if (is_string($role)) {
            return $this->role->slug === $role;
        }
        
        if ($role instanceof Role) {
            return $this->role->id === $role->id;
        }
        
        return false;
    }

    public function hasPermission($permission): bool
    {
        if (!$this->role) {
            return false;
        }
        
        return $this->role->hasPermission($permission);
    }

    public function hasPermissionTo($module, $action): bool
    {
        if (!$this->role) {
            return false;
        }
        
        return $this->role->hasPermissionTo($module, $action);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('administrador');
    }

    // Accessors
    public function getRoleNameAttribute(): string
    {
        return $this->role ? $this->role->nombre : 'Sin rol';
    }
}
