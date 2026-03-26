<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->string('lugar', 100)->nullable();
            $table->date('fecha')->nullable();
            $table->date('fecha_publicacion')->nullable();
            $table->enum('tipo_contenido', ['pagina', 'noticia', 'entrevista', 'galeria'])->default('noticia');
            $table->string('imagen', 100)->nullable();
            $table->text('imagen_alt')->nullable();
            $table->string('imagen_portada', 191)->nullable();
            $table->text('imagen_portada_alt')->nullable();
            $table->boolean('pagina_estatica')->default(false);
            $table->tinyInteger('columnas')->default(1);
            $table->text('fb_pixel')->nullable();
            $table->boolean('portada')->default(false);
            $table->foreignId('galeria_id')->nullable()->constrained('galleries')->onDelete('set null');
            $table->enum('actions', ['inicio', 'noticias', 'contacto'])->nullable();
            $table->integer('orden')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
