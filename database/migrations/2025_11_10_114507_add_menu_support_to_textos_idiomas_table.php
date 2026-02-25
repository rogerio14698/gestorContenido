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
        Schema::table('textos_idiomas', function (Blueprint $table) {
            // Agregar campos polimórficos para soportar diferentes modelos
            $table->string('objeto_type')->nullable()->after('tipo_contenido_id');
            $table->unsignedBigInteger('objeto_id')->nullable()->after('objeto_type');
            $table->string('campo')->nullable()->after('objeto_id'); // Para identificar qué campo es (titulo, descripcion, etc.)
            $table->text('texto')->nullable()->after('campo'); // Campo genérico para el texto
            $table->boolean('activo')->default(true)->after('visible');
            
            // No usar ->change() (requiere doctrine/dbal). En su lugar, dejaremos la columna
            // `contenido_id` como está y, si existe, la hacemos nullable con SQL cuando sea necesario.
            
            // Agregar índices para mejor rendimiento
            $table->index(['objeto_type', 'objeto_id']);
            $table->index(['objeto_type', 'objeto_id', 'idioma_id', 'campo']);
        });

        // Si la columna existe, asegurar que sea NULLABLE usando SQL directo (MySQL).
        if (Schema::hasColumn('textos_idiomas', 'contenido_id')) {
            try {
                DB::statement("ALTER TABLE textos_idiomas MODIFY contenido_id BIGINT UNSIGNED NULL");
            } catch (\Throwable $e) {
                // Si falla, no abortamos la migración; el cambio puede requerir doctrine/dbal.
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('textos_idiomas', function (Blueprint $table) {
            $table->dropIndex(['objeto_type', 'objeto_id']);
            $table->dropIndex(['objeto_type', 'objeto_id', 'idioma_id', 'campo']);
            $table->dropColumn(['objeto_type', 'objeto_id', 'campo', 'texto', 'activo']);
            // Intentar restaurar `contenido_id` a not-null si existe, usando SQL directo.
        });

        if (Schema::hasColumn('textos_idiomas', 'contenido_id')) {
            try {
                DB::statement("ALTER TABLE textos_idiomas MODIFY contenido_id BIGINT UNSIGNED NOT NULL");
            } catch (\Throwable $e) {
                // Ignorar: puede requerir doctrine/dbal para ciertos motores.
            }
        }

        // Nota: Se eliminó un cierre extra '});' que provocaba un ParseError.
        // Este cierre sobrante fue removido para corregir la sintaxis de la migración.
    }
};
