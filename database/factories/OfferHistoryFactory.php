<?php

namespace Database\Factories;

use App\Models\OfferHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OfferHistory>
 */
class OfferHistoryFactory extends Factory
{
    protected $model = OfferHistory::class;

    public function definition(): array
    {
        $offerTypes = ['electricity', 'gas', 'renewable', 'hybrid', 'solar', 'wind'];
        $companies = [
            'Iberdrola', 'Endesa', 'Naturgy', 'Repsol', 'EDP', 'Viesgo',
            'Holaluz', 'Podo', 'Lucera', 'Gana Energía', 'Som Energia'
        ];
        $statuses = ['active', 'inactive', 'expired', 'suspended'];
        $currencies = ['EUR', 'USD', 'GBP'];
        $units = ['MWh', 'kWh', 'GWh', 'therm'];

        $validFrom = $this->faker->dateTimeBetween('-6 months', '+1 month');
        $validUntil = $this->faker->optional(0.7)->dateTimeBetween($validFrom, '+2 years');

        return [
            'company_name' => $this->faker->randomElement($companies),
            'offer_type' => $this->faker->randomElement($offerTypes),
            'offer_details' => [
                'name' => $this->faker->sentence(3),
                'description' => $this->faker->paragraph(2),
                'contract_length' => $this->faker->numberBetween(12, 36) . ' months',
                'renewable_percentage' => $this->faker->numberBetween(0, 100),
                'bonus_features' => $this->faker->randomElements([
                    'Smart meter included',
                    'Mobile app access',
                    '24/7 customer service',
                    'Green energy certificate',
                    'Price protection',
                    'Online account management'
                ], $this->faker->numberBetween(1, 4)),
            ],
            'valid_from' => $validFrom,
            'valid_until' => $validUntil,
            'price' => $this->faker->randomFloat(4, 0.05, 0.25),
            'currency' => $this->faker->randomElement($currencies),
            'unit' => $this->faker->randomElement($units),
            'terms_conditions' => [
                'minimum_consumption' => $this->faker->numberBetween(1000, 5000) . ' kWh/year',
                'penalty_fee' => $this->faker->randomFloat(2, 50, 200) . ' EUR',
                'billing_frequency' => $this->faker->randomElement(['monthly', 'quarterly', 'annually']),
                'payment_methods' => $this->faker->randomElements([
                    'Direct debit', 'Credit card', 'Bank transfer', 'Online payment'
                ], $this->faker->numberBetween(2, 4)),
            ],
            'status' => $this->faker->randomElement($statuses),
            'restrictions' => [
                'geographic_limitations' => $this->faker->optional(0.3)->randomElement([
                    'Only available in certain regions',
                    'Not available in rural areas',
                    'Limited to urban areas only'
                ]),
                'consumption_requirements' => $this->faker->optional(0.4)->randomElement([
                    'Minimum 2000 kWh/year',
                    'Maximum 10000 kWh/year',
                    'Commercial customers only'
                ]),
                'technical_requirements' => $this->faker->optional(0.2)->randomElement([
                    'Smart meter required',
                    'Three-phase connection needed',
                    'Specific voltage requirements'
                ]),
            ],
            'is_featured' => $this->faker->boolean(20),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'valid_from' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'valid_until' => $this->faker->dateTimeBetween('now', '+1 year'),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'expired',
            'valid_from' => $this->faker->dateTimeBetween('-2 years', '-1 year'),
            'valid_until' => $this->faker->dateTimeBetween('-1 year', '-1 month'),
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'status' => 'active',
        ]);
    }

    public function renewable(): static
    {
        return $this->state(fn (array $attributes) => [
            'offer_type' => 'renewable',
            'offer_details' => array_merge($attributes['offer_details'] ?? [], [
                'renewable_percentage' => 100,
                'green_certificate' => true,
                'carbon_neutral' => true,
            ]),
        ]);
    }
}
