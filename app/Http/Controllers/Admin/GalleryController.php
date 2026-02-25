<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\GalleryImage;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;

        $this->middleware('permission:galerias,mostrar')->only(['index', 'show']);
        $this->middleware('permission:galerias,crear')->only(['create', 'store', 'uploadImages']);
        $this->middleware('permission:galerias,editar')->only([
            'edit',
            'update',
            'updateImageOrder',
            'getImageTexts',
            'saveImageTexts'
        ]);
        $this->middleware('permission:galerias,eliminar')->only(['destroy', 'deleteImage']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $galleries = Gallery::with('images')
            ->withCount('images')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.galleries.index', compact('galleries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.galleries.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'activa' => 'boolean'
        ]);

        $gallery = Gallery::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'activa' => $request->boolean('activa', true)
        ]);

        return redirect()->route('admin.galleries.index')
            ->with('success', 'Galería creada exitosamente.');
    }

    /**
     * Display the specified resource (ver imágenes de la galería).
     */
    public function show(Gallery $gallery)
    {
        $gallery->load(['images' => function($query) {
            $query->orderBy('orden')->with('texts');
        }]);

        // Cargar idiomas activos para el modal de edición
        $idiomasActivos = \App\Models\Idioma::where('activo', true)->orderBy('orden')->get();
        $idiomaPrincipal = $idiomasActivos->firstWhere('es_principal', true);
        
        // Debug temporal
        \Log::info('Galería: ' . $gallery->nombre . ', Idiomas activos: ' . $idiomasActivos->count());

    return view('admin.galleries.show', compact('gallery', 'idiomasActivos', 'idiomaPrincipal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Gallery $gallery)
    {
        return view('admin.galleries.edit', compact('gallery'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Gallery $gallery)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'activa' => 'boolean'
        ]);

        $gallery->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'activa' => $request->boolean('activa')
        ]);

        return redirect()->route('admin.galleries.index')
            ->with('success', 'Galería actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gallery $gallery)
    {
        // Eliminar todas las imágenes de la galería
        foreach ($gallery->images as $image) {
            if ($image->imagen) {
                Storage::disk('public')->delete($image->imagen);
            }
            if ($image->imagen_miniatura) {
                Storage::disk('public')->delete($image->imagen_miniatura);
            }
        }

        // Eliminar imagen de portada
        if ($gallery->imagen_portada) {
            Storage::disk('public')->delete($gallery->imagen_portada);
        }

        $gallery->delete();

        return redirect()->route('admin.galleries.index')
            ->with('success', 'Galería eliminada exitosamente.');
    }

    /**
     * Subir imágenes a la galería (AJAX)
     */
    public function uploadImages(Request $request, Gallery $gallery)
    {
        \Log::info('Upload iniciado para galería: ' . $gallery->id);
        
        try {
            $request->validate([
                'images' => 'required|array',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB por imagen
            ]);

            \Log::info('Validación exitosa. Número de archivos: ' . count($request->file('images')));

            $uploadedImages = [];
            $errors = [];

            foreach ($request->file('images') as $index => $file) {
                \Log::info('Procesando archivo ' . ($index + 1) . ': ' . $file->getClientOriginalName());
                
                try {
                    // Procesar imagen con sistema responsive
                    $result = $this->imageService->processAndSaveResponsiveImage(
                        $file,
                        'galeria',
                        'imagen',
                        $gallery->id
                    );

                    \Log::info('Resultado del servicio de imagen: ', $result ?? ['null']);

                    if ($result && isset($result['success']) && $result['success']) {
                        // Obtener el próximo orden
                        $nextOrder = $gallery->images()->max('orden') ?? 0;
                        $nextOrder++;

                        // Crear entrada en base de datos
                        $galleryImage = GalleryImage::create([
                            'gallery_id' => $gallery->id,
                            'imagen' => $result['desktop'],
                            'imagen_miniatura' => $result['mobile'] ?? $result['desktop'],
                            'orden' => $nextOrder,
                            'alt_text' => 'Imagen de ' . $gallery->nombre,
                            'activa' => true,
                            'metadatos' => [
                                'original_name' => $file->getClientOriginalName(),
                                'size' => $file->getSize(),
                                'mime_type' => $file->getMimeType()
                            ]
                        ]);

                        \Log::info('Imagen guardada en BD con ID: ' . $galleryImage->id);
                        $uploadedImages[] = $galleryImage;
                    } else {
                        $error = "Error procesando imagen " . ($index + 1) . ": " . ($result['message'] ?? 'Error desconocido en el servicio de imagen');
                        \Log::error($error);
                        $errors[] = $error;
                    }
                } catch (\Exception $e) {
                    $error = "Error con imagen " . ($index + 1) . ": " . $e->getMessage();
                    \Log::error($error);
                    $errors[] = $error;
                }
            }

            \Log::info('Upload completado. Imágenes subidas: ' . count($uploadedImages) . ', Errores: ' . count($errors));

            return response()->json([
                'success' => count($uploadedImages) > 0,
                'message' => count($uploadedImages) . ' imágenes subidas exitosamente.',
                'images' => $uploadedImages,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            \Log::error('Error general en upload: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error en el upload: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar orden de las imágenes (AJAX)
     */
    public function updateImageOrder(Request $request, Gallery $gallery)
    {
        // Aceptar tanto 'images' como 'updates' para compatibilidad
        $updates = $request->input('updates', $request->input('images', []));
        
        $request->validate([
            'updates' => 'required|array',
            'updates.*.id' => 'required|exists:gallery_images,id',
            'updates.*.orden' => 'required|integer|min:1'
        ]);

        foreach ($updates as $updateData) {
            GalleryImage::where('id', $updateData['id'])
                ->where('gallery_id', $gallery->id)
                ->update(['orden' => $updateData['orden']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Orden actualizado exitosamente.'
        ]);
    }

    /**
     * Eliminar una imagen específica de la galería
     */
    public function deleteImage(Gallery $gallery, GalleryImage $image)
    {
        // Verificar que la imagen pertenece a la galería
        if ($image->gallery_id !== $gallery->id) {
            return response()->json([
                'success' => false,
                'message' => 'La imagen no pertenece a esta galería.'
            ], 403);
        }

        try {
            // Eliminar el archivo del storage
            if ($image->imagen && Storage::exists($image->imagen)) {
                Storage::delete($image->imagen);
            }

            // Eliminar registro de la base de datos
            $image->delete();

            // Reordenar las imágenes restantes
            $remainingImages = GalleryImage::where('gallery_id', $gallery->id)
                ->orderBy('orden')
                ->get();

            foreach ($remainingImages as $index => $img) {
                $img->update(['orden' => $index + 1]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Imagen eliminada exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la imagen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener textos multiidioma de una imagen
     */
    public function getImageTexts(GalleryImage $image)
    {
        try {
            $image->load(['texts.idioma', 'gallery']);
            
            // Obtener todos los idiomas activos
            $idiomasActivos = \App\Models\Idioma::where('activo', true)->orderBy('orden')->get();
            
            // Preparar array de textos por idioma
            $texts = [];
            foreach ($idiomasActivos as $idioma) {
                $text = $image->texts->where('idioma_id', $idioma->id)->first();
                $texts[] = [
                    'idioma_id' => $idioma->id,
                    'titulo' => $text->titulo ?? '',
                    'descripcion' => $text->descripcion ?? '',
                    'alt_text' => $text->alt_text ?? ''
                ];
            }

            return response()->json([
                'success' => true,
                'image' => [
                    'id' => $image->id,
                    'imagen_url' => asset('storage/' . $image->imagen),
                    'alt_text' => $image->alt_text
                ],
                'texts' => $texts
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar textos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guardar textos multiidioma de una imagen
     */
    public function saveImageTexts(Request $request, GalleryImage $image)
    {
        try {
            $request->validate([
                'titulo.*' => 'nullable|string|max:255',
                'descripcion.*' => 'nullable|string',
                'alt_text.*' => 'nullable|string|max:255'
            ]);

            $titulos = $request->input('titulo', []);
            $descripciones = $request->input('descripcion', []);
            $altTexts = $request->input('alt_text', []);
            
            // Obtener idiomas activos
            $idiomasActivos = \App\Models\Idioma::where('activo', true)->get();
            
            foreach ($idiomasActivos as $idioma) {
                $idiomaId = $idioma->id;
                
                // Buscar o crear el texto para este idioma
                $imageText = $image->texts()->where('idioma_id', $idiomaId)->first();
                
                if (!$imageText) {
                    $imageText = new \App\Models\GalleryImageText([
                        'gallery_image_id' => $image->id,
                        'idioma_id' => $idiomaId
                    ]);
                }
                
                // Actualizar campos
                $imageText->titulo = $titulos[$idiomaId] ?? null;
                $imageText->descripcion = $descripciones[$idiomaId] ?? null;
                $imageText->alt_text = $altTexts[$idiomaId] ?? null;
                
                // Solo guardar si al menos un campo tiene contenido
                if ($imageText->titulo || $imageText->descripcion || $imageText->alt_text) {
                    $imageText->save();
                } elseif ($imageText->exists) {
                    // Eliminar si todos los campos están vacíos y el registro existe
                    $imageText->delete();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Textos guardados exitosamente.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar textos: ' . $e->getMessage()
            ], 500);
        }
    }
}
