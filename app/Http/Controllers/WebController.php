<?php

namespace App\Http\Controllers;

use App\Models\Idioma;
use App\Helpers\IdiomaHelper;
use App\Models\Content;
use App\Models\Menu;
use App\Models\Configuracion;
use App\Models\Slide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use App\Models\ConfiguracionEmpresa;
use App\Http\Controllers\Traits\RedesSocialesTrait;

class WebController extends Controller
{
    use RedesSocialesTrait;
    /**
     * Página principal - redirección al idioma por defecto
     */
    public function index()
    {
        $idioma = Session::get('idioma') ?? 
                  Idioma::where('es_principal', true)
                        ->where('activo', true)
                        ->first()->etiqueta ?? 'es';
        
        return redirect("/{$idioma}");
    }

    /**
     * Página de inicio con idioma específico
     */
    public function inicio($idioma)
    {
        $idiomaNormalizado = IdiomaHelper::normalizarEtiqueta($idioma) ?? 'es';
        app()->setLocale($idiomaNormalizado);
        Session::put('idioma_actual', $idiomaNormalizado);

        // Obtener configuración general. Si no existe, crear un objeto vacío con valores por defecto
        $configuracion = Configuracion::first();
        if (!$configuracion) {
            $configuracion = new Configuracion([
                'nombre_empresa' => 'Nuntris Teatro',
                'metadescripcion' => 'Compañía de teatro',
                'email' => null,
                'telefono_empresa' => null,
                'movil_empresa' => null,
                'direccion_empresa' => null,
            ]);
        }
        
        // Obtener menús principales
        $menus = Menu::principal()
            ->where('visible', true)
            ->with([
                'textos.idioma',
                'content.textos.idioma',
                'children' => function ($query) {
                    $query->where('visible', true)
                        ->with([
                            'textos.idioma',
                            'content.textos.idioma',
                        ]);
                },
            ])
            ->get();

        // Obtener menús de pie de página
        $menusPie = Menu::menuPie()->where('visible', true)->get();

        // Configuración empresa y redes sociales
        // Aseguramos que siempre haya un objeto (evita errores en vistas si la tabla está vacía)
        $configEmpresa = ConfiguracionEmpresa::first();
        if (!$configEmpresa) {
            // No queremos insertar en la base al vuelo aquí; creamos un objeto temporal con valores por defecto
            $configEmpresa = new ConfiguracionEmpresa([
                'nombre' => 'Nuntris Teatro',
                'metatitulo' => null,
                'telefono' => null,
                'email' => null,
            ]);
        }
        $redesSociales = $this->obtenerRedesSociales($configEmpresa);

        // Obtener contenido de portada: marcado como portada y página estática
        $contenidoInicio = Content::where('portada', true)
            ->where('pagina_estatica', true)
            ->with(['textos.idioma'])
            ->first();
        
        // Obtener noticias de portada
        $noticiasPortada = Content::noticias()
                                 ->portada()
                                 ->with(['textos' => function($query) use ($idiomaNormalizado) {
                                     $query->byIdioma($idiomaNormalizado)->visible();
                                 }])
                                 ->orderBy('fecha_publicacion', 'desc')
                                 ->limit(6)
                                 ->get();


        // Obtener slides activos
        $slides = Slide::query()
            ->visible()
            ->ordered()
            ->with(['translations.idioma'])
            ->get();

        // Galerías activas para el carrusel
        $galerias = \App\Models\Gallery::activas()
            ->with(['images' => function($q) {
                $q->active();
            }])
            ->orderBy('id', 'desc')
            ->limit(1)
            ->get();
        
        return view('web.inicio', compact(
            'configuracion', 
            'menus', 
            'menusPie',
            'contenidoInicio', 
            'noticiasPortada',
            'slides',
            'galerias',
            'configEmpresa',
            'redesSociales'
        ));
    }

    /**
     * Página de contenido dinámico
     */
    public function contenido($idioma, $slug)
    {
        // Buscar el texto por slug e idioma
        $idiomaNormalizado = \App\Helpers\IdiomaHelper::normalizarEtiqueta($idioma) ?? 'es';

        $texto = \App\Models\TextoIdioma::where('slug', $slug)
            ->whereHas('idioma', function($q) use ($idiomaNormalizado) {
                $q->where('etiqueta', $idiomaNormalizado);
            })
            ->where('visible', true)
            ->firstOrFail();


            // Obtener el contenido asociado y cargar relaciones
            $contenido = $texto->contenidoModel;
            if ($contenido) {
                $contenido->load(['textos' => function($query) {
                    $query->visible();
                }, 'galeria']);
            }

        $configuracion = Configuracion::first();
        $menus = Menu::principal()
            ->where('visible', true)
            ->with([
                'textos.idioma',
                'content.textos.idioma',
                'children' => function ($query) {
                    $query->where('visible', true)
                        ->with([
                            'textos.idioma',
                            'content.textos.idioma',
                        ]);
                },
            ])
            ->get();

       // dd($contenido, $contenido?->textos);
        return view('web.contenido', [
            'contenido' => $contenido,
            'configuracion' => $configuracion,
            'menus' => $menus,
            'texto' => $texto,
            'idioma' => $idioma,
            'galeria' => $contenido ? $contenido->galeria : null,
        ]);
    }

    /**
     * Listado de noticias
     */
    public function noticias($idioma)
    {
        $idiomaNormalizado = normalizar_etiqueta_idioma($idioma) ?? 'es';

        $noticias = Content::noticias()
                          ->with(['textos.idioma'])
                          ->orderBy('fecha_publicacion', 'desc')
                          ->paginate(10);
        
        $configuracion = Configuracion::first();
        $menus = Menu::principal()
            ->where('visible', true)
            ->with([
                'textos.idioma',
                'content.textos.idioma',
                'children' => function ($query) {
                    $query->where('visible', true)
                        ->with([
                            'textos.idioma',
                            'content.textos.idioma',
                        ]);
                },
            ])
            ->get();
        
        return view('web.noticias', compact('noticias', 'configuracion', 'menus'));
    }

    /**
     * Cambiar idioma
     */
    public function cambiarIdioma($idioma)
    {
        $idiomaModel = Idioma::where('codigo', $idioma)
                            ->where('activado', true)
                            ->first();
        
        if ($idiomaModel) {
            Session::put('idioma', $idioma);
            Session::put('idioma_id', $idiomaModel->id);
        }
        
        return redirect()->back();
    }
}
