<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('menus')->onDelete('cascade');
            $table->enum('tipo_enlace', ['contenido', 'url_externa', 'ninguno'])->default('contenido');
            $table->foreignId('tipo_contenido_id')->nullable()->constrained('content_types')->onDelete('set null');
            $table->foreignId('content_id')->nullable()->constrained('contents')->onDelete('set null');
            $table->text('url')->nullable();
            $table->string('url_externa')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('menu_pie')->default(false);
            $table->boolean('visible')->default(true);
            $table->boolean('abrir_nueva_ventana')->default(false);
            $table->integer('orden')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
