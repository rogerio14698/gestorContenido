<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_texts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->foreignId('content_id')->nullable()->constrained('contents')->onDelete('cascade');
            $table->foreignId('content_type_id')->nullable()->constrained('content_types')->onDelete('cascade');
            $table->string('objeto_type')->nullable();
            $table->unsignedBigInteger('objeto_id')->nullable();
            $table->string('campo')->nullable();
            $table->text('texto')->nullable();
            $table->string('titulo')->nullable();
            $table->string('subtitulo')->nullable();
            $table->text('resumen')->nullable();
            $table->longText('contenido')->nullable();
            $table->text('metadescripcion')->nullable();
            $table->text('metatitulo')->nullable();
            $table->string('imagen_alt', 255)->nullable();
            $table->string('imagen_portada_alt', 255)->nullable();
            $table->string('slug', 191)->nullable();
            $table->boolean('visible')->default(true);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['objeto_type', 'objeto_id']);
            $table->index(['objeto_type', 'objeto_id', 'language_id', 'campo']);
            $table->unique(['slug', 'content_type_id', 'language_id'], 'slug_content_type_language_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_texts');
    }
};
