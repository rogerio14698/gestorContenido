<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\ConfiguracionEmpresa;
use App\Models\Idioma;
use App\Models\TextoIdioma;

return new class extends Migration {
    public function up(): void
    {
        $empresa = ConfiguracionEmpresa::first();
        if (!$empresa) return;
        $idiomas = Idioma::all();
        foreach ($idiomas as $idioma) {
            $etiqueta = $idioma->etiqueta;
            $metatitulo = $empresa->{"metatitulo_{$etiqueta}"} ?? null;
            $metadescripcion = $empresa->{"metadescripcion_{$etiqueta}"} ?? null;
            if ($metatitulo || $metadescripcion) {
                TextoIdioma::updateOrCreate([
                    'objeto_type' => ConfiguracionEmpresa::class,
                    'objeto_id' => $empresa->id,
                    'idioma_id' => $idioma->id,
                ], [
                    'metatitulo' => $metatitulo,
                    'metadescripcion' => $metadescripcion,
                    'visible' => true,
                    'activo' => true,
                ]);
            }
        }
    }

    public function down(): void
    {
        $empresa = ConfiguracionEmpresa::first();
        if (!$empresa) return;
        $idiomas = Idioma::all();
        foreach ($idiomas as $idioma) {
            TextoIdioma::where([
                'objeto_type' => ConfiguracionEmpresa::class,
                'objeto_id' => $empresa->id,
                'idioma_id' => $idioma->id,
            ])->delete();
        }
    }
};
