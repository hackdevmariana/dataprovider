<?php

namespace Database\Factories;

use App\Models\OfferComparison;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OfferComparison>
 */
class OfferComparisonFactory extends Factory
{
    protected $model = OfferComparison::class;

    public function definition(): array
    {
        $energyTypes = ['electricity', 'gas', 'oil', 'coal', 'renewable', 'nuclear'];
        $consumptionProfiles = [
            'low_consumption', 'medium_consumption', 'high_consumption',
            'residential', 'commercial', 'industrial', 'mixed'
        ];
        
        $offersCount = $this->faker->numberBetween(2, 8);
        $offersCompared = [];
        
        // Generar ofertas comparadas
        for ($i = 0; $i < $offersCount; $i++) {
            $offersCompared[] = [
                'offer_id' => $this->faker->uuid(),
                'company_name' => $this->faker->randomElement([
                    'Iberdrola', 'Endesa', 'Naturgy', 'Repsol', 'EDP', 'Viesgo',
                    'Holaluz', 'Podo', 'Lucera', 'Gana Energía', 'Som Energia'
                ]),
                'offer_name' => $this->faker->sentence(3),
                'price' => $this->faker->randomFloat(4, 0.05, 0.25),
                'contract_length' => $this->faker->numberBetween(12, 36),
                'is_renewable' => $this->faker->boolean(60),
                'features' => $this->faker->randomElements([
                    'Smart meter included',
                    'Mobile app access',
                    '24/7 customer service',
                    'Green energy certificate',
                    'Price protection',
                    'Online account management'
                ], $this->faker->numberBetween(1, 4)),
            ];
        }
        
        // Ordenar por precio para determinar la mejor oferta
        usort($offersCompared, fn($a, $b) => $a['price'] <=> $b['price']);
        $bestOffer = $offersCompared[0];
        $worstOffer = $offersCompared[count($offersCompared) - 1];
        
        $savingsAmount = $worstOffer['price'] - $bestOffer['price'];
        $savingsPercentage = ($savingsAmount / $worstOffer['price']) * 100;

        return [
            'user_id' => User::factory(),
            'energy_type' => $this->faker->randomElement($energyTypes),
            'consumption_profile' => $this->faker->randomElement($consumptionProfiles),
            'offers_compared' => $offersCompared,
            'best_offer_id' => $bestOffer['offer_id'],
            'savings_amount' => $savingsAmount,
            'savings_percentage' => $savingsPercentage,
            'comparison_criteria' => $this->generateComparisonCriteria(),
            'comparison_date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'is_shared' => $this->faker->boolean(30),
        ];
    }

    private function generateComparisonCriteria(): array
    {
        $allCriteria = [
            'price' => 'Precio por kWh/MWh',
            'contract_length' => 'Duración del contrato',
            'renewable_energy' => 'Energía renovable',
            'customer_service' => 'Atención al cliente',
            'additional_services' => 'Servicios adicionales',
            'flexibility' => 'Flexibilidad contractual',
            'reputation' => 'Reputación de la empresa',
            'payment_methods' => 'Métodos de pago',
            'billing_frequency' => 'Frecuencia de facturación',
            'penalty_fees' => 'Penalizaciones por cancelación',
            'bonus_features' => 'Características adicionales',
            'geographic_coverage' => 'Cobertura geográfica',
        ];

        $selectedCriteria = [];
        $criteriaCount = $this->faker->numberBetween(3, 8);
        $availableCriteria = array_keys($allCriteria);
        $selectedKeys = $this->faker->randomElements($availableCriteria, $criteriaCount);

        foreach ($selectedKeys as $key) {
            $selectedCriteria[$key] = [
                'name' => $allCriteria[$key],
                'weight' => $this->faker->randomFloat(2, 0.1, 1.0),
                'description' => $this->faker->sentence(),
            ];
        }

        return $selectedCriteria;
    }

    public function electricity(): static
    {
        return $this->state(fn (array $attributes) => [
            'energy_type' => 'electricity',
        ]);
    }

    public function gas(): static
    {
        return $this->state(fn (array $attributes) => [
            'energy_type' => 'gas',
        ]);
    }

    public function renewable(): static
    {
        return $this->state(fn (array $attributes) => [
            'energy_type' => 'renewable',
        ]);
    }

    public function highSavings(): static
    {
        return $this->state(function (array $attributes) {
            $savingsPercentage = $this->faker->randomFloat(2, 15, 35);
            $worstPrice = $this->faker->randomFloat(4, 0.15, 0.25);
            $savingsAmount = ($worstPrice * $savingsPercentage) / 100;
            
            return [
                'savings_percentage' => $savingsPercentage,
                'savings_amount' => $savingsAmount,
            ];
        });
    }

    public function lowSavings(): static
    {
        return $this->state(function (array $attributes) {
            $savingsPercentage = $this->faker->randomFloat(2, 1, 8);
            $worstPrice = $this->faker->randomFloat(4, 0.08, 0.15);
            $savingsAmount = ($worstPrice * $savingsPercentage) / 100;
            
            return [
                'savings_percentage' => $savingsPercentage,
                'savings_amount' => $savingsAmount,
            ];
        });
    }

    public function shared(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_shared' => true,
        ]);
    }

    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'comparison_date' => $this->faker->dateTimeBetween('-7 days', 'now'),
        ]);
    }

    public function old(): static
    {
        return $this->state(fn (array $attributes) => [
            'comparison_date' => $this->faker->dateTimeBetween('-60 days', '-30 days'),
        ]);
    }

    public function residential(): static
    {
        return $this->state(fn (array $attributes) => [
            'consumption_profile' => 'residential',
        ]);
    }

    public function commercial(): static
    {
        return $this->state(fn (array $attributes) => [
            'consumption_profile' => 'commercial',
        ]);
    }

    public function industrial(): static
    {
        return $this->state(fn (array $attributes) => [
            'consumption_profile' => 'industrial',
        ]);
    }
}
