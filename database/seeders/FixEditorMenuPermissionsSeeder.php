<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class FixEditorMenuPermissionsSeeder extends Seeder
{
    /**
     * Ajusta los permisos de menús para el rol editor.
     */
    public function run(): void
    {
        $editorRole = Role::where('slug', 'editor')->first();

        if (!$editorRole) {
            $this->command?->warn('Rol "editor" no encontrado. Ningún permiso actualizado.');
            return;
        }

        $permisosMenusEditar = Permission::where('modulo', 'menus')
            ->whereIn('tipo_permiso', ['mostrar', 'editar'])
            ->pluck('id')
            ->all();

        if (empty($permisosMenusEditar)) {
            $this->command?->warn('No se encontraron permisos de visualización/edición para el módulo "menus".');
            return;
        }

        $permisosMenusNoPermitidos = Permission::where('modulo', 'menus')
            ->whereIn('tipo_permiso', ['crear', 'eliminar'])
            ->pluck('id')
            ->all();

        if (!empty($permisosMenusNoPermitidos)) {
            $editorRole->permissions()->detach($permisosMenusNoPermitidos);
        }

        $editorRole->permissions()->syncWithoutDetaching($permisosMenusEditar);

        $this->command?->info(
            sprintf(
                'Permisos de menús ajustados para el rol "%s". Otorgados: %d, Revocados: %d.',
                $editorRole->nombre,
                count($permisosMenusEditar),
                count($permisosMenusNoPermitidos)
            )
        );
    }
}
