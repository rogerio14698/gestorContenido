<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Idioma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class IdiomaController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:idiomas,mostrar')->only(['index', 'show']);
        $this->middleware('permission:idiomas,crear')->only(['create', 'store']);
        $this->middleware('permission:idiomas,editar')->only(['edit', 'update', 'updateOrder', 'toggleActive']);
        $this->middleware('permission:idiomas,eliminar')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $idiomas = Idioma::orderBy('orden')
            ->orderBy('nombre')
            ->get();

        return view('admin.idiomas.index', compact('idiomas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.idiomas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'etiqueta' => 'required|string|max:10|unique:idiomas,etiqueta|regex:/^[a-zA-Z][a-zA-Z-_]*$/',
            'imagen' => 'nullable|image|mimes:jpeg,jpg,png,gif,svg,webp|max:2048',
            'activo' => 'boolean',
            'es_principal' => 'boolean',
            'orden' => 'nullable|integer|min:0'
        ], [
            'nombre.required' => 'El nombre del idioma es obligatorio.',
            'nombre.max' => 'El nombre no puede tener más de 100 caracteres.',
            'etiqueta.required' => 'La etiqueta del idioma es obligatoria.',
            'etiqueta.unique' => 'Ya existe un idioma con esta etiqueta.',
            'etiqueta.regex' => 'La etiqueta debe comenzar con una letra y puede contener letras, guiones y guiones bajos.',
            'imagen.image' => 'El archivo debe ser una imagen válida.',
            'imagen.mimes' => 'La imagen debe ser de tipo: jpeg, jpg, png, gif, svg o webp.',
            'imagen.max' => 'La imagen no puede ser mayor a 2MB.',
            'orden.integer' => 'El orden debe ser un número entero.',
            'orden.min' => 'El orden no puede ser negativo.'
        ]);

        // Procesar imagen si se subió
        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen');
            $nombreArchivo = 'idiomas/' . time() . '_' . $imagen->getClientOriginalName();
            $rutaImagen = $imagen->storeAs('public', $nombreArchivo);
            $validated['imagen'] = str_replace('public/', '', $rutaImagen);
        }

        // Si no se especifica orden, asignar el siguiente disponible
        if (!isset($validated['orden'])) {
            $validated['orden'] = (Idioma::max('orden') ?? 0) + 1;
        }

        // Crear el idioma
        $idioma = Idioma::create($validated);

        return redirect()
            ->route('admin.idiomas.index')
            ->with('success', "Idioma '{$idioma->nombre}' creado correctamente.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Idioma $idioma_id)
    {
        $idioma = $idioma_id;
        return view('admin.idiomas.show', compact('idioma'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Idioma $idioma_id)
    {
        $idioma = $idioma_id;
        return view('admin.idiomas.edit', compact('idioma'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Idioma $idioma_id)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'etiqueta' => [
                'required',
                'string',
                'max:10',
                Rule::unique('idiomas', 'etiqueta')->ignore($idioma_id->id),
                'regex:/^[a-zA-Z][a-zA-Z-_]*$/'
            ],
            'imagen' => 'nullable|image|mimes:jpeg,jpg,png,gif,svg,webp|max:2048',
            'activo' => 'boolean',
            'es_principal' => 'boolean',
            'orden' => 'nullable|integer|min:0'
        ], [
            'nombre.required' => 'El nombre del idioma es obligatorio.',
            'nombre.max' => 'El nombre no puede tener más de 100 caracteres.',
            'etiqueta.required' => 'La etiqueta del idioma es obligatoria.',
            'etiqueta.unique' => 'Ya existe un idioma con esta etiqueta.',
            'etiqueta.regex' => 'La etiqueta debe comenzar con una letra y puede contener letras, guiones y guiones bajos.',
            'imagen.image' => 'El archivo debe ser una imagen válida.',
            'imagen.mimes' => 'La imagen debe ser de tipo: jpeg, jpg, png, gif, svg o webp.',
            'imagen.max' => 'La imagen no puede ser mayor a 2MB.',
            'orden.integer' => 'El orden debe ser un número entero.',
            'orden.min' => 'El orden no puede ser negativo.'
        ]);

        // Procesar nueva imagen si se subió
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            if ($idioma->imagen && Storage::disk('public')->exists($idioma->imagen)) {
                Storage::disk('public')->delete($idioma->imagen);
            }

            $imagen = $request->file('imagen');
            $nombreArchivo = 'idiomas/' . time() . '_' . $imagen->getClientOriginalName();
            $rutaImagen = $imagen->storeAs('public', $nombreArchivo);
            $validated['imagen'] = str_replace('public/', '', $rutaImagen);
        }

        // Si se marcó para eliminar imagen
        if ($request->has('eliminar_imagen') && $request->eliminar_imagen) {
            if ($idioma->imagen && Storage::disk('public')->exists($idioma->imagen)) {
                Storage::disk('public')->delete($idioma->imagen);
            }
            $validated['imagen'] = null;
        }

        // Actualizar el idioma
        $idioma_id->update($validated);

        return redirect()
            ->route('admin.idiomas.index')
            ->with('success', "Idioma '{$idioma_id->nombre}' actualizado correctamente.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Idioma $idioma_id)
    {
        try {
            $nombre = $idioma_id->nombre;
            
            // Verificar si es el único idioma principal activo
            if ($idioma_id->es_principal && Idioma::where('activo', true)->count() === 1) {
                return redirect()
                    ->route('admin.idiomas.index')
                    ->with('error', 'No se puede eliminar el único idioma principal activo del sistema.');
            }

            // Eliminar imagen si existe
            if ($idioma_id->imagen && Storage::disk('public')->exists($idioma_id->imagen)) {
                Storage::disk('public')->delete($idioma_id->imagen);
            }

            $idioma_id->delete();

            return redirect()
                ->route('admin.idiomas.index')
                ->with('success', "Idioma '{$nombre}' eliminado correctamente.");

        } catch (\Exception $e) {
            logger()->error('Error eliminando idioma: ' . $e->getMessage(), [
                'idioma_id' => $idioma->id,
                'nombre' => $idioma->nombre
            ]);

            return redirect()
                ->route('admin.idiomas.index')
                ->with('error', 'Error al eliminar el idioma: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar el orden de los idiomas
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'orden' => 'required|array',
            'orden.*' => 'integer|exists:idiomas,id'
        ]);

        try {
            foreach ($request->orden as $posicion => $idiomaId) {
                Idioma::where('id', $idiomaId)->update(['orden' => $posicion + 1]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Orden de idiomas actualizado correctamente.'
            ]);

        } catch (\Exception $e) {
            logger()->error('Error actualizando orden de idiomas: ' . $e->getMessage(), [
                'orden_enviado' => $request->orden
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el orden de los idiomas.'
            ], 500);
        }
    }

    /**
     * Toggle estado activo de un idioma
     */
    public function toggleActive(Idioma $idioma_id)
    {
        try {
            // Si es el principal y se quiere desactivar, verificar que haya otros activos
            if ($idioma_id->es_principal && $idioma_id->activo && Idioma::where('activo', true)->count() === 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede desactivar el único idioma principal activo.'
                ], 400);
            }

            $idioma_id->update(['activo' => !$idioma_id->activo]);

            return response()->json([
                'success' => true,
                'activo' => $idioma_id->activo,
                'message' => $idioma_id->activo ? 
                    "Idioma '{$idioma_id->nombre}' activado." : 
                    "Idioma '{$idioma_id->nombre}' desactivado."
            ]);

        } catch (\Exception $e) {
            logger()->error('Error cambiando estado de idioma: ' . $e->getMessage(), [
                'idioma_id' => $idioma->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado del idioma.'
            ], 500);
        }
    }
}