<?php

namespace Database\Seeders;

use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ApiKeySeeder extends Seeder
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

        $this->command->info('Creando API Keys...');

        // API Keys básicas (60% de las keys)
        ApiKey::factory(60)
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // API Keys de solo lectura (20% de las keys)
        ApiKey::factory(20)
            ->readOnly()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // API Keys de escritura (10% de las keys)
        ApiKey::factory(10)
            ->write()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // API Keys de acceso completo (5% de las keys)
        ApiKey::factory(5)
            ->fullAccess()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // API Keys activas (15% de las keys)
        ApiKey::factory(15)
            ->active()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // API Keys revocadas (3% de las keys)
        ApiKey::factory(3)
            ->revoked()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // API Keys expiradas (2% de las keys)
        ApiKey::factory(2)
            ->expired()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // API Keys que expiran pronto (5% de las keys)
        ApiKey::factory(5)
            ->expiringSoon()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // API Keys que nunca expiran (8% de las keys)
        ApiKey::factory(8)
            ->neverExpires()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // API Keys con límite alto (10% de las keys)
        ApiKey::factory(10)
            ->highRateLimit()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // API Keys con límite bajo (8% de las keys)
        ApiKey::factory(8)
            ->lowRateLimit()
            ->create([
                'user_id' => $users->random()->id,
            ]);

        // API Keys con límite medio (7% de las keys)
        ApiKey::factory(7)
            ->mediumRateLimit()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // API Keys específicas para diferentes propósitos
        ApiKey::factory(5)
            ->development()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        ApiKey::factory(8)
            ->production()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        ApiKey::factory(6)
            ->testing()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        ApiKey::factory(4)
            ->apiIntegration()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        ApiKey::factory(5)
            ->mobileApp()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        ApiKey::factory(6)
            ->webApp()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        ApiKey::factory(4)
            ->thirdParty()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        ApiKey::factory(3)
            ->admin()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        ApiKey::factory(3)
            ->service()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Crear API Keys específicas para usuarios conocidos
        $this->createSpecificApiKeys($users);

        $this->command->info('✅ API Keys creadas exitosamente!');
    }

    private function createSpecificApiKeys($users): void
    {
        // API Key de desarrollo para usuario específico
        $user1 = $users->first();
        if ($user1) {
            ApiKey::create([
                'user_id' => $user1->id,
                'token' => 'dev_' . fake()->regexify('[A-Za-z0-9]{32}'),
                'scope' => 'full-access',
                'rate_limit' => 10000,
                'expires_at' => null,
                'is_revoked' => false,
            ]);

            ApiKey::create([
                'user_id' => $user1->id,
                'token' => 'prod_' . fake()->regexify('[A-Za-z0-9]{40}'),
                'scope' => 'write',
                'rate_limit' => 5000,
                'expires_at' => now()->addYear(),
                'is_revoked' => false,
            ]);
        }

        // API Key de solo lectura para otro usuario
        $user2 = $users->skip(1)->first();
        if ($user2) {
            ApiKey::create([
                'user_id' => $user2->id,
                'token' => 'read_' . fake()->regexify('[A-Za-z0-9]{36}'),
                'scope' => 'read-only',
                'rate_limit' => 2000,
                'expires_at' => now()->addMonths(6),
                'is_revoked' => false,
            ]);

            ApiKey::create([
                'user_id' => $user2->id,
                'token' => 'mobile_' . fake()->regexify('[A-Za-z0-9]{28}'),
                'scope' => 'read-only',
                'rate_limit' => 1500,
                'expires_at' => now()->addYears(2),
                'is_revoked' => false,
            ]);
        }

        // API Key de integración para otro usuario
        $user3 = $users->skip(2)->first();
        if ($user3) {
            ApiKey::create([
                'user_id' => $user3->id,
                'token' => 'int_' . fake()->regexify('[A-Za-z0-9]{36}'),
                'scope' => 'write',
                'rate_limit' => 8000,
                'expires_at' => now()->addMonths(3),
                'is_revoked' => false,
            ]);

            ApiKey::create([
                'user_id' => $user3->id,
                'token' => 'web_' . fake()->regexify('[A-Za-z0-9]{32}'),
                'scope' => 'write',
                'rate_limit' => 3000,
                'expires_at' => now()->addYear(),
                'is_revoked' => false,
            ]);
        }

        // API Key de administración para otro usuario
        $user4 = $users->skip(3)->first();
        if ($user4) {
            ApiKey::create([
                'user_id' => $user4->id,
                'token' => 'admin_' . fake()->regexify('[A-Za-z0-9]{44}'),
                'scope' => 'full-access',
                'rate_limit' => 10000,
                'expires_at' => null,
                'is_revoked' => false,
            ]);

            ApiKey::create([
                'user_id' => $user4->id,
                'token' => 'svc_' . fake()->regexify('[A-Za-z0-9]{40}'),
                'scope' => 'write',
                'rate_limit' => 10000,
                'expires_at' => null,
                'is_revoked' => false,
            ]);
        }

        // API Key de terceros para otro usuario
        $user5 = $users->skip(4)->first();
        if ($user5) {
            ApiKey::create([
                'user_id' => $user5->id,
                'token' => '3rd_' . fake()->regexify('[A-Za-z0-9]{30}'),
                'scope' => 'read-only',
                'rate_limit' => 500,
                'expires_at' => now()->addMonths(3),
                'is_revoked' => false,
            ]);

            ApiKey::create([
                'user_id' => $user5->id,
                'token' => 'test_' . fake()->regexify('[A-Za-z0-9]{24}'),
                'scope' => 'read-only',
                'rate_limit' => 100,
                'expires_at' => now()->addDays(30),
                'is_revoked' => false,
            ]);
        }

        // API Key expirada para demostración
        $user6 = $users->skip(5)->first();
        if ($user6) {
            ApiKey::create([
                'user_id' => $user6->id,
                'token' => 'expired_' . fake()->regexify('[A-Za-z0-9]{32}'),
                'scope' => 'read-only',
                'rate_limit' => 1000,
                'expires_at' => now()->subDays(30),
                'is_revoked' => false,
            ]);
        }

        // API Key revocada para demostración
        $user7 = $users->skip(6)->first();
        if ($user7) {
            ApiKey::create([
                'user_id' => $user7->id,
                'token' => 'revoked_' . fake()->regexify('[A-Za-z0-9]{32}'),
                'scope' => 'write',
                'rate_limit' => 2000,
                'expires_at' => now()->addYear(),
                'is_revoked' => true,
            ]);
        }

        // API Key que expira pronto para demostración
        $user8 = $users->skip(7)->first();
        if ($user8) {
            ApiKey::create([
                'user_id' => $user8->id,
                'token' => 'expiring_' . fake()->regexify('[A-Za-z0-9]{32}'),
                'scope' => 'full-access',
                'rate_limit' => 5000,
                'expires_at' => now()->addDays(3),
                'is_revoked' => false,
            ]);
        }
    }
}