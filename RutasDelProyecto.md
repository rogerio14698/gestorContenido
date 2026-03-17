
/principal -> notfound
/inicio o /es -> ok
GET /contacto -> ok | /contacto POST -> ok
/idioma -> notfound
/login -> ok 
/debug-textos -> ver que hace


//RUTAS DENTRO DE IDIOMAS

/{idioma} Inicio
/{idioma}/noticias
/{idioma}/galerias
/{idioma}/galerias/slug
/{idioma}/slug -> ruta dinámica para las páginas.

//Rutas admin y CRUD
/admin/login -> ok inicio y cierre de sesión

CRUD
Idiomas: /admin/idiomas -> ruta funciona -> ver CRUD si funciona
Contenidos: /admin/contents y /admin/tipos -> error ERR_NAME_NOT_RESOLVED -105
Imágenes: /admin/image-configs y /admin/slides, ok
Galerías: /admin/galleries, ok
Menús: /admin/menus, ok
Seguridad: /admin/roles, /admin/permissions, /admin/users; ok

UTILIDADES DEL ADMIN: 
Matriz de permisos: /admin/roles/permission-matrix/view
Ver datos de sesion: /admin/debug-session
Ver JSON con datos del usuario actual, su rol y permisos: /admin/debug-me
