<?php

namespace Database\Factories;

use App\Models\ExchangeRate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para generar tipos de cambio realistas.
 */
class ExchangeRateFactory extends Factory
{
    protected $model = ExchangeRate::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $exchangeData = $this->getRealisticExchangeData();
        $selectedPair = fake()->randomElement($exchangeData);
        
        return [
            'from_currency' => 'EUR',
            'to_currency' => $selectedPair['currency'],
            'rate' => $selectedPair['rate'],
            'date' => fake()->dateTimeBetween('-7 days', 'now')->format('Y-m-d'),
            'source' => $selectedPair['source'],
            'market_type' => $selectedPair['market_type'],
            'precision' => $selectedPair['precision'],
            'unit' => $selectedPair['unit'],
            'volume_usd' => $selectedPair['volume_usd'] ?? null,
            'market_cap' => $selectedPair['market_cap'] ?? null,
            'retrieved_at' => fake()->dateTimeBetween('-6 hours', 'now'),
            'is_active' => fake()->boolean(95),
            'is_promoted' => fake()->boolean(20),
        ];
    }

    /**
     * Datos realistas de tipos de cambio.
     */
    private function getRealisticExchangeData(): array
    {
        return [
            // Monedas fiat principales
            [
                'currency' => 'USD',
                'rate' => fake()->randomFloat(4, 1.05, 1.15),
                'source' => 'exchangerate.host',
                'market_type' => 'fiat',
                'precision' => 4,
                'unit' => 'dólar',
                'volume_usd' => fake()->numberBetween(1000000000, 5000000000),
            ],
            [
                'currency' => 'GBP',
                'rate' => fake()->randomFloat(4, 0.82, 0.92),
                'source' => 'exchangerate.host',
                'market_type' => 'fiat',
                'precision' => 4,
                'unit' => 'libra esterlina',
                'volume_usd' => fake()->numberBetween(500000000, 2000000000),
            ],
            [
                'currency' => 'JPY',
                'rate' => fake()->randomFloat(2, 140.0, 165.0),
                'source' => 'exchangerate.host',
                'market_type' => 'fiat',
                'precision' => 2,
                'unit' => 'yen japonés',
                'volume_usd' => fake()->numberBetween(800000000, 3000000000),
            ],
            [
                'currency' => 'CHF',
                'rate' => fake()->randomFloat(4, 0.92, 1.02),
                'source' => 'exchangerate.host',
                'market_type' => 'fiat',
                'precision' => 4,
                'unit' => 'franco suizo',
                'volume_usd' => fake()->numberBetween(200000000, 800000000),
            ],
            [
                'currency' => 'CAD',
                'rate' => fake()->randomFloat(4, 1.35, 1.55),
                'source' => 'exchangerate.host',
                'market_type' => 'fiat',
                'precision' => 4,
                'unit' => 'dólar canadiense',
                'volume_usd' => fake()->numberBetween(300000000, 1200000000),
            ],

            // Criptomonedas principales
            [
                'currency' => 'BTC',
                'rate' => fake()->randomFloat(8, 0.000015, 0.000025), // EUR por BTC
                'source' => 'coingecko',
                'market_type' => 'crypto',
                'precision' => 8,
                'unit' => 'bitcoin',
                'volume_usd' => fake()->numberBetween(15000000000, 35000000000),
                'market_cap' => fake()->numberBetween(800000000000, 1200000000000),
            ],
            [
                'currency' => 'ETH',
                'rate' => fake()->randomFloat(6, 0.0003, 0.0006), // EUR por ETH
                'source' => 'coingecko',
                'market_type' => 'crypto',
                'precision' => 6,
                'unit' => 'ethereum',
                'volume_usd' => fake()->numberBetween(8000000000, 20000000000),
                'market_cap' => fake()->numberBetween(200000000000, 400000000000),
            ],
            [
                'currency' => 'ADA',
                'rate' => fake()->randomFloat(6, 1.8, 3.2), // EUR por ADA
                'source' => 'coingecko',
                'market_type' => 'crypto',
                'precision' => 6,
                'unit' => 'cardano',
                'volume_usd' => fake()->numberBetween(200000000, 800000000),
                'market_cap' => fake()->numberBetween(15000000000, 35000000000),
            ],
            [
                'currency' => 'DOT',
                'rate' => fake()->randomFloat(4, 0.12, 0.25), // EUR por DOT
                'source' => 'coingecko',
                'market_type' => 'crypto',
                'precision' => 4,
                'unit' => 'polkadot',
                'volume_usd' => fake()->numberBetween(150000000, 600000000),
                'market_cap' => fake()->numberBetween(8000000000, 20000000000),
            ],

            // Metales preciosos (precio por gramo en EUR)
            [
                'currency' => 'XAU',
                'rate' => fake()->randomFloat(2, 55.0, 75.0), // EUR por gramo de oro
                'source' => 'metals-api',
                'market_type' => 'metal',
                'precision' => 2,
                'unit' => 'gramo de oro',
                'volume_usd' => fake()->numberBetween(50000000, 200000000),
            ],
            [
                'currency' => 'XAG',
                'rate' => fake()->randomFloat(3, 0.65, 1.1), // EUR por gramo de plata
                'source' => 'metals-api',
                'market_type' => 'metal',
                'precision' => 3,
                'unit' => 'gramo de plata',
                'volume_usd' => fake()->numberBetween(20000000, 80000000),
            ],

            // Energía como commodity (clasificado como metal)
            [
                'currency' => 'kWh',
                'rate' => fake()->randomFloat(4, 0.18, 0.35), // EUR por kWh
                'source' => 'REE',
                'market_type' => 'metal', // Clasificado como commodity
                'precision' => 4,
                'unit' => 'kilovatio hora',
                'volume_usd' => null,
            ],
        ];
    }

    /**
     * Tipo de cambio activo y reciente.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'retrieved_at' => fake()->dateTimeBetween('-2 hours', 'now'),
            'date' => now()->format('Y-m-d'),
        ]);
    }

    /**
     * Tipo de cambio promocionado.
     */
    public function promoted(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_promoted' => true,
            'is_active' => true,
        ]);
    }

    /**
     * Criptomoneda específica.
     */
    public function crypto(): static
    {
        return $this->state(fn (array $attributes) => [
            'market_type' => 'crypto',
            'source' => 'coingecko',
            'precision' => 8,
        ]);
    }

    /**
     * Metal precioso.
     */
    public function metal(): static
    {
        return $this->state(fn (array $attributes) => [
            'market_type' => 'metal',
            'source' => 'metals-api',
            'precision' => 3,
        ]);
    }

    /**
     * Moneda fiat.
     */
    public function fiat(): static
    {
        return $this->state(fn (array $attributes) => [
            'market_type' => 'fiat',
            'source' => 'exchangerate.host',
            'precision' => 4,
        ]);
    }
}
