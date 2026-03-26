<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_image_texts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gallery_image_id')->constrained('gallery_images')->onDelete('cascade');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->string('titulo')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('alt_text', 255)->nullable();
            $table->timestamps();

            $table->index(['gallery_image_id', 'language_id']);
            $table->unique(['gallery_image_id', 'language_id'], 'unique_gallery_image_language');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_image_texts');
    }
};
