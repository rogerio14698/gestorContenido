<?php

namespace Database\Seeders;

use App\Models\Idioma;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IdiomaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Idioma::create([
            'idioma' => 'Español',
            'codigo' => 'es',
            'label' => 'CAS',
            'imagen' => 'es.png',
            'principal' => true,
            'activado' => true,
        ]);

        Idioma::create([
            'idioma' => 'Asturiano',
            'codigo' => 'as',
            'label' => 'AST',
            'imagen' => 'as.png',
            'principal' => false,
            'activado' => true,
        ]);
    }
}
