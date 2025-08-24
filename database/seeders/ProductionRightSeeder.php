<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductionRight;
use App\Models\User;
use App\Models\EnergyInstallation;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ProductionRightSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('⚡ Sembrando derechos de producción energética...');

        // Obtener usuarios y instalaciones disponibles
        $users = User::take(10)->get();
        $installations = EnergyInstallation::take(20)->get();

        if ($users->isEmpty() || $installations->isEmpty()) {
            $this->command->error('❌ No hay usuarios o instalaciones disponibles para crear ProductionRight');
            return;
        }

        $productionRights = [
            // ===== DERECHOS DE PRODUCCIÓN SOLAR =====
            [
                'title' => 'Derechos de Producción Solar - Parque Fotovoltaico Andalucía',
                'right_type' => 'energy_production',
                'total_capacity_kw' => 5000.00,
                'available_capacity_kw' => 3500.00,
                'reserved_capacity_kw' => 1000.00,
                'sold_capacity_kw' => 500.00,
                'estimated_annual_production_kwh' => 8750000.00,
                'guaranteed_annual_production_kwh' => 7875000.00,
                'valid_from' => '2024-01-01',
                'valid_until' => '2034-01-01',
                'duration_years' => 10,
                'renewable_right' => true,
                'renewal_period_years' => 5,
                'pricing_model' => 'fixed_price_kwh',
                'price_per_kwh' => 0.0850,
                'market_premium_percentage' => 15.00,
                'minimum_guaranteed_price' => 0.0750,
                'maximum_price_cap' => 0.1200,
                'upfront_payment' => 50000.00,
                'periodic_payment' => 5000.00,
                'payment_frequency' => 'monthly',
                'security_deposit' => 10000.00,
                'production_guaranteed' => true,
                'production_guarantee_percentage' => 90.00,
                'insurance_included' => true,
                'is_transferable' => true,
                'max_transfers' => 3,
                'status' => 'available',
                'is_active' => true,
                'is_featured' => true,
                'allow_partial_sales' => true,
                'minimum_sale_capacity_kw' => 100.00,
                'description' => 'Derechos de producción del mayor parque fotovoltaico de Andalucía. Instalación de 5MW con tecnología de última generación y garantía de producción del 90%.',
                'regulatory_framework' => 'Real Decreto 244/2019',
                'grid_code_compliant' => true,
                'certifications' => ['ISO 14001', 'ISO 50001', 'UNE-EN 16247'],
            ],
            [
                'title' => 'Excedentes Solares Residenciales - Comunidad Valenciana',
                'right_type' => 'excess_energy',
                'total_capacity_kw' => 250.00,
                'available_capacity_kw' => 200.00,
                'reserved_capacity_kw' => 50.00,
                'sold_capacity_kw' => 0.00,
                'estimated_annual_production_kwh' => 350000.00,
                'guaranteed_annual_production_kwh' => 315000.00,
                'valid_from' => '2024-03-01',
                'valid_until' => '2029-03-01',
                'duration_years' => 5,
                'renewable_right' => true,
                'renewal_period_years' => 3,
                'pricing_model' => 'market_price',
                'price_per_kwh' => 0.1200,
                'market_premium_percentage' => 8.00,
                'minimum_guaranteed_price' => 0.1000,
                'upfront_payment' => 5000.00,
                'periodic_payment' => 300.00,
                'payment_frequency' => 'monthly',
                'security_deposit' => 2000.00,
                'production_guaranteed' => false,
                'production_guarantee_percentage' => 75.00,
                'insurance_included' => false,
                'is_transferable' => true,
                'max_transfers' => 5,
                'status' => 'available',
                'is_active' => true,
                'is_featured' => false,
                'allow_partial_sales' => true,
                'minimum_sale_capacity_kw' => 25.00,
                'description' => 'Derechos sobre excedentes de instalaciones solares residenciales en la Comunidad Valenciana. Ideal para pequeñas inversiones en energía renovable.',
                'regulatory_framework' => 'Real Decreto 244/2019',
                'grid_code_compliant' => true,
                'certifications' => ['UNE-EN 16247'],
            ],
            [
                'title' => 'Créditos de Carbono - Parque Eólico Galicia',
                'right_type' => 'carbon_credits',
                'total_capacity_kw' => 8000.00,
                'available_capacity_kw' => 6000.00,
                'reserved_capacity_kw' => 1500.00,
                'sold_capacity_kw' => 500.00,
                'estimated_annual_production_kwh' => 20000000.00,
                'guaranteed_annual_production_kwh' => 18000000.00,
                'valid_from' => '2024-01-01',
                'valid_until' => '2039-01-01',
                'duration_years' => 15,
                'renewable_right' => true,
                'renewal_period_years' => 10,
                'pricing_model' => 'premium_over_market',
                'price_per_kwh' => 0.0950,
                'market_premium_percentage' => 25.00,
                'minimum_guaranteed_price' => 0.0800,
                'maximum_price_cap' => 0.1500,
                'upfront_payment' => 80000.00,
                'periodic_payment' => 8000.00,
                'payment_frequency' => 'quarterly',
                'security_deposit' => 15000.00,
                'production_guaranteed' => true,
                'production_guarantee_percentage' => 95.00,
                'insurance_included' => true,
                'is_transferable' => true,
                'max_transfers' => 2,
                'status' => 'available',
                'is_active' => true,
                'is_featured' => true,
                'allow_partial_sales' => false,
                'minimum_sale_capacity_kw' => 500.00,
                'description' => 'Derechos de créditos de carbono del mayor parque eólico de Galicia. Certificación VERRA y garantía de producción del 95%.',
                'regulatory_framework' => 'Protocolo de Kioto',
                'grid_code_compliant' => true,
                'certifications' => ['VERRA', 'ISO 14064', 'Gold Standard'],
            ],

            // ===== DERECHOS DE INYECCIÓN A RED =====
            [
                'title' => 'Derechos de Inyección a Red - Solar Industrial Madrid',
                'right_type' => 'grid_injection',
                'total_capacity_kw' => 3000.00,
                'available_capacity_kw' => 2000.00,
                'reserved_capacity_kw' => 800.00,
                'sold_capacity_kw' => 200.00,
                'estimated_annual_production_kwh' => 5250000.00,
                'guaranteed_annual_production_kwh' => 4725000.00,
                'valid_from' => '2024-02-01',
                'valid_until' => '2032-02-01',
                'duration_years' => 8,
                'renewable_right' => true,
                'renewal_period_years' => 4,
                'pricing_model' => 'performance_based',
                'price_per_kwh' => 0.1100,
                'market_premium_percentage' => 20.00,
                'minimum_guaranteed_price' => 0.0900,
                'maximum_price_cap' => 0.1400,
                'upfront_payment' => 30000.00,
                'periodic_payment' => 4000.00,
                'payment_frequency' => 'monthly',
                'security_deposit' => 8000.00,
                'production_guaranteed' => true,
                'production_guarantee_percentage' => 88.00,
                'insurance_included' => true,
                'is_transferable' => true,
                'max_transfers' => 4,
                'status' => 'under_negotiation',
                'is_active' => true,
                'is_featured' => false,
                'allow_partial_sales' => true,
                'minimum_sale_capacity_kw' => 200.00,
                'description' => 'Derechos de inyección a red de instalación solar industrial en Madrid. Conexión directa a red de alta tensión.',
                'regulatory_framework' => 'Real Decreto 244/2019',
                'grid_code_compliant' => true,
                'certifications' => ['ISO 14001', 'UNE-EN 16247'],
            ],

            // ===== BATERÍA VIRTUAL =====
            [
                'title' => 'Batería Virtual - Almacenamiento Solar Cataluña',
                'right_type' => 'virtual_battery',
                'total_capacity_kw' => 1500.00,
                'available_capacity_kw' => 1200.00,
                'reserved_capacity_kw' => 300.00,
                'sold_capacity_kw' => 0.00,
                'estimated_annual_production_kwh' => 2625000.00,
                'guaranteed_annual_production_kwh' => 2362500.00,
                'valid_from' => '2024-04-01',
                'valid_until' => '2030-04-01',
                'duration_years' => 6,
                'renewable_right' => true,
                'renewal_period_years' => 3,
                'pricing_model' => 'subscription_model',
                'price_per_kwh' => 0.1300,
                'market_premium_percentage' => 18.00,
                'minimum_guaranteed_price' => 0.1100,
                'maximum_price_cap' => 0.1600,
                'upfront_payment' => 20000.00,
                'periodic_payment' => 2500.00,
                'payment_frequency' => 'monthly',
                'security_deposit' => 5000.00,
                'production_guaranteed' => true,
                'production_guarantee_percentage' => 92.00,
                'insurance_included' => true,
                'is_transferable' => true,
                'max_transfers' => 6,
                'status' => 'available',
                'is_active' => true,
                'is_featured' => true,
                'allow_partial_sales' => true,
                'minimum_sale_capacity_kw' => 100.00,
                'description' => 'Servicio de batería virtual para almacenamiento de energía solar. Permite gestionar picos de demanda y optimizar el consumo.',
                'regulatory_framework' => 'Real Decreto 244/2019',
                'grid_code_compliant' => true,
                'certifications' => ['ISO 50001', 'UNE-EN 16247'],
            ],

            // ===== RESPUESTA A LA DEMANDA =====
            [
                'title' => 'Respuesta a la Demanda - Eólica Navarra',
                'right_type' => 'demand_response',
                'total_capacity_kw' => 4000.00,
                'available_capacity_kw' => 3000.00,
                'reserved_capacity_kw' => 800.00,
                'sold_capacity_kw' => 200.00,
                'estimated_annual_production_kwh' => 10000000.00,
                'guaranteed_annual_production_kwh' => 9000000.00,
                'valid_from' => '2024-01-01',
                'valid_until' => '2035-01-01',
                'duration_years' => 11,
                'renewable_right' => true,
                'renewal_period_years' => 5,
                'pricing_model' => 'revenue_sharing',
                'price_per_kwh' => 0.1000,
                'market_premium_percentage' => 22.00,
                'minimum_guaranteed_price' => 0.0850,
                'maximum_price_cap' => 0.1300,
                'upfront_payment' => 40000.00,
                'periodic_payment' => 6000.00,
                'payment_frequency' => 'quarterly',
                'security_deposit' => 12000.00,
                'production_guaranteed' => true,
                'production_guarantee_percentage' => 93.00,
                'insurance_included' => true,
                'is_transferable' => true,
                'max_transfers' => 3,
                'status' => 'available',
                'is_active' => true,
                'is_featured' => false,
                'allow_partial_sales' => true,
                'minimum_sale_capacity_kw' => 300.00,
                'description' => 'Derechos de respuesta a la demanda del parque eólico de Navarra. Participación en servicios auxiliares del sistema eléctrico.',
                'regulatory_framework' => 'Real Decreto 244/2019',
                'grid_code_compliant' => true,
                'certifications' => ['ISO 14001', 'ISO 50001'],
            ],

            // ===== DERECHOS DE CAPACIDAD =====
            [
                'title' => 'Derechos de Capacidad - Hidroeléctrica Asturias',
                'right_type' => 'capacity_rights',
                'total_capacity_kw' => 6000.00,
                'available_capacity_kw' => 4500.00,
                'reserved_capacity_kw' => 1200.00,
                'sold_capacity_kw' => 300.00,
                'estimated_annual_production_kwh' => 15000000.00,
                'guaranteed_annual_production_kwh' => 13500000.00,
                'valid_from' => '2024-01-01',
                'valid_until' => '2040-01-01',
                'duration_years' => 16,
                'renewable_right' => true,
                'renewal_period_years' => 8,
                'pricing_model' => 'hybrid',
                'price_per_kwh' => 0.0750,
                'market_premium_percentage' => 12.00,
                'minimum_guaranteed_price' => 0.0650,
                'maximum_price_cap' => 0.1000,
                'upfront_payment' => 60000.00,
                'periodic_payment' => 7500.00,
                'payment_frequency' => 'biannual',
                'security_deposit' => 15000.00,
                'production_guaranteed' => true,
                'production_guarantee_percentage' => 96.00,
                'insurance_included' => true,
                'is_transferable' => true,
                'max_transfers' => 2,
                'status' => 'available',
                'is_active' => true,
                'is_featured' => true,
                'allow_partial_sales' => false,
                'minimum_sale_capacity_kw' => 500.00,
                'description' => 'Derechos de capacidad de la central hidroeléctrica de Asturias. Garantía de disponibilidad del 96% y precio estable.',
                'regulatory_framework' => 'Real Decreto 244/2019',
                'grid_code_compliant' => true,
                'certifications' => ['ISO 14001', 'ISO 50001', 'UNE-EN 16247'],
            ],

            // ===== CERTIFICADOS VERDES =====
            [
                'title' => 'Certificados Verdes - Biomasa Castilla y León',
                'right_type' => 'green_certificates',
                'total_capacity_kw' => 2000.00,
                'available_capacity_kw' => 1500.00,
                'reserved_capacity_kw' => 400.00,
                'sold_capacity_kw' => 100.00,
                'estimated_annual_production_kwh' => 14000000.00,
                'guaranteed_annual_production_kwh' => 12600000.00,
                'valid_from' => '2024-03-01',
                'valid_until' => '2032-03-01',
                'duration_years' => 8,
                'renewable_right' => true,
                'renewal_period_years' => 4,
                'pricing_model' => 'auction_based',
                'price_per_kwh' => 0.1150,
                'market_premium_percentage' => 28.00,
                'minimum_guaranteed_price' => 0.0950,
                'maximum_price_cap' => 0.1500,
                'upfront_payment' => 25000.00,
                'periodic_payment' => 3500.00,
                'payment_frequency' => 'monthly',
                'security_deposit' => 8000.00,
                'production_guaranteed' => true,
                'production_guarantee_percentage' => 89.00,
                'insurance_included' => true,
                'is_transferable' => true,
                'max_transfers' => 5,
                'status' => 'available',
                'is_active' => true,
                'is_featured' => false,
                'allow_partial_sales' => true,
                'minimum_sale_capacity_kw' => 150.00,
                'description' => 'Certificados verdes de la planta de biomasa de Castilla y León. Combustible sostenible y certificación FSC.',
                'regulatory_framework' => 'Real Decreto 244/2019',
                'grid_code_compliant' => true,
                'certifications' => ['FSC', 'ISO 14001', 'PEFC'],
            ],

            // ===== DERECHOS MIXTOS =====
            [
                'title' => 'Derechos Mixtos - Parque Híbrido Extremadura',
                'right_type' => 'other',
                'total_capacity_kw' => 7000.00,
                'available_capacity_kw' => 5000.00,
                'reserved_capacity_kw' => 1500.00,
                'sold_capacity_kw' => 500.00,
                'estimated_annual_production_kwh' => 17500000.00,
                'guaranteed_annual_production_kwh' => 15750000.00,
                'valid_from' => '2024-01-01',
                'valid_until' => '2038-01-01',
                'duration_years' => 14,
                'renewable_right' => true,
                'renewal_period_years' => 7,
                'pricing_model' => 'hybrid',
                'price_per_kwh' => 0.0900,
                'market_premium_percentage' => 16.00,
                'minimum_guaranteed_price' => 0.0750,
                'maximum_price_cap' => 0.1200,
                'upfront_payment' => 70000.00,
                'periodic_payment' => 9000.00,
                'payment_frequency' => 'quarterly',
                'security_deposit' => 18000.00,
                'production_guaranteed' => true,
                'production_guarantee_percentage' => 94.00,
                'insurance_included' => true,
                'is_transferable' => true,
                'max_transfers' => 3,
                'status' => 'available',
                'is_active' => true,
                'is_featured' => true,
                'allow_partial_sales' => true,
                'minimum_sale_capacity_kw' => 400.00,
                'description' => 'Derechos mixtos del parque híbrido solar-eólico de Extremadura. Combinación de tecnologías para máxima eficiencia.',
                'regulatory_framework' => 'Real Decreto 244/2019',
                'grid_code_compliant' => true,
                'certifications' => ['ISO 14001', 'ISO 50001', 'UNE-EN 16247'],
            ],
        ];

        $createdCount = 0;
        $updatedCount = 0;

        foreach ($productionRights as $index => $rightData) {
            // Generar slug único
            $rightData['slug'] = Str::slug($rightData['title']);
            
            // Generar identificador único del derecho
            $rightData['right_identifier'] = 'PR-' . strtoupper(Str::random(8));
            
            // Asignar vendedor aleatorio
            $rightData['seller_id'] = $users->random()->id;
            
            // Asignar instalación si está disponible
            if ($index < $installations->count()) {
                $rightData['installation_id'] = $installations[$index]->id;
            }
            
            // Calcular campos derivados
            $rightData['reserved_capacity_kw'] = $rightData['reserved_capacity_kw'] ?? 0;
            $rightData['sold_capacity_kw'] = $rightData['sold_capacity_kw'] ?? 0;
            $rightData['current_transfers'] = 0;
            $rightData['performance_ratio'] = 100.00;
            $rightData['views_count'] = rand(10, 500);
            $rightData['inquiries_count'] = rand(0, 50);
            $rightData['offers_received'] = rand(0, 20);
            
            // Generar precios de mercado realistas
            $rightData['highest_offer_price'] = $rightData['price_per_kwh'] * (1 + (rand(-10, 20) / 100));
            $rightData['average_market_price'] = $rightData['price_per_kwh'] * (1 + (rand(-5, 15) / 100));
            
            // Generar datos de producción realistas
            $rightData['current_month_production_kwh'] = $rightData['estimated_annual_production_kwh'] / 12 * (rand(80, 120) / 100);
            $rightData['ytd_production_kwh'] = $rightData['estimated_annual_production_kwh'] * (rand(70, 110) / 100);
            $rightData['lifetime_production_kwh'] = $rightData['estimated_annual_production_kwh'] * rand(1, 3);
            
            // Generar datos JSON
            $rightData['price_escalation_terms'] = [
                'annual_increase' => rand(2, 5) . '%',
                'inflation_adjustment' => true,
                'market_index_linking' => false,
            ];
            
            $rightData['payment_terms'] = [
                'payment_method' => 'bank_transfer',
                'currency' => 'EUR',
                'late_payment_penalty' => '5% monthly',
                'early_payment_discount' => '2% if paid within 15 days',
            ];
            
            $rightData['penalty_clauses'] = [
                'production_shortfall' => 'Compensación proporcional al déficit',
                'delayed_payment' => 'Intereses de demora del 5% anual',
                'contract_breach' => 'Penalización del 20% del valor anual',
            ];
            
            $rightData['risk_allocation'] = [
                'force_majeure' => 'Compartido 50/50',
                'technical_failure' => 'Vendedor asume 80%',
                'market_volatility' => 'Comprador asume 70%',
            ];
            
            $rightData['buyer_rights'] = [
                'energy_delivery' => 'Derecho a recibir energía según contrato',
                'quality_standards' => 'Energía conforme a estándares de red',
                'compensation' => 'Indemnización por incumplimientos',
            ];
            
            $rightData['buyer_obligations'] = [
                'timely_payment' => 'Pago puntual según términos acordados',
                'grid_compliance' => 'Cumplimiento de códigos de red',
                'reporting' => 'Informes mensuales de consumo',
            ];
            
            $rightData['seller_rights'] = [
                'payment_collection' => 'Derecho a cobro según contrato',
                'production_control' => 'Control sobre instalación y producción',
                'contract_modification' => 'Modificación con consentimiento mutuo',
            ];
            
            $rightData['seller_obligations'] = [
                'energy_delivery' => 'Entrega de energía según garantías',
                'maintenance' => 'Mantenimiento de instalación',
                'compliance' => 'Cumplimiento regulatorio',
            ];
            
            $rightData['transfer_restrictions'] = [
                'approval_required' => 'Aprobación del vendedor necesaria',
                'eligible_buyers' => 'Solo entidades cualificadas',
                'documentation' => 'Documentación legal completa',
            ];
            
            $rightData['monthly_production_history'] = [];
            for ($i = 0; $i < 12; $i++) {
                $month = Carbon::now()->subMonths($i);
                $rightData['monthly_production_history'][$month->format('Y-m')] = [
                    'production_kwh' => $rightData['estimated_annual_production_kwh'] / 12 * (rand(70, 130) / 100),
                    'performance_ratio' => rand(85, 115),
                    'availability' => rand(90, 99),
                ];
            }
            
            $rightData['applicable_regulations'] = [
                'renewable_energy' => 'Real Decreto 244/2019',
                'grid_connection' => 'Real Decreto 1955/2000',
                'environmental' => 'Ley 21/2013',
                'energy_efficiency' => 'Real Decreto 56/2016',
            ];
            
            $rightData['certifications'] = $rightData['certifications'] ?? ['ISO 14001'];
            
            $rightData['legal_documents'] = [
                'contract_template' => 'Plantilla estándar AEE',
                'technical_specifications' => 'Especificaciones técnicas detalladas',
                'regulatory_compliance' => 'Certificado de cumplimiento regulatorio',
                'insurance_certificate' => 'Certificado de seguro',
            ];
            
            $rightData['signature_details'] = [
                'signature_method' => 'Firma electrónica cualificada',
                'certification_authority' => 'FNMT',
                'signature_timestamp' => Carbon::now()->toISOString(),
                'signature_validity' => 'Válida hasta ' . Carbon::now()->addYears(5)->format('Y-m-d'),
            ];

            $right = ProductionRight::updateOrCreate(
                [
                    'slug' => $rightData['slug'],
                ],
                $rightData
            );

            if ($right->wasRecentlyCreated) {
                $createdCount++;
            } else {
                $updatedCount++;
            }
        }

        // Mostrar estadísticas
        $this->command->info("✅ Derechos creados: {$createdCount}");
        $this->command->info("🔄 Derechos actualizados: {$updatedCount}");
        $this->command->info("📊 Total de derechos: " . ProductionRight::count());

        // Mostrar resumen por tipo de derecho
        $this->command->info("\n📋 Resumen por tipo de derecho:");
        $types = ProductionRight::all()->groupBy('right_type');
        foreach ($types as $type => $rights) {
            $totalCapacity = $rights->sum('total_capacity_kw');
            $avgPrice = $rights->avg('price_per_kwh');
            $this->command->info("  {$type}: {$rights->count()} derechos, capacidad total: {$totalCapacity} kW, precio medio: " . number_format($avgPrice, 4) . " €/kWh");
        }

        // Mostrar resumen por estado
        $this->command->info("\n📊 Resumen por estado:");
        $statuses = ProductionRight::all()->groupBy('status');
        foreach ($statuses as $status => $rights) {
            $this->command->info("  {$status}: {$rights->count()} derechos");
        }

        // Mostrar resumen por modelo de precios
        $this->command->info("\n💰 Resumen por modelo de precios:");
        $pricingModels = ProductionRight::all()->groupBy('pricing_model');
        foreach ($pricingModels as $model => $rights) {
            $avgPrice = $rights->avg('price_per_kwh');
            $this->command->info("  {$model}: {$rights->count()} derechos, precio medio: " . number_format($avgPrice, 4) . " €/kWh");
        }

        // Mostrar algunos derechos destacados
        $this->command->info("\n🔬 Derechos destacados:");
        $featuredRights = ProductionRight::where('is_featured', true)->take(3)->get();
        foreach ($featuredRights as $right) {
            $this->command->info("  ⚡ {$right->title}");
            $this->command->info("     🏷️  Tipo: {$right->right_type}");
            $this->command->info("     📏 Capacidad: {$right->total_capacity_kw} kW");
            $this->command->info("     💰 Precio: {$right->price_per_kwh} €/kWh");
            $this->command->info("     📅 Duración: {$right->duration_years} años");
            $this->command->info("     ---");
        }

        $this->command->info("\n🎯 Seeder de ProductionRight completado exitosamente!");
    }
}
