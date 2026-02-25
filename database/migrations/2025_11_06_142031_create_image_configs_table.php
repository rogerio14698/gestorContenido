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
        Schema::create('image_configs', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_contenido'); // 'noticia', 'pagina', 'entrevista'
            $table->string('tipo_imagen'); // 'imagen', 'imagen_portada'
            $table->integer('ancho'); // Ancho en píxeles
            $table->integer('alto'); // Alto en píxeles
            $table->boolean('mantener_aspecto')->default(true); // Mantener proporción
            $table->string('formato')->default('jpg'); // jpg, png, webp
            $table->integer('calidad')->default(85); // Calidad de compresión (1-100)
            $table->boolean('redimensionar')->default(true); // Si redimensionar automáticamente
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            // Índices únicos
            $table->unique(['tipo_contenido', 'tipo_imagen']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_configs');
    }
};
