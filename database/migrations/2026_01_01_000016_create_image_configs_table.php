<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('image_configs', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_contenido');
            $table->string('tipo_imagen');
            $table->integer('ancho');
            $table->integer('alto');
            $table->integer('ancho_movil')->nullable();
            $table->integer('alto_movil')->nullable();
            $table->boolean('mantener_aspecto')->default(true);
            $table->boolean('mantener_aspecto_movil')->default(true);
            $table->integer('calidad_movil')->default(85);
            $table->boolean('generar_version_movil')->default(true);
            $table->string('descripcion')->nullable();
            $table->string('formato')->default('jpg');
            $table->integer('calidad')->default(85);
            $table->boolean('redimensionar')->default(true);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['tipo_contenido', 'tipo_imagen']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('image_configs');
    }
};
