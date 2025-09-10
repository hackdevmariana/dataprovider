<?php

namespace Database\Seeders;

use App\Models\UserDevice;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserDeviceSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Verificar que existan usuarios
        if (User::count() === 0) {
            $this->command->warn('No hay usuarios. Creando algunos usuarios de ejemplo...');
            User::factory(10)->create();
        }

        $users = User::all();

        $this->command->info('Creando dispositivos de usuario...');

        // Dispositivos básicos (60% de los dispositivos)
        UserDevice::factory(60)
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Dispositivos móviles (20% de los dispositivos)
        UserDevice::factory(20)
            ->mobile()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Tablets (10% de los dispositivos)
        UserDevice::factory(10)
            ->tablet()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Dispositivos de escritorio (8% de los dispositivos)
        UserDevice::factory(8)
            ->desktop()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Laptops (7% de los dispositivos)
        UserDevice::factory(7)
            ->laptop()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Dispositivos iOS (15% de los dispositivos)
        UserDevice::factory(15)
            ->ios()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Dispositivos Android (15% de los dispositivos)
        UserDevice::factory(15)
            ->android()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Dispositivos Windows (10% de los dispositivos)
        UserDevice::factory(10)
            ->windows()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Dispositivos macOS (8% de los dispositivos)
        UserDevice::factory(8)
            ->macos()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Dispositivos activos (80% de los dispositivos)
        UserDevice::factory(80)
            ->active()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Dispositivos inactivos (20% de los dispositivos)
        UserDevice::factory(20)
            ->inactive()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Dispositivos con token (70% de los dispositivos)
        UserDevice::factory(70)
            ->withToken()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Dispositivos sin token (30% de los dispositivos)
        UserDevice::factory(30)
            ->withoutToken()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Dispositivos Chrome (25% de los dispositivos)
        UserDevice::factory(25)
            ->chrome()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Dispositivos Firefox (15% de los dispositivos)
        UserDevice::factory(15)
            ->firefox()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Dispositivos Safari (10% de los dispositivos)
        UserDevice::factory(10)
            ->safari()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Dispositivos Edge (8% de los dispositivos)
        UserDevice::factory(8)
            ->edge()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Aplicaciones móviles (12% de los dispositivos)
        UserDevice::factory(12)
            ->mobileApp()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Aplicaciones de escritorio (8% de los dispositivos)
        UserDevice::factory(8)
            ->desktopApp()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Dispositivos recientes (30% de los dispositivos)
        UserDevice::factory(30)
            ->recent()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Dispositivos antiguos (20% de los dispositivos)
        UserDevice::factory(20)
            ->old()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Crear dispositivos específicos para usuarios conocidos
        $this->createSpecificDevices($users);

        $this->command->info('✅ Dispositivos de usuario creados exitosamente!');
    }

    private function createSpecificDevices($users): void
    {
        // Dispositivos para usuario específico
        $user1 = $users->first();
        if ($user1) {
            UserDevice::create([
                'user_id' => $user1->id,
                'device_name' => 'iPhone 14 Pro',
                'device_type' => 'mobile',
                'platform' => 'iOS',
                'browser' => 'Safari',
                'ip_address' => '192.168.1.100',
                'token' => 'fcm_' . fake()->regexify('[A-Za-z0-9]{64}'),
                'notifications_enabled' => true,
            ]);

            UserDevice::create([
                'user_id' => $user1->id,
                'device_name' => 'MacBook Pro 16"',
                'device_type' => 'laptop',
                'platform' => 'macOS',
                'browser' => 'Chrome',
                'ip_address' => '192.168.1.101',
                'token' => 'web_' . fake()->regexify('[A-Za-z0-9]{64}'),
                'notifications_enabled' => true,
            ]);
        }

        // Dispositivos para otro usuario
        $user2 = $users->skip(1)->first();
        if ($user2) {
            UserDevice::create([
                'user_id' => $user2->id,
                'device_name' => 'Samsung Galaxy S23 Ultra',
                'device_type' => 'mobile',
                'platform' => 'Android',
                'browser' => 'Chrome',
                'ip_address' => '192.168.1.102',
                'token' => 'fcm_' . fake()->regexify('[A-Za-z0-9]{64}'),
                'notifications_enabled' => true,
            ]);

            UserDevice::create([
                'user_id' => $user2->id,
                'device_name' => 'Dell XPS 13',
                'device_type' => 'laptop',
                'platform' => 'Windows',
                'browser' => 'Edge',
                'ip_address' => '192.168.1.103',
                'token' => 'web_' . fake()->regexify('[A-Za-z0-9]{64}'),
                'notifications_enabled' => false,
            ]);
        }

        // Dispositivos para otro usuario
        $user3 = $users->skip(2)->first();
        if ($user3) {
            UserDevice::create([
                'user_id' => $user3->id,
                'device_name' => 'iPad Pro 12.9"',
                'device_type' => 'tablet',
                'platform' => 'iOS',
                'browser' => 'Safari',
                'ip_address' => '192.168.1.104',
                'token' => 'apns_' . fake()->regexify('[A-Za-z0-9]{64}'),
                'notifications_enabled' => true,
            ]);

            UserDevice::create([
                'user_id' => $user3->id,
                'device_name' => 'Surface Pro 9',
                'device_type' => 'tablet',
                'platform' => 'Windows',
                'browser' => 'Edge',
                'ip_address' => '192.168.1.105',
                'token' => 'web_' . fake()->regexify('[A-Za-z0-9]{64}'),
                'notifications_enabled' => true,
            ]);
        }

        // Dispositivos para otro usuario
        $user4 = $users->skip(3)->first();
        if ($user4) {
            UserDevice::create([
                'user_id' => $user4->id,
                'device_name' => 'Google Pixel 7 Pro',
                'device_type' => 'mobile',
                'platform' => 'Android',
                'browser' => 'Mobile App',
                'ip_address' => '192.168.1.106',
                'token' => 'fcm_' . fake()->regexify('[A-Za-z0-9]{64}'),
                'notifications_enabled' => true,
            ]);

            UserDevice::create([
                'user_id' => $user4->id,
                'device_name' => 'iMac 24"',
                'device_type' => 'desktop',
                'platform' => 'macOS',
                'browser' => 'Desktop App',
                'ip_address' => '192.168.1.107',
                'token' => 'app_' . fake()->regexify('[A-Za-z0-9]{64}'),
                'notifications_enabled' => true,
            ]);
        }

        // Dispositivos para otro usuario
        $user5 = $users->skip(4)->first();
        if ($user5) {
            UserDevice::create([
                'user_id' => $user5->id,
                'device_name' => 'OnePlus 11',
                'device_type' => 'mobile',
                'platform' => 'Android',
                'browser' => 'Firefox',
                'ip_address' => '192.168.1.108',
                'token' => 'fcm_' . fake()->regexify('[A-Za-z0-9]{64}'),
                'notifications_enabled' => false,
            ]);

            UserDevice::create([
                'user_id' => $user5->id,
                'device_name' => 'ThinkPad X1 Carbon',
                'device_type' => 'laptop',
                'platform' => 'Linux',
                'browser' => 'Firefox',
                'ip_address' => '192.168.1.109',
                'token' => 'web_' . fake()->regexify('[A-Za-z0-9]{64}'),
                'notifications_enabled' => true,
            ]);
        }

        // Dispositivos para otro usuario
        $user6 = $users->skip(5)->first();
        if ($user6) {
            UserDevice::create([
                'user_id' => $user6->id,
                'device_name' => 'Apple Watch Series 8',
                'device_type' => 'smartwatch',
                'platform' => 'iOS',
                'browser' => 'Mobile App',
                'ip_address' => '192.168.1.110',
                'token' => 'apns_' . fake()->regexify('[A-Za-z0-9]{64}'),
                'notifications_enabled' => true,
            ]);
        }

        // Dispositivos para otro usuario
        $user7 = $users->skip(6)->first();
        if ($user7) {
            UserDevice::create([
                'user_id' => $user7->id,
                'device_name' => 'Samsung Galaxy Tab S8',
                'device_type' => 'tablet',
                'platform' => 'Android',
                'browser' => 'Chrome',
                'ip_address' => '192.168.1.111',
                'token' => 'fcm_' . fake()->regexify('[A-Za-z0-9]{64}'),
                'notifications_enabled' => true,
            ]);
        }

        // Dispositivos para otro usuario
        $user8 = $users->skip(7)->first();
        if ($user8) {
            UserDevice::create([
                'user_id' => $user8->id,
                'device_name' => 'Chromebook Pixel',
                'device_type' => 'laptop',
                'platform' => 'Chrome OS',
                'browser' => 'Chrome',
                'ip_address' => '192.168.1.112',
                'token' => 'web_' . fake()->regexify('[A-Za-z0-9]{64}'),
                'notifications_enabled' => true,
            ]);
        }
    }
}