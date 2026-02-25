<?php
// Migración SQL directa para forzar tipo_contenido_id nullable en SQLite
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Esta migración está diseñada para SQLite (PRAGMA). Si el driver no es
        // sqlite, nos saltamos la operación para evitar errores en MySQL/MariaDB.
        try {
            $driver = DB::getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);
        } catch (\Throwable $e) {
            // Si no podemos detectar el driver, abortamos para evitar comandos inválidos.
            return;
        }

        if ($driver !== 'sqlite') {
            // No aplicar esta migración en MySQL/MariaDB
            return;
        }

        // SQLite no soporta ALTER COLUMN, hay que recrear la tabla
        DB::statement('PRAGMA foreign_keys=off;');
        DB::transaction(function () {
            DB::statement('CREATE TABLE IF NOT EXISTS textos_idiomas_tmp AS SELECT * FROM textos_idiomas;');
            DB::statement('DROP TABLE textos_idiomas;');
            DB::statement('CREATE TABLE textos_idiomas (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                idioma_id INTEGER NOT NULL,
                contenido_id INTEGER,
                tipo_contenido_id INTEGER NULL,
                titulo VARCHAR(255) NULL,
                subtitulo VARCHAR(255) NULL,
                resumen VARCHAR(255) NULL,
                contenido TEXT NULL,
                metadescripcion TEXT NULL,
                metatitulo TEXT NULL,
                slug VARCHAR(191) NULL,
                visible BOOLEAN DEFAULT 1,
                imagen_alt TEXT NULL,
                imagen_portada_alt TEXT NULL,
                objeto_type VARCHAR(255) NULL,
                objeto_id INTEGER NULL,
                campo VARCHAR(255) NULL,
                texto TEXT NULL,
                activo BOOLEAN DEFAULT 1,
                created_at DATETIME NULL,
                updated_at DATETIME NULL
            );');
            DB::statement('INSERT INTO textos_idiomas (id, idioma_id, contenido_id, tipo_contenido_id, titulo, subtitulo, resumen, contenido, metadescripcion, metatitulo, slug, visible, imagen_alt, imagen_portada_alt, objeto_type, objeto_id, campo, texto, activo, created_at, updated_at) SELECT id, idioma_id, contenido_id, tipo_contenido_id, titulo, subtitulo, resumen, contenido, metadescripcion, metatitulo, slug, visible, imagen_alt, imagen_portada_alt, objeto_type, objeto_id, campo, texto, activo, created_at, updated_at FROM textos_idiomas_tmp;');
            DB::statement('DROP TABLE textos_idiomas_tmp;');
        });
        DB::statement('PRAGMA foreign_keys=on;');
    }

    public function down(): void
    {
        // No reversible
    }
};
