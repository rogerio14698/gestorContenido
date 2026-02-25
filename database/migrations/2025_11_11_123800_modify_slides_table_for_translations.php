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
        // Primero eliminamos las columnas de texto multiidioma de la tabla slides
        Schema::table('slides', function (Blueprint $table) {
            // Eliminar campos que van a estar en traducciones
            $table->dropColumn(['titulo', 'descripcion', 'alt_text', 'url']);
            
            // Hacer que orden sea nullable y autoincrement
            $table->integer('orden')->nullable()->change();
        });

        // Crear tabla de traducciones para slides
        Schema::create('slide_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slide_id')->constrained()->onDelete('cascade');
            $table->foreignId('idioma_id')->constrained('idiomas')->onDelete('cascade');
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('alt_text')->nullable();
            $table->string('url', 500)->nullable();
            $table->timestamps();
            
            // Índice único para evitar duplicar idioma por slide
            $table->unique(['slide_id', 'idioma_id']);
            
            // Índices para mejorar rendimiento
            $table->index(['slide_id', 'idioma_id']);
            $table->index('titulo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar tabla de traducciones
        Schema::dropIfExists('slide_translations');
        
        // Restaurar campos en tabla slides
        Schema::table('slides', function (Blueprint $table) {
            $table->string('titulo')->after('id');
            $table->text('descripcion')->nullable()->after('titulo');
            $table->string('alt_text')->nullable()->after('descripcion');
            $table->string('url', 500)->nullable()->after('alt_text');
            $table->integer('orden')->nullable(false)->change();
        });
    }
};