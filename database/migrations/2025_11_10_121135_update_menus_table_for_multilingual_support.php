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
        // Actualizar la tabla de menús para el nuevo sistema multiidioma
        Schema::table('menus', function (Blueprint $table) {
            // Quitar campos que no necesitamos más
            $table->dropColumn(['title', 'label']);
            
            // Añadir nuevos campos
            $table->enum('tipo_enlace', ['contenido', 'url_externa', 'ninguno'])->default('contenido')->after('parent_id');
            $table->unsignedBigInteger('tipo_contenido_id')->nullable()->after('tipo_enlace');
            $table->boolean('visible')->default(true)->after('icon');
            $table->boolean('abrir_nueva_ventana')->default(false)->after('visible');
            $table->integer('orden')->default(1)->after('order');
            
            // Renombrar order a orden_old temporalmente para migrar datos
            $table->renameColumn('order', 'orden_old');
        });
        
        // Migrar datos de order a orden
        DB::statement('UPDATE menus SET orden = COALESCE(orden_old, 1)');
        
        // Eliminar column temporal
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('orden_old');
        });
        
        // Añadir foreign key para tipo_contenido
        Schema::table('menus', function (Blueprint $table) {
            $table->foreign('tipo_contenido_id')->references('id')->on('tipo_contenidos')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            // Restaurar campos originales
            $table->string('title')->after('parent_id');
            $table->string('label')->nullable()->after('title');
            $table->integer('order')->nullable()->after('url');
            
            // Eliminar nuevos campos
            $table->dropForeign(['tipo_contenido_id']);
            $table->dropColumn([
                'tipo_enlace',
                'tipo_contenido_id', 
                'visible',
                'abrir_nueva_ventana',
                'orden'
            ]);
        });
    }
};