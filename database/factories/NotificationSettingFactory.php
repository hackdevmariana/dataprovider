<?php

namespace Database\Factories;

use App\Models\NotificationSetting;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NotificationSetting>
 */
class NotificationSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['electricity_price', 'event', 'solar_production'];
        $deliveryMethods = ['app', 'email', 'sms'];
        $type = $this->faker->randomElement($types);

        // Generar threshold basado en el tipo
        $threshold = match($type) {
            'electricity_price' => $this->faker->randomFloat(4, 0.05, 0.50), // Precios de electricidad en €/kWh
            'solar_production' => $this->faker->randomFloat(4, 100, 5000), // Producción solar en kWh
            'event' => null, // Los eventos no tienen threshold
            default => null,
        };

        return [
            'user_id' => User::factory(),
            'type' => $type,
            'target_id' => $this->faker->optional(0.7)->numberBetween(1, 100), // ID del objetivo (municipio, evento, etc.)
            'threshold' => $threshold,
            'delivery_method' => $this->faker->randomElement($deliveryMethods),
            'is_silent' => $this->faker->boolean(20), // 20% de probabilidad de ser silencioso
            'active' => $this->faker->boolean(85), // 85% de probabilidad de estar activo
        ];
    }

    /**
     * Indicate that the notification is for electricity price alerts.
     */
    public function electricityPrice(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'electricity_price',
            'threshold' => $this->faker->randomFloat(4, 0.10, 0.40),
            'delivery_method' => $this->faker->randomElement(['app', 'email']),
        ]);
    }

    /**
     * Indicate that the notification is for event alerts.
     */
    public function event(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'event',
            'threshold' => null,
            'delivery_method' => $this->faker->randomElement(['app', 'email', 'sms']),
        ]);
    }

    /**
     * Indicate that the notification is for solar production alerts.
     */
    public function solarProduction(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'solar_production',
            'threshold' => $this->faker->randomFloat(4, 500, 3000),
            'delivery_method' => $this->faker->randomElement(['app', 'email']),
        ]);
    }

    /**
     * Indicate that the notification is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => true,
        ]);
    }

    /**
     * Indicate that the notification is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }

    /**
     * Indicate that the notification is silent.
     */
    public function silent(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_silent' => true,
        ]);
    }

    /**
     * Indicate that the notification is for email delivery.
     */
    public function email(): static
    {
        return $this->state(fn (array $attributes) => [
            'delivery_method' => 'email',
        ]);
    }

    /**
     * Indicate that the notification is for SMS delivery.
     */
    public function sms(): static
    {
        return $this->state(fn (array $attributes) => [
            'delivery_method' => 'sms',
        ]);
    }

    /**
     * Indicate that the notification is for app delivery.
     */
    public function app(): static
    {
        return $this->state(fn (array $attributes) => [
            'delivery_method' => 'app',
        ]);
    }
}
