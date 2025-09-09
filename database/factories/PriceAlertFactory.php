<?php

namespace Database\Factories;

use App\Models\PriceAlert;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PriceAlert>
 */
class PriceAlertFactory extends Factory
{
    protected $model = PriceAlert::class;

    public function definition(): array
    {
        $energyTypes = [
            'electricity', 'gas', 'oil', 'coal', 'solar', 'wind',
            'hydro', 'nuclear', 'biomass', 'geothermal', 'hybrid', 'all'
        ];

        $zones = [
            'peninsula', 'canarias', 'baleares', 'ceuta', 'melilla',
            'national', 'international'
        ];

        $alertTypes = [
            'price_drop', 'price_rise', 'price_threshold', 'volatility',
            'spike', 'low_price', 'high_price', 'average_price',
            'forecast_change', 'market_alert'
        ];

        $conditions = [
            'below', 'above', 'equals', 'not_equals', 'between', 'outside'
        ];

        $frequencies = [
            'once', 'daily', 'weekly', 'monthly', 'realtime', 'hourly', 'custom'
        ];

        $notificationChannels = [
            'email', 'sms', 'push', 'webhook'
        ];

        $energyType = $this->faker->randomElement($energyTypes);
        $alertType = $this->faker->randomElement($alertTypes);
        $condition = $this->faker->randomElement($conditions);
        $frequency = $this->faker->randomElement($frequencies);

        // Generar precio umbral basado en el tipo de energía
        $thresholdPrice = $this->generateThresholdPrice($energyType, $alertType);
        
        // Generar configuración de notificaciones
        $notificationSettings = $this->generateNotificationSettings($frequency);

        // Generar fecha de último disparo (algunas alertas ya se han disparado)
        $lastTriggered = $this->faker->boolean(30) ? $this->faker->dateTimeBetween('-30 days', 'now') : null;
        $triggerCount = $lastTriggered ? $this->faker->numberBetween(1, 50) : 0;

        return [
            'user_id' => User::factory(),
            'energy_type' => $energyType,
            'zone' => $this->faker->randomElement($zones),
            'alert_type' => $alertType,
            'threshold_price' => $thresholdPrice,
            'condition' => $condition,
            'is_active' => $this->faker->boolean(85), // 85% activas
            'last_triggered' => $lastTriggered,
            'trigger_count' => $triggerCount,
            'notification_settings' => $notificationSettings,
            'frequency' => $frequency,
        ];
    }

    private function generateThresholdPrice(string $energyType, string $alertType): float
    {
        // Precios base por tipo de energía (€/MWh)
        $basePrices = [
            'electricity' => 80.0,
            'gas' => 60.0,
            'oil' => 120.0,
            'coal' => 40.0,
            'solar' => 50.0,
            'wind' => 45.0,
            'hydro' => 30.0,
            'nuclear' => 25.0,
            'biomass' => 70.0,
            'geothermal' => 55.0,
            'hybrid' => 65.0,
            'all' => 60.0,
        ];

        $basePrice = $basePrices[$energyType] ?? 60.0;

        // Ajustar precio según el tipo de alerta
        $multiplier = match ($alertType) {
            'price_drop', 'low_price' => 0.5, // Precios bajos
            'price_rise', 'high_price', 'spike' => 1.5, // Precios altos
            'volatility' => 1.0, // Precios normales
            'average_price' => 1.0, // Precios promedio
            'forecast_change' => 0.8, // Precios de pronóstico
            'market_alert' => 1.2, // Precios de mercado
            default => 1.0,
        };

        $price = $basePrice * $multiplier;
        
        // Añadir variación aleatoria
        $variation = $this->faker->randomFloat(4, 0.8, 1.2);
        
        return round($price * $variation, 4);
    }

    private function generateNotificationSettings(string $frequency): array
    {
        $channels = $this->faker->randomElements(
            ['email', 'sms', 'push', 'webhook'],
            $this->faker->numberBetween(1, 3)
        );

        $settings = [
            'channels' => $channels,
            'frequency' => $frequency,
            'quiet_hours' => $this->faker->boolean(40) ? [
                'start' => '22:00',
                'end' => '08:00',
                'enabled' => true
            ] : null,
            'max_daily_alerts' => $this->faker->numberBetween(1, 10),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high', 'urgent']),
        ];

        // Configuraciones específicas por canal
        if (in_array('email', $channels)) {
            $settings['email'] = [
                'template' => $this->faker->randomElement(['simple', 'detailed', 'custom']),
                'include_charts' => $this->faker->boolean(60),
                'include_forecast' => $this->faker->boolean(40),
            ];
        }

        if (in_array('sms', $channels)) {
            $settings['sms'] = [
                'max_length' => 160,
                'include_price' => true,
                'include_trend' => $this->faker->boolean(70),
            ];
        }

        if (in_array('push', $channels)) {
            $settings['push'] = [
                'sound' => $this->faker->boolean(80),
                'vibration' => $this->faker->boolean(60),
                'badge' => true,
            ];
        }

        if (in_array('webhook', $channels)) {
            $settings['webhook'] = [
                'url' => $this->faker->url(),
                'method' => $this->faker->randomElement(['POST', 'PUT', 'PATCH']),
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->faker->uuid(),
                ],
                'retry_attempts' => $this->faker->numberBetween(1, 5),
            ];
        }

        return $settings;
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function triggered(): static
    {
        return $this->state(fn (array $attributes) => [
            'last_triggered' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'trigger_count' => $this->faker->numberBetween(1, 50),
        ]);
    }

    public function frequentlyTriggered(): static
    {
        return $this->state(fn (array $attributes) => [
            'last_triggered' => $this->faker->dateTimeBetween('-7 days', 'now'),
            'trigger_count' => $this->faker->numberBetween(20, 100),
        ]);
    }

    public function neverTriggered(): static
    {
        return $this->state(fn (array $attributes) => [
            'last_triggered' => null,
            'trigger_count' => 0,
        ]);
    }

    public function recentlyTriggered(): static
    {
        return $this->state(fn (array $attributes) => [
            'last_triggered' => $this->faker->dateTimeBetween('-3 days', 'now'),
            'trigger_count' => $this->faker->numberBetween(1, 10),
        ]);
    }

    public function electricity(): static
    {
        return $this->state(fn (array $attributes) => [
            'energy_type' => 'electricity',
            'threshold_price' => $this->faker->randomFloat(4, 20, 150),
        ]);
    }

    public function gas(): static
    {
        return $this->state(fn (array $attributes) => [
            'energy_type' => 'gas',
            'threshold_price' => $this->faker->randomFloat(4, 30, 100),
        ]);
    }

    public function renewable(): static
    {
        return $this->state(fn (array $attributes) => [
            'energy_type' => $this->faker->randomElement(['solar', 'wind', 'hydro', 'biomass', 'geothermal']),
            'threshold_price' => $this->faker->randomFloat(4, 20, 80),
        ]);
    }

    public function priceDrop(): static
    {
        return $this->state(fn (array $attributes) => [
            'alert_type' => 'price_drop',
            'condition' => 'below',
            'threshold_price' => $this->faker->randomFloat(4, 20, 60),
        ]);
    }

    public function priceRise(): static
    {
        return $this->state(fn (array $attributes) => [
            'alert_type' => 'price_rise',
            'condition' => 'above',
            'threshold_price' => $this->faker->randomFloat(4, 80, 150),
        ]);
    }

    public function volatility(): static
    {
        return $this->state(fn (array $attributes) => [
            'alert_type' => 'volatility',
            'condition' => 'above',
            'threshold_price' => $this->faker->randomFloat(4, 10, 30),
            'frequency' => 'realtime',
        ]);
    }

    public function spike(): static
    {
        return $this->state(fn (array $attributes) => [
            'alert_type' => 'spike',
            'condition' => 'above',
            'threshold_price' => $this->faker->randomFloat(4, 100, 300),
            'frequency' => 'realtime',
        ]);
    }

    public function lowPrice(): static
    {
        return $this->state(fn (array $attributes) => [
            'alert_type' => 'low_price',
            'condition' => 'below',
            'threshold_price' => $this->faker->randomFloat(4, 10, 40),
        ]);
    }

    public function highPrice(): static
    {
        return $this->state(fn (array $attributes) => [
            'alert_type' => 'high_price',
            'condition' => 'above',
            'threshold_price' => $this->faker->randomFloat(4, 100, 200),
        ]);
    }

    public function realtime(): static
    {
        return $this->state(fn (array $attributes) => [
            'frequency' => 'realtime',
            'notification_settings' => [
                'channels' => ['push', 'email'],
                'frequency' => 'realtime',
                'max_daily_alerts' => 50,
                'priority' => 'high',
            ],
        ]);
    }

    public function daily(): static
    {
        return $this->state(fn (array $attributes) => [
            'frequency' => 'daily',
            'notification_settings' => [
                'channels' => ['email'],
                'frequency' => 'daily',
                'max_daily_alerts' => 1,
                'priority' => 'medium',
            ],
        ]);
    }

    public function weekly(): static
    {
        return $this->state(fn (array $attributes) => [
            'frequency' => 'weekly',
            'notification_settings' => [
                'channels' => ['email'],
                'frequency' => 'weekly',
                'max_daily_alerts' => 1,
                'priority' => 'low',
            ],
        ]);
    }

    public function withEmail(): static
    {
        return $this->state(function (array $attributes) {
            $settings = $attributes['notification_settings'] ?? [];
            $channels = $settings['channels'] ?? [];
            
            if (!in_array('email', $channels)) {
                $channels[] = 'email';
            }
            
            $settings['channels'] = $channels;
            $settings['email'] = [
                'template' => 'detailed',
                'include_charts' => true,
                'include_forecast' => true,
            ];
            
            return ['notification_settings' => $settings];
        });
    }

    public function withSms(): static
    {
        return $this->state(function (array $attributes) {
            $settings = $attributes['notification_settings'] ?? [];
            $channels = $settings['channels'] ?? [];
            
            if (!in_array('sms', $channels)) {
                $channels[] = 'sms';
            }
            
            $settings['channels'] = $channels;
            $settings['sms'] = [
                'max_length' => 160,
                'include_price' => true,
                'include_trend' => true,
            ];
            
            return ['notification_settings' => $settings];
        });
    }

    public function withPush(): static
    {
        return $this->state(function (array $attributes) {
            $settings = $attributes['notification_settings'] ?? [];
            $channels = $settings['channels'] ?? [];
            
            if (!in_array('push', $channels)) {
                $channels[] = 'push';
            }
            
            $settings['channels'] = $channels;
            $settings['push'] = [
                'sound' => true,
                'vibration' => true,
                'badge' => true,
            ];
            
            return ['notification_settings' => $settings];
        });
    }

    public function withWebhook(): static
    {
        return $this->state(function (array $attributes) {
            $settings = $attributes['notification_settings'] ?? [];
            $channels = $settings['channels'] ?? [];
            
            if (!in_array('webhook', $channels)) {
                $channels[] = 'webhook';
            }
            
            $settings['channels'] = $channels;
            $settings['webhook'] = [
                'url' => $this->faker->url(),
                'method' => 'POST',
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->faker->uuid(),
                ],
                'retry_attempts' => 3,
            ];
            
            return ['notification_settings' => $settings];
        });
    }
}
