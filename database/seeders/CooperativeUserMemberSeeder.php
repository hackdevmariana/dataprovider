<?php

namespace Database\Seeders;

use App\Models\CooperativeUserMember;
use App\Models\Cooperative;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CooperativeUserMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener cooperativas y usuarios existentes
        $cooperatives = Cooperative::all();
        $users = User::limit(50)->get();
        
        if ($cooperatives->isEmpty()) {
            $this->command->warn('No hay cooperativas disponibles. Creando cooperativas de ejemplo...');
            $cooperatives = Cooperative::factory()->count(3)->create();
        }
        
        if ($users->isEmpty()) {
            $this->command->warn('No hay usuarios disponibles. Creando usuarios de ejemplo...');
            $users = User::factory()->count(20)->create();
        }

        // Crear membresías específicas y realistas
        $memberships = [];

        // Para cada cooperativa, crear varios miembros con diferentes roles
        foreach ($cooperatives as $cooperative) {
            $numMembers = rand(5, 15); // Entre 5 y 15 miembros por cooperativa
            $selectedUsers = $users->random(min($numMembers, $users->count()));
            
            foreach ($selectedUsers as $index => $user) {
                // Asignar roles según el índice (primeros usuarios tienen roles más importantes)
                $role = match($index) {
                    0 => 'presidente',
                    1 => 'vicepresidente', 
                    2 => 'secretario',
                    3 => 'tesorero',
                    4 => 'gestor',
                    default => fake()->randomElement(['miembro', 'socio', 'consejero', 'delegado'])
                };

                $memberships[] = [
                    'cooperative_id' => $cooperative->id,
                    'user_id' => $user->id,
                    'role' => $role,
                    'joined_at' => fake()->dateTimeBetween('-3 years', 'now'),
                    'is_active' => fake()->boolean(90), // 90% activos
                ];
            }
        }

        // Insertar las membresías
        foreach ($memberships as $membership) {
            CooperativeUserMember::updateOrCreate(
                [
                    'cooperative_id' => $membership['cooperative_id'],
                    'user_id' => $membership['user_id']
                ], // Evitar duplicados usando la clave única
                $membership
            );
        }

        // Crear algunas membresías adicionales usando el factory
        CooperativeUserMember::factory()
            ->count(20)
            ->create();

        $this->command->info('Membresías de cooperativas creadas exitosamente.');
    }
}