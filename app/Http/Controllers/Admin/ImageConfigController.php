<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImageConfig;
use Illuminate\Http\Request;

class ImageConfigController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:imagenes,mostrar')->only(['index']);
        $this->middleware('permission:imagenes,crear')->only(['create', 'store']);
        $this->middleware('permission:imagenes,editar')->only(['edit', 'update']);
        $this->middleware('permission:imagenes,eliminar')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $configs = ImageConfig::orderBy('tipo_contenido')
                             ->orderBy('tipo_imagen')
                             ->get()
                             ->groupBy('tipo_contenido');
                             
        return view('admin.image-configs.index', compact('configs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.image-configs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tipo_contenido' => 'required|string',
            'tipo_imagen' => 'required|string',
            'ancho' => 'required|integer|min:50|max:5000',
            'alto' => 'required|integer|min:50|max:5000',
            'mantener_aspecto' => 'boolean',
            'formato' => 'required|in:jpg,png,webp',
            'calidad' => 'required|integer|min:1|max:100',
            'redimensionar' => 'boolean',
            'activo' => 'boolean',
        ]);

        ImageConfig::create($request->all());

        return redirect()->route('admin.image-configs.index')
                        ->with('success', 'Configuración de imagen creada exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ImageConfig $imageConfig)
    {
        return view('admin.image-configs.edit', compact('imageConfig'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ImageConfig $imageConfig)
    {
        $request->validate([
            'ancho' => 'required|integer|min:50|max:5000',
            'alto' => 'nullable|integer|min:0|max:5000',
            'mantener_aspecto' => 'boolean',
            'formato' => 'required|in:jpg,png,webp',
            'calidad' => 'required|integer|min:1|max:100',
            'redimensionar' => 'boolean',
            'activo' => 'boolean',
        ]);

        $imageConfig->update($request->all());

        return redirect()->route('admin.image-configs.index')
                        ->with('success', 'Configuración de imagen actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ImageConfig $imageConfig)
    {
        $imageConfig->delete();

        return redirect()->route('admin.image-configs.index')
                        ->with('success', 'Configuración de imagen eliminada exitosamente.');
    }
}
