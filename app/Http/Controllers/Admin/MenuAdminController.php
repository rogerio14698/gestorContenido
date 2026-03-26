<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\TextoIdioma;
use App\Models\Idioma;
use App\Models\TipoContenido;
use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:menus,mostrar')->only(['index', 'show', 'getContentsByType', 'testAjax']);
        $this->middleware('permission:menus,crear')->only(['create', 'store']);
        $this->middleware('permission:menus,editar')->only(['edit', 'update', 'updateOrder']);
        $this->middleware('permission:menus,eliminar')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menus = Menu::with(['parent', 'children', 'tipoContenido', 'content'])
                    ->whereNull('parent_id')
                    ->orderBy('orden')
                    ->get();
                    
        return view('admin.menus.index', compact('menus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $idiomas = Idioma::where('activo', true)->get();
        $menusParent = Menu::whereNull('parent_id')
                          ->orderBy('orden')
                          ->get();
        $tiposContenido = TipoContenido::tiposParaMenu();
        $contenidos = Content::with('textos.idioma')->get();
        
        // Pre-cargar todos los contenidos organizados por tipo para evitar AJAX
        $contenidosPorTipo = [];
        foreach ($tiposContenido as $tipo) {
            $tipoMapeado = $this->mapearTipoContenido($tipo->tipo_contenido);
            $contenidosDelTipo = Content::where('tipo_contenido', $tipoMapeado)
                                     ->get()
                                     ->map(function ($content) {
                                         return [
                                             'id' => $content->id,
                                             'titulo' => $content->titulo,
                                             'slug' => $content->slug ?? 'sin-slug',
                                         ];
                                     });
            $contenidosPorTipo[$tipo->id] = $contenidosDelTipo;
        }
        
        return view('admin.menus.create', compact('idiomas', 'menusParent', 'tiposContenido', 'contenidos', 'contenidosPorTipo'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'parent_id' => 'nullable|exists:menus,id',
            'tipo_enlace' => 'required|in:contenido,url_externa,ninguno',
            'tipo_contenido_id' => 'nullable|exists:content_types,id',
            'content_id' => 'nullable|exists:contents,id',
            'url' => 'nullable|string',
            'visible' => 'boolean',
            'abrir_nueva_ventana' => 'boolean',
            'menu_pie' => 'boolean',
            'orden' => 'required|integer|min:1',
            'textos' => 'required|array',
            'textos.*.titulo' => 'required|string|max:255',
        ]);

        // Crear el menú principal
        $menu = Menu::create([
            'parent_id' => $request->parent_id,
            'tipo_enlace' => $request->tipo_enlace,
            'tipo_contenido_id' => $request->tipo_contenido_id,
            'content_id' => $request->content_id,
            'url' => $request->url,
            'icon' => null, // Quitamos los iconos
            'visible' => $request->has('visible'),
            'abrir_nueva_ventana' => $request->has('abrir_nueva_ventana'),
            'menu_pie' => $request->has('menu_pie'),
            'orden' => $request->orden,
        ]);

        // Obtener el primer tipo de contenido (para compatibilidad)
        $tipoContenido = \App\Models\TipoContenido::first();

        // Crear textos en diferentes idiomas
        foreach ($request->textos as $idiomaId => $textoData) {
            if (!empty($textoData['titulo'])) {
                // Si el menú es de tipo contenido y tiene content_id, usar el slug del contenido en ese idioma
                $slug = null;
                if ($menu->tipo_enlace === 'contenido' && $menu->content_id) {
                    $contenido = \App\Models\Content::find($menu->content_id);
                    if ($contenido) {
                        $textoContenido = $contenido->textos()->where('language_id', $idiomaId)->first();
                        $slug = $textoContenido?->slug;
                    }
                }
                // Si no hay slug de contenido, usar el generado por el título del menú
                if (empty($slug)) {
                    $baseSlug = Str::slug($textoData['titulo']);
                    if (empty($baseSlug)) {
                        $baseSlug = 'menu-' . $menu->id . '-' . $idiomaId;
                    }
                    $slug = $baseSlug . '-menu-' . $menu->id;
                }
                TextoIdioma::create([
                    'objeto_type' => 'App\\Models\\Menu',
                    'objeto_id' => $menu->id,
                    'language_id' => $idiomaId,
                    'titulo' => $textoData['titulo'],
                    'slug' => $slug,
                    'activo' => true,
                    'visible' => true,
                    // Campos requeridos por compatibilidad
                    'content_type_id' => $tipoContenido ? $tipoContenido->id : null,
                    'content_id' => null,
                ]);
            }
        }

        return redirect()->route('admin.menus.index')
                        ->with('success', 'Menú creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Menu $menu)
    {
        $menu->load(['parent', 'children', 'tipoContenido', 'content']);
        return view('admin.menus.show', compact('menu'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Menu $menu)
    {
        // Cargar el menú con sus textos
        $menu = $menu->load('textos');
        
        $idiomas = Idioma::where('activo', true)->get();
        $menusParent = Menu::where('id', '!=', $menu->id)
                          ->whereNull('parent_id')
                          ->orderBy('orden')
                          ->get();
        $tiposContenido = TipoContenido::tiposParaMenu();
        $contenidos = Content::with('textos.idioma')->get();
        
        return view('admin.menus.edit', compact('menu', 'idiomas', 'menusParent', 'tiposContenido', 'contenidos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'parent_id' => 'nullable|exists:menus,id',
            'tipo_enlace' => 'required|in:contenido,url_externa,ninguno',
            'tipo_contenido_id' => 'nullable|exists:content_types,id',
            'content_id' => 'nullable|exists:contents,id',
            'url_externa' => 'nullable|url',
            'icon' => 'nullable|string|max:100',
            'visible' => 'boolean',
            'abrir_nueva_ventana' => 'boolean',
            'menu_pie' => 'boolean',
            'orden' => 'required|integer|min:1',
            'textos' => 'required|array',
            'textos.*.titulo' => 'required|string|max:255',
        ]);

        // Actualizar el menú principal
        $menu->update([
            'parent_id' => $request->parent_id,
            'tipo_enlace' => $request->tipo_enlace,
            'tipo_contenido_id' => $request->tipo_contenido_id,
            'content_id' => $request->content_id,
            'url_externa' => $request->url_externa,
            'icon' => $request->icon,
            'visible' => $request->has('visible'),
            'abrir_nueva_ventana' => $request->has('abrir_nueva_ventana'),
            'menu_pie' => $request->has('menu_pie'),
            'orden' => $request->orden,
        ]);

        // Actualizar o crear textos
        foreach ($request->textos as $idiomaId => $textoData) {
            if (!empty($textoData['titulo'])) {
                $texto = $menu->textos()
                             ->where('language_id', $idiomaId)
                             ->first();
                // Si el menú es de tipo contenido y tiene content_id, usar el slug del contenido en ese idioma
                $slug = null;
                if ($menu->tipo_enlace === 'contenido' && $menu->content_id) {
                    $contenido = \App\Models\Content::find($menu->content_id);
                    if ($contenido) {
                        $textoContenido = $contenido->textos()->where('language_id', $idiomaId)->first();
                        $slug = $textoContenido?->slug;
                    }
                }
                // Si no hay slug de contenido, usar el generado por el título del menú
                if (empty($slug)) {
                    $baseSlug = Str::slug($textoData['titulo']);
                    if (empty($baseSlug)) {
                        $baseSlug = 'menu-' . $menu->id . '-' . $idiomaId;
                    }
                    $slug = $baseSlug . '-menu-' . $menu->id;
                }
                $data = [
                    'titulo' => $textoData['titulo'],
                    'slug' => $slug,
                    'activo' => true,
                    'visible' => true,
                ];
                if ($texto) {
                    $texto->update($data);
                } else {
                    TextoIdioma::create(array_merge($data, [
                        'objeto_type' => 'App\\Models\\Menu',
                        'objeto_id' => $menu->id,
                        'language_id' => $idiomaId,
                        'content_type_id' => $menu->tipo_contenido_id,
                        'content_id' => $menu->content_id,
                    ]));
                }
            }
        }

        return redirect()->route('admin.menus.index')
                        ->with('success', 'Menú actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
        // Eliminar textos relacionados
        $menu->textos()->delete();
        
        // Eliminar submenús si los hay
        $menu->children()->each(function($child) {
            $child->textos()->delete();
            $child->delete();
        });
        
        // Eliminar el menú
        $menu->delete();

        return redirect()->route('admin.menus.index')
                        ->with('success', 'Menú eliminado exitosamente.');
    }

    /**
     * Actualizar el orden de los menús y sus relaciones padre-hijo
     */
    public function updateOrder(Request $request)
    {
        \Log::info('updateOrder - Datos recibidos:', [
            'request_data' => $request->all(),
            'menus_count' => count($request->menus ?? [])
        ]);

        $request->validate([
            'menus' => 'required|array',
            'menus.*.id' => 'required|exists:menus,id',
            'menus.*.orden' => 'required|integer',
            'menus.*.parent_id' => 'nullable|exists:menus,id',
        ]);

        $actualizado = 0;
        foreach ($request->menus as $menuData) {
            $resultado = Menu::where('id', $menuData['id'])
                ->update([
                    'orden' => $menuData['orden'],
                    'parent_id' => $menuData['parent_id']
                ]);
            
            \Log::info('updateOrder - Menu actualizado:', [
                'menu_id' => $menuData['id'],
                'new_orden' => $menuData['orden'],
                'new_parent_id' => $menuData['parent_id'],
                'affected_rows' => $resultado
            ]);
            
            $actualizado += $resultado;
        }

        \Log::info('updateOrder - Proceso completado:', [
            'total_menus_procesados' => count($request->menus),
            'total_filas_actualizadas' => $actualizado
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Estructura de menús actualizada',
            'menus_procesados' => count($request->menus),
            'filas_actualizadas' => $actualizado
        ]);
    }

    /**
     * Obtener contenidos filtrados por tipo de contenido
     */
    public function getContentsByType(Request $request)
    {
        \Log::info('getContentsByType llamado', ['request' => $request->all()]);
        
        $tipoContenidoId = $request->tipo_contenido_id;
        
        if (!$tipoContenidoId) {
            \Log::info('Sin tipo_contenido_id');
            return response()->json([]);
        }
        
        // Obtener el tipo de contenido para obtener su nombre
        $tipoContenido = TipoContenido::find($tipoContenidoId);
        \Log::info('TipoContenido encontrado', ['tipo' => $tipoContenido ? $tipoContenido->toArray() : null]);
        
        if (!$tipoContenido) {
            \Log::info('TipoContenido no encontrado');
            return response()->json([]);
        }
        
        // Mapear tipos de contenido a los valores en la base de datos
        $tipoMapeado = $this->mapearTipoContenido($tipoContenido->tipo_contenido);
        \Log::info('Mapeo realizado', ['original' => $tipoContenido->tipo_contenido, 'mapeado' => $tipoMapeado]);
        
        $contenidos = Content::where('tipo_contenido', $tipoMapeado)
                            ->with(['textos' => function($query) {
                                $query->where('visible', true);
                            }])
                            ->get();

        \Log::info('Contenidos encontrados', ['count' => $contenidos->count()]);

        $result = $contenidos->map(function ($content) {
            return [
                'id' => $content->id,
                'titulo' => $content->titulo,
                'slug' => $content->slug ?? 'sin-slug',
            ];
        });

        \Log::info('Resultado final', ['result' => $result->toArray()]);
        
        return response()->json($result);
    }
    
    /**
     * Método de prueba simple (temporal)
     */
    public function testAjax()
    {
        \Log::info('testAjax llamado');
        
        try {
            // Datos hardcodeados para probar
            $result = [
                [
                    'id' => 1,
                    'titulo' => 'Contenido de Prueba 1',
                    'slug' => 'contenido-prueba-1'
                ],
                [
                    'id' => 2,
                    'titulo' => 'Contenido de Prueba 2', 
                    'slug' => 'contenido-prueba-2'
                ]
            ];
            
            \Log::info('testAjax resultado', ['result' => $result]);
            return response()->json($result);
            
        } catch (\Exception $e) {
            \Log::error('Error en testAjax', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Mapear tipos de contenido del dropdown a valores de BD
     */
    private function mapearTipoContenido($tipoContenido)
    {
        $mapeo = [
            'Contenido' => 'pagina',
            'contenido' => 'pagina',
            'Páginas' => 'pagina',
            'paginas' => 'pagina',
            'pagina' => 'pagina',
            'Noticias' => 'noticia',
            'noticias' => 'noticia',
            'Noticia' => 'noticia',
            'noticia' => 'noticia',
            'Entrevistas' => 'entrevista',
            'entrevistas' => 'entrevista',
            'Entrevista' => 'entrevista',
            'entrevista' => 'entrevista',
            'Portada' => 'portada',
            'portada' => 'portada',
            'Galerías' => 'galeria',
            'galerias' => 'galeria',
            'Galeria' => 'galeria',
            'galeria' => 'galeria',
            'Multimedia' => 'multimedia',
            'multimedia' => 'multimedia'
        ];
        
        return $mapeo[$tipoContenido] ?? strtolower($tipoContenido);
    }
}
