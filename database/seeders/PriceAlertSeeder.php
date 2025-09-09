<?php

namespace Database\Seeders;

use App\Models\PriceAlert;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PriceAlertSeeder extends Seeder
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

        $this->command->info('Creando alertas de precios...');

        // Alertas básicas (60% de las alertas)
        PriceAlert::factory(60)
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Alertas activas (20% de las alertas)
        PriceAlert::factory(20)
            ->active()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Alertas inactivas (10% de las alertas)
        PriceAlert::factory(10)
            ->inactive()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Alertas disparadas (5% de las alertas)
        PriceAlert::factory(5)
            ->triggered()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Alertas frecuentemente disparadas (3% de las alertas)
        PriceAlert::factory(3)
            ->frequentlyTriggered()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Alertas nunca disparadas (2% de las alertas)
        PriceAlert::factory(2)
            ->neverTriggered()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Alertas de electricidad especializadas
        PriceAlert::factory(15)
            ->electricity()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Alertas de gas
        PriceAlert::factory(10)
            ->gas()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Alertas de energías renovables
        PriceAlert::factory(12)
            ->renewable()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Alertas de bajada de precio
        PriceAlert::factory(8)
            ->priceDrop()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Alertas de subida de precio
        PriceAlert::factory(8)
            ->priceRise()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Alertas de volatilidad
        PriceAlert::factory(6)
            ->volatility()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Alertas de picos de precio
        PriceAlert::factory(5)
            ->spike()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Alertas de precio bajo
        PriceAlert::factory(7)
            ->lowPrice()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Alertas de precio alto
        PriceAlert::factory(7)
            ->highPrice()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Alertas en tiempo real
        PriceAlert::factory(10)
            ->realtime()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Alertas diarias
        PriceAlert::factory(8)
            ->daily()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Alertas semanales
        PriceAlert::factory(6)
            ->weekly()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Alertas con notificaciones por email
        PriceAlert::factory(15)
            ->withEmail()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Alertas con notificaciones SMS
        PriceAlert::factory(10)
            ->withSms()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Alertas con notificaciones push
        PriceAlert::factory(12)
            ->withPush()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Alertas con webhooks
        PriceAlert::factory(8)
            ->withWebhook()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Crear alertas específicas para usuarios conocidos
        $this->createSpecificAlerts($users);

        $this->command->info('✅ Alertas de precios creadas exitosamente!');
    }

    private function createSpecificAlerts($users): void
    {
        // Alerta de electricidad para usuario específico
        $user1 = $users->first();
        if ($user1) {
            PriceAlert::create([
                'user_id' => $user1->id,
                'energy_type' => 'electricity',
                'zone' => 'peninsula',
                'alert_type' => 'price_drop',
                'threshold_price' => 50.00,
                'condition' => 'below',
                'is_active' => true,
                'last_triggered' => now()->subDays(2),
                'trigger_count' => 5,
                'notification_settings' => [
                    'channels' => ['email', 'push'],
                    'frequency' => 'realtime',
                    'quiet_hours' => [
                        'start' => '23:00',
                        'end' => '07:00',
                        'enabled' => true
                    ],
                    'max_daily_alerts' => 10,
                    'priority' => 'high',
                    'email' => [
                        'template' => 'detailed',
                        'include_charts' => true,
                        'include_forecast' => true,
                    ],
                    'push' => [
                        'sound' => true,
                        'vibration' => true,
                        'badge' => true,
                    ],
                ],
                'frequency' => 'realtime',
            ]);

            PriceAlert::create([
                'user_id' => $user1->id,
                'energy_type' => 'solar',
                'zone' => 'peninsula',
                'alert_type' => 'high_price',
                'threshold_price' => 80.00,
                'condition' => 'above',
                'is_active' => true,
                'last_triggered' => null,
                'trigger_count' => 0,
                'notification_settings' => [
                    'channels' => ['email'],
                    'frequency' => 'daily',
                    'max_daily_alerts' => 1,
                    'priority' => 'medium',
                    'email' => [
                        'template' => 'simple',
                        'include_charts' => false,
                        'include_forecast' => false,
                    ],
                ],
                'frequency' => 'daily',
            ]);
        }

        // Alerta de gas para otro usuario
        $user2 = $users->skip(1)->first();
        if ($user2) {
            PriceAlert::create([
                'user_id' => $user2->id,
                'energy_type' => 'gas',
                'zone' => 'peninsula',
                'alert_type' => 'volatility',
                'threshold_price' => 15.00,
                'condition' => 'above',
                'is_active' => true,
                'last_triggered' => now()->subHours(6),
                'trigger_count' => 12,
                'notification_settings' => [
                    'channels' => ['sms', 'webhook'],
                    'frequency' => 'realtime',
                    'max_daily_alerts' => 20,
                    'priority' => 'urgent',
                    'sms' => [
                        'max_length' => 160,
                        'include_price' => true,
                        'include_trend' => true,
                    ],
                    'webhook' => [
                        'url' => 'https://api.example.com/alerts',
                        'method' => 'POST',
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'Authorization' => 'Bearer ' . fake()->uuid(),
                        ],
                        'retry_attempts' => 5,
                    ],
                ],
                'frequency' => 'realtime',
            ]);
        }

        // Alerta de energías renovables para otro usuario
        $user3 = $users->skip(2)->first();
        if ($user3) {
            PriceAlert::create([
                'user_id' => $user3->id,
                'energy_type' => 'wind',
                'zone' => 'canarias',
                'alert_type' => 'low_price',
                'threshold_price' => 25.00,
                'condition' => 'below',
                'is_active' => true,
                'last_triggered' => now()->subDays(1),
                'trigger_count' => 3,
                'notification_settings' => [
                    'channels' => ['email', 'push', 'sms'],
                    'frequency' => 'daily',
                    'quiet_hours' => [
                        'start' => '22:00',
                        'end' => '08:00',
                        'enabled' => false
                    ],
                    'max_daily_alerts' => 5,
                    'priority' => 'medium',
                    'email' => [
                        'template' => 'detailed',
                        'include_charts' => true,
                        'include_forecast' => true,
                    ],
                    'push' => [
                        'sound' => false,
                        'vibration' => true,
                        'badge' => true,
                    ],
                    'sms' => [
                        'max_length' => 160,
                        'include_price' => true,
                        'include_trend' => false,
                    ],
                ],
                'frequency' => 'daily',
            ]);
        }

        // Alerta de pico de precio para otro usuario
        $user4 = $users->skip(3)->first();
        if ($user4) {
            PriceAlert::create([
                'user_id' => $user4->id,
                'energy_type' => 'electricity',
                'zone' => 'baleares',
                'alert_type' => 'spike',
                'threshold_price' => 200.00,
                'condition' => 'above',
                'is_active' => true,
                'last_triggered' => now()->subHours(3),
                'trigger_count' => 8,
                'notification_settings' => [
                    'channels' => ['push', 'webhook'],
                    'frequency' => 'realtime',
                    'max_daily_alerts' => 50,
                    'priority' => 'urgent',
                    'push' => [
                        'sound' => true,
                        'vibration' => true,
                        'badge' => true,
                    ],
                    'webhook' => [
                        'url' => 'https://monitoring.example.com/alerts',
                        'method' => 'POST',
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'X-API-Key' => fake()->uuid(),
                        ],
                        'retry_attempts' => 3,
                    ],
                ],
                'frequency' => 'realtime',
            ]);
        }

        // Alerta semanal para otro usuario
        $user5 = $users->skip(4)->first();
        if ($user5) {
            PriceAlert::create([
                'user_id' => $user5->id,
                'energy_type' => 'all',
                'zone' => 'national',
                'alert_type' => 'average_price',
                'threshold_price' => 60.00,
                'condition' => 'above',
                'is_active' => true,
                'last_triggered' => now()->subDays(5),
                'trigger_count' => 1,
                'notification_settings' => [
                    'channels' => ['email'],
                    'frequency' => 'weekly',
                    'max_daily_alerts' => 1,
                    'priority' => 'low',
                    'email' => [
                        'template' => 'simple',
                        'include_charts' => true,
                        'include_forecast' => false,
                    ],
                ],
                'frequency' => 'weekly',
            ]);
        }
    }
}
