<?php

namespace Database\Seeders;

use App\Models\OfferComparison;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Carbon\Carbon;

class OfferComparisonSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Obtener algunos usuarios existentes o crear uno si no existen
        $users = User::take(5)->get();
        if ($users->isEmpty()) {
            $users = collect([User::factory()->create()]);
        }

        // Crear comparaciones de electricidad (40%)
        OfferComparison::factory()
            ->count(20)
            ->electricity()
            ->create();

        // Crear comparaciones de gas (25%)
        OfferComparison::factory()
            ->count(12)
            ->gas()
            ->create();

        // Crear comparaciones renovables (20%)
        OfferComparison::factory()
            ->count(10)
            ->renewable()
            ->create();

        // Crear comparaciones con altos ahorros (15%)
        OfferComparison::factory()
            ->count(8)
            ->highSavings()
            ->create();

        // Crear comparaciones con bajos ahorros (10%)
        OfferComparison::factory()
            ->count(5)
            ->lowSavings()
            ->create();

        // Crear comparaciones compartidas (20%)
        OfferComparison::factory()
            ->count(10)
            ->shared()
            ->create();

        // Crear comparaciones recientes (30%)
        OfferComparison::factory()
            ->count(15)
            ->recent()
            ->create();

        // Crear comparaciones residenciales (40%)
        OfferComparison::factory()
            ->count(20)
            ->residential()
            ->create();

        // Crear comparaciones comerciales (25%)
        OfferComparison::factory()
            ->count(12)
            ->commercial()
            ->create();

        // Crear comparaciones industriales (15%)
        OfferComparison::factory()
            ->count(8)
            ->industrial()
            ->create();

        // Crear algunas comparaciones específicas y realistas
        $this->createSpecificComparisons($users);
    }

    private function createSpecificComparisons($users): void
    {
        // Comparación de electricidad residencial con alta variabilidad
        OfferComparison::create([
            'user_id' => $users->first()->id,
            'energy_type' => 'electricity',
            'consumption_profile' => 'residential',
            'offers_compared' => [
                [
                    'offer_id' => 'offer_001',
                    'company_name' => 'Iberdrola',
                    'offer_name' => 'Plan Verde Iberdrola',
                    'price' => 0.0895,
                    'contract_length' => 24,
                    'is_renewable' => true,
                    'features' => ['Certificado verde', 'App móvil', 'Atención 24/7'],
                ],
                [
                    'offer_id' => 'offer_002',
                    'company_name' => 'Endesa',
                    'offer_name' => 'Tarifa One Luz',
                    'price' => 0.0750,
                    'contract_length' => 12,
                    'is_renewable' => false,
                    'features' => ['Precio fijo', 'Sin permanencia', 'Gestión online'],
                ],
                [
                    'offer_id' => 'offer_003',
                    'company_name' => 'Holaluz',
                    'offer_name' => 'Energía Solar Holaluz',
                    'price' => 0.1200,
                    'contract_length' => 12,
                    'is_renewable' => true,
                    'features' => ['Compensación excedentes', 'Instalación paneles', 'Monitorización'],
                ],
                [
                    'offer_id' => 'offer_004',
                    'company_name' => 'Naturgy',
                    'offer_name' => 'Tarifa Gas & Luz',
                    'price' => 0.0950,
                    'contract_length' => 18,
                    'is_renewable' => false,
                    'features' => ['Descuento gas', 'Mantenimiento incluido', 'Servicio premium'],
                ],
            ],
            'best_offer_id' => 'offer_002',
            'savings_amount' => 0.0450,
            'savings_percentage' => 37.5,
            'comparison_criteria' => [
                'price' => ['name' => 'Precio por kWh', 'weight' => 0.4, 'description' => 'Factor más importante'],
                'renewable_energy' => ['name' => 'Energía renovable', 'weight' => 0.2, 'description' => 'Certificación verde'],
                'contract_length' => ['name' => 'Duración contrato', 'weight' => 0.15, 'description' => 'Flexibilidad contractual'],
                'customer_service' => ['name' => 'Atención al cliente', 'weight' => 0.15, 'description' => 'Calidad del servicio'],
                'additional_services' => ['name' => 'Servicios adicionales', 'weight' => 0.1, 'description' => 'Apps, herramientas, etc.'],
            ],
            'comparison_date' => now()->subDays(2),
            'is_shared' => true,
        ]);

        // Comparación de gas comercial
        OfferComparison::create([
            'user_id' => $users->first()->id,
            'energy_type' => 'gas',
            'consumption_profile' => 'commercial',
            'offers_compared' => [
                [
                    'offer_id' => 'gas_001',
                    'company_name' => 'Naturgy',
                    'offer_name' => 'Plan Premium Gas',
                    'price' => 0.0450,
                    'contract_length' => 36,
                    'is_renewable' => false,
                    'features' => ['Mantenimiento incluido', 'Instalación gratuita', 'Emergencias 24h'],
                ],
                [
                    'offer_id' => 'gas_002',
                    'company_name' => 'Repsol',
                    'offer_name' => 'Gas Repsol Plus',
                    'price' => 0.0520,
                    'contract_length' => 24,
                    'is_renewable' => false,
                    'features' => ['Descuento promocional', 'Sin permanencia', 'App móvil'],
                ],
                [
                    'offer_id' => 'gas_003',
                    'company_name' => 'Endesa',
                    'offer_name' => 'Gas Endesa',
                    'price' => 0.0480,
                    'contract_length' => 18,
                    'is_renewable' => false,
                    'features' => ['Precio competitivo', 'Gestión online', 'Facturación digital'],
                ],
            ],
            'best_offer_id' => 'gas_001',
            'savings_amount' => 0.0070,
            'savings_percentage' => 13.5,
            'comparison_criteria' => [
                'price' => ['name' => 'Precio por kWh', 'weight' => 0.5, 'description' => 'Factor principal'],
                'contract_length' => ['name' => 'Duración contrato', 'weight' => 0.2, 'description' => 'Estabilidad'],
                'additional_services' => ['name' => 'Servicios incluidos', 'weight' => 0.2, 'description' => 'Mantenimiento y soporte'],
                'flexibility' => ['name' => 'Flexibilidad', 'weight' => 0.1, 'description' => 'Condiciones contractuales'],
            ],
            'comparison_date' => now()->subDays(5),
            'is_shared' => false,
        ]);

        // Comparación de energía renovable industrial
        OfferComparison::create([
            'user_id' => $users->first()->id,
            'energy_type' => 'renewable',
            'consumption_profile' => 'industrial',
            'offers_compared' => [
                [
                    'offer_id' => 'ren_001',
                    'company_name' => 'Som Energia',
                    'offer_name' => 'Energía Cooperativa',
                    'price' => 0.0850,
                    'contract_length' => 12,
                    'is_renewable' => true,
                    'features' => ['100% renovable', 'Cooperativa', 'Transparencia total'],
                ],
                [
                    'offer_id' => 'ren_002',
                    'company_name' => 'Holaluz',
                    'offer_name' => 'Solar Industrial',
                    'price' => 0.1100,
                    'contract_length' => 24,
                    'is_renewable' => true,
                    'features' => ['Instalación solar', 'Compensación excedentes', 'Monitorización'],
                ],
                [
                    'offer_id' => 'ren_003',
                    'company_name' => 'Gana Energía',
                    'offer_name' => 'Verde Industrial',
                    'price' => 0.0920,
                    'contract_length' => 18,
                    'is_renewable' => true,
                    'features' => ['Certificado verde', 'Precio fijo', 'Sin permanencia'],
                ],
                [
                    'offer_id' => 'ren_004',
                    'company_name' => 'Podo',
                    'offer_name' => 'Renovable Podo',
                    'price' => 0.0880,
                    'contract_length' => 12,
                    'is_renewable' => true,
                    'features' => ['App móvil', 'Gestión online', 'Atención personalizada'],
                ],
            ],
            'best_offer_id' => 'ren_001',
            'savings_amount' => 0.0250,
            'savings_percentage' => 22.7,
            'comparison_criteria' => [
                'price' => ['name' => 'Precio por kWh', 'weight' => 0.3, 'description' => 'Factor importante'],
                'renewable_energy' => ['name' => 'Certificación renovable', 'weight' => 0.3, 'description' => 'Garantía de origen'],
                'contract_length' => ['name' => 'Duración contrato', 'weight' => 0.2, 'description' => 'Flexibilidad'],
                'reputation' => ['name' => 'Reputación empresa', 'weight' => 0.2, 'description' => 'Confianza y estabilidad'],
            ],
            'comparison_date' => now()->subDays(1),
            'is_shared' => true,
        ]);

        // Comparación de petróleo con baja variabilidad
        OfferComparison::create([
            'user_id' => $users->first()->id,
            'energy_type' => 'oil',
            'consumption_profile' => 'commercial',
            'offers_compared' => [
                [
                    'offer_id' => 'oil_001',
                    'company_name' => 'Repsol',
                    'offer_name' => 'Fuel Oil Repsol',
                    'price' => 0.1250,
                    'contract_length' => 12,
                    'is_renewable' => false,
                    'features' => ['Precio competitivo', 'Entrega programada', 'Soporte técnico'],
                ],
                [
                    'offer_id' => 'oil_002',
                    'company_name' => 'Cepsa',
                    'offer_name' => 'Oil Cepsa Pro',
                    'price' => 0.1280,
                    'contract_length' => 12,
                    'is_renewable' => false,
                    'features' => ['Calidad garantizada', 'Red de distribución', 'Servicio 24h'],
                ],
            ],
            'best_offer_id' => 'oil_001',
            'savings_amount' => 0.0030,
            'savings_percentage' => 2.3,
            'comparison_criteria' => [
                'price' => ['name' => 'Precio por litro', 'weight' => 0.6, 'description' => 'Factor principal'],
                'quality' => ['name' => 'Calidad del producto', 'weight' => 0.3, 'description' => 'Especificaciones técnicas'],
                'delivery' => ['name' => 'Servicio de entrega', 'weight' => 0.1, 'description' => 'Logística y soporte'],
            ],
            'comparison_date' => now()->subDays(7),
            'is_shared' => false,
        ]);

        // Comparación de carbón industrial
        OfferComparison::create([
            'user_id' => $users->first()->id,
            'energy_type' => 'coal',
            'consumption_profile' => 'industrial',
            'offers_compared' => [
                [
                    'offer_id' => 'coal_001',
                    'company_name' => 'Carbones del Norte',
                    'offer_name' => 'Carbón Industrial Premium',
                    'price' => 0.0780,
                    'contract_length' => 24,
                    'is_renewable' => false,
                    'features' => ['Calidad premium', 'Entrega garantizada', 'Soporte técnico'],
                ],
                [
                    'offer_id' => 'coal_002',
                    'company_name' => 'Minas de Asturias',
                    'offer_name' => 'Carbón Asturiano',
                    'price' => 0.0820,
                    'contract_length' => 18,
                    'is_renewable' => false,
                    'features' => ['Origen local', 'Precio estable', 'Logística optimizada'],
                ],
                [
                    'offer_id' => 'coal_003',
                    'company_name' => 'Carboníferas del Sur',
                    'offer_name' => 'Carbón Sur',
                    'price' => 0.0850,
                    'contract_length' => 12,
                    'is_renewable' => false,
                    'features' => ['Flexibilidad contractual', 'Precio variable', 'Servicio personalizado'],
                ],
            ],
            'best_offer_id' => 'coal_001',
            'savings_amount' => 0.0070,
            'savings_percentage' => 8.2,
            'comparison_criteria' => [
                'price' => ['name' => 'Precio por tonelada', 'weight' => 0.4, 'description' => 'Factor principal'],
                'quality' => ['name' => 'Calidad del carbón', 'weight' => 0.3, 'description' => 'Poder calorífico'],
                'logistics' => ['name' => 'Logística', 'weight' => 0.2, 'description' => 'Entrega y almacenamiento'],
                'contract_length' => ['name' => 'Duración contrato', 'weight' => 0.1, 'description' => 'Estabilidad de precios'],
            ],
            'comparison_date' => now()->subDays(10),
            'is_shared' => true,
        ]);
    }
}
