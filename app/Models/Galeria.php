<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Galeria extends Model
{
    use HasFactory;

    protected $table = 'galleries';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activa',
    ];

    /**
     * Relación: Una galería puede tener muchos contenidos
     */
    public function contents()
    {
        return $this->hasMany(Content::class);
    }
}