<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ImageConfig;

class ImageConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Eliminar configuraciones existentes para regenerar con responsive
        ImageConfig::truncate();
        
        $configs = [
            // === CONFIGURACIONES PARA NOTICIAS ===
            [
                'tipo_contenido' => 'noticia',
                'tipo_imagen' => 'imagen',
                'ancho' => 800,
                'alto' => 600,
                'ancho_movil' => 400,
                'alto_movil' => 300,
                'mantener_aspecto' => true,
                'mantener_aspecto_movil' => true,
                'formato' => 'jpg',
                'calidad' => 85,
                'calidad_movil' => 80,
                'redimensionar' => true,
                'generar_version_movil' => true,
                'activo' => true,
                'descripcion' => 'Imagen principal de noticias - Desktop: 800x600, Móvil: 400x300',
            ],
            [
                'tipo_contenido' => 'noticia',
                'tipo_imagen' => 'imagen_portada',
                'ancho' => 400,
                'alto' => 300,
                'ancho_movil' => 200,
                'alto_movil' => 150,
                'mantener_aspecto' => true,
                'mantener_aspecto_movil' => true,
                'formato' => 'jpg',
                'calidad' => 85,
                'calidad_movil' => 80,
                'redimensionar' => true,
                'generar_version_movil' => true,
                'activo' => true,
                'descripcion' => 'Imagen de portada para listados - Desktop: 400x300, Móvil: 200x150',
            ],

            // === CONFIGURACIONES PARA PÁGINAS ===
            [
                'tipo_contenido' => 'pagina',
                'tipo_imagen' => 'imagen',
                'ancho' => 1200,
                'alto' => 800,
                'ancho_movil' => 600,
                'alto_movil' => 400,
                'mantener_aspecto' => true,
                'mantener_aspecto_movil' => true,
                'formato' => 'jpg',
                'calidad' => 90,
                'calidad_movil' => 85,
                'redimensionar' => true,
                'generar_version_movil' => true,
                'activo' => true,
                'descripcion' => 'Imagen principal de páginas - Desktop: 1200x800, Móvil: 600x400',
            ],
            [
                'tipo_contenido' => 'pagina',
                'tipo_imagen' => 'imagen_portada',
                'ancho' => 600,
                'alto' => 400,
                'ancho_movil' => 300,
                'alto_movil' => 200,
                'mantener_aspecto' => true,
                'mantener_aspecto_movil' => true,
                'formato' => 'jpg',
                'calidad' => 90,
                'calidad_movil' => 85,
                'redimensionar' => true,
                'generar_version_movil' => true,
                'activo' => true,
                'descripcion' => 'Imagen de portada de páginas - Desktop: 600x400, Móvil: 300x200',
            ],

            // === CONFIGURACIONES PARA ENTREVISTAS ===
            [
                'tipo_contenido' => 'entrevista',
                'tipo_imagen' => 'imagen',
                'ancho' => 800,
                'alto' => 600,
                'ancho_movil' => 400,
                'alto_movil' => 300,
                'mantener_aspecto' => true,
                'mantener_aspecto_movil' => true,
                'formato' => 'jpg',
                'calidad' => 85,
                'calidad_movil' => 80,
                'redimensionar' => true,
                'generar_version_movil' => true,
                'activo' => true,
                'descripcion' => 'Imagen principal de entrevistas - Desktop: 800x600, Móvil: 400x300',
            ],
            [
                'tipo_contenido' => 'entrevista',
                'tipo_imagen' => 'imagen_portada',
                'ancho' => 400,
                'alto' => 300,
                'ancho_movil' => 200,
                'alto_movil' => 150,
                'mantener_aspecto' => true,
                'mantener_aspecto_movil' => true,
                'formato' => 'jpg',
                'calidad' => 85,
                'calidad_movil' => 80,
                'redimensionar' => true,
                'generar_version_movil' => true,
                'activo' => true,
                'descripcion' => 'Imagen de portada de entrevistas - Desktop: 400x300, Móvil: 200x150',
            ],

            // === CONFIGURACIONES PARA GALERÍAS ===
            [
                'tipo_contenido' => 'galeria',
                'tipo_imagen' => 'imagen',
                'ancho' => 1000,
                'alto' => 750,
                'ancho_movil' => 500,
                'alto_movil' => 375,
                'mantener_aspecto' => true,
                'mantener_aspecto_movil' => true,
                'formato' => 'jpg',
                'calidad' => 90,
                'calidad_movil' => 85,
                'redimensionar' => true,
                'generar_version_movil' => true,
                'activo' => true,
                'descripcion' => 'Imágenes de galería - Desktop: 1000x750, Móvil: 500x375',
            ],
            [
                'tipo_contenido' => 'galeria',
                'tipo_imagen' => 'thumbnail',
                'ancho' => 200,
                'alto' => 150,
                'ancho_movil' => 150,
                'alto_movil' => 112,
                'mantener_aspecto' => true,
                'mantener_aspecto_movil' => true,
                'formato' => 'jpg',
                'calidad' => 80,
                'calidad_movil' => 75,
                'redimensionar' => true,
                'generar_version_movil' => true,
                'activo' => true,
                'descripcion' => 'Miniaturas de galería - Desktop: 200x150, Móvil: 150x112',
            ],

            // === CONFIGURACIONES PARA SLIDES ===
            [
                'tipo_contenido' => 'slide',
                'tipo_imagen' => 'imagen',
                'ancho' => 1920,
                'alto' => 1080,
                'ancho_movil' => 800,
                'alto_movil' => 450,
                'mantener_aspecto' => true,
                'mantener_aspecto_movil' => true,
                'formato' => 'jpg',
                'calidad' => 90,
                'calidad_movil' => 85,
                'redimensionar' => true,
                'generar_version_movil' => true,
                'activo' => true,
                'descripcion' => 'Imagen principal de slides - Desktop: 1920x1080 (Full HD), Móvil: 800x450',
            ],
            [
                'tipo_contenido' => 'slide',
                'tipo_imagen' => 'imagen_miniatura',
                'ancho' => 300,
                'alto' => 200,
                'ancho_movil' => 150,
                'alto_movil' => 100,
                'mantener_aspecto' => true,
                'mantener_aspecto_movil' => true,
                'formato' => 'jpg',
                'calidad' => 80,
                'calidad_movil' => 75,
                'redimensionar' => true,
                'generar_version_movil' => true,
                'activo' => true,
                'descripcion' => 'Miniatura de slides - Desktop: 300x200, Móvil: 150x100',
            ],
        ];

        foreach ($configs as $config) {
            ImageConfig::create($config);
        }

        $this->command->info('✅ Configuraciones de imagen responsive creadas exitosamente.');
        $this->command->info('📱 Desktop y móvil configurados para: noticias, páginas, entrevistas y galerías.');
    }
}
