<?php

namespace Database\Factories;

use App\Models\ElectricityOffer;
use App\Models\EnergyCompany;
use App\Models\PriceUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para generar ofertas eléctricas realistas.
 */
class ElectricityOfferFactory extends Factory
{
    protected $model = ElectricityOffer::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $offerData = $this->getRealisticOfferData();
        $selectedOffer = fake()->randomElement($offerData);
        
        return [
            'energy_company_id' => EnergyCompany::inRandomOrder()->first()?->id ?? 
                                  EnergyCompany::factory()->create()->id,
            'name' => $selectedOffer['name'],
            'slug' => \Str::slug($selectedOffer['name']),
            'description' => $selectedOffer['description'],
            'price_fixed_eur_month' => $selectedOffer['price_fixed_eur_month'],
            'price_variable_eur_kwh' => $selectedOffer['price_variable_eur_kwh'],
            'price_unit_id' => PriceUnit::inRandomOrder()->first()?->id,
            'offer_type' => $selectedOffer['offer_type'],
            'valid_from' => fake()->dateTimeBetween('-1 month', 'now'),
            'valid_until' => fake()->dateTimeBetween('now', '+1 year'),
            'conditions_url' => $selectedOffer['conditions_url'],
            'contract_length_months' => $selectedOffer['contract_length_months'],
            'requires_smart_meter' => $selectedOffer['requires_smart_meter'],
            'renewable_origin_certified' => $selectedOffer['renewable_origin_certified'],
        ];
    }

    /**
     * Datos realistas de ofertas eléctricas españolas.
     */
    private function getRealisticOfferData(): array
    {
        return [
            // Ofertas fijas
            [
                'name' => 'Tarifa Fija 12 Meses',
                'description' => 'Precio fijo durante 12 meses. Sin sorpresas en tu factura.',
                'price_fixed_eur_month' => null,
                'price_variable_eur_kwh' => fake()->randomFloat(4, 0.12, 0.18),
                'offer_type' => 'fixed',
                'conditions_url' => fake()->url(),
                'contract_length_months' => 12,
                'requires_smart_meter' => false,
                'renewable_origin_certified' => fake()->boolean(60),
            ],
            [
                'name' => 'Estable 24 Meses',
                'description' => 'Tarifa estable a largo plazo con energía 100% renovable.',
                'price_fixed_eur_month' => null,
                'price_variable_eur_kwh' => fake()->randomFloat(4, 0.13, 0.19),
                'offer_type' => 'fixed',
                'conditions_url' => fake()->url(),
                'contract_length_months' => 24,
                'requires_smart_meter' => false,
                'renewable_origin_certified' => true,
            ],
            [
                'name' => 'Plan Fijo Verde',
                'description' => 'Energía 100% renovable con precio fijo. Compromiso sostenible.',
                'price_fixed_eur_month' => null,
                'price_variable_eur_kwh' => fake()->randomFloat(4, 0.14, 0.20),
                'offer_type' => 'fixed',
                'conditions_url' => fake()->url(),
                'contract_length_months' => 12,
                'requires_smart_meter' => false,
                'renewable_origin_certified' => true,
            ],

            // Ofertas variables
            [
                'name' => 'Indexada PVPC',
                'description' => 'Precio indexado al PVPC oficial. Máxima transparencia.',
                'price_fixed_eur_month' => fake()->randomFloat(2, 3.50, 6.00),
                'price_variable_eur_kwh' => null,
                'offer_type' => 'variable',
                'conditions_url' => fake()->url(),
                'contract_length_months' => null,
                'requires_smart_meter' => true,
                'renewable_origin_certified' => fake()->boolean(40),
            ],
            [
                'name' => 'Mercado Libre Variable',
                'description' => 'Tarifa variable que sigue las fluctuaciones del mercado.',
                'price_fixed_eur_month' => fake()->randomFloat(2, 4.00, 7.00),
                'price_variable_eur_kwh' => null,
                'offer_type' => 'variable',
                'conditions_url' => fake()->url(),
                'contract_length_months' => null,
                'requires_smart_meter' => false,
                'renewable_origin_certified' => fake()->boolean(30),
            ],
            [
                'name' => 'Flex Horaria',
                'description' => 'Tarifa variable con discriminación horaria. Ahorra en horas valle.',
                'price_fixed_eur_month' => fake()->randomFloat(2, 4.50, 8.00),
                'price_variable_eur_kwh' => null,
                'offer_type' => 'variable',
                'conditions_url' => fake()->url(),
                'contract_length_months' => null,
                'requires_smart_meter' => true,
                'renewable_origin_certified' => fake()->boolean(50),
            ],

            // Ofertas híbridas
            [
                'name' => 'Híbrida Inteligente',
                'description' => 'Combinación de precio fijo y variable según consumo.',
                'price_fixed_eur_month' => fake()->randomFloat(2, 8.00, 15.00),
                'price_variable_eur_kwh' => fake()->randomFloat(4, 0.10, 0.15),
                'offer_type' => 'hybrid',
                'conditions_url' => fake()->url(),
                'contract_length_months' => 12,
                'requires_smart_meter' => true,
                'renewable_origin_certified' => fake()->boolean(70),
            ],
            [
                'name' => 'Plan Mixto Sostenible',
                'description' => 'Tarifa híbrida con energía renovable certificada.',
                'price_fixed_eur_month' => fake()->randomFloat(2, 10.00, 18.00),
                'price_variable_eur_kwh' => fake()->randomFloat(4, 0.11, 0.16),
                'offer_type' => 'hybrid',
                'conditions_url' => fake()->url(),
                'contract_length_months' => 18,
                'requires_smart_meter' => true,
                'renewable_origin_certified' => true,
            ],

            // Ofertas especiales
            [
                'name' => 'Autoconsumo Plus',
                'description' => 'Tarifa especial para instalaciones de autoconsumo solar.',
                'price_fixed_eur_month' => fake()->randomFloat(2, 5.00, 10.00),
                'price_variable_eur_kwh' => fake()->randomFloat(4, 0.08, 0.12),
                'offer_type' => 'hybrid',
                'conditions_url' => fake()->url(),
                'contract_length_months' => 24,
                'requires_smart_meter' => true,
                'renewable_origin_certified' => true,
            ],
            [
                'name' => 'Cooperativa Energética',
                'description' => 'Tarifa solidaria para miembros de cooperativas energéticas.',
                'price_fixed_eur_month' => fake()->randomFloat(2, 2.00, 5.00),
                'price_variable_eur_kwh' => fake()->randomFloat(4, 0.09, 0.13),
                'offer_type' => 'variable',
                'conditions_url' => fake()->url(),
                'contract_length_months' => null,
                'requires_smart_meter' => false,
                'renewable_origin_certified' => true,
            ],
            [
                'name' => 'Nocturna Eficiente',
                'description' => 'Tarifa especial con descuentos en consumo nocturno.',
                'price_fixed_eur_month' => fake()->randomFloat(2, 6.00, 12.00),
                'price_variable_eur_kwh' => fake()->randomFloat(4, 0.07, 0.11),
                'offer_type' => 'variable',
                'conditions_url' => fake()->url(),
                'contract_length_months' => 12,
                'requires_smart_meter' => true,
                'renewable_origin_certified' => fake()->boolean(80),
            ],
            [
                'name' => 'Empresas Sostenibles',
                'description' => 'Tarifa corporativa con certificación de origen renovable.',
                'price_fixed_eur_month' => fake()->randomFloat(2, 15.00, 30.00),
                'price_variable_eur_kwh' => fake()->randomFloat(4, 0.10, 0.14),
                'offer_type' => 'fixed',
                'conditions_url' => fake()->url(),
                'contract_length_months' => 36,
                'requires_smart_meter' => true,
                'renewable_origin_certified' => true,
            ],
        ];
    }

    /**
     * Oferta con energía renovable certificada.
     */
    public function renewable(): static
    {
        return $this->state(fn (array $attributes) => [
            'renewable_origin_certified' => true,
            'name' => 'Oferta Verde ' . fake()->word(),
            'description' => 'Energía 100% renovable certificada con Garantías de Origen.',
        ]);
    }

    /**
     * Oferta que requiere contador inteligente.
     */
    public function smartMeter(): static
    {
        return $this->state(fn (array $attributes) => [
            'requires_smart_meter' => true,
            'offer_type' => fake()->randomElement(['variable', 'hybrid']),
        ]);
    }

    /**
     * Oferta de precio fijo.
     */
    public function fixed(): static
    {
        return $this->state(fn (array $attributes) => [
            'offer_type' => 'fixed',
            'price_fixed_eur_month' => null,
            'price_variable_eur_kwh' => fake()->randomFloat(4, 0.12, 0.20),
            'contract_length_months' => fake()->randomElement([12, 24, 36]),
        ]);
    }

    /**
     * Oferta de precio variable.
     */
    public function variable(): static
    {
        return $this->state(fn (array $attributes) => [
            'offer_type' => 'variable',
            'price_fixed_eur_month' => fake()->randomFloat(2, 3.00, 8.00),
            'price_variable_eur_kwh' => null,
            'contract_length_months' => null,
        ]);
    }

    /**
     * Oferta híbrida.
     */
    public function hybrid(): static
    {
        return $this->state(fn (array $attributes) => [
            'offer_type' => 'hybrid',
            'price_fixed_eur_month' => fake()->randomFloat(2, 8.00, 20.00),
            'price_variable_eur_kwh' => fake()->randomFloat(4, 0.08, 0.15),
            'contract_length_months' => fake()->randomElement([12, 18, 24]),
        ]);
    }

    /**
     * Oferta para autoconsumo.
     */
    public function selfConsumption(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Autoconsumo ' . fake()->word(),
            'description' => 'Tarifa especializada para instalaciones de autoconsumo solar.',
            'requires_smart_meter' => true,
            'renewable_origin_certified' => true,
            'price_variable_eur_kwh' => fake()->randomFloat(4, 0.08, 0.12),
        ]);
    }
}
