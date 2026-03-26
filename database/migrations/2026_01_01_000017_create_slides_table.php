<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slides', function (Blueprint $table) {
            $table->id();
            $table->boolean('nueva_ventana')->default(false);
            $table->string('imagen')->nullable();
            $table->string('imagen_miniatura')->nullable();
            $table->json('metadatos')->nullable();
            $table->boolean('visible')->default(true);
            $table->integer('orden')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['visible', 'activo', 'orden']);
            $table->index(['orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slides');
    }
};
