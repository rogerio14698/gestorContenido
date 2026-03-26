<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gallery_id')->constrained('galleries')->onDelete('cascade');
            $table->string('titulo')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('imagen');
            $table->string('imagen_miniatura')->nullable();
            $table->string('alt_text')->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('activa')->default(true);
            $table->json('metadatos')->nullable();
            $table->timestamps();

            $table->index(['gallery_id', 'orden']);
            $table->index(['gallery_id', 'activa']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_images');
    }
};
