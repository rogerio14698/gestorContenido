<?php

namespace Database\Seeders;

use App\Models\TipoContenido;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoContenidoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            ['tipo_contenido' => 'Contenido'],
            ['tipo_contenido' => 'Imagenes Slide'],
            ['tipo_contenido' => 'Noticias'],
            ['tipo_contenido' => 'Portada'],
            ['tipo_contenido' => 'Galerías'],
            ['tipo_contenido' => 'Menú'],
            ['tipo_contenido' => 'Multimedia'],
            ['tipo_contenido' => 'Configuración'],
        ];

        foreach ($tipos as $tipo) {
            TipoContenido::create($tipo);
        }
    }
}
