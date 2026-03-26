<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slide_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slide_id')->constrained('slides')->onDelete('cascade');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('alt_text')->nullable();
            $table->string('url', 500)->nullable();
            $table->timestamps();

            $table->unique(['slide_id', 'language_id']);
            $table->index(['slide_id', 'language_id']);
            $table->index('titulo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slide_translations');
    }
};
