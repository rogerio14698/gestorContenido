<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    use HasFactory;

    protected $table = 'configuracion';

    protected $fillable = [
        'nombre_empresa',
        'direccion_empresa',
        'telefono_empresa',
        'movil_empresa',
        'email',
        'nif_cif',
        'metatitulo',
        'metadescripcion',
        'g_analytics',
        'url',
        'youtube',
        'google_plus',
        'instagram',
        'twitter',
        'facebook',
    ];
}