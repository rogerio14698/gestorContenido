<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->nullable();
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('metatitulo_es')->nullable();
            $table->text('metadescripcion_es')->nullable();
            $table->string('metatitulo_ast')->nullable();
            $table->text('metadescripcion_ast')->nullable();
            $table->string('metatitulo_en')->nullable();
            $table->text('metadescripcion_en')->nullable();
            $table->json('redes_sociales')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
