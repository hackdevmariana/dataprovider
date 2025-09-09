<?php

namespace Database\Seeders;

use App\Models\OfferHistory;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OfferHistorySeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Crear ofertas activas (60%)
        OfferHistory::factory()
            ->count(30)
            ->active()
            ->create();

        // Crear ofertas destacadas (10%)
        OfferHistory::factory()
            ->count(5)
            ->featured()
            ->create();

        // Crear ofertas renovables (15%)
        OfferHistory::factory()
            ->count(8)
            ->renewable()
            ->active()
            ->create();

        // Crear ofertas expiradas (15%)
        OfferHistory::factory()
            ->count(8)
            ->expired()
            ->create();

        // Crear algunas ofertas específicas de empresas conocidas
        $this->createSpecificOffers();
    }

    private function createSpecificOffers(): void
    {
        // Oferta destacada de Iberdrola
        OfferHistory::create([
            'company_name' => 'Iberdrola',
            'offer_type' => 'renewable',
            'offer_details' => [
                'name' => 'Plan Verde Iberdrola',
                'description' => 'Energía 100% renovable con certificado de garantía de origen',
                'contract_length' => '24 months',
                'renewable_percentage' => 100,
                'bonus_features' => [
                    'Certificado de energía verde',
                    'App móvil gratuita',
                    'Atención al cliente 24/7',
                    'Descuento por fidelidad'
                ],
            ],
            'valid_from' => now()->subDays(30),
            'valid_until' => now()->addYear(),
            'price' => 0.0895,
            'currency' => 'EUR',
            'unit' => 'kWh',
            'terms_conditions' => [
                'minimum_consumption' => '2000 kWh/year',
                'penalty_fee' => '150.00 EUR',
                'billing_frequency' => 'monthly',
                'payment_methods' => ['Direct debit', 'Credit card', 'Online payment'],
            ],
            'status' => 'active',
            'restrictions' => [
                'geographic_limitations' => 'Disponible en toda España peninsular',
                'technical_requirements' => 'Contador digital recomendado',
            ],
            'is_featured' => true,
        ]);

        // Oferta económica de Endesa
        OfferHistory::create([
            'company_name' => 'Endesa',
            'offer_type' => 'hybrid',
            'offer_details' => [
                'name' => 'Tarifa One Luz',
                'description' => 'Precio fijo durante 12 meses, después precio variable',
                'contract_length' => '12 months',
                'renewable_percentage' => 30,
                'bonus_features' => [
                    'Precio fijo garantizado',
                    'Sin permanencia',
                    'Gestión online',
                    'Descuento en servicios adicionales'
                ],
            ],
            'valid_from' => now()->subDays(15),
            'valid_until' => now()->addMonths(6),
            'price' => 0.0750,
            'currency' => 'EUR',
            'unit' => 'kWh',
            'terms_conditions' => [
                'minimum_consumption' => '1000 kWh/year',
                'penalty_fee' => '100.00 EUR',
                'billing_frequency' => 'monthly',
                'payment_methods' => ['Direct debit', 'Bank transfer'],
            ],
            'status' => 'active',
            'restrictions' => [
                'consumption_requirements' => 'Máximo 10000 kWh/year',
            ],
            'is_featured' => false,
        ]);

        // Oferta premium de Naturgy
        OfferHistory::create([
            'company_name' => 'Naturgy',
            'offer_type' => 'gas',
            'offer_details' => [
                'name' => 'Plan Premium Gas',
                'description' => 'Gas natural con servicio premium y mantenimiento incluido',
                'contract_length' => '36 months',
                'renewable_percentage' => 0,
                'bonus_features' => [
                    'Mantenimiento incluido',
                    'Instalación gratuita',
                    'Servicio de emergencias 24h',
                    'Descuento en calefacción'
                ],
            ],
            'valid_from' => now()->subDays(7),
            'valid_until' => now()->addYears(2),
            'price' => 0.0450,
            'currency' => 'EUR',
            'unit' => 'kWh',
            'terms_conditions' => [
                'minimum_consumption' => '5000 kWh/year',
                'penalty_fee' => '200.00 EUR',
                'billing_frequency' => 'bimonthly',
                'payment_methods' => ['Direct debit', 'Credit card', 'Online payment'],
            ],
            'status' => 'active',
            'restrictions' => [
                'geographic_limitations' => 'Solo en zonas con red de gas natural',
                'technical_requirements' => 'Instalación de gas natural requerida',
            ],
            'is_featured' => true,
        ]);

        // Oferta solar de Holaluz
        OfferHistory::create([
            'company_name' => 'Holaluz',
            'offer_type' => 'solar',
            'offer_details' => [
                'name' => 'Energía Solar Holaluz',
                'description' => 'Energía solar con excedentes compensados',
                'contract_length' => '12 months',
                'renewable_percentage' => 100,
                'bonus_features' => [
                    'Compensación de excedentes',
                    'Instalación de paneles',
                    'Monitorización en tiempo real',
                    'Garantía de 25 años'
                ],
            ],
            'valid_from' => now()->subDays(45),
            'valid_until' => now()->addMonths(9),
            'price' => 0.1200,
            'currency' => 'EUR',
            'unit' => 'kWh',
            'terms_conditions' => [
                'minimum_consumption' => '3000 kWh/year',
                'penalty_fee' => '300.00 EUR',
                'billing_frequency' => 'monthly',
                'payment_methods' => ['Direct debit', 'Online payment'],
            ],
            'status' => 'active',
            'restrictions' => [
                'geographic_limitations' => 'Requiere orientación sur y sin sombras',
                'technical_requirements' => 'Instalación de paneles solares',
            ],
            'is_featured' => true,
        ]);

        // Oferta expirada de ejemplo
        OfferHistory::create([
            'company_name' => 'Repsol',
            'offer_type' => 'electricity',
            'offer_details' => [
                'name' => 'Tarifa Repsol Plus',
                'description' => 'Oferta promocional con descuento del 15%',
                'contract_length' => '18 months',
                'renewable_percentage' => 20,
                'bonus_features' => [
                    'Descuento promocional',
                    'Sin permanencia',
                    'App móvil'
                ],
            ],
            'valid_from' => now()->subYear(),
            'valid_until' => now()->subMonths(3),
            'price' => 0.0820,
            'currency' => 'EUR',
            'unit' => 'kWh',
            'terms_conditions' => [
                'minimum_consumption' => '1500 kWh/year',
                'penalty_fee' => '120.00 EUR',
                'billing_frequency' => 'monthly',
                'payment_methods' => ['Direct debit', 'Credit card'],
            ],
            'status' => 'expired',
            'restrictions' => [
                'geographic_limitations' => 'Solo para nuevos clientes',
            ],
            'is_featured' => false,
        ]);
    }
}
