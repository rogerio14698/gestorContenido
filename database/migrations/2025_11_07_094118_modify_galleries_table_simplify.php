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
        // Respaldamos los datos existentes
        $galleries = DB::table('galleries')->get();
        
        // Recreamos la tabla simplificada
        // Desactivamos temporalmente las restricciones FK para evitar errores
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('galleries');
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
        
        // Restauramos los datos (solo nombre, descripción y activa)
        foreach ($galleries as $gallery) {
            DB::table('galleries')->insert([
                'id' => $gallery->id,
                'nombre' => $gallery->nombre,
                'descripcion' => $gallery->descripcion,
                'activa' => $gallery->activa ?? true,
                'created_at' => $gallery->created_at,
                'updated_at' => $gallery->updated_at,
            ]);
        }

        // Reactivamos las restricciones FK
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('galleries', function (Blueprint $table) {
            // Restaurar columnas eliminadas
            $table->string('slug')->unique()->after('nombre');
            $table->string('imagen_portada')->nullable()->after('descripcion');
            $table->integer('orden')->default(0)->after('activa');
            $table->boolean('visible_web')->default(true)->after('activa');
            $table->json('configuracion')->nullable()->after('orden');
        });
    }
};
