<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:permisos,mostrar')->only(['index', 'show']);
        $this->middleware('permission:permisos,crear')->only(['create', 'store']);
        $this->middleware('permission:permisos,editar')->only(['edit', 'update']);
        $this->middleware('permission:permisos,eliminar')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = Permission::with(['roles'])
            ->withCount('roles')
            ->orderBy('modulo')
            ->orderBy('tipo_permiso')
            ->orderBy('nombre')
            ->get();

        $permissionsByModule = $permissions->groupBy('modulo');

        return view('admin.permissions.index', compact('permissions', 'permissionsByModule'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $modulos = Permission::getModulosOptions();
        $tiposPermiso = Permission::getTiposPermisoOptions();

        return view('admin.permissions.create', compact('modulos', 'tiposPermiso'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:permissions',
            'modulo' => 'required|string|max:100',
            'tipo_permiso' => ['required', Rule::in(Permission::TIPOS_PERMISO)],
            'descripcion' => 'nullable|string|max:1000',
            'activo' => 'boolean',
        ]);

        // Verificar que no exista ya esta combinación módulo-tipo
        $exists = Permission::where('modulo', $validated['modulo'])
            ->where('tipo_permiso', $validated['tipo_permiso'])
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ya existe un permiso de "' . $validated['tipo_permiso'] . '" para el módulo "' . $validated['modulo'] . '".');
        }

        // Generar slug si no se proporciona
        if (empty($validated['slug'])) {
            $validated['slug'] = $validated['tipo_permiso'] . '-' . Str::slug($validated['modulo']);
        }

        $validated['activo'] = $request->has('activo');

        Permission::create($validated);

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permiso creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        $permission->load('roles');

        return view('admin.permissions.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        $modulos = Permission::getModulosOptions();
        $tiposPermiso = Permission::getTiposPermisoOptions();

        return view('admin.permissions.edit', compact('permission', 'modulos', 'tiposPermiso'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('permissions')->ignore($permission->id)],
            'modulo' => 'required|string|max:100',
            'tipo_permiso' => ['required', Rule::in(Permission::TIPOS_PERMISO)],
            'descripcion' => 'nullable|string|max:1000',
            'activo' => 'boolean',
        ]);

        // Verificar que no exista ya esta combinación módulo-tipo (excluyendo el actual)
        $exists = Permission::where('modulo', $validated['modulo'])
            ->where('tipo_permiso', $validated['tipo_permiso'])
            ->where('id', '!=', $permission->id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ya existe un permiso de "' . $validated['tipo_permiso'] . '" para el módulo "' . $validated['modulo'] . '".');
        }

        // Generar slug si no se proporciona
        if (empty($validated['slug'])) {
            $validated['slug'] = $validated['tipo_permiso'] . '-' . Str::slug($validated['modulo']);
        }

        $validated['activo'] = $request->has('activo');

        $permission->update($validated);

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permiso actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        // Verificar si el permiso está asignado a algún rol
        if ($permission->roles()->count() > 0) {
            return redirect()->route('admin.permissions.index')
                ->with('error', 'No se puede eliminar el permiso porque está asignado a uno o más roles.');
        }

        $permission->delete();

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permiso eliminado exitosamente.');
    }
}
