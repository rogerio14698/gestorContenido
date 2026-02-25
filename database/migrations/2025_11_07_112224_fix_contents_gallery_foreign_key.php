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
        // Protección: desactivar restricciones FK para operaciones de alteración
        Schema::disableForeignKeyConstraints();

        // Intentamos eliminar la FK existente (si existe) y actualizar el enum.
        // Usamos SQL directo para alterar el enum (más fiable en MySQL sin doctrine/dbal).
        try {
            Schema::table('contents', function (Blueprint $table) {
                // Intentar eliminar FK (si existe). Si no existe, capturamos excepción abajo.
                try { $table->dropForeign(['galeria_id']); } catch (\Throwable $e) {}
            });

            // Cambiamos el enum por sentencia SQL para evitar dependencia de doctrine/dbal.
            // Esto asume MySQL; en otros motores puede necesitar adaptación.
            DB::statement("ALTER TABLE contents MODIFY tipo_contenido ENUM('pagina','noticia','entrevista','galeria') NOT NULL DEFAULT 'noticia'");

            // Añadir nueva FK apuntando a `galleries` si la columna existe
            Schema::table('contents', function (Blueprint $table) {
                if (Schema::hasColumn('contents', 'galeria_id')) {
                    $table->foreign('galeria_id')->references('id')->on('galleries')->onDelete('set null');
                }
            });
        } finally {
            Schema::enableForeignKeyConstraints();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertimos de forma segura: desactivar FK, intentar revertir enum y recrear FK antigua
        Schema::disableForeignKeyConstraints();
        try {
            Schema::table('contents', function (Blueprint $table) {
                try { $table->dropForeign(['galeria_id']); } catch (\Throwable $e) {}
            });

            // Revertir enum vía SQL (MySQL)
            DB::statement("ALTER TABLE contents MODIFY tipo_contenido ENUM('pagina','noticia','entrevista') NOT NULL DEFAULT 'noticia'");

            Schema::table('contents', function (Blueprint $table) {
                if (Schema::hasColumn('contents', 'galeria_id')) {
                    $table->foreign('galeria_id')->references('id')->on('galerias')->onDelete('set null');
                }
            });
        } finally {
            Schema::enableForeignKeyConstraints();
        }
    }
};
