<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\WebController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ContentAdminController;
use App\Http\Controllers\Admin\MenuAdminController;
use App\Http\Controllers\Admin\SlideAdminController;
use App\Http\Controllers\Admin\ImageConfigController;
use App\Http\Controllers\Admin\GalleryController as AdminGalleryController;
use App\Http\Controllers\Admin\IdiomaController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\UserController;

// Página de contacto
Route::get('/contacto', [\App\Http\Controllers\ContactoController::class, 'form'])->name('contacto.form');
Route::post('/contacto', [\App\Http\Controllers\ContactoController::class, 'enviar'])->name('contacto.enviar');

// Restringir el parámetro {idioma} a tres letras
// Se limita a 3 ya que catalan es cat
Route::pattern('idioma', '[a-zA-Z]{3}');

// Ruta principal
Route::get('/', [WebController::class, 'index'])->name('principal');
Route::get('/inicio', function () { return redirect('/'); })->name('inicio.redirect');

// Debug / utilidades
Route::get('api/matrix-data', function () {
    $roles = App\Models\Role::with('permissions')->orderBy('nombre')->get();
    $permissions = App\Models\Permission::where('activo', true)->orderBy('modulo')->orderBy('tipo_permiso')->get();

    $matrix = [];
    foreach ($roles as $role) {
        $matrix[$role->id] = $role->permissions->pluck('id')->toArray();
    }

    return response()->json([
        'roles' => $roles->map(fn($role) => [
            'id' => $role->id,
            'nombre' => $role->nombre,
            'permissions_count' => $role->permissions->count(),
            'permissions' => $role->permissions->pluck('id')->toArray(),
        ]),
        'permissions' => $permissions->map(fn($permission) => [
            'id' => $permission->id,
            'nombre' => $permission->nombre,
            'modulo' => $permission->modulo,
            'activo' => $permission->activo,
        ]),
        'matrix' => $matrix,
        'raw_relations' => DB::table('role_permissions')
            ->join('roles', 'roles.id', '=', 'role_permissions.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
            ->select('roles.nombre as role_name', 'roles.id as role_id', 'permissions.nombre as perm_name', 'permissions.id as perm_id')
            ->get(),
    ]);
});

Route::get('test-matrix', fn() => view('admin.clean-matrix'));
Route::get('matrix-test', fn() => view('admin.matrix-test'));
Route::get('matrix-original', function () {
    $roles = App\Models\Role::with('permissions')->orderBy('nombre')->get();
    $permissions = App\Models\Permission::where('activo', true)->orderBy('modulo')->orderBy('tipo_permiso')->get();
    $permissionsByModule = $permissions->groupBy('modulo');

    $matrix = [];
    foreach ($roles as $role) {
        $matrix[$role->id] = $role->permissions->pluck('id')->toArray();
    }

    return view('admin.roles.permission-matrix', compact('roles', 'permissions', 'permissionsByModule', 'matrix'));
});

Route::get('matrix-debug', fn() => view('admin.matrix-debug'));
Route::get('matrix-clean', fn() => view('admin.matrix-clean'));

Route::post('test-matrix-save', function (Request $request) {
    try {
        $matrix = $request->json('matrix') ?? $request->input('matrix');
        if (!$matrix || !is_array($matrix)) {
            return response()->json(['success' => false, 'message' => 'Datos de matriz inválidos.'], 400);
        }

        DB::transaction(function () use ($matrix) {
            foreach ($matrix as $roleId => $permissionIds) {
                $role = App\Models\Role::findOrFail($roleId);
                $role->permissions()->sync($permissionIds);
            }
        });

        return response()->json(['success' => true, 'message' => 'Matriz de permisos actualizada correctamente (TEST MODE).']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
    }
});

Route::get('test-save-permissions/{roleId}/{permissionIds}', function ($roleId, $permissionIds) {
    try {
        $permissionArray = array_filter(array_map('intval', explode(',', $permissionIds)));
        $role = App\Models\Role::findOrFail($roleId);
        $role->permissions()->sync($permissionArray);
        return response()->json(['success' => true, 'message' => "Rol {$role->nombre} actualizado con permisos: " . implode(',', $permissionArray)]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
    }
});

// Gestión de idiomas (pública)
Route::get('/idioma/{idioma}', [WebController::class, 'cambiarIdioma'])->name('cambiar-idioma');

// Rutas públicas con prefijo de idioma
Route::middleware(['locale'])->group(function () {
    Route::get('/{idioma}', [WebController::class, 'inicio'])->name('inicio');
    Route::get('/{idioma}/noticias', [WebController::class, 'noticias'])->name('noticias');
    Route::get('/{idioma}/galerias', [GalleryController::class, 'index'])->name('galleries.index');
    Route::get('/{idioma}/galerias/{slug}', [GalleryController::class, 'show'])->where(['slug' => '[a-zA-Z0-9\\-_]+'])->name('galleries.show');
    Route::get('/{idioma}/{slug}', [WebController::class, 'contenido'])->where(['slug' => '[a-zA-Z0-9\\-_]+'])->name('contenido');
});

// Ruta global de login (requerida por Laravel)
Route::get('/login', function () { return redirect()->route('admin.login'); })->name('login');

// Ruta para crear admin de prueba (temporal)
Route::get('/create-admin', [AdminController::class, 'createTestAdmin'])->name('create.admin');

Route::get('test/menus-contents-by-type', [MenuAdminController::class, 'getContentsByType'])->name('test.menus.get-contents-by-type');
Route::get('test-ajax-real', [MenuAdminController::class, 'getContentsByType'])->name('test.ajax.real');
Route::get('test/menu-create', fn() => view('admin.menus.create-test'))->name('test.menu.create');

// Rutas protegidas (área administrativa)
Route::middleware(['auth'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('configuracion-empresa', [\App\Http\Controllers\Admin\ConfiguracionEmpresaController::class, 'edit'])->name('configuracion_empresa.edit');
        Route::put('configuracion-empresa', [\App\Http\Controllers\Admin\ConfiguracionEmpresaController::class, 'update'])->name('configuracion_empresa.update');

        Route::get('/', [AdminController::class, 'index'])->name('dashboard');

        Route::get('/login', [AdminController::class, 'showLogin'])->withoutMiddleware('auth')->name('login');
        Route::post('/login', [AdminController::class, 'login'])->withoutMiddleware('auth')->name('authenticate');
        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

        Route::resource('idiomas', IdiomaController::class)->parameters(['idiomas' => 'idioma_id']);
        Route::post('idiomas/update-order', [IdiomaController::class, 'updateOrder'])->name('idiomas.update-order');
        Route::post('idiomas/{idioma_id}/toggle-active', [IdiomaController::class, 'toggleActive'])->name('idiomas.toggle-active');

        Route::resource('contents', ContentAdminController::class);
        Route::resource('tipos-contenido', \App\Http\Controllers\Admin\TipoContenidoController::class);
        Route::resource('image-configs', ImageConfigController::class);

        Route::resource('galleries', AdminGalleryController::class);
        Route::post('galleries/{gallery}/images', [AdminGalleryController::class, 'uploadImages'])->name('galleries.images.upload');
        Route::post('galleries/{gallery}/update-order', [AdminGalleryController::class, 'updateImageOrder'])->name('galleries.images.update-order');
        Route::delete('galleries/{gallery}/images/{image}', [AdminGalleryController::class, 'deleteImage'])->name('galleries.images.delete');
        Route::get('gallery-images/{image}/texts', [AdminGalleryController::class, 'getImageTexts'])->name('gallery-images.texts.show');
        Route::post('gallery-images/{image}/texts', [AdminGalleryController::class, 'saveImageTexts'])->name('gallery-images.texts.save');

        Route::resource('menus', MenuAdminController::class);
        Route::get('menus-contents-by-type', [MenuAdminController::class, 'getContentsByType'])->name('menus.get-contents-by-type');
        Route::get('menus-test-ajax', [MenuAdminController::class, 'testAjax'])->name('menus.test-ajax');
        Route::post('menus/update-order', [MenuAdminController::class, 'updateOrder'])->name('menus.update-order');

        Route::resource('slides', SlideAdminController::class);
        Route::post('slides/update-order', [SlideAdminController::class, 'updateOrder'])->name('slides.update-order');

        Route::resource('roles', RoleController::class);
        Route::post('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.update-permissions');
        Route::get('roles/permission-matrix/view', [RoleController::class, 'permissionMatrix'])->name('roles.permission-matrix');
        Route::post('roles/permission-matrix/update', [RoleController::class, 'updatePermissionMatrix'])->name('roles.permission-matrix.update');

        Route::get('debug-session', fn() => view('admin.debug-session'))->name('debug-session');
        Route::get('simple-matrix', fn() => view('admin.simple-matrix'))->name('simple-matrix');
        Route::get('clean-matrix', fn() => view('admin.clean-matrix'))->name('clean-matrix');

        Route::resource('permissions', PermissionController::class);
        Route::resource('users', UserController::class);

        Route::get('debug-me', function () {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['error' => 'No autenticado'], 401);
            }

            $role = $user->role ? $user->role->only(['id', 'nombre', 'slug']) : null;
            $permissions = $user->role ? $user->role->permissions->map(fn ($p) => $p->only(['id', 'modulo', 'tipo_permiso', 'slug', 'nombre'])) : collect();

            return response()->json([
                'user' => $user->only(['id', 'name', 'email', 'role_id']),
                'role' => $role,
                'permissions' => $permissions,
            ]);
        })->name('debug.me');
    });
});

Route::get('/debug-textos', function () {
    return \App\Models\TextoIdioma::select('id', 'slug', 'visible', 'contenido_id')->get();
});
