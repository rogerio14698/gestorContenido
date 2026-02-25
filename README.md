# 🎭 Nuntris Teatro - Laravel 12

Sitio web oficial de la compañía teatral asturiana **Nuntris Teatro**, desarrollado con Laravel 12 y diseñado para ser completamente bilingüe (Español/Asturianu) y accesible.

## 🌟 Características Principales

### 🌍 **Sistema Multiidioma**
- **Español (ES)** - Idioma principal
- **Asturianu (AS)** - Idioma regional
- Contenido independiente por idioma
- URLs localizadas (`/es/` y `/as/`)
- Cambio dinámico de idioma

### ♿ **Accesibilidad Web**
- Descripciones ALT multiidioma para imágenes
- Cumplimiento de estándares WCAG
- Soporte para lectores de pantalla
- Navegación accesible

### 📝 **Gestión de Contenidos**
- **Tipos de contenido**: Páginas, Noticias, Entrevistas
- **Editor TinyMCE 7** con plantillas teatrales
- **Gestión de imágenes** con redimensionado automático
- **Sistema de menús** dinámico
- **Galerías de imágenes**

### 🎨 **Interfaz de Administración**
- Panel AdminLTE 3.2 con Bootstrap 5
- Gestión intuitiva de contenido multiidioma
- Sistema de autenticación personalizado
- Dashboard con estadísticas

## 🚀 Tecnologías

- **Framework**: Laravel 12
- **Base de Datos**: SQLite (desarrollo)
- **Frontend**: Bootstrap 5 + AdminLTE 3.2
- **Editor**: TinyMCE 7 Community (auto-hospedado)
- **Imágenes**: Intervention Image
- **Iconos**: Font Awesome 6

## 📦 Instalación

```bash
# Clonar repositorio
git clone [URL_DEL_REPOSITORIO]
cd nuntristeatro-laravel12

# Instalar dependencias
composer install
npm install

# Configurar entorno
cp .env.example .env
php artisan key:generate

# Configurar base de datos
php artisan migrate
php artisan db:seed

# Crear enlace simbólico para storage
php artisan storage:link

# Iniciar servidor de desarrollo
php artisan serve
```

## 🗄️ Base de Datos

### Tablas Principales
- `contents` - Contenidos principales
- `textos_idiomas` - Textos en diferentes idiomas
- `idiomas` - Configuración de idiomas
- `menus` - Sistema de menús
- `image_configs` - Configuraciones de imagen

## 📁 Estructura del Proyecto

```
├── app/
│   ├── Http/Controllers/Admin/     # Controladores del panel admin
│   ├── Models/                     # Modelos Eloquent
│   ├── Services/                   # Servicios (ImageService)
│   └── Middleware/                 # Middleware personalizado
├── resources/
│   ├── views/admin/               # Vistas del panel de administración
│   ├── views/web/                 # Vistas del frontend
│   └── views/layouts/             # Layouts principales
├── database/
│   ├── migrations/                # Migraciones de BD
│   └── seeders/                   # Datos iniciales
└── public/
    ├── storage/images/            # Imágenes subidas
    └── tinymce/                   # TinyMCE auto-hospedado
```

## 🎯 Funcionalidades

### ✅ **Completadas**
- [x] Sistema multiidioma completo
- [x] Gestión de contenidos (CRUD)
- [x] Panel de administración
- [x] TinyMCE con plantillas teatrales
- [x] Gestión de imágenes con redimensionado
- [x] Descripciones ALT multiidioma
- [x] Sistema de menús
- [x] Frontend responsivo
- [x] Autenticación de administrador

### 🔄 **Próximas Funcionalidades**
- [ ] Sistema de galerías completo
- [ ] Optimización SEO avanzada
- [ ] Cache de contenido
- [ ] Backup automático

## 🎪 Contexto Teatral

Este proyecto está específicamente diseñado para **Nuntris Teatro**, una compañía teatral asturiana. Incluye:

- Plantillas de contenido específicas para teatro
- Terminología teatral en ambos idiomas
- Gestión de obras, entrevistas y noticias
- Soporte para contenido cultural bilingüe

## 🌟 Características Especiales

### **Accesibilidad Multiidioma**
- Descripciones ALT específicas por idioma
- Navegación localizada
- Contenido culturalmente apropiado

### **Gestión de Imágenes Avanzada**
- Redimensionado automático por tipo de contenido
- Múltiples formatos (WebP, JPG, PNG)
- Optimización automática

### **Editor Enriquecido**
- Plantillas predefinidas para teatro
- Integración con YouTube
- Gestión de tablas e imágenes

## 🛠️ Desarrollo

```bash
# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders
php artisan db:seed

# Limpiar caché
php artisan cache:clear
php artisan view:clear
php artisan config:clear

# Generar enlaces simbólicos
php artisan storage:link
```

## 📝 Notas de Desarrollo

### Última Actualización: 6 Noviembre 2025
- ✅ Implementadas descripciones ALT multiidioma
- ✅ Reorganizada interfaz de administración
- ✅ Limpieza de código y estructura
- ✅ Sistema totalmente funcional

---

**Desarrollado con ❤️ para la cultura asturiana** 🎭