<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipo_contenidos', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_contenido', 100)->nullable();
            $table->string('icono', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipo_contenidos');
    }
};
