<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('slides', function (Blueprint $table) {
            $table->string('titulo'); // Título del slide
            $table->text('descripcion')->nullable(); // Descripción del slide
            $table->string('alt_text')->nullable(); // Texto alternativo para la imagen
            $table->string('url')->nullable(); // URL de destino del enlace
            $table->boolean('nueva_ventana')->default(false); // Abrir en ventana nueva
            $table->string('imagen'); // Ruta de la imagen principal
            $table->string('imagen_miniatura')->nullable(); // Ruta de la miniatura
            $table->json('metadatos')->nullable(); // Datos EXIF, tamaño, dimensiones originales
            $table->boolean('visible')->default(true); // Visible/Oculto
            $table->integer('orden')->default(0); // Orden de aparición
            $table->boolean('activo')->default(true); // Estado general del slide
            
            // Índices para optimizar consultas
            $table->index(['visible', 'activo', 'orden']); // Para obtener slides visibles ordenados
            $table->index('orden'); // Para reordenamiento
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('slides', function (Blueprint $table) {
            $table->dropColumn([
                'titulo', 'descripcion', 'alt_text', 'url', 'nueva_ventana',
                'imagen', 'imagen_miniatura', 'metadatos', 'visible', 'orden', 'activo'
            ]);
            $table->dropIndex(['slides_visible_activo_orden_index']);
            $table->dropIndex(['slides_orden_index']);
        });
    }
};
