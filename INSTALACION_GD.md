# Configuración de Extensión GD para Procesamiento de Imágenes

## Problema
El sistema requiere la extensión **GD de PHP** para el procesamiento automático de imágenes (redimensionado, compresión, conversión de formatos).

## Estado Actual
- ✅ **Sistema funcional**: Las imágenes se guardan sin procesamiento
- ⚠️ **Sin GD**: No hay redimensionado ni optimización automática
- 📝 **Solución implementada**: Fallback que guarda imágenes originales

## Instalación de GD (Recomendada)

### Ubuntu/Debian:
```bash
sudo apt update
sudo apt install php-gd php8.2-gd
sudo systemctl restart apache2  # Si usas Apache
sudo systemctl restart nginx    # Si usas Nginx
```

### CentOS/RHEL:
```bash
sudo yum install php-gd
# o con dnf:
sudo dnf install php-gd
sudo systemctl restart httpd
```

### Verificar instalación:
```bash
php -m | grep gd
php -i | grep -i gd
```

## Después de instalar GD

1. **Reiniciar el servidor Laravel:**
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```

2. **Verificar funcionalidad:**
   - Subir una imagen desde `/admin/contents/create`
   - Verificar que se procesa según configuraciones
   - Comprobar redimensionado automático

## Características con GD habilitada

- ✅ **Redimensionado automático** según configuraciones por tipo
- ✅ **Optimización de calidad** configurable
- ✅ **Conversión de formatos** (JPG, PNG, WebP)
- ✅ **Mantenimiento de proporciones** opcional
- ✅ **Nombres de archivo únicos** y organizados

## Configuraciones actuales

| Tipo Contenido | Tipo Imagen | Dimensiones | Formato | Calidad |
|----------------|-------------|-------------|---------|---------|
| Noticia | Imagen | 800×600px | JPG | 85% |
| Noticia | Portada | 400×300px | JPG | 80% |
| Página | Imagen | 1200×800px | JPG | 90% |
| Página | Portada | 600×400px | JPG | 85% |
| Entrevista | Imagen | 800×600px | JPG | 85% |
| Entrevista | Portada | 400×300px | JPG | 80% |

## Panel de configuración
- **URL**: http://localhost:8000/admin/image-configs
- **Funcionalidad**: Gestión completa de configuraciones de imagen
- **Modificable**: Tamaños, formatos, calidad por tipo de contenido