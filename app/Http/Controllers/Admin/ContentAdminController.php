<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\TextoIdioma;
use App\Models\Idioma;
use App\Models\TipoContenido;
use App\Models\Gallery;
use App\Models\ImageConfig;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ContentAdminController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;

        $this->middleware('permission:contenidos,mostrar')->only(['index', 'show']);
        $this->middleware('permission:contenidos,crear')->only(['create', 'store', 'uploadImage']);
        $this->middleware('permission:contenidos,editar')->only(['edit', 'update']);
        $this->middleware('permission:contenidos,eliminar')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Content::with(['textos.idioma', 'galeria']);
        
        // Filtrar por tipo si se especifica
        if ($request->has('tipo') && $request->tipo) {
            $query->where('tipo_contenido', $request->tipo);
        }
        
        // Búsqueda por título, subtítulo, resumen o contenido
        if ($request->has('search') && trim($request->search)) {
            $searchTerm = trim($request->search);
            $query->whereHas('textos', function($q) use ($searchTerm) {
                $q->where(function($q2) use ($searchTerm) {
                    $q2->where('titulo', 'like', '%' . $searchTerm . '%')
                       ->orWhere('subtitulo', 'like', '%' . $searchTerm . '%')
                       ->orWhere('resumen', 'like', '%' . $searchTerm . '%')
                       ->orWhere('contenido', 'like', '%' . $searchTerm . '%');
                });
            });
        }
        
        $contents = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Obtener tipos de contenido para el filtro
        $tiposContenido = TipoContenido::all();
        
        return view('admin.contents.index', compact('contents', 'tiposContenido'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $idiomas = Idioma::where('activo', true)->get();
        $tiposContenido = TipoContenido::all();
        $galerias = Gallery::where('activa', true)->get();
        
        // Obtener configuraciones de imagen para todos los tipos de contenido
        $imageConfigs = ImageConfig::getActiveConfigs();
        
        return view('admin.contents.create', compact('idiomas', 'tiposContenido', 'galerias', 'imageConfigs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Obtener tipos válidos dinámicamente
            $tiposValidos = TipoContenido::pluck('tipo_contenido')->map(function($tipo) {
                return strtolower($tipo);
            })->implode(',');
            
            $request->validate([
                'tipo_contenido' => 'required|in:' . $tiposValidos,
                'fecha_publicacion' => 'nullable|date',
                'lugar' => 'nullable|string|max:100',
                'portada' => 'nullable|boolean',
                'textos' => 'required|array',
                // Solo validar que exista al menos un título y contenido (no específico por idioma)
                'textos.*.titulo' => 'nullable|string|max:255',
                'textos.*.resumen' => 'nullable|string',
                'textos.*.contenido' => 'nullable|string',
                'textos.*.slug' => 'nullable|string|max:255|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                'textos.*.imagen_alt' => 'nullable|string|max:255',
                'textos.*.imagen_portada_alt' => 'nullable|string|max:255',
                // Validación de imágenes
                'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
                'imagen_portada' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            ], [
                'tipo_contenido.required' => 'El tipo de contenido es obligatorio.',
                'tipo_contenido.in' => 'El tipo de contenido seleccionado no es válido.',
                'fecha_publicacion.date' => 'La fecha de publicación no es válida.',
                'lugar.max' => 'El lugar no puede superar los 100 caracteres.',
                'textos.required' => 'Debe proporcionar textos en al menos un idioma.',
                'textos.array' => 'El formato de los textos no es válido.',
                'textos.*.titulo.max' => 'El título no puede superar los 255 caracteres.',
                'textos.*.slug.max' => 'El slug no puede superar los 255 caracteres.',
                'textos.*.slug.regex' => 'El slug solo puede contener letras minúsculas, números y guiones.',
                'textos.*.imagen_alt.max' => 'El texto alternativo de la imagen no puede superar los 255 caracteres.',
                'textos.*.imagen_portada_alt.max' => 'El texto alternativo de la portada no puede superar los 255 caracteres.',
                'imagen.image' => 'La imagen principal debe ser un archivo de imagen válido.',
                'imagen.mimes' => 'La imagen principal debe ser de tipo: jpeg, png, jpg, gif o webp.',
                'imagen.max' => 'La imagen principal no puede superar los 10MB.',
                'imagen_portada.image' => 'La imagen de portada debe ser un archivo de imagen válido.',
                'imagen_portada.mimes' => 'La imagen de portada debe ser de tipo: jpeg, png, jpg, gif o webp.',
                'imagen_portada.max' => 'La imagen de portada no puede superar los 10MB.',
            ]);

            // Validación personalizada: al menos debe tener título y contenido en un idioma
            $hasValidContent = false;
            foreach ($request->textos as $textoData) {
                if (!empty($textoData['titulo']) && !empty($textoData['contenido'])) {
                    $hasValidContent = true;
                    break;
                }
            }

            if (!$hasValidContent) {
                return back()->withErrors(['textos' => 'Debe proporcionar al menos título y contenido en un idioma.'])
                             ->withInput();
            }

            // Crear el contenido principal
            $content = Content::create([
                'lugar' => $request->lugar,
                'fecha' => $request->fecha_publicacion, // Usar fecha_publicacion para ambos campos por ahora
                'fecha_publicacion' => $request->fecha_publicacion,
                'tipo_contenido' => $request->tipo_contenido,
                'pagina_estatica' => $request->pagina_estatica ?? false,
                'portada' => $request->portada ?? false,
                'galeria_id' => $request->galeria_id,
                'actions' => $request->actions,
                'orden' => $request->orden ?? 0,
            ]);

            // Procesar y guardar imágenes usando el sistema responsive
            if ($request->hasFile('imagen')) {
                $result = $this->imageService->processAndSaveResponsiveImage(
                    $request->file('imagen'), 
                    $request->tipo_contenido, 
                    'imagen',
                    $content->id
                );
                if ($result && $result['success'] && $result['desktop']) {
                    $content->update(['imagen' => $result['desktop']]);
                }
            }

            if ($request->hasFile('imagen_portada')) {
                $result = $this->imageService->processAndSaveResponsiveImage(
                    $request->file('imagen_portada'), 
                    $request->tipo_contenido, 
                    'imagen_portada',
                    $content->id
                );
                if ($result && $result['success'] && $result['desktop']) {
                    $content->update(['imagen_portada' => $result['desktop']]);
                }
            }

        // Crear textos en diferentes idiomas
        foreach ($request->textos as $idiomaId => $textoData) {
            if (!empty($textoData['titulo'])) {
                // Buscar TipoContenido o usar un valor por defecto
                $tipoContenido = TipoContenido::where('tipo_contenido', 'Contenido')->first();
                $tipoContenidoId = $tipoContenido ? $tipoContenido->id : 1; // Usar 1 como fallback
                
                // Generar slug automático si no se proporciona uno
                $slug = !empty($textoData['slug']) ? $textoData['slug'] : Str::slug($textoData['titulo']);
                
                // Verificar que el slug sea único para este idioma
                $originalSlug = $slug;
                $counter = 1;
                while (TextoIdioma::where('slug', $slug)
                                 ->where('idioma_id', $idiomaId)
                                 ->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
                
                TextoIdioma::create([
                    'idioma_id' => $idiomaId,
                    'contenido_id' => $content->id,
                    'tipo_contenido_id' => $tipoContenidoId,
                    'titulo' => $textoData['titulo'],
                    'subtitulo' => $textoData['subtitulo'] ?? null,
                    'resumen' => $textoData['resumen'] ?? null,
                    'contenido' => $textoData['contenido'],
                    'metatitulo' => $textoData['metatitulo'] ?? null,
                    'metadescripcion' => $textoData['metadescripcion'] ?? null,
                    'imagen_alt' => $textoData['imagen_alt'] ?? null,
                    'imagen_portada_alt' => $textoData['imagen_portada_alt'] ?? null,
                    'slug' => $slug,
                    'visible' => isset($textoData['visible']) ? (bool)$textoData['visible'] : true,
                ]);
            }
        }

        return redirect()->route('admin.contents.index')
                        ->with('success', 'Contenido creado exitosamente.');
                        
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al crear contenido: ' . $e->getMessage()])
                         ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Content $content)
    {
        $content->load(['textos.idioma', 'galeria']);
        return view('admin.contents.show', compact('content'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Content $content)
    {
        $content->load(['textos.idioma']);
        $idiomas = Idioma::where('activo', true)->get();
        $tiposContenido = TipoContenido::all();
        $galerias = Gallery::where('activa', true)->get();
        
        return view('admin.contents.edit', compact('content', 'idiomas', 'tiposContenido', 'galerias'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Content $content)
    {
        // Obtener tipos válidos dinámicamente
        $tiposValidos = TipoContenido::pluck('tipo_contenido')->map(function($tipo) {
            return strtolower($tipo);
        })->implode(',');
        
        $request->validate([
            'tipo_contenido' => 'required|in:' . $tiposValidos,
            'fecha_publicacion' => 'nullable|date',
            'lugar' => 'nullable|string|max:100',
            'portada' => 'nullable|boolean',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'imagen_portada' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'eliminar_imagenes' => 'nullable|boolean',
            'textos' => 'required|array',
            'textos.*.titulo' => 'nullable|string|max:255',
            'textos.*.resumen' => 'nullable|string',
            'textos.*.contenido' => 'nullable|string',
            'textos.*.slug' => 'nullable|string|max:255|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            'textos.*.imagen_alt' => 'nullable|string|max:255',
            'textos.*.imagen_portada_alt' => 'nullable|string|max:255',
        ], [
            'tipo_contenido.required' => 'El tipo de contenido es obligatorio.',
            'tipo_contenido.in' => 'El tipo de contenido seleccionado no es válido.',
            'fecha_publicacion.date' => 'La fecha de publicación no es válida.',
            'lugar.max' => 'El lugar no puede superar los 100 caracteres.',
            'textos.required' => 'Debe proporcionar textos en al menos un idioma.',
            'textos.array' => 'El formato de los textos no es válido.',
            'textos.*.titulo.max' => 'El título no puede superar los 255 caracteres.',
            'textos.*.slug.max' => 'El slug no puede superar los 255 caracteres.',
            'textos.*.slug.regex' => 'El slug solo puede contener letras minúsculas, números y guiones.',
            'textos.*.imagen_alt.max' => 'El texto alternativo de la imagen no puede superar los 255 caracteres.',
            'textos.*.imagen_portada_alt.max' => 'El texto alternativo de la portada no puede superar los 255 caracteres.',
            'imagen.image' => 'La imagen principal debe ser un archivo de imagen válido.',
            'imagen.mimes' => 'La imagen principal debe ser de tipo: jpeg, png, jpg, gif o webp.',
            'imagen.max' => 'La imagen principal no puede superar los 2MB.',
            'imagen_portada.image' => 'La imagen de portada debe ser un archivo de imagen válido.',
            'imagen_portada.mimes' => 'La imagen de portada debe ser de tipo: jpeg, png, jpg, gif o webp.',
            'imagen_portada.max' => 'La imagen de portada no puede superar los 2MB.',
        ]);

        // Validación personalizada: al menos debe tener título y contenido en un idioma
        $hasValidContent = false;
        foreach ($request->textos as $textoData) {
            if (!empty($textoData['titulo']) && !empty($textoData['contenido'])) {
                $hasValidContent = true;
                break;
            }
        }

        if (!$hasValidContent) {
            return back()->withErrors(['textos' => 'Debe proporcionar al menos título y contenido en un idioma.'])
                         ->withInput();
        }

        // Manejar eliminación de imágenes
        if ($request->eliminar_imagenes) {
            if ($content->imagen) {
                Storage::disk('public')->delete($content->imagen);
                $content->imagen = null;
            }
            if ($content->imagen_portada) {
                Storage::disk('public')->delete($content->imagen_portada);
                $content->imagen_portada = null;
            }
        }

        // Procesar nueva imagen principal
        $imagenPath = $content->imagen;
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            if ($content->imagen) {
                Storage::disk('public')->delete($content->imagen);
            }
            
            $result = $this->imageService->processAndSaveResponsiveImage(
                $request->file('imagen'),
                $request->tipo_contenido,
                'imagen',
                $content->id
            );
            $imagenPath = ($result && $result['success']) ? $result['desktop'] : $content->imagen;
        }

        // Procesar nueva imagen de portada
        $imagenPortadaPath = $content->imagen_portada;
        if ($request->hasFile('imagen_portada')) {
            // Eliminar imagen anterior si existe
            if ($content->imagen_portada) {
                Storage::disk('public')->delete($content->imagen_portada);
            }
            
            $result = $this->imageService->processAndSaveResponsiveImage(
                $request->file('imagen_portada'),
                $request->tipo_contenido,
                'imagen_portada',
                $content->id
            );
            $imagenPortadaPath = ($result && $result['success']) ? $result['desktop'] : $content->imagen_portada;
        }

        // Actualizar el contenido principal
        $content->update([
            'lugar' => $request->lugar,
            'fecha' => $request->fecha_publicacion,
            'fecha_publicacion' => $request->fecha_publicacion,
            'tipo_contenido' => $request->tipo_contenido,
            'pagina_estatica' => $request->pagina_estatica ?? false,
            'portada' => $request->portada ?? false,
            'galeria_id' => $request->galeria_id,
            'actions' => $request->actions,
            'orden' => $request->orden ?? 0,
            'imagen' => $imagenPath,
            'imagen_portada' => $imagenPortadaPath,
        ]);

        // Actualizar o crear textos
        foreach ($request->textos as $idiomaId => $textoData) {
            if (!empty($textoData['titulo'])) {
                $texto = TextoIdioma::where('contenido_id', $content->id)
                                   ->where('idioma_id', $idiomaId)
                                   ->first();

                // Recalcular el slug si el usuario lo cambia manualmente, si el título cambia, o si está vacío
                $slug = $texto ? $texto->slug : null;
                $slugEnviado = $textoData['slug'] ?? null;
                $tituloCambiado = $texto && $texto->titulo !== $textoData['titulo'];
                $slugCambiado = $slugEnviado && $slugEnviado !== $slug;
                // Si el usuario cambia el slug manualmente, o si deja el slug vacío y cambia el título, o si el slug está vacío
                if ($slugCambiado || (empty($slugEnviado) && $tituloCambiado) || empty($slug)) {
                    $slug = !empty($slugEnviado) ? $slugEnviado : Str::slug($textoData['titulo']);
                    // Verificar que el slug sea único para este idioma (excluyendo el registro actual)
                    $originalSlug = $slug;
                    $counter = 1;
                    while (TextoIdioma::where('slug', $slug)
                                     ->where('idioma_id', $idiomaId)
                                     ->where('id', '!=', $texto ? $texto->id : 0)
                                     ->exists()) {
                        $slug = $originalSlug . '-' . $counter;
                        $counter++;
                    }
                }

                $data = [
                    'titulo' => $textoData['titulo'],
                    'subtitulo' => $textoData['subtitulo'] ?? null,
                    'resumen' => $textoData['resumen'] ?? null,
                    'contenido' => $textoData['contenido'],
                    'metatitulo' => $textoData['metatitulo'] ?? null,
                    'metadescripcion' => $textoData['metadescripcion'] ?? null,
                    'imagen_alt' => $textoData['imagen_alt'] ?? null,
                    'imagen_portada_alt' => $textoData['imagen_portada_alt'] ?? null,
                    'slug' => $slug,
                    'visible' => isset($textoData['visible']) ? (bool)$textoData['visible'] : true,
                ];

                if ($texto) {
                    $texto->update($data);
                } else {
                    $tipoContenido = TipoContenido::where('tipo_contenido', 'Contenido')->first();
                    $tipoContenidoId = $tipoContenido ? $tipoContenido->id : 1; // Usar 1 como fallback
                    
                    TextoIdioma::create(array_merge($data, [
                        'idioma_id' => $idiomaId,
                        'contenido_id' => $content->id,
                        'tipo_contenido_id' => $tipoContenidoId,
                    ]));
                }
            }
        }

        return redirect()->route('admin.contents.index')
                        ->with('success', 'Contenido actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Content $content)
    {
        // Eliminar textos relacionados
        $content->textos()->delete();
        
        // Eliminar el contenido
        $content->delete();

        return redirect()->route('admin.contents.index')
                        ->with('success', 'Contenido eliminado exitosamente.');
    }

    /**
     * Upload image for TinyMCE editor
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120' // 5MB max
        ]);

        try {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            
            // Crear directorio para imágenes del editor
            $directory = 'editor-images';
            Storage::disk('public')->makeDirectory($directory);
            
            // Guardar archivo
            $path = $file->storeAs($directory, $fileName, 'public');
            
            // Retornar la URL para TinyMCE
            return response()->json([
                'location' => Storage::disk('public')->url($path)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al subir la imagen: ' . $e->getMessage()
            ], 500);
        }
    }
}
