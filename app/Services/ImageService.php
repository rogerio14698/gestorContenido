<?php

namespace App\Services;

use App\Models\ImageConfig;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Str;

class ImageService
{
    protected $disk = 'public';
    protected $imageManager;

    public function __construct()
    {
        // Verificar si GD está disponible antes de inicializar
        if (extension_loaded('gd')) {
            $this->imageManager = new ImageManager(new Driver());
        } else {
            $this->imageManager = null;
            \Log::warning('Extensión GD no disponible. Las imágenes se guardarán sin procesamiento.');
        }
    }

    /**
     * Procesar y guardar imagen según configuración
     */
    public function processAndSaveImage(UploadedFile $file, string $tipoContenido, string $tipoImagen, int $contentId = null): ?string
    {
        try {
            // Obtener configuración para este tipo de contenido e imagen
            $config = ImageConfig::getConfig($tipoContenido, $tipoImagen);
            
            if (!$config) {
                // Configuración por defecto si no existe
                $config = (object)[
                    'ancho' => 800,
                    'alto' => 600,
                    'mantener_aspecto' => true,
                    'formato' => 'jpg',
                    'calidad' => 85,
                    'redimensionar' => true
                ];
            }

            // Crear directorio si no existe
            $directory = "images/{$tipoContenido}/{$tipoImagen}";
            Storage::disk($this->disk)->makeDirectory($directory);
            
            // Si GD no está disponible, simplemente guardar el archivo original
            if (!$this->imageManager) {
                $originalExtension = $file->getClientOriginalExtension();
                $filename = $this->generateFilename($file, $originalExtension, $contentId);
                $path = "{$directory}/{$filename}";
                
                Storage::disk($this->disk)->putFileAs($directory, $file, $filename);
                
                \Log::info("Imagen guardada sin procesamiento (GD no disponible): {$path}");
                return $path;
            }

            // Generar nombre único para el archivo
            $filename = $this->generateFilename($file, $config->formato, $contentId);
            
            // Procesar imagen según configuración
            $image = $this->imageManager->read($file->getRealPath());
            
            if ($config->redimensionar) {
                if ($config->mantener_aspecto) {
                    // Si alto es null o 0, solo fijar ancho y mantener proporción
                    if (empty($config->alto) || $config->alto == 0) {
                        $image->resize($config->ancho, null, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                    } else {
                        // Redimensionar manteniendo proporción (fit dentro de las dimensiones)
                        $image->scaleDown(width: $config->ancho, height: $config->alto);
                    }
                } else {
                    // Redimensionar forzando tamaño exacto
                    $image->resize($config->ancho, $config->alto);
                }
            }

            // Aplicar compresión según formato
            switch ($config->formato) {
                case 'jpg':
                case 'jpeg':
                    $encodedImage = $image->toJpeg($config->calidad);
                    break;
                case 'png':
                    $encodedImage = $image->toPng();
                    break;
                case 'webp':
                    $encodedImage = $image->toWebp($config->calidad);
                    break;
                default:
                    $encodedImage = $image->toJpeg($config->calidad);
            }

            // Guardar imagen procesada
            $path = "{$directory}/{$filename}";
            Storage::disk($this->disk)->put($path, $encodedImage);

            return $path;

        } catch (\Exception $e) {
            \Log::error('Error procesando imagen: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generar nombre único para archivo
     */
    protected function generateFilename(UploadedFile $file, string $formato, int $contentId = null): string
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = Str::slug($originalName);
        $timestamp = time();
        $random = Str::random(6);
        
        if ($contentId) {
            return "content_{$contentId}_{$safeName}_{$timestamp}_{$random}.{$formato}";
        }
        
        return "{$safeName}_{$timestamp}_{$random}.{$formato}";
    }

    /**
     * Eliminar imagen del storage
     */
    public function deleteImage(string $imagePath): bool
    {
        try {
            if (Storage::disk($this->disk)->exists($imagePath)) {
                return Storage::disk($this->disk)->delete($imagePath);
            }
            return true;
        } catch (\Exception $e) {
            \Log::error('Error eliminando imagen: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener URL pública de la imagen
     */
    public function getImageUrl(string $imagePath): string
    {
        return Storage::disk($this->disk)->url($imagePath);
    }

    /**
     * Procesar y guardar imagen con versiones responsive (desktop y móvil)
     */
    public function processAndSaveResponsiveImage(UploadedFile $file, string $tipoContenido, string $tipoImagen, int $contentId = null): array
    {
        try {
            // Obtener configuración para este tipo de contenido e imagen
            $config = ImageConfig::getConfig($tipoContenido, $tipoImagen);
            
            if (!$config) {
                // Usar método tradicional si no hay configuración responsive
                $desktopPath = $this->processAndSaveImage($file, $tipoContenido, $tipoImagen, $contentId);
                return [
                    'desktop' => $desktopPath,
                    'mobile' => null,
                    'success' => true,
                    'message' => 'Imagen procesada sin configuración responsive'
                ];
            }

            // Si GD no está disponible, usar método tradicional
            if (!$this->imageManager) {
                $desktopPath = $this->processAndSaveImage($file, $tipoContenido, $tipoImagen, $contentId);
                return [
                    'desktop' => $desktopPath,
                    'mobile' => null,
                    'success' => true,
                    'message' => 'Imagen guardada sin procesamiento (GD no disponible)'
                ];
            }

            // Crear directorios si no existen
            $desktopDirectory = "images/{$tipoContenido}/{$tipoImagen}";
            $mobileDirectory = "images/{$tipoContenido}/{$tipoImagen}/mobile";
            Storage::disk($this->disk)->makeDirectory($desktopDirectory);
            
            if ($config->generar_version_movil) {
                Storage::disk($this->disk)->makeDirectory($mobileDirectory);
            }

            // Generar nombres de archivo
            $baseFilename = $this->generateFilename($file, $config->formato, $contentId);
            $mobileFilename = str_replace('.' . $config->formato, '_mobile.' . $config->formato, $baseFilename);

            // Procesar imagen original
            $image = $this->imageManager->read($file->getRealPath());
            
            // === GENERAR VERSIÓN DESKTOP ===
            $desktopImage = clone $image;
            
            if ($config->redimensionar) {
                if ($config->mantener_aspecto) {
                    $desktopImage->scaleDown(width: $config->ancho, height: $config->alto);
                } else {
                    $desktopImage->resize($config->ancho, $config->alto);
                }
            }

            // Guardar versión desktop
            $desktopPath = "{$desktopDirectory}/{$baseFilename}";
            $encodedDesktop = $this->encodeImage($desktopImage, $config->formato, $config->calidad);
            Storage::disk($this->disk)->put($desktopPath, $encodedDesktop);

            $mobilePath = null;

            // === GENERAR VERSIÓN MÓVIL (si está habilitada) ===
            if ($config->generar_version_movil && $config->ancho_movil && $config->alto_movil) {
                $mobileImage = clone $image;
                
                if ($config->mantener_aspecto_movil) {
                    $mobileImage->scaleDown(width: $config->ancho_movil, height: $config->alto_movil);
                } else {
                    $mobileImage->resize($config->ancho_movil, $config->alto_movil);
                }

                // Guardar versión móvil
                $mobilePath = "{$mobileDirectory}/{$mobileFilename}";
                $encodedMobile = $this->encodeImage($mobileImage, $config->formato, $config->calidad_movil);
                Storage::disk($this->disk)->put($mobilePath, $encodedMobile);
            }

            \Log::info("Imagen responsive procesada", [
                'desktop' => $desktopPath,
                'mobile' => $mobilePath,
                'config' => $config->descripcion
            ]);

            return [
                'desktop' => $desktopPath,
                'mobile' => $mobilePath,
                'success' => true,
                'message' => 'Imagen responsive generada exitosamente',
                'desktop_size' => "{$config->ancho}x{$config->alto}",
                'mobile_size' => $mobilePath ? "{$config->ancho_movil}x{$config->alto_movil}" : null
            ];

        } catch (\Exception $e) {
            \Log::error('Error al procesar imagen responsive: ' . $e->getMessage());
            
            return [
                'desktop' => null,
                'mobile' => null,
                'success' => false,
                'message' => 'Error al procesar imagen: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Codificar imagen según formato y calidad
     */
    protected function encodeImage($image, string $formato, int $calidad)
    {
        switch ($formato) {
            case 'jpg':
            case 'jpeg':
                return $image->toJpeg($calidad);
            case 'png':
                return $image->toPng();
            case 'webp':
                return $image->toWebp($calidad);
            default:
                return $image->toJpeg($calidad);
        }
    }

    /**
     * Validar archivo de imagen
     */
    public function validateImageFile(UploadedFile $file): array
    {
        $errors = [];
        
        // Validar tamaño (máximo 10MB)
        if ($file->getSize() > 10 * 1024 * 1024) {
            $errors[] = 'El archivo es demasiado grande. Máximo 10MB.';
        }
        
        // Validar tipo MIME
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            $errors[] = 'Formato de imagen no válido. Use JPG, PNG, GIF o WebP.';
        }
        
        // Validar dimensiones mínimas
        $imageInfo = getimagesize($file->getRealPath());
        if ($imageInfo) {
            if ($imageInfo[0] < 100 || $imageInfo[1] < 100) {
                $errors[] = 'La imagen es demasiado pequeña. Mínimo 100x100 píxeles.';
            }
        }
        
        return $errors;
    }

    /**
     * Obtener configuraciones de imagen para un tipo de contenido
     */
    public function getImageConfigs(string $tipoContenido): array
    {
        return ImageConfig::where('tipo_contenido', $tipoContenido)
                         ->where('activo', true)
                         ->get()
                         ->keyBy('tipo_imagen')
                         ->toArray();
    }

    /**
     * Obtener URL de imagen responsive
     */
    public function getResponsiveImageUrl(string $desktopPath, bool $isMobile = false): string
    {
        if (!$desktopPath) {
            return '';
        }

        if (!$isMobile) {
            return asset('storage/' . $desktopPath);
        }

        // Construir ruta de versión móvil
        $pathParts = pathinfo($desktopPath);
        $mobilePath = $pathParts['dirname'] . '/mobile/' . $pathParts['filename'] . '_mobile.' . $pathParts['extension'];
        
        // Verificar si existe la versión móvil
        if (Storage::disk($this->disk)->exists($mobilePath)) {
            return asset('storage/' . $mobilePath);
        }

        // Fallback a versión desktop si no existe móvil
        return asset('storage/' . $desktopPath);
    }

    /**
     * Generar HTML de imagen responsive con srcset
     */
    public function generateResponsiveImageHtml(string $desktopPath, string $alt = '', string $class = '', string $style = ''): string
    {
        if (!$desktopPath) {
            return '';
        }

        $desktopUrl = $this->getResponsiveImageUrl($desktopPath, false);
        $mobileUrl = $this->getResponsiveImageUrl($desktopPath, true);
        
        // Preparar atributos
        $styleAttr = $style ? "style=\"{$style}\"" : '';
        $classAttr = $class ? "class=\"{$class}\"" : '';
        
        // Si no hay versión móvil diferente, usar imagen simple
        if ($desktopUrl === $mobileUrl) {
            return "<img src=\"{$desktopUrl}\" alt=\"{$alt}\" {$classAttr} {$styleAttr}>";
        }

        // Obtener dimensiones reales de las imágenes para srcset correcto
        try {
            $desktopFullPath = storage_path('app/public/' . $desktopPath);
            $mobileFullPath = str_replace('/mobile/', '/mobile/', storage_path('app/public/' . $desktopPath));
            $pathParts = pathinfo($desktopPath);
            $mobileRelativePath = $pathParts['dirname'] . '/mobile/' . $pathParts['filename'] . '_mobile.' . $pathParts['extension'];
            $mobileFullPath = storage_path('app/public/' . $mobileRelativePath);
            
            $desktopSize = @getimagesize($desktopFullPath);
            $mobileSize = @getimagesize($mobileFullPath);
            
            if ($desktopSize && $mobileSize) {
                // Usar anchos reales de las imágenes en srcset
                $desktopWidth = $desktopSize[0];
                $mobileWidth = $mobileSize[0];
                
                return "<img src=\"{$desktopUrl}\" 
                             srcset=\"{$mobileUrl} {$mobileWidth}w, {$desktopUrl} {$desktopWidth}w\" 
                             sizes=\"(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw\"
                             alt=\"{$alt}\" 
                             {$classAttr}
                             {$styleAttr}>";
            }
        } catch (\Exception $e) {
            \Log::warning('Error obteniendo dimensiones de imagen: ' . $e->getMessage());
        }

        // Fallback: usar breakpoints de viewport si no se pueden obtener dimensiones
        return "<img src=\"{$desktopUrl}\" 
                     srcset=\"{$mobileUrl} 400w, {$desktopUrl} 800w\" 
                     sizes=\"(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw\"
                     alt=\"{$alt}\" 
                     {$classAttr}
                     {$styleAttr}>";
    }
}