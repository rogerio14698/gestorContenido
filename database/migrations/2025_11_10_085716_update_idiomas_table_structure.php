<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Crear tabla temporal con nueva estructura
        // Aseguramos que no exista de antemano (por ejecuciones fallidas anteriores)
        Schema::dropIfExists('idiomas_new');
        Schema::create('idiomas_new', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->comment('Nombre completo del idioma (ej: Español, English, Asturiano)');
            $table->string('etiqueta', 10)->unique()->comment('Código ISO del idioma (ej: es, en, ast) para HTML lang');
            $table->string('imagen', 255)->nullable()->comment('Ruta a la imagen/bandera del idioma');
            $table->boolean('activo')->default(true)->comment('Si el idioma está disponible en el sitio web');
            $table->boolean('es_principal')->default(false)->comment('Si es el idioma por defecto del sitio');
            $table->integer('orden')->default(0)->comment('Orden de visualización en selectores');
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index(['activo']);
            $table->index(['es_principal']);
            $table->index(['orden']);
        });

        // Migrar datos existentes usando query builder para ser portable entre motores
        $rows = DB::table('idiomas')->get();
        foreach ($rows as $r) {
            DB::table('idiomas_new')->insert([
                'id' => $r->id,
                'nombre' => $r->idioma ?? ('Idioma ' . $r->id),
                'etiqueta' => $r->codigo ?? ('lang' . $r->id),
                'imagen' => $r->imagen ?? null,
                'activo' => isset($r->activado) ? $r->activado : 1,
                'es_principal' => isset($r->principal) ? $r->principal : 0,
                'orden' => $r->id,
                'created_at' => $r->created_at ?? now(),
                'updated_at' => $r->updated_at ?? now(),
            ]);
        }

        // Eliminar tabla antigua y renombrar la nueva
        // Desactivamos temporalmente las restricciones FK para permitir el drop/rename
        Schema::disableForeignKeyConstraints();
        try {
            Schema::dropIfExists('idiomas');
            Schema::rename('idiomas_new', 'idiomas');
        } finally {
            Schema::enableForeignKeyConstraints();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Crear tabla con estructura original
        // Aseguramos que no exista de antemano
        Schema::dropIfExists('idiomas_old');
        Schema::create('idiomas_old', function (Blueprint $table) {
            $table->id();
            $table->string('idioma');
            $table->string('codigo');
            $table->string('label');
            $table->string('imagen')->nullable();
            $table->boolean('principal')->default(false);
            $table->boolean('activado')->default(true);
            $table->timestamps();
        });

        // Migrar datos de vuelta usando query builder (más portable)
        $rows = DB::table('idiomas')->get();
        foreach ($rows as $r) {
            DB::table('idiomas_old')->insert([
                'id' => $r->id,
                'idioma' => $r->nombre,
                'codigo' => $r->etiqueta,
                'label' => $r->etiqueta,
                'imagen' => $r->imagen ?? null,
                'principal' => $r->es_principal ?? 0,
                'activado' => $r->activo ?? 1,
                'created_at' => $r->created_at ?? now(),
                'updated_at' => $r->updated_at ?? now(),
            ]);
        }

        // Eliminar tabla nueva y restaurar la original
        // Desactivamos temporalmente las restricciones FK para permitir el drop/rename
        Schema::disableForeignKeyConstraints();
        try {
            Schema::dropIfExists('idiomas');
            Schema::rename('idiomas_old', 'idiomas');
        } finally {
            Schema::enableForeignKeyConstraints();
        }
    }
};
