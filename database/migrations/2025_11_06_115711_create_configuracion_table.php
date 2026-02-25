<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('configuracion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_empresa')->nullable();
            $table->string('direccion_empresa')->nullable();
            $table->string('telefono_empresa', 20)->nullable();
            $table->string('movil_empresa', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('nif_cif', 20)->nullable();
            $table->string('metatitulo')->nullable();
            $table->string('metadescripcion', 500)->nullable();
            $table->string('g_analytics', 20)->nullable();
            $table->string('url')->nullable();
            $table->string('youtube')->nullable();
            $table->string('google_plus')->nullable();
            $table->string('instagram')->nullable();
            $table->string('twitter')->nullable();
            $table->string('facebook')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracion');
    }
};
