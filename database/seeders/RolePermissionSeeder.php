<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear roles básicos
        $adminRole = Role::updateOrCreate(
            ['slug' => 'administrador'],
            [
                'nombre' => 'Administrador',
                'descripcion' => 'Usuario con acceso completo al sistema',
                'activo' => true,
            ]
        );

        $userRole = Role::updateOrCreate(
            ['slug' => 'usuario'],
            [
                'nombre' => 'Usuario',
                'descripcion' => 'Usuario con permisos limitados',
                'activo' => true,
            ]
        );

        $editorRole = Role::updateOrCreate(
            ['slug' => 'editor'],
            [
                'nombre' => 'Editor',
                'descripcion' => 'Usuario que puede gestionar contenido',
                'activo' => true,
            ]
        );

        // Crear permisos básicos
        $modulos = Permission::MODULOS;
        $tiposPermiso = Permission::TIPOS_PERMISO;

        $permisos = [];

        foreach ($modulos as $moduloKey => $moduloNombre) {
            foreach ($tiposPermiso as $tipo) {
                $permiso = Permission::updateOrCreate(
                    [
                        'modulo' => $moduloKey,
                        'tipo_permiso' => $tipo,
                        'slug' => $tipo . '-' . $moduloKey
                    ],
                    [
                        'nombre' => ucfirst($tipo) . ' ' . $moduloNombre,
                        'descripcion' => "Permiso para {$tipo} {$moduloNombre}",
                        'activo' => true,
                    ]
                );
                $permisos[] = $permiso;
            }
        }

        // Asignar TODOS los permisos al Administrador (limpiar y reasignar)
        $adminRole->permissions()->detach();
        $adminRole->permissions()->attach($permisos);

        // Asignar permisos limitados al Editor
        $permisosEditorGenerales = Permission::whereIn('modulo', [
            'contenidos', 'noticias', 'eventos', 'galerias', 'slides'
        ])->whereIn('tipo_permiso', ['crear', 'mostrar', 'editar', 'eliminar'])->get();

        $permisosEditorMenus = Permission::where('modulo', 'menus')
            ->whereIn('tipo_permiso', ['mostrar', 'editar'])
            ->get();

        $editorRole->permissions()->detach();
        $editorRole->permissions()->attach($permisosEditorGenerales->pluck('id')->merge($permisosEditorMenus->pluck('id')));

        // Asignar permisos muy limitados al Usuario
        $permisosUsuario = Permission::whereIn('modulo', [
            'contenidos', 'noticias', 'eventos', 'galerias'
        ])->whereIn('tipo_permiso', ['mostrar'])->get();
        
        $userRole->permissions()->detach();
        $userRole->permissions()->attach($permisosUsuario);

        // Crear usuario administrador por defecto (si no existe)
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@nuntristeatro.com'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('password123'),
                'role_id' => $adminRole->id,
                'email_verified_at' => now(),
            ]
        );

        // Crear usuario editor de ejemplo
        $editorUser = User::updateOrCreate(
            ['email' => 'editor@nuntristeatro.com'],
            [
                'name' => 'Editor',
                'password' => bcrypt('password123'),
                'role_id' => $editorRole->id,
                'email_verified_at' => now(),
            ]
        );

        // Crear usuario normal de ejemplo
        $normalUser = User::updateOrCreate(
            ['email' => 'usuario@nuntristeatro.com'],
            [
                'name' => 'Usuario Normal',
                'password' => bcrypt('password123'),
                'role_id' => $userRole->id,
                'email_verified_at' => now(),
            ]
        );

        // Asignar rol de administrador al usuario existente de admin@admin.com
        $existingAdmin = User::where('email', 'admin@admin.com')->first();
        if ($existingAdmin) {
            $existingAdmin->update(['role_id' => $adminRole->id]);
        }

        $this->command->info('Roles, permisos y usuarios de ejemplo creados exitosamente:');
        $this->command->info("- Administrador: admin@nuntristeatro.com / password123");
        $this->command->info("- Editor: editor@nuntristeatro.com / password123");
        $this->command->info("- Usuario: usuario@nuntristeatro.com / password123");
    }
}
