<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/**
 * Seeder para crear usuarios con diferentes roles y perfiles.
 */
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuarios específicos con roles definidos
        $this->createSpecificUsers();
        
        // Crear usuarios adicionales usando factory
        $this->createFactoryUsers();
        
        echo "✅ Creados " . User::count() . " usuarios\n";
    }

    /**
     * Crear usuarios específicos con roles y perfiles definidos.
     */
    private function createSpecificUsers(): void
    {
        $users = [
            [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'roles' => ['user'],
            ],
            [
                'name' => 'Administrador',
                'email' => 'admin@dataprovider.com',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
                'roles' => ['admin', 'super_admin'],
            ],
            [
                'name' => 'Usuario Seguido',
                'email' => 'usuario@dataprovider.com',
                'password' => Hash::make('user123'),
                'email_verified_at' => now(),
                'roles' => ['user'],
            ],
            [
                'name' => 'María García',
                'email' => 'maria.garcia@email.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'roles' => ['user'],
            ],
            [
                'name' => 'Carlos Rodríguez',
                'email' => 'carlos.rodriguez@email.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'roles' => ['user'],
            ],
            [
                'name' => 'Ana Martínez',
                'email' => 'ana.martinez@email.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'roles' => ['user'],
            ],
            [
                'name' => 'Luis Fernández',
                'email' => 'luis.fernandez@email.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'roles' => ['user'],
            ],
            [
                'name' => 'Carmen López',
                'email' => 'carmen.lopez@email.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'roles' => ['user'],
            ],
            [
                'name' => 'Javier Pérez',
                'email' => 'javier.perez@email.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'roles' => ['user'],
            ],
            [
                'name' => 'Isabel Sánchez',
                'email' => 'isabel.sanchez@email.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'roles' => ['user'],
            ],
            [
                'name' => 'Roberto Jiménez',
                'email' => 'roberto.jimenez@email.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'roles' => ['user'],
            ],
            [
                'name' => 'Laura Torres',
                'email' => 'laura.torres@email.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'roles' => ['user'],
            ],
            [
                'name' => 'Miguel Ruiz',
                'email' => 'miguel.ruiz@email.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'roles' => ['user'],
            ],
            [
                'name' => 'Elena Moreno',
                'email' => 'elena.moreno@email.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'roles' => ['user'],
            ],
            [
                'name' => 'David Alonso',
                'email' => 'david.alonso@email.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'roles' => ['user'],
            ],
            [
                'name' => 'Sofía Gutiérrez',
                'email' => 'sofia.gutierrez@email.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'roles' => ['user'],
            ],
            [
                'name' => 'Pablo Morales',
                'email' => 'pablo.morales@email.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'roles' => ['user'],
            ],
            [
                'name' => 'Natalia Castro',
                'email' => 'natalia.castro@email.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'roles' => ['user'],
            ],
            [
                'name' => 'Diego Vega',
                'email' => 'diego.vega@email.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'roles' => ['user'],
            ],
            [
                'name' => 'Claudia Herrera',
                'email' => 'claudia.herrera@email.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'roles' => ['user'],
            ],
            [
                'name' => 'Moderador Principal',
                'email' => 'moderador@dataprovider.com',
                'password' => Hash::make('mod123'),
                'email_verified_at' => now(),
                'roles' => ['moderator', 'user'],
            ],
            [
                'name' => 'Editor de Contenido',
                'email' => 'editor@dataprovider.com',
                'password' => Hash::make('edit123'),
                'email_verified_at' => now(),
                'roles' => ['editor', 'user'],
            ],
            [
                'name' => 'Analista de Datos',
                'email' => 'analista@dataprovider.com',
                'password' => Hash::make('ana123'),
                'email_verified_at' => now(),
                'roles' => ['analyst', 'user'],
            ],
            [
                'name' => 'Experto en Sostenibilidad',
                'email' => 'experto@dataprovider.com',
                'password' => Hash::make('exp123'),
                'email_verified_at' => now(),
                'roles' => ['expert', 'user'],
            ],
            [
                'name' => 'Periodista Ambiental',
                'email' => 'periodista@dataprovider.com',
                'password' => Hash::make('per123'),
                'email_verified_at' => now(),
                'roles' => ['journalist', 'user'],
            ],
        ];

        foreach ($users as $userData) {
            $roles = $userData['roles'];
            unset($userData['roles']);
            
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
            
            // Asignar roles
            foreach ($roles as $roleName) {
                $role = Role::firstOrCreate(['name' => $roleName]);
                $user->assignRole($role);
            }
            
            echo "✅ Usuario creado: {$user->name} ({$user->email})\n";
        }
    }

    /**
     * Crear usuarios adicionales usando factory.
     */
    private function createFactoryUsers(): void
    {
        // Crear usuarios adicionales para tener más variedad
        $additionalUsers = User::factory(50)->create([
            'email_verified_at' => now(),
        ]);
        
        // Asignar rol de usuario a todos los usuarios de factory
        $userRole = Role::firstOrCreate(['name' => 'user']);
        foreach ($additionalUsers as $user) {
            $user->assignRole($userRole);
        }
        
        echo "✅ Creados 50 usuarios adicionales con factory\n";
    }
}
