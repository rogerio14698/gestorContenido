<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            IdiomasSeeder::class,
            TipoContenidoSeeder::class,
            ConfiguracionSeeder::class,
            RolePermissionSeeder::class,
        ]);

        $adminRole = Role::where('slug', 'administrador')->first();

        User::updateOrCreate(
            ['email' => 'admin@nuntristeatro.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password123'),
                'role_id' => $adminRole?->id,
            ]
        );
    }
}
