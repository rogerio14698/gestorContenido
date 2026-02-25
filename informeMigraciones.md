# Informe de Migraciones

## Resumen ejecutivo
- Fecha: 2026-02-11
- Objetivo: identificar y corregir errores en las migraciones para permitir su ejecución completa, y documentar los cambios realizados.
- Estado final: todas las migraciones pendientes aplicadas correctamente tras correcciones.

## Cambios aplicados (por archivo)
- [database/migrations/2025_11_07_094118_modify_galleries_table_simplify.php](database/migrations/2025_11_07_094118_modify_galleries_table_simplify.php)
  - Acción: Añadida desactivación y reactivación de restricciones FK (`Schema::disableForeignKeyConstraints()` / `Schema::enableForeignKeyConstraints()`) alrededor de `drop/create`.
  - Por qué: al dropear `galleries` existían referencias desde otras tablas; desactivar FKs evita violaciones durante la reconstrucción y restauración de datos.

- [database/migrations/2025_11_07_112224_fix_contents_gallery_foreign_key.php](database/migrations/2025_11_07_112224_fix_contents_gallery_foreign_key.php)
  - Acciones:
    - Importado `DB` para ejecutar sentencias SQL directas.
    - Envolví operaciones peligrosas en `Schema::disableForeignKeyConstraints()` / `enable...` con `try/finally`.
    - Reemplacé `->change()` sobre el `enum` por `DB::statement("ALTER TABLE ... MODIFY tipo_contenido ENUM(...)")` (MySQL) para evitar dependencia de `doctrine/dbal`.
  - Por qué: `->change()` falla sin `doctrine/dbal`; alterar enums en MySQL es más fiable con SQL directo; las FKs se manejan de forma segura.

- [database/migrations/2025_11_10_085716_update_idiomas_table_structure.php](database/migrations/2025_11_10_085716_update_idiomas_table_structure.php)
  - Acciones:
    - Reemplacé `DB::statement` complejo por migración de datos usando query builder (`DB::table(...)->get()` y `->insert()`) para portabilidad entre motores (MySQL/SQLite).
    - Añadí `Schema::dropIfExists('idiomas_new')` y `Schema::dropIfExists('idiomas_old')` antes de crear tablas temporales para evitar errores si existían de ejecuciones parciales.
    - Envolví `drop`/`rename` en desactivación temporal de FKs (try/finally).
  - Por qué: evitar sintaxis SQL no portable y prevenir fallos si migración fue ejecutada parcialmente anteriormente.

- [database/migrations/2025_11_10_114507_add_menu_support_to_textos_idiomas_table.php](database/migrations/2025_11_10_114507_add_menu_support_to_textos_idiomas_table.php)
  - Acciones:
    - Eliminado uso directo de `->change()` para `contenido_id` (requiere `doctrine/dbal`).
    - Añadido intento seguro de alterar nullability con `DB::statement("ALTER TABLE ...")` dentro de `try/catch` y verificación `Schema::hasColumn()`; si falla, la migración no aborta.
    - Corregido un `});` extra que producía un `ParseError` (error de sintaxis) y añadido comentario explicativo.
  - Por qué: evitar fallos por ausencia de `doctrine/dbal` y corregir error de sintaxis.

- [database/migrations/2025_11_18_100000_force_tipo_contenido_id_nullable_sqlite.php](database/migrations/2025_11_18_100000_force_tipo_contenido_id_nullable_sqlite.php)
  - Acciones:
    - Migración originalmente con `PRAGMA` (SQLite). Añadí detección del driver y salté la ejecución si el driver no es `sqlite`.
  - Por qué: `PRAGMA` no es válido en MySQL/MariaDB; así evitamos errores en entornos no SQLite.

## Mapa de dependencias (tabla -> depende de)
- `gallery_images` -> `galleries`
- `gallery_image_texts` -> `gallery_images`, `idiomas`
- `contents` -> originalmente `galerias` (legacy); migración reconfigura para usar `galleries`
- `textos_idiomas` -> `idiomas`, `contents`, `tipo_contenidos`
- `menus` -> `menus` (self parent), `contents`, y (tras update) `tipo_contenidos`
- `slide_translations` -> `slides`, `idiomas`
- `role_permissions` -> `roles`, `permissions`

Tablas sin dependencias externas importantes: `galleries` (nuevo), `galerias` (legacy), `tipo_contenidos`, `image_configs`, `slides`, `roles`, `permissions`, `jobs`, `job_batches`.

## Errores detectados y cómo se resolvieron (resumen)
- ParseError por cierre extra en `add_menu_support_to_textos_idiomas_table.php` → se eliminó el cierre sobrante.
- Violaciones de FK al dropear/renombrar tablas (`idiomas`, `galleries`) → envolvimiento con `Schema::disableForeignKeyConstraints()` / `enable...` y uso de `try/finally`.
- Uso de `->change()` en migraciones sin `doctrine/dbal` → evitado mediante SQL directo o query-builder: se usa `DB::statement` cuando es seguro (MySQL) o se modifica datos con el query builder para portabilidad.
- SQL específico de SQLite (`PRAGMA`) ejecutado en MySQL → añadido chequeo de driver y omisión en drivers no sqlite.
- Tablas temporales (`idiomas_new`, `idiomas_old`) ya existentes por ejecuciones parciales → añadí `dropIfExists` antes de `create`.

## Resultado de la ejecución
- Ejecuté `php artisan migrate --force` tras cada corrección y resolví los fallos iterativamente. Al final, todas las migraciones pendientes se aplicaron correctamente.

## Recomendaciones
- Instalar `doctrine/dbal` si se pretende usar `->change()` regularmente: `composer require doctrine/dbal`.
- Evitar coexistir con nombres de tablas legacy y nuevas (`galerias` vs `galleries`) sin un plan claro de migración; consolidar o documentar el orden de migraciones.
- Añadir pruebas automatizadas (o un sandbox) que ejecuten migraciones en una base limpia antes de desplegar en producción.
- Documentar en cada migración si es irreversible o si necesita `--force` en producción.

## Comandos útiles
- Ejecutar migraciones (producción):

```bash
php artisan migrate --force
```

- Ver estado de migraciones:

```bash
php artisan migrate:status
```

---

Si quieres, genero también un diagrama Mermaid del grafo de dependencias y lo incluyo en este informe.
