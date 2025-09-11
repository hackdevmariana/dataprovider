<?php

namespace Database\Seeders;

use App\Models\NotificationSetting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener algunos usuarios existentes para asignar notificaciones
        $users = User::limit(50)->get();
        
        if ($users->isEmpty()) {
            $this->command->warn('No hay usuarios disponibles. Creando usuarios de ejemplo...');
            $users = User::factory()->count(10)->create();
        }

        // Crear configuraciones de notificación específicas y realistas
        $notificationSettings = [];

        foreach ($users as $user) {
            // Configuración de alertas de precio de electricidad
            $notificationSettings[] = [
                'user_id' => $user->id,
                'type' => 'electricity_price',
                'target_id' => rand(1, 50), // ID de municipio
                'threshold' => round(rand(10, 40) / 100, 4), // Entre 0.10 y 0.40 €/kWh
                'delivery_method' => fake()->randomElement(['app', 'email']),
                'is_silent' => fake()->boolean(15), // 15% silencioso
                'active' => fake()->boolean(90), // 90% activo
            ];

            // Configuración de alertas de eventos
            $notificationSettings[] = [
                'user_id' => $user->id,
                'type' => 'event',
                'target_id' => rand(1, 100), // ID de evento
                'threshold' => null,
                'delivery_method' => fake()->randomElement(['app', 'email', 'sms']),
                'is_silent' => fake()->boolean(10), // 10% silencioso
                'active' => fake()->boolean(85), // 85% activo
            ];

            // Configuración de alertas de producción solar (solo para algunos usuarios)
            if (fake()->boolean(60)) { // 60% de usuarios tienen alertas solares
                $notificationSettings[] = [
                    'user_id' => $user->id,
                    'type' => 'solar_production',
                    'target_id' => rand(1, 30), // ID de instalación solar
                    'threshold' => round(rand(500, 3000), 4), // Entre 500 y 3000 kWh
                    'delivery_method' => fake()->randomElement(['app', 'email']),
                    'is_silent' => fake()->boolean(20), // 20% silencioso
                    'active' => fake()->boolean(80), // 80% activo
                ];
            }
        }

        // Insertar las configuraciones de notificación
        foreach ($notificationSettings as $setting) {
            NotificationSetting::create($setting);
        }

        // Crear algunas configuraciones adicionales usando el factory
        NotificationSetting::factory()
            ->count(20)
            ->create();

        $this->command->info('Configuraciones de notificación creadas exitosamente.');
    }
}