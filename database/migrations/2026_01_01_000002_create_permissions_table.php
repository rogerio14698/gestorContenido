<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->string('modulo');
            $table->enum('tipo_permiso', ['crear', 'mostrar', 'editar', 'eliminar']);
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['modulo', 'tipo_permiso']);
            $table->unique(['modulo', 'tipo_permiso', 'slug'], 'permissions_unique_module_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
