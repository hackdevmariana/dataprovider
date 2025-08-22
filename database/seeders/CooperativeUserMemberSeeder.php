<?php

namespace Database\Seeders;

use App\Models\Cooperative;
use App\Models\CooperativeUserMember;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CooperativeUserMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener cooperativas y usuarios disponibles
        $cooperatives = Cooperative::all();
        $users = User::all();
        
        if ($cooperatives->isEmpty()) {
            $this->command->warn('No hay cooperativas en la base de datos. No se pueden crear membresías.');
            return;
        }
        
        if ($users->isEmpty()) {
            $this->command->warn('No hay usuarios en la base de datos. No se pueden crear membresías.');
            return;
        }

        // Roles disponibles para miembros de cooperativas
        $roles = [
            'miembro' => 'Miembro activo de la cooperativa',
            'gestor' => 'Gestor o administrador',
            'representante_legal' => 'Representante legal',
            'tesorero' => 'Responsable de finanzas',
            'secretario' => 'Secretario de la cooperativa',
            'vocal' => 'Vocal del consejo',
            'presidente' => 'Presidente de la cooperativa',
            'vicepresidente' => 'Vicepresidente',
            'supervisor' => 'Supervisor de proyectos',
            'coordinador' => 'Coordinador de actividades'
        ];

        // Datos de membresías de ejemplo para cooperativas reales
        $sampleMemberships = [
            // Cooperativas energéticas
            [
                'cooperative_name' => 'Cooperativa Energética Verde',
                'members' => [
                    ['role' => 'presidente', 'joined_at' => '2020-03-15', 'is_active' => true],
                    ['role' => 'gestor', 'joined_at' => '2020-04-20', 'is_active' => true],
                    ['role' => 'tesorero', 'joined_at' => '2020-05-10', 'is_active' => true],
                    ['role' => 'miembro', 'joined_at' => '2020-06-01', 'is_active' => true],
                    ['role' => 'miembro', 'joined_at' => '2020-07-15', 'is_active' => true],
                ]
            ],
            [
                'cooperative_name' => 'Cooperativa Solar Comunitaria',
                'members' => [
                    ['role' => 'presidente', 'joined_at' => '2019-08-12', 'is_active' => true],
                    ['role' => 'representante_legal', 'joined_at' => '2019-09-01', 'is_active' => true],
                    ['role' => 'gestor', 'joined_at' => '2019-10-15', 'is_active' => true],
                    ['role' => 'miembro', 'joined_at' => '2019-11-20', 'is_active' => true],
                    ['role' => 'miembro', 'joined_at' => '2020-01-10', 'is_active' => false], // Miembro inactivo
                ]
            ],
            [
                'cooperative_name' => 'Cooperativa Eólica Local',
                'members' => [
                    ['role' => 'presidente', 'joined_at' => '2021-02-28', 'is_active' => true],
                    ['role' => 'vicepresidente', 'joined_at' => '2021-03-15', 'is_active' => true],
                    ['role' => 'secretario', 'joined_at' => '2021-04-01', 'is_active' => true],
                    ['role' => 'miembro', 'joined_at' => '2021-05-20', 'is_active' => true],
                ]
            ],
            [
                'cooperative_name' => 'Cooperativa de Biomasa',
                'members' => [
                    ['role' => 'presidente', 'joined_at' => '2018-06-10', 'is_active' => true],
                    ['role' => 'gestor', 'joined_at' => '2018-07-01', 'is_active' => true],
                    ['role' => 'coordinador', 'joined_at' => '2018-08-15', 'is_active' => true],
                    ['role' => 'miembro', 'joined_at' => '2018-09-20', 'is_active' => true],
                    ['role' => 'miembro', 'joined_at' => '2018-10-05', 'is_active' => false], // Miembro inactivo
                ]
            ],
            [
                'cooperative_name' => 'Cooperativa Hidroeléctrica',
                'members' => [
                    ['role' => 'presidente', 'joined_at' => '2022-01-15', 'is_active' => true],
                    ['role' => 'representante_legal', 'joined_at' => '2022-02-01', 'is_active' => true],
                    ['role' => 'gestor', 'joined_at' => '2022-03-10', 'is_active' => true],
                    ['role' => 'supervisor', 'joined_at' => '2022-04-05', 'is_active' => true],
                ]
            ],
        ];

        $createdMemberships = [];
        $membershipCount = 0;

        // Crear membresías de ejemplo para cooperativas específicas
        foreach ($sampleMemberships as $coopData) {
            $cooperative = $cooperatives->where('name', 'LIKE', '%' . $coopData['cooperative_name'] . '%')->first();
            
            if (!$cooperative) {
                // Si no encuentra la cooperativa exacta, usar una aleatoria
                $cooperative = $cooperatives->random();
            }
            
            foreach ($coopData['members'] as $memberData) {
                // Seleccionar un usuario aleatorio que no esté ya en esta cooperativa
                $availableUsers = $users->filter(function($user) use ($cooperative) {
                    return !CooperativeUserMember::where('cooperative_id', $cooperative->id)
                        ->where('user_id', $user->id)
                        ->exists();
                });
                
                if ($availableUsers->isEmpty()) {
                    continue; // No hay usuarios disponibles para esta cooperativa
                }
                
                $user = $availableUsers->random();
                
                $membership = CooperativeUserMember::create([
                    'cooperative_id' => $cooperative->id,
                    'user_id' => $user->id,
                    'role' => $memberData['role'],
                    'joined_at' => $memberData['joined_at'],
                    'is_active' => $memberData['is_active'],
                ]);
                
                $createdMemberships[] = [
                    'id' => $membership->id,
                    'cooperative' => $cooperative->name,
                    'user' => $user->name ?? $user->email,
                    'role' => ucfirst(str_replace('_', ' ', $memberData['role'])),
                    'joined_at' => $memberData['joined_at'],
                    'status' => $memberData['is_active'] ? '✅ Activo' : '❌ Inactivo',
                ];
                
                $membershipCount++;
            }
        }

        // Crear membresías aleatorias para las cooperativas restantes
        $usedCooperativeIds = collect($createdMemberships)->pluck('cooperative_id')->unique();
        $remainingCooperatives = $cooperatives->whereNotIn('id', $usedCooperativeIds);
        
        foreach ($remainingCooperatives as $cooperative) {
            // Determinar cuántos miembros tendrá esta cooperativa (entre 3 y 8)
            $memberCount = rand(3, 8);
            
            for ($i = 0; $i < $memberCount; $i++) {
                // Seleccionar un usuario aleatorio que no esté ya en esta cooperativa
                $availableUsers = $users->filter(function($user) use ($cooperative) {
                    return !CooperativeUserMember::where('cooperative_id', $cooperative->id)
                        ->where('user_id', $user->id)
                        ->exists();
                });
                
                if ($availableUsers->isEmpty()) {
                    break; // No hay usuarios disponibles para esta cooperativa
                }
                
                $user = $availableUsers->random();
                $role = array_rand($roles);
                $joinedAt = $this->generateRandomJoinDate();
                $isActive = rand(1, 10) <= 8; // 80% probabilidad de estar activo
                
                $membership = CooperativeUserMember::create([
                    'cooperative_id' => $cooperative->id,
                    'user_id' => $user->id,
                    'role' => $role,
                    'joined_at' => $joinedAt,
                    'is_active' => $isActive,
                ]);
                
                $createdMemberships[] = [
                    'id' => $membership->id,
                    'cooperative' => $cooperative->name,
                    'user' => $user->name ?? $user->email,
                    'role' => ucfirst(str_replace('_', ' ', $role)),
                    'joined_at' => $joinedAt,
                    'status' => $isActive ? '✅ Activo' : '❌ Inactivo',
                ];
                
                $membershipCount++;
            }
        }

        $this->command->info("Se han creado {$membershipCount} membresías de cooperativas.");
        
        // Mostrar tabla con las membresías creadas
        $displayData = array_slice($createdMemberships, 0, 20); // Mostrar solo los primeros 20
        $this->command->table(
            ['ID', 'Cooperativa', 'Usuario', 'Rol', 'Fecha Ingreso', 'Estado'],
            $displayData
        );
        
        if (count($createdMemberships) > 20) {
            $this->command->info("... y " . (count($createdMemberships) - 20) . " membresías más.");
        }

        // Estadísticas
        $totalMemberships = CooperativeUserMember::count();
        $activeMemberships = CooperativeUserMember::where('is_active', true)->count();
        $inactiveMemberships = CooperativeUserMember::where('is_active', false)->count();
        
        // Estadísticas por rol
        $roleStats = CooperativeUserMember::selectRaw('role, COUNT(*) as count')
            ->groupBy('role')
            ->pluck('count', 'role')
            ->toArray();
        
        // Estadísticas por cooperativa
        $cooperativeStats = CooperativeUserMember::selectRaw('cooperatives.name, COUNT(*) as member_count')
            ->join('cooperatives', 'cooperative_user_members.cooperative_id', '=', 'cooperatives.id')
            ->groupBy('cooperatives.id', 'cooperatives.name')
            ->pluck('member_count', 'name')
            ->toArray();
        
        $this->command->newLine();
        $this->command->info("📊 Estadísticas:");
        $this->command->info("   • Total de membresías: {$totalMemberships}");
        $this->command->info("   • Membresías activas: {$activeMemberships}");
        $this->command->info("   • Membresías inactivas: {$inactiveMemberships}");
        
        $this->command->newLine();
        $this->command->info("👥 Por rol:");
        foreach ($roleStats as $role => $count) {
            $roleLabel = ucfirst(str_replace('_', ' ', $role));
            $this->command->info("   • {$roleLabel}: {$count}");
        }
        
        $this->command->newLine();
        $this->command->info("🏢 Por cooperativa:");
        foreach ($cooperativeStats as $coopName => $memberCount) {
            $this->command->info("   • {$coopName}: {$memberCount} miembros");
        }
        
        $this->command->newLine();
        $this->command->info("✅ Seeder de CooperativeUserMember completado exitosamente.");
    }

    /**
     * Generar una fecha de ingreso aleatoria realista
     */
    private function generateRandomJoinDate(): string
    {
        // Fechas entre 2015 y 2024
        $startDate = Carbon::create(2015, 1, 1);
        $endDate = Carbon::now();
        
        $randomDate = Carbon::createFromTimestamp(rand($startDate->timestamp, $endDate->timestamp));
        
        return $randomDate->format('Y-m-d');
    }
}
