<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionEmpresa extends Model
{
    protected $table = 'company_settings';
    protected $guarded = [];
    protected $fillable = [
        'nombre', 'direccion', 'telefono', 'email', 'redes_sociales',
        // Campos meta multilingües eliminados, ahora gestionados en textos_idiomas
    ];

    /**
     * Relación polimórfica: la empresa puede tener muchos textos multilingües
     */
    public function textos()
    {
        return $this->morphMany(\App\Models\TextoIdioma::class, 'objeto');
    }

    protected $casts = [
        'redes_sociales' => 'array',
    ];
}
