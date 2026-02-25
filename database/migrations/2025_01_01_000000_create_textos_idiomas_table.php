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
        Schema::create('textos_idiomas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idioma_id')->constrained('idiomas')->onDelete('cascade');
            $table->foreignId('contenido_id')->nullable()->constrained('contents')->onDelete('cascade');
            $table->foreignId('tipo_contenido_id')->nullable()->constrained('tipo_contenidos')->onDelete('cascade');
            $table->string('titulo')->nullable();
            $table->string('subtitulo')->nullable();
            $table->string('resumen')->nullable();
            $table->longText('contenido')->nullable();
            $table->text('metadescripcion')->nullable();
            $table->text('metatitulo')->nullable();
            $table->string('slug', 191)->nullable();
            $table->boolean('visible')->default(true);
            $table->timestamps();
            $table->unique(['slug', 'tipo_contenido_id', 'idioma_id'], 'slug_tipo_idioma_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('textos_idiomas');
    }
};
