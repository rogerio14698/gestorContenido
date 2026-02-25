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
        Schema::table('image_configs', function (Blueprint $table) {
            // Campos para versión móvil
            $table->integer('ancho_movil')->nullable()->after('alto');
            $table->integer('alto_movil')->nullable()->after('ancho_movil');
            $table->boolean('mantener_aspecto_movil')->default(true)->after('alto_movil');
            $table->integer('calidad_movil')->default(85)->after('mantener_aspecto_movil');
            
            // Campo para activar/desactivar versión móvil
            $table->boolean('generar_version_movil')->default(true)->after('calidad_movil');
            
            // Descripción de la configuración
            $table->string('descripcion')->nullable()->after('generar_version_movil');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('image_configs', function (Blueprint $table) {
            $table->dropColumn([
                'ancho_movil',
                'alto_movil',
                'mantener_aspecto_movil',
                'calidad_movil',
                'generar_version_movil',
                'descripcion'
            ]);
        });
    }
};
