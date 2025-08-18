<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NotificationSetting;
use App\Models\User;
use App\Models\Cooperative;
use App\Models\EnergyInstallation;

class NotificationSettingSeeder extends Seeder
{
    /**
     * Ejecutar el seeder para configuraciones de notificaciones.
     */
    public function run(): void
    {
        $this->command->info('Creando configuraciones de notificaciones para KiroLux...');

        // Buscar usuarios existentes
        $users = User::all();
        if ($users->isEmpty()) {
            $this->command->warn('No hay usuarios. Creando usuario de ejemplo...');
            $users = collect([
                User::create([
                    'name' => 'Usuario Demo KiroLux',
                    'email' => 'demo@kirolux.com',
                    'password' => bcrypt('password'),
                ])
            ]);
        }

        // Crear configuraciones para cada usuario
        $createdCount = 0;
        foreach ($users as $user) {
            $userNotifications = $this->createNotificationSettingsForUser($user);
            $createdCount += $userNotifications;
        }

        $this->command->info("✅ Creadas {$createdCount} configuraciones de notificaciones");

        // Mostrar estadísticas
        $this->showStatistics();
    }

    /**
     * Crear configuraciones de notificaciones para un usuario específico.
     */
    private function createNotificationSettingsForUser(User $user): int
    {
        // Buscar cooperativas e instalaciones para configuraciones específicas
        $cooperative = Cooperative::first();
        $installation = EnergyInstallation::first();

        $notificationTypes = [
            // Notificaciones de precio de electricidad
            [
                'type' => 'electricity_price',
                'target_id' => null, // Global
                'threshold' => 0.25, // €/kWh precio alto
                'delivery_method' => 'app',
                'is_silent' => false,
                'active' => true,
            ],
            [
                'type' => 'electricity_price',
                'target_id' => null, // Global
                'threshold' => 0.10, // €/kWh precio bajo
                'delivery_method' => 'app',
                'is_silent' => false,
                'active' => true,
            ],
            [
                'type' => 'electricity_price',
                'target_id' => null, // Global
                'threshold' => 0.20, // €/kWh precio medio-alto
                'delivery_method' => 'email',
                'is_silent' => false,
                'active' => fake()->boolean(70),
            ],

            // Notificaciones de producción solar
            [
                'type' => 'solar_production',
                'target_id' => $installation?->id,
                'threshold' => 5.0, // kWh producción diaria mínima
                'delivery_method' => 'app',
                'is_silent' => false,
                'active' => true,
            ],
            [
                'type' => 'solar_production',
                'target_id' => $installation?->id,
                'threshold' => 20.0, // kWh producción diaria máxima
                'delivery_method' => 'app',
                'is_silent' => true,
                'active' => true,
            ],
            [
                'type' => 'solar_production',
                'target_id' => $installation?->id,
                'threshold' => 15.0, // kWh producción objetivo
                'delivery_method' => 'email',
                'is_silent' => false,
                'active' => fake()->boolean(60),
            ],

            // Notificaciones de eventos
            [
                'type' => 'event',
                'target_id' => $cooperative?->id,
                'threshold' => null,
                'delivery_method' => 'app',
                'is_silent' => false,
                'active' => true,
            ],
            [
                'type' => 'event',
                'target_id' => null, // Eventos globales
                'threshold' => null,
                'delivery_method' => 'app',
                'is_silent' => false,
                'active' => fake()->boolean(80),
            ],
            [
                'type' => 'event',
                'target_id' => $cooperative?->id,
                'threshold' => null,
                'delivery_method' => 'email',
                'is_silent' => false,
                'active' => fake()->boolean(50),
            ],
            [
                'type' => 'event',
                'target_id' => null, // Mantenimiento del sistema
                'threshold' => null,
                'delivery_method' => 'email',
                'is_silent' => false,
                'active' => true,
            ],

            // Configuraciones adicionales con variaciones
            [
                'type' => 'electricity_price',
                'target_id' => null,
                'threshold' => 0.15, // €/kWh precio medio
                'delivery_method' => 'sms',
                'is_silent' => false,
                'active' => fake()->boolean(30), // SMS menos común
            ],
            [
                'type' => 'solar_production',
                'target_id' => $installation?->id,
                'threshold' => 0.5, // kWh producción muy baja
                'delivery_method' => 'sms',
                'is_silent' => false,
                'active' => fake()->boolean(40),
            ],
            [
                'type' => 'event',
                'target_id' => null,
                'threshold' => null,
                'delivery_method' => 'sms',
                'is_silent' => false,
                'active' => fake()->boolean(20), // SMS para emergencias
            ],
        ];

        $count = 0;
        foreach ($notificationTypes as $notificationData) {
            NotificationSetting::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'type' => $notificationData['type'],
                    'target_id' => $notificationData['target_id'],
                ],
                [
                    'user_id' => $user->id,
                    'type' => $notificationData['type'],
                    'target_id' => $notificationData['target_id'],
                    'threshold' => $notificationData['threshold'],
                    'delivery_method' => $notificationData['delivery_method'],
                    'is_silent' => $notificationData['is_silent'],
                    'active' => $notificationData['active'],
                ]
            );
            $count++;
        }

        return $count;
    }

    /**
     * Mostrar estadísticas de las configuraciones de notificaciones creadas.
     */
    private function showStatistics(): void
    {
        $stats = [
            'Total configuraciones' => NotificationSetting::count(),
            'Activas' => NotificationSetting::where('active', true)->count(),
            'Inactivas' => NotificationSetting::where('active', false)->count(),
            'Push notifications' => NotificationSetting::where('delivery_method', 'push')->count(),
            'Email notifications' => NotificationSetting::where('delivery_method', 'email')->count(),
            'Silenciosas' => NotificationSetting::where('is_silent', true)->count(),
            'Con sonido' => NotificationSetting::where('is_silent', false)->count(),
        ];

        $this->command->info("\n📊 Estadísticas de configuraciones:");
        foreach ($stats as $type => $count) {
            $this->command->info("   {$type}: {$count}");
        }

        // Tipos más comunes
        $types = NotificationSetting::selectRaw('type, COUNT(*) as count')
                                   ->groupBy('type')
                                   ->orderBy('count', 'desc')
                                   ->limit(5)
                                   ->get();

        if ($types->isNotEmpty()) {
            $this->command->info("\n🔔 Tipos más configurados:");
            foreach ($types as $type) {
                $this->command->info("   {$type->type}: {$type->count}");
            }
        }

        // Métodos de entrega
        $deliveryMethods = NotificationSetting::selectRaw('delivery_method, COUNT(*) as count')
                                             ->groupBy('delivery_method')
                                             ->orderBy('count', 'desc')
                                             ->get();

        if ($deliveryMethods->isNotEmpty()) {
            $this->command->info("\n📨 Métodos de entrega:");
            foreach ($deliveryMethods as $method) {
                $this->command->info("   {$method->delivery_method}: {$method->count}");
            }
        }

        // Información para KiroLux
        $electricityNotifications = NotificationSetting::where('type', 'electricity_price')->count();
        $solarNotifications = NotificationSetting::where('type', 'solar_production')->count();
        $eventNotifications = NotificationSetting::where('type', 'event')->count();
        $activeRate = NotificationSetting::where('active', true)->count() / NotificationSetting::count() * 100;
        
        $this->command->info("\n⚡ Para KiroLux:");
        $this->command->info("   💡 Notificaciones de precio: {$electricityNotifications}");
        $this->command->info("   ☀️ Notificaciones solares: {$solarNotifications}");
        $this->command->info("   📅 Notificaciones de eventos: {$eventNotifications}");
        $this->command->info("   📊 Tasa de activación: " . round($activeRate, 1) . "%");
        $this->command->info("   🎯 Engagement potencial: Alto");
    }
}
