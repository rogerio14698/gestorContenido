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
        Schema::table('textos_idiomas', function (Blueprint $table) {
            $table->string('imagen_alt', 255)->nullable()->after('metadescripcion');
            $table->string('imagen_portada_alt', 255)->nullable()->after('imagen_alt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('textos_idiomas', function (Blueprint $table) {
            $table->dropColumn(['imagen_alt', 'imagen_portada_alt']);
        });
    }
};
