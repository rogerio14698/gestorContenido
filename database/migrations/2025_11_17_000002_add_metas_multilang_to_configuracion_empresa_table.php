<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('configuracion_empresa', function (Blueprint $table) {
            $table->string('metatitulo_es')->nullable()->after('email');
            $table->text('metadescripcion_es')->nullable()->after('metatitulo_es');
            $table->string('metatitulo_ast')->nullable()->after('metadescripcion_es');
            $table->text('metadescripcion_ast')->nullable()->after('metatitulo_ast');
            $table->string('metatitulo_en')->nullable()->after('metadescripcion_ast');
            $table->text('metadescripcion_en')->nullable()->after('metatitulo_en');
        });
    }

    public function down(): void
    {
        Schema::table('configuracion_empresa', function (Blueprint $table) {
            $table->dropColumn([
                'metatitulo_es', 'metadescripcion_es',
                'metatitulo_ast', 'metadescripcion_ast',
                'metatitulo_en', 'metadescripcion_en',
            ]);
        });
    }
};
