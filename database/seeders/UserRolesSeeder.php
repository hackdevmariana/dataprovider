<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asegurar que existan los roles básicos
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $gestorRole = Role::firstOrCreate(['name' => 'gestor']);
        $tecnicoRole = Role::firstOrCreate(['name' => 'tecnico']);
        $usuarioRole = Role::firstOrCreate(['name' => 'usuario']);

        // Asegurar que exista el permiso de acceso a Filament
        $filamentPermission = Permission::firstOrCreate(['name' => 'access filament']);

        // Asignar el permiso de Filament al rol admin
        $adminRole->givePermissionTo($filamentPermission);

        // Asignar roles a usuarios existentes
        $users = User::all();

        foreach ($users as $user) {
            if ($user->email === 'admin@demo.com') {
                $user->assignRole('admin');
                $this->command->info("Usuario {$user->name} asignado al rol 'admin'");
            } elseif ($user->email === 'test@example.com') {
                $user->assignRole('gestor');
                $this->command->info("Usuario {$user->name} asignado al rol 'gestor'");
            } elseif ($user->email === 'seguido@kirolux.com') {
                $user->assignRole('usuario');
                $this->command->info("Usuario {$user->name} asignado al rol 'usuario'");
            }
        }

        // Asignar permisos adicionales a roles
        $gestorRole->givePermissionTo([
            'access filament',
        ]);

        $tecnicoRole->givePermissionTo([
            'access filament',
        ]);

        $this->command->info('Roles y permisos asignados correctamente');
        
        // Mostrar resumen
        $this->command->table(
            ['Usuario', 'Email', 'Roles', 'Permisos'],
            User::with('roles', 'permissions')->get()->map(function ($user) {
                return [
                    $user->name,
                    $user->email,
                    $user->roles->pluck('name')->implode(', '),
                    $user->permissions->pluck('name')->implode(', '),
                ];
            })->toArray()
        );
    }
}
