# Instrucciones de Desarrollo - Proyecto Horae (Laravel 12)

## 1. Perfil y Stack Técnico
- **Rol:** Desarrollador de aplicaciones web Junior.
- **Stack:** Laravel 12, PHP 8.3, MySQL, JavaScript (Vanilla), HTML5, CSS3.
- **Idioma:** Explicaciones y comentarios en Español. Código (variables, funciones, clases) en Inglés.

## 2. Estilo de Código PHP (Laravel 12 & PHP 8.3)
- **Tipado Estricto:** Usa siempre `declare(strict_types=1);` al inicio de archivos PHP.
- **Funciones:** Usa `arrow functions` (`fn() => ...`) para funciones anónimas cortas.
- **Retornos:** Declara siempre el tipo de retorno en todas las funciones y métodos.
- **Modern PHP:** Usa "Constructor Property Promotion" para inyectar dependencias en controladores o servicios.
- **Clean Code:** Nombres descriptivos, código fácil de mantener y comentarios explicativos para nivel Junior.
- **Eloquent:** Usa siempre el ORM Eloquent. Evita consultas SQL puras (Raw SQL).

## 3. Arquitectura y Vistas (Blade)
- **Estructura:** - Layout principal: Usar `@yield` y `@section`.
  - Componentes pequeños/reutilizables: Usar `@include`.
- **Simplicidad:** Mantén las vistas simples. Si la lógica es compleja, muévela a un `Helper` o `Componente` y añade comentarios explicativos.
- **Blade Directives:** Usa directivas de Blade para evitar código PHP sucio en las vistas.
- **Rutas:** Usa la sintaxis de array para controladores: `Route::get('/', [Controller::class, 'index']);`.

## 4. Frontend: CSS y Estilos
- **Organización:** - Centraliza todo en un `main.css`.
  - Crea directorios por sección (ej: `/css/layouts/`, `/css/pages/`) e impórtalos en el principal.
- **Nomenclatura:** Usa **CamelCase** para los nombres de las clases CSS (ej: `.tablaPrincipal`, `.botonGuardar`).
- **Variables:** Usa variables CSS (`:root { --primaryColor: #... }`) para colores, fuentes y valores reutilizables.
- **Prohibición:** **JAMÁS** uses estilos en línea (`style="..."`).

## 5. Frontend: JavaScript
- **Filosofía:** JavaScript puro (Vanilla JS). Evita librerías externas (como jQuery) a menos que sea estrictamente necesario.
- **Funciones Flecha:** Usa `const miFuncion = () => { ... }` con tipos de retorno documentados.
- **Claridad:** Código sencillo, limpio y muy bien comentado para que un Junior entienda la lógica.

## 6. HTML y Accesibilidad
- **Semántica:** Usa etiquetas semánticas (`<main>`, `<section>`, `<article>`, `<header>`, `<footer>`).
- **Validación:** Asegúrate de cerrar todas las etiquetas y mantener una indentación de 2 espacios.

## 7. Manejo de Errores y Seguridad
- **Excepciones:** Usa bloques `try-catch` tanto en PHP como en JS.
- **Feedback:** Proporciona mensajes de error claros y amigables.
- **Validación:** En Laravel, usa siempre `$request->validate([...])` antes de procesar datos.
- **Modelos:** Define siempre `protected $fillable` en los modelos para evitar asignaciones masivas inseguras.

## 8. Reglas de "Oro" (Nunca hagas esto)
- No dupliques código (DRY - Don't Repeat Yourself).
- No dejes lógica compleja en las vistas (mueve la lógica al Controlador o Modelo).
- No instales librerías de JS si se puede resolver con 5 líneas de JS puro.
- No generes tablas sin la clase contenedora de protección de desbordamiento (ej: `.tableResponsive`).
 
## 9. Comentarios y Nivel de Detalle (REGLA IMPORTANTE)
- **Nivel requerido:** Todos los fragmentos de código que generes deben incluir comentarios extremadamente explicativos, casi línea por línea. El objetivo es que un desarrollador Junior entienda exactamente qué hace cada línea y por qué está ahí.
- **Formato general:** Comentarios en Español. Para PHP y JS, usa comentarios de bloque (`/** ... */`) al inicio de funciones/métodos y comentarios de línea (`//`) antes de instrucciones complejas. Evita comentarios redundantes obvios (no comentes `i++` como "incrementar i" sin contexto).
- **Antes de cada función/método:** Incluye un bloque con:
  - **Descripción:** una frase que resume el objetivo de la función.
  - **Parámetros:** lista con nombre, tipo y explicación de cada parámetro.
  - **Retorno:** tipo y significado del valor devuelto.
  - **Efectos secundarios:** si modifica estado, base de datos, sesión, archivos, etc.
  - **Ejemplo de uso breve** si aplica.
- **Comentarios dentro de la función/método:**
  - Comenta cada paso lógico con una línea explicativa justo antes de las instrucciones relevantes.
  - Explica decisiones no triviales (por ejemplo, por qué se elige una consulta Eloquent en vez de otra, o por qué se hace un `->with('relation')`).
  - Señala supuestos (p. ej. "asumo que `$user` tiene `role_id` porque el seeder lo garantiza").
  - Indica valores esperados y formatos (p. ej. "`$date` debe ser string 'Y-m-d'"), y cómo se manejan errores.
- **En vistas Blade:** antes de bloques complejos (`@foreach`, `@if`, helpers personalizados) añade un comentario explicando qué variables llegan, su estructura (tipo/shape) y el resultado esperado en HTML.
- **En JavaScript:** documenta cada función y cada bloque asíncrono; cuando manipules el DOM, explica por qué se elige ese selector y qué efecto produce.
- **En migraciones y seeders:** documenta el propósito del cambio en DB, y si es irreversible deja nota clara.
- **Ejemplo breve (PHP):**
  ```php
  /**
   * Obtener contenidos recientes.
   * @param int $limit Número máximo de elementos a devolver.
   * @return \Illuminate\Database\Eloquent\Collection Colección de Content
   */
  public function recent(int $limit = 5): Collection
  {
      // Seleccionamos sólo contenidos publicados porque los drafts no deben mostrarse en la web pública.
      // Ordenamos por fecha de creación descendente para mostrar los más recientes primero.
      return Content::where('status', 'published')
                    ->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get();
  }
  ```

**IMPORTANTE:** este nivel de comentarios debe aplicarse por defecto en cada cambio de código que solicites. Si en algún caso deseas comentarios más concisos, indícalo explícitamente en la petición.