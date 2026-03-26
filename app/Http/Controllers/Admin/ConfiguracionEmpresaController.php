<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ConfiguracionEmpresa;

class ConfiguracionEmpresaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:admin');
    }

    public function edit()
    {
        $config = ConfiguracionEmpresa::firstOrCreate([], [
            'nombre' => null,
            'direccion' => null,
            'telefono' => null,
            'email' => null,
            'redes_sociales' => [],
        ]);
        $idiomas = \App\Models\Idioma::orderBy('orden')->orderBy('nombre')->get();
        // Obtener textos meta por idioma
        $textosMeta = [];
        foreach ($idiomas as $idioma) {
            $texto = $config->textos()->where('language_id', $idioma->id)->first();
            $textosMeta[$idioma->id] = $texto;
        }
        return view('admin.configuracion_empresa.edit', compact('config', 'idiomas', 'textosMeta'));
    }

    public function update(Request $request)
    {
        $idiomas = \App\Models\Idioma::orderBy('orden')->orderBy('nombre')->get();
        $rules = [
            'nombre' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'redes_sociales' => 'nullable',
        ];
        foreach ($idiomas as $idioma) {
            $rules["metatitulo_{$idioma->id}"] = 'nullable|string|max:255';
            $rules["metadescripcion_{$idioma->id}"] = 'nullable|string';
        }
        $data = $request->validate($rules);

        $config = ConfiguracionEmpresa::firstOrCreate([], [
            'nombre' => null,
            'direccion' => null,
            'telefono' => null,
            'email' => null,
            'redes_sociales' => [],
        ]);
        $redes_actuales = $config ? ($config->redes_sociales ?? []) : [];
        $redes = $request->input('redes_sociales', []);

        // Guardar/actualizar textos meta en textos_idiomas
        foreach ($idiomas as $idioma) {
            $texto = $config->textos()->where('language_id', $idioma->id)->first();
            $metaData = [
                'metatitulo' => $data["metatitulo_{$idioma->id}"] ?? null,
                'metadescripcion' => $data["metadescripcion_{$idioma->id}"] ?? null,
                'visible' => true,
                'activo' => true,
            ];
            if ($texto) {
                $texto->update($metaData);
            } else {
                $config->textos()->create(array_merge($metaData, [
                    'language_id' => $idioma->id,
                ]));
            }
        }

        // Eliminar red social si se presionó el botón
        if ($request->has('eliminar_red')) {
            $eliminar = (int)$request->input('eliminar_red');
        } else {
            $eliminar = null;
        }

        // Siempre recorrer los 6 slots y reconstruir el array final
        $redes_final = [];
        foreach (range(0, 5) as $i) {
            if ($eliminar !== null && $eliminar === $i) {
                // Limpiar solo la red social seleccionada
                $redes_final[$i] = [
                    'icono' => null,
                    'url' => null,
                    'alt' => null,
                ];
                continue;
            }
            // Si hay datos en el request, usarlos
            if (isset($redes[$i])) {
                $red = $redes[$i];
            } else {
                $red = [];
            }
            // Procesar icono
            if ($request->hasFile("redes_sociales.$i.icono")) {
                $file = $request->file("redes_sociales.$i.icono");
                $path = $file->store('redes', 'public');
                $red['icono'] = $path;
            } elseif (isset($red['icono']) && $red['icono']) {
                // Ya viene un valor del formulario
                $red['icono'] = $red['icono'];
            } elseif (isset($redes_actuales[$i]['icono']) && $redes_actuales[$i]['icono']) {
                // Mantener el icono existente en BD
                $red['icono'] = $redes_actuales[$i]['icono'];
            } else {
                $red['icono'] = null;
            }
            // Procesar url y alt
            $red['url'] = $red['url'] ?? ($redes_actuales[$i]['url'] ?? null);
            $red['alt'] = $red['alt'] ?? ($redes_actuales[$i]['alt'] ?? null);
            $redes_final[$i] = $red;
        }
        $data['redes_sociales'] = $redes_final;

        $config->update($data);
        return redirect()->back()->with('success', 'Datos actualizados correctamente');
    }
}
