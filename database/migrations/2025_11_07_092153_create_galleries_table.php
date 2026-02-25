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
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->string('slug', 255)->unique();
            $table->text('descripcion')->nullable();
            $table->string('imagen_portada')->nullable(); // Imagen principal de la galería
            $table->boolean('activa')->default(true);
            $table->boolean('visible_web')->default(true);
            $table->integer('orden')->default(0);
            $table->json('configuracion')->nullable(); // Configuraciones específicas de la galería
            $table->timestamps();
            
            $table->index(['activa', 'visible_web']);
            $table->index('orden');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('galleries');
    }
};
