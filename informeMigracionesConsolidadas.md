# Migraciones Consolidadas 2026

Estructura consolidada y limpia de migraciones en `database/migrations`.

## 2026_01_01_000001_create_roles_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
```

## 2026_01_01_000002_create_permissions_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->string('modulo');
            $table->enum('tipo_permiso', ['crear', 'mostrar', 'editar', 'eliminar']);
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['modulo', 'tipo_permiso']);
            $table->unique(['modulo', 'tipo_permiso', 'slug'], 'permissions_unique_module_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
```

## 2026_01_01_000003_create_users_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->foreignId('role_id')->nullable()->constrained('roles')->onDelete('set null');
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
```

## 2026_01_01_000004_create_cache_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
    }
};
```

## 2026_01_01_000005_create_jobs_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
    }
};
```

## 2026_01_01_000006_create_languages_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('etiqueta', 10)->unique();
            $table->string('imagen', 255)->nullable();
            $table->boolean('activo')->default(true);
            $table->boolean('es_principal')->default(false);
            $table->integer('orden')->default(0);
            $table->timestamps();

            $table->index(['activo']);
            $table->index(['es_principal']);
            $table->index(['orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
```

## 2026_01_01_000007_create_content_types_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_types', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_contenido', 100)->nullable();
            $table->string('descripcion', 255)->nullable();
            $table->string('icono', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_types');
    }
};
```

## 2026_01_01_000008_create_settings_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_empresa')->nullable();
            $table->string('direccion_empresa')->nullable();
            $table->string('telefono_empresa', 20)->nullable();
            $table->string('movil_empresa', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('nif_cif', 20)->nullable();
            $table->string('metatitulo')->nullable();
            $table->string('metadescripcion', 500)->nullable();
            $table->string('g_analytics', 20)->nullable();
            $table->string('url')->nullable();
            $table->string('youtube')->nullable();
            $table->string('google_plus')->nullable();
            $table->string('instagram')->nullable();
            $table->string('twitter')->nullable();
            $table->string('facebook')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
```

## 2026_01_01_000009_create_company_settings_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->nullable();
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('metatitulo_es')->nullable();
            $table->text('metadescripcion_es')->nullable();
            $table->string('metatitulo_ast')->nullable();
            $table->text('metadescripcion_ast')->nullable();
            $table->string('metatitulo_en')->nullable();
            $table->text('metadescripcion_en')->nullable();
            $table->json('redes_sociales')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
```

## 2026_01_01_000010_create_galleries_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('galleries');
    }
};
```

## 2026_01_01_000011_create_contents_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->string('lugar', 100)->nullable();
            $table->date('fecha')->nullable();
            $table->date('fecha_publicacion')->nullable();
            $table->enum('tipo_contenido', ['pagina', 'noticia', 'entrevista', 'galeria'])->default('noticia');
            $table->string('imagen', 100)->nullable();
            $table->text('imagen_alt')->nullable();
            $table->string('imagen_portada', 191)->nullable();
            $table->text('imagen_portada_alt')->nullable();
            $table->boolean('pagina_estatica')->default(false);
            $table->tinyInteger('columnas')->default(1);
            $table->text('fb_pixel')->nullable();
            $table->boolean('portada')->default(false);
            $table->foreignId('galeria_id')->nullable()->constrained('galleries')->onDelete('set null');
            $table->enum('actions', ['inicio', 'noticias', 'contacto'])->nullable();
            $table->integer('orden')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
```

## 2026_01_01_000012_create_gallery_images_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gallery_id')->constrained('galleries')->onDelete('cascade');
            $table->string('titulo')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('imagen');
            $table->string('imagen_miniatura')->nullable();
            $table->string('alt_text')->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('activa')->default(true);
            $table->json('metadatos')->nullable();
            $table->timestamps();

            $table->index(['gallery_id', 'orden']);
            $table->index(['gallery_id', 'activa']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_images');
    }
};
```

## 2026_01_01_000013_create_gallery_image_texts_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_image_texts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gallery_image_id')->constrained('gallery_images')->onDelete('cascade');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->string('titulo')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('alt_text', 255)->nullable();
            $table->timestamps();

            $table->index(['gallery_image_id', 'language_id']);
            $table->unique(['gallery_image_id', 'language_id'], 'unique_gallery_image_language');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_image_texts');
    }
};
```

## 2026_01_01_000014_create_menus_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('menus')->onDelete('cascade');
            $table->enum('tipo_enlace', ['contenido', 'url_externa', 'ninguno'])->default('contenido');
            $table->foreignId('tipo_contenido_id')->nullable()->constrained('content_types')->onDelete('set null');
            $table->foreignId('content_id')->nullable()->constrained('contents')->onDelete('set null');
            $table->text('url')->nullable();
            $table->string('url_externa')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('menu_pie')->default(false);
            $table->boolean('visible')->default(true);
            $table->boolean('abrir_nueva_ventana')->default(false);
            $table->integer('orden')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
```

## 2026_01_01_000015_create_content_texts_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_texts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->foreignId('content_id')->nullable()->constrained('contents')->onDelete('cascade');
            $table->foreignId('content_type_id')->nullable()->constrained('content_types')->onDelete('cascade');
            $table->string('objeto_type')->nullable();
            $table->unsignedBigInteger('objeto_id')->nullable();
            $table->string('campo')->nullable();
            $table->text('texto')->nullable();
            $table->string('titulo')->nullable();
            $table->string('subtitulo')->nullable();
            $table->text('resumen')->nullable();
            $table->longText('contenido')->nullable();
            $table->text('metadescripcion')->nullable();
            $table->text('metatitulo')->nullable();
            $table->string('imagen_alt', 255)->nullable();
            $table->string('imagen_portada_alt', 255)->nullable();
            $table->string('slug', 191)->nullable();
            $table->boolean('visible')->default(true);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['objeto_type', 'objeto_id']);
            $table->index(['objeto_type', 'objeto_id', 'language_id', 'campo']);
            $table->unique(['slug', 'content_type_id', 'language_id'], 'slug_content_type_language_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_texts');
    }
};
```

## 2026_01_01_000016_create_image_configs_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('image_configs', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_contenido');
            $table->string('tipo_imagen');
            $table->integer('ancho');
            $table->integer('alto');
            $table->integer('ancho_movil')->nullable();
            $table->integer('alto_movil')->nullable();
            $table->boolean('mantener_aspecto')->default(true);
            $table->boolean('mantener_aspecto_movil')->default(true);
            $table->integer('calidad_movil')->default(85);
            $table->boolean('generar_version_movil')->default(true);
            $table->string('descripcion')->nullable();
            $table->string('formato')->default('jpg');
            $table->integer('calidad')->default(85);
            $table->boolean('redimensionar')->default(true);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['tipo_contenido', 'tipo_imagen']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('image_configs');
    }
};
```

## 2026_01_01_000017_create_slides_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slides', function (Blueprint $table) {
            $table->id();
            $table->boolean('nueva_ventana')->default(false);
            $table->string('imagen')->nullable();
            $table->string('imagen_miniatura')->nullable();
            $table->json('metadatos')->nullable();
            $table->boolean('visible')->default(true);
            $table->integer('orden')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['visible', 'activo', 'orden']);
            $table->index(['orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slides');
    }
};
```

## 2026_01_01_000018_create_slide_translations_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slide_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slide_id')->constrained('slides')->onDelete('cascade');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('alt_text')->nullable();
            $table->string('url', 500)->nullable();
            $table->timestamps();

            $table->unique(['slide_id', 'language_id']);
            $table->index(['slide_id', 'language_id']);
            $table->index('titulo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slide_translations');
    }
};
```

## 2026_01_01_000019_create_role_permissions_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['role_id', 'permission_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};
```

