<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('etiqueta', 10)->unique();
            $table->string('imagen', 255)->nullable();
            $table->boolean('activo')->default(true);
            $table->boolean('es_principal')->default(false);
            $table->integer('orden')->default(0);
            $table->timestamps();

            $table->index(['activo']);
            $table->index(['es_principal']);
            $table->index(['orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
