<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserDevice;
use App\Models\User;

class UserDeviceSeeder extends Seeder
{
    /**
     * Ejecutar el seeder para dispositivos de usuarios.
     */
    public function run(): void
    {
        $this->command->info('Creando dispositivos de usuarios para KiroLux...');

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

        // Crear dispositivos para cada usuario
        $createdCount = 0;
        foreach ($users as $user) {
            $userDevices = $this->createDevicesForUser($user);
            $createdCount += $userDevices;
        }

        $this->command->info("✅ Creados {$createdCount} dispositivos de usuario");

        // Mostrar estadísticas
        $this->showStatistics();
    }

    /**
     * Crear dispositivos para un usuario específico.
     */
    private function createDevicesForUser(User $user): int
    {
        $devices = [
            // Smartphone principal
            [
                'device_name' => 'iPhone de ' . $user->name,
                'device_type' => 'mobile',
                'platform' => 'iOS',
                'browser' => 'Safari Mobile',
                'ip_address' => fake()->ipv4(),
                'token' => 'apns_' . fake()->uuid(),
                'notifications_enabled' => true,
            ],
            // Tablet secundario
            [
                'device_name' => 'iPad de ' . $user->name,
                'device_type' => 'tablet',
                'platform' => 'iOS',
                'browser' => 'Safari Mobile',
                'ip_address' => fake()->ipv4(),
                'token' => 'apns_' . fake()->uuid(),
                'notifications_enabled' => true,
            ],
            // Ordenador de casa
            [
                'device_name' => 'MacBook de ' . $user->name,
                'device_type' => 'desktop',
                'platform' => 'macOS',
                'browser' => 'Safari',
                'ip_address' => fake()->ipv4(),
                'token' => null, // No push notifications en desktop
                'notifications_enabled' => false,
            ],
        ];

        // Añadir algunos dispositivos Android aleatorios
        if (fake()->boolean(60)) {
            $devices[] = [
                'device_name' => 'Samsung Galaxy de ' . $user->name,
                'device_type' => 'mobile',
                'platform' => 'Android',
                'browser' => 'Chrome Mobile',
                'ip_address' => fake()->ipv4(),
                'token' => 'fcm_' . fake()->uuid(),
                'notifications_enabled' => true,
            ];
        }

        $count = 0;
        foreach ($devices as $deviceData) {
            UserDevice::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'device_name' => $deviceData['device_name'],
                ],
                [
                    'user_id' => $user->id,
                    'device_name' => $deviceData['device_name'],
                    'device_type' => $deviceData['device_type'],
                    'platform' => $deviceData['platform'],
                    'browser' => $deviceData['browser'],
                    'ip_address' => $deviceData['ip_address'],
                    'token' => $deviceData['token'],
                    'notifications_enabled' => $deviceData['notifications_enabled'],
                ]
            );
            $count++;
        }

        return $count;
    }

    /**
     * Mostrar estadísticas de los dispositivos creados.
     */
    private function showStatistics(): void
    {
        $stats = [
            'Total dispositivos' => UserDevice::count(),
            'Dispositivos móviles' => UserDevice::where('device_type', 'mobile')->count(),
            'Tablets' => UserDevice::where('device_type', 'tablet')->count(),
            'Ordenadores' => UserDevice::where('device_type', 'desktop')->count(),
            'iOS' => UserDevice::where('platform', 'iOS')->count(),
            'Android' => UserDevice::where('platform', 'Android')->count(),
            'macOS' => UserDevice::where('platform', 'macOS')->count(),
            'Con notificaciones' => UserDevice::where('notifications_enabled', true)->count(),
        ];

        $this->command->info("\n📊 Estadísticas de dispositivos:");
        foreach ($stats as $type => $count) {
            $this->command->info("   {$type}: {$count}");
        }

        // Plataformas más comunes
        $platforms = UserDevice::selectRaw('platform, COUNT(*) as count')
                               ->groupBy('platform')
                               ->orderBy('count', 'desc')
                               ->get();

        if ($platforms->isNotEmpty()) {
            $this->command->info("\n📱 Plataformas más comunes:");
            foreach ($platforms as $platform) {
                $this->command->info("   {$platform->platform}: {$platform->count}");
            }
        }

        // Tipos de dispositivo
        $deviceTypes = UserDevice::selectRaw('device_type, COUNT(*) as count')
                                 ->groupBy('device_type')
                                 ->orderBy('count', 'desc')
                                 ->get();

        if ($deviceTypes->isNotEmpty()) {
            $this->command->info("\n📟 Tipos de dispositivo:");
            foreach ($deviceTypes as $type) {
                $this->command->info("   {$type->device_type}: {$type->count}");
            }
        }

        // Información para KiroLux
        $mobileCount = UserDevice::where('device_type', 'mobile')->count();
        $pushEnabled = UserDevice::where('notifications_enabled', true)->count();
        $totalDevices = UserDevice::count();
        
        $this->command->info("\n📲 Para KiroLux:");
        $this->command->info("   🎯 Dispositivos móviles: {$mobileCount}");
        $this->command->info("   🔔 Push notifications: {$pushEnabled}");
        $this->command->info("   📊 Tasa de engagement: " . ($totalDevices > 0 ? round(($pushEnabled / $totalDevices) * 100, 1) : 0) . "%");
        $this->command->info("   💡 Usuarios multi-dispositivo: " . User::has('userDevices', '>', 1)->count());
    }
}
