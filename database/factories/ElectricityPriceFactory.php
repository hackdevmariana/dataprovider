<?php

namespace Database\Factories;

use App\Models\ElectricityPrice;
use App\Models\PriceUnit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ElectricityPrice>
 */
class ElectricityPriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['pvpc', 'spot'];
        $type = $this->faker->randomElement($types);
        
        // Precios típicos del mercado eléctrico español (€/MWh)
        $basePrice = $this->faker->randomFloat(2, 30, 150);
        
        // Determinar si es precio horario o resumen diario
        $isHourly = $this->faker->boolean(80); // 80% horarios, 20% resúmenes diarios
        
        $hour = $isHourly ? $this->faker->numberBetween(0, 23) : null;
        
        // Crear fecha aleatoria en los últimos 30 días
        $date = $this->faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d');
        
        $data = [
            'date' => $date,
            'hour' => $hour,
            'type' => $type,
            'price_eur_mwh' => $basePrice,
            'source' => $type === 'pvpc' ? 'REE' : 'OMIE',
            'price_unit_id' => PriceUnit::first()?->id ?? PriceUnit::create([
                'name' => 'Euro por megavatio hora',
                'short_name' => '€/MWh',
                'unit_code' => 'EUR_MWH',
                'conversion_factor' => 1.0,
            ])->id,
            'forecast_for_tomorrow' => $this->faker->boolean(20), // 20% son previsiones
        ];
        
        // Si es resumen diario, añadir estadísticas
        if (!$isHourly) {
            $minPrice = $basePrice * 0.6;
            $maxPrice = $basePrice * 1.4;
            
            $data['price_min'] = round($minPrice, 2);
            $data['price_max'] = round($maxPrice, 2);
            $data['price_avg'] = $basePrice;
        }
        
        return $data;
    }

    /**
     * Indicate that the price is for today.
     */
    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'date' => Carbon::today()->format('Y-m-d'),
            'forecast_for_tomorrow' => false,
        ]);
    }

    /**
     * Indicate that the price is for tomorrow.
     */
    public function tomorrow(): static
    {
        return $this->state(fn (array $attributes) => [
            'date' => Carbon::tomorrow()->format('Y-m-d'),
            'forecast_for_tomorrow' => true,
        ]);
    }

    /**
     * Indicate that the price is PVPC type.
     */
    public function pvpc(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'pvpc',
            'source' => 'REE',
        ]);
    }

    /**
     * Indicate that the price is spot market type.
     */
    public function spot(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'spot',
            'source' => 'OMIE',
        ]);
    }

    /**
     * Indicate that the price is for a specific hour.
     */
    public function hourly($hour = null): static
    {
        return $this->state(fn (array $attributes) => [
            'hour' => $hour ?? $this->faker->numberBetween(0, 23),
        ]);
    }

    /**
     * Indicate that the price is a daily summary.
     */
    public function dailySummary(): static
    {
        return $this->state(function (array $attributes) {
            $basePrice = $attributes['price_eur_mwh'] ?? $this->faker->randomFloat(2, 30, 150);
            $minPrice = $basePrice * 0.6;
            $maxPrice = $basePrice * 1.4;
            
            return [
                'hour' => null,
                'price_min' => round($minPrice, 2),
                'price_max' => round($maxPrice, 2),
                'price_avg' => $basePrice,
            ];
        });
    }

    /**
     * Indicate that the price is cheap (valley hours).
     */
    public function cheap(): static
    {
        return $this->state(fn (array $attributes) => [
            'price_eur_mwh' => $this->faker->randomFloat(2, 20, 50),
            'hour' => $this->faker->randomElement([0, 1, 2, 3, 4, 5, 23]), // Horas valle
        ]);
    }

    /**
     * Indicate that the price is expensive (peak hours).
     */
    public function expensive(): static
    {
        return $this->state(fn (array $attributes) => [
            'price_eur_mwh' => $this->faker->randomFloat(2, 100, 200),
            'hour' => $this->faker->randomElement([18, 19, 20, 21]), // Horas punta
        ]);
    }
}