<?php

namespace Database\Seeders;

use App\Models\Configuracion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConfiguracionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Configuracion::create([
            'nombre_empresa' => 'Nuntris Teatro',
            'direccion_empresa' => '',
            'telefono_empresa' => '',
            'movil_empresa' => '',
            'email' => 'info@nuntristeatro.com',
            'nif_cif' => '',
            'metatitulo' => 'Nuntris Teatro - Compañía de Teatro',
            'metadescripcion' => 'Compañía de teatro asturiana especializada en obras clásicas y contemporáneas',
            'g_analytics' => '',
            'url' => 'https://nuntristeatro.com',
            'youtube' => '',
            'google_plus' => '',
            'instagram' => '',
            'twitter' => '',
            'facebook' => '',
        ]);
    }
}
