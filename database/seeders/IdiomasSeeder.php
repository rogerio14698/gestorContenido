<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Idioma;
use Illuminate\Support\Facades\Storage;

class IdiomasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar tabla antes de sembrar
        Idioma::query()->delete();

        $idiomas = [
            [
                'nombre' => 'Español',
                'etiqueta' => 'es',
                'imagen' => null, // Se puede agregar después
                'activo' => true,
                'es_principal' => true,
                'orden' => 1,
            ],
            [
                'nombre' => 'Asturiano',
                'etiqueta' => 'ast',
                'imagen' => null, // Se puede agregar después
                'activo' => true,
                'es_principal' => false,
                'orden' => 2,
            ],
            [
                'nombre' => 'English',
                'etiqueta' => 'en',
                'imagen' => null, // Se puede agregar después
                'activo' => false, // Desactivado por defecto
                'es_principal' => false,
                'orden' => 3,
            ]
        ];

        foreach ($idiomas as $idiomaData) {
            Idioma::create($idiomaData);
        }

        $this->command->info('✅ Idiomas base creados correctamente:');
        $this->command->info('   - Español (principal, activo)');
        $this->command->info('   - Asturiano (activo)');
        $this->command->info('   - English (inactivo)');
        $this->command->info('');
        $this->command->info('💡 Puedes activar/desactivar idiomas desde el panel de administración');
        $this->command->info('💡 También puedes subir banderas/imágenes para cada idioma');
    }
}