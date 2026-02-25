<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TipoContenido;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TipoContenidoController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:contenidos,mostrar')->only(['index', 'show']);
        $this->middleware('permission:contenidos,crear')->only(['create', 'store']);
        $this->middleware('permission:contenidos,editar')->only(['edit', 'update']);
        $this->middleware('permission:contenidos,eliminar')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tipos = TipoContenido::orderBy('tipo_contenido')->get();
        return view('admin.tipos-contenido.index', compact('tipos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.tipos-contenido.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tipo_contenido' => 'required|string|max:50|unique:tipo_contenidos,tipo_contenido',
            'descripcion' => 'nullable|string|max:255',
        ]);

        TipoContenido::create([
            'tipo_contenido' => $request->tipo_contenido,
            'descripcion' => $request->descripcion,
        ]);

        return redirect()->route('admin.tipos-contenido.index')
                        ->with('success', 'Tipo de contenido creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TipoContenido $tipos_contenido)
    {
        $tipo = $tipos_contenido; // Alias para mantener consistencia con la vista
        return view('admin.tipos-contenido.show', compact('tipo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TipoContenido $tipos_contenido)
    {
        $tipo = $tipos_contenido; // Alias para mantener consistencia con la vista
        return view('admin.tipos-contenido.edit', compact('tipo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TipoContenido $tipos_contenido)
    {
        $request->validate([
            'tipo_contenido' => 'required|string|max:50|unique:tipo_contenidos,tipo_contenido,' . $tipos_contenido->id,
            'descripcion' => 'nullable|string|max:255',
        ]);

        $tipos_contenido->update([
            'tipo_contenido' => $request->tipo_contenido,
            'descripcion' => $request->descripcion,
        ]);

        return redirect()->route('admin.tipos-contenido.index')
                        ->with('success', 'Tipo de contenido actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TipoContenido $tipos_contenido)
    {
        try {
            // Log para debug
            \Log::info('TipoContenidoController@destroy - Iniciando eliminación', [
                'id' => $tipos_contenido->id,
                'tipo' => $tipos_contenido->tipo_contenido
            ]);
            
            // Verificar si tiene contenidos asociados
            $contenidosAsociados = \App\Models\TextoIdioma::where('tipo_contenido_id', $tipos_contenido->id)->count();
            
            \Log::info('TipoContenidoController@destroy - Contenidos asociados: ' . $contenidosAsociados);
            
            if ($contenidosAsociados > 0) {
                \Log::info('TipoContenidoController@destroy - Eliminación bloqueada por contenidos asociados');
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar este tipo de contenido porque tiene ' . $contenidosAsociados . ' contenido(s) asociado(s).'
                ], 400);
            }

            \Log::info('TipoContenidoController@destroy - Procediendo a eliminar');
            $tipos_contenido->delete();
            \Log::info('TipoContenidoController@destroy - Eliminación completada');

            return response()->json([
                'success' => true,
                'message' => 'Tipo de contenido eliminado exitosamente.'
            ]);
        } catch (\Exception $e) {
            \Log::error('TipoContenidoController@destroy - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar: ' . $e->getMessage()
            ], 500);
        }
    }
}