<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slide;
use App\Models\Idioma;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class SlideAdminController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;

        $this->middleware('permission:slides,mostrar')->only(['index', 'show']);
        $this->middleware('permission:slides,crear')->only(['create', 'store']);
        $this->middleware('permission:slides,editar')->only(['edit', 'update', 'updateOrder']);
        $this->middleware('permission:slides,eliminar')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $slides = Slide::with('translations.idioma')
                      ->orderBy('orden')
                      ->orderBy('created_at', 'desc')
                      ->get();
        
        return view('admin.slides.index', compact('slides'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $idiomas = Idioma::where('activo', true)->orderBy('orden')->get();
        
        return view('admin.slides.create', compact('idiomas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación
        $rules = [
            'nueva_ventana' => 'nullable|in:0,1',
            'visible' => 'nullable|in:0,1',
            'activo' => 'nullable|in:0,1',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ];

        // Validar traducciones por idioma
        $idiomas = Idioma::where('activo', true)->get();
        foreach ($idiomas as $idioma) {
            $rules["translations.{$idioma->id}.titulo"] = 'required|string|max:255';
            $rules["translations.{$idioma->id}.descripcion"] = 'nullable|string';
            $rules["translations.{$idioma->id}.alt_text"] = 'nullable|string|max:255';
            $rules["translations.{$idioma->id}.url"] = 'nullable|url';
        }

        $request->validate($rules);

        DB::beginTransaction();

        try {
            // Crear el slide con valores convertidos a boolean
            $slide = Slide::create([
                'nueva_ventana' => (bool) $request->input('nueva_ventana', 0),
                'visible' => (bool) $request->input('visible', 1),
                'activo' => (bool) $request->input('activo', 1),
                // orden se genera automáticamente en el modelo
            ]);

            // Procesar imagen si se subió
            if ($request->hasFile('imagen')) {
                $this->processImage($request->file('imagen'), $slide);
            }

            // Guardar traducciones
            if ($request->has('translations')) {
                $slide->saveTranslations($request->input('translations'));
            }

            DB::commit();

            return redirect()
                ->route('admin.slides.index')
                ->with('success', 'Slide creado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating slide: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Error al crear el slide: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Slide $slide)
    {
        $slide->load('translations.idioma');
        
        return view('admin.slides.show', compact('slide'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Slide $slide)
    {
        $slide->load('translations.idioma');
        $idiomas = Idioma::where('activo', true)->orderBy('orden')->get();
        
        // Preparar traducciones por idioma
        $translations = [];
        foreach ($idiomas as $idioma) {
            $translation = $slide->translations()->where('idioma_id', $idioma->id)->first();
            $translations[$idioma->id] = [
                'titulo' => $translation->titulo ?? '',
                'descripcion' => $translation->descripcion ?? '',
                'alt_text' => $translation->alt_text ?? '',
                'url' => $translation->url ?? '',
            ];
        }
        
        return view('admin.slides.edit', compact('slide', 'idiomas', 'translations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Slide $slide)
    {
        // Validación
        $rules = [
            'nueva_ventana' => 'nullable|in:0,1',
            'visible' => 'nullable|in:0,1',
            'activo' => 'nullable|in:0,1',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ];

        // Validar traducciones por idioma
        $idiomas = Idioma::where('activo', true)->get();
        foreach ($idiomas as $idioma) {
            $rules["translations.{$idioma->id}.titulo"] = 'required|string|max:255';
            $rules["translations.{$idioma->id}.descripcion"] = 'nullable|string';
            $rules["translations.{$idioma->id}.alt_text"] = 'nullable|string|max:255';
            $rules["translations.{$idioma->id}.url"] = 'nullable|url';
        }

        $request->validate($rules);

        DB::beginTransaction();

        try {
            // Actualizar datos del slide con conversión explícita a boolean
            $slide->update([
                'nueva_ventana' => (bool) $request->input('nueva_ventana', 0),
                'visible' => (bool) $request->input('visible', 0),
                'activo' => (bool) $request->input('activo', 0),
            ]);

            // Procesar nueva imagen si se subió
            if ($request->hasFile('imagen')) {
                // Eliminar imagen anterior
                $this->deleteSlideImages($slide);
                
                // Procesar nueva imagen
                $this->processImage($request->file('imagen'), $slide);
            }

            // Guardar traducciones
            if ($request->has('translations')) {
                $slide->saveTranslations($request->input('translations'));
            }

            DB::commit();

            return redirect()
                ->route('admin.slides.index')
                ->with('success', 'Slide actualizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating slide: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Error al actualizar el slide: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Slide $slide)
    {
        DB::beginTransaction();

        try {
            // Eliminar imágenes
            $this->deleteSlideImages($slide);

            // Eliminar el slide (las traducciones se eliminan por cascada)
            $slide->delete();

            DB::commit();

            return redirect()
                ->route('admin.slides.index')
                ->with('success', 'Slide eliminado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting slide: ' . $e->getMessage());

            return back()->with('error', 'Error al eliminar el slide: ' . $e->getMessage());
        }
    }

    /**
     * Update slides order
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'slides' => 'required|array',
            'slides.*.id' => 'required|integer|exists:slides,id',
            'slides.*.orden' => 'required|integer|min:1'
        ]);

        DB::beginTransaction();

        try {
            foreach ($request->slides as $slideData) {
                Slide::where('id', $slideData['id'])
                     ->update(['orden' => $slideData['orden']]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Orden actualizado correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating slides order: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el orden'
            ], 500);
        }
    }

    /**
     * Process uploaded image
     */
    private function processImage($file, Slide $slide)
    {
        // Procesar imagen principal
        $imagePath = $this->imageService->processAndSaveImage(
            $file,
            'slide',
            'imagen',
            $slide->id
        );

        if (!$imagePath) {
            throw new \Exception('Error al procesar la imagen principal');
        }

        // Generar miniatura
        $miniaturaPath = $this->imageService->processAndSaveImage(
            $file,
            'slide',
            'imagen_miniatura',
            $slide->id
        );

        // Obtener información básica del archivo
        $imageInfo = getimagesize($file->getRealPath());
        $metadata = [
            'original_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'processed_at' => now()->toISOString()
        ];

        // Agregar dimensiones si están disponibles
        if ($imageInfo) {
            $metadata['ancho'] = $imageInfo[0];
            $metadata['alto'] = $imageInfo[1];
        }

        // Actualizar slide con rutas de imágenes
        $slide->update([
            'imagen' => $imagePath,
            'imagen_miniatura' => $miniaturaPath,
            'metadatos' => array_merge($slide->metadatos ?? [], $metadata)
        ]);
    }

    /**
     * Delete slide images from storage
     */
    private function deleteSlideImages(Slide $slide)
    {
        $imagesToDelete = array_filter([
            $slide->imagen,
            $slide->imagen_miniatura
        ]);

        foreach ($imagesToDelete as $imagePath) {
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
        }
    }
}
