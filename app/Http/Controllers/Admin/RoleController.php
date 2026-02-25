<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:roles,mostrar')->only(['index', 'show', 'permissionMatrix']);
        $this->middleware('permission:roles,crear')->only(['create', 'store']);
        $this->middleware('permission:roles,editar')->only(['edit', 'update', 'updatePermissions', 'updatePermissionMatrix']);
        $this->middleware('permission:roles,eliminar')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::with(['users', 'permissions'])
            ->withCount(['users', 'permissions'])
            ->orderBy('nombre')
            ->get();

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::activos()
            ->orderBy('modulo')
            ->orderBy('tipo_permiso')
            ->get();

        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:roles',
            'slug' => 'nullable|string|max:255|unique:roles',
            'descripcion' => 'nullable|string|max:1000',
            'activo' => 'boolean',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        // Generar slug si no se proporciona
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['nombre']);
        }

        $validated['activo'] = $request->has('activo');

        $role = Role::create($validated);

        // Asignar permisos
        if (!empty($validated['permissions'])) {
            $role->permissions()->attach($validated['permissions']);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', 'Rol creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $role->load(['users', 'permissions']);
        
        $permissionsByModule = $role->permissions
            ->groupBy('modulo')
            ->map(function ($permissions) {
                return $permissions->groupBy('tipo_permiso');
            });

        return view('admin.roles.show', compact('role', 'permissionsByModule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $role->load('permissions');
        
        $permissions = Permission::activos()
            ->orderBy('modulo')
            ->orderBy('tipo_permiso')
            ->get();

        $selectedPermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'selectedPermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'descripcion' => 'nullable|string|max:1000',
            'activo' => 'boolean',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        // Generar slug si no se proporciona
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['nombre']);
        }

        $validated['activo'] = $request->has('activo');

        $role->update($validated);

        // Sincronizar permisos
        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Rol actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // Verificar si el rol tiene usuarios asignados
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'No se puede eliminar el rol porque tiene usuarios asignados.');
        }

        // Verificar si es un rol del sistema
        if (in_array($role->slug, ['administrador', 'usuario', 'editor'])) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'No se puede eliminar un rol del sistema.');
        }

        $role->permissions()->detach();
        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Rol eliminado exitosamente.');
    }

    /**
     * Actualizar permisos de un rol via AJAX
     */
    public function updatePermissions(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);

        return response()->json([
            'success' => true,
            'message' => 'Permisos actualizados correctamente.',
            'permissions_count' => count($validated['permissions'] ?? [])
        ]);
    }

    /**
     * Show permission matrix for all roles
     */
    public function permissionMatrix()
    {
        $roles = Role::with('permissions')->orderBy('nombre')->get();
        $permissions = Permission::activos()
            ->orderBy('modulo')
            ->orderBy('tipo_permiso')
            ->get();
        
        $permissionsByModule = $permissions->groupBy('modulo');
        
        // Crear matriz de roles x permisos
        $matrix = [];
        foreach ($roles as $role) {
            $rolePermissions = $role->permissions->pluck('id')->toArray();
            $matrix[$role->id] = $rolePermissions;
        }

        return view('admin.roles.permission-matrix', compact('roles', 'permissions', 'permissionsByModule', 'matrix'));
    }

    /**
     * Update permission matrix for all roles
     */
    public function updatePermissionMatrix(Request $request)
    {
        try {
            // Obtener datos de la matriz - puede venir como JSON directo o en input
            $matrix = $request->json('matrix') ?? $request->input('matrix');
            
            if (!$matrix || !is_array($matrix)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de matriz inválidos.'
                ], 400);
            }

            \DB::transaction(function() use ($matrix) {
                foreach ($matrix as $roleId => $permissionIds) {
                    $role = Role::findOrFail($roleId);
                    $role->permissions()->sync($permissionIds);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Matriz de permisos actualizada correctamente.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en updatePermissionMatrix:', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la matriz de permisos: ' . $e->getMessage()
            ], 500);
        }
    }
}
