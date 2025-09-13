<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductionRight;
use App\Models\User;
use App\Models\EnergyInstallation;
use Carbon\Carbon;

class ProductionRightSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('⚡ Sembrando derechos de producción energética...');

        $users = User::take(20)->get();
        $installations = EnergyInstallation::take(15)->get();

        if ($users->isEmpty() || $installations->isEmpty()) {
            $this->command->error('❌ Faltan usuarios o instalaciones. Ejecuta los seeders correspondientes.');
            return;
        }

        $createdCount = 0;

        foreach ($installations as $installation) {
            $seller = $users->random();
            $totalCapacity = $installation->capacity_kw ?? rand(10, 1000);
            
            $rightData = [
                'seller_id' => $seller->id,
                'installation_id' => $installation->id,
                'title' => "Derecho de Producción - {$installation->name}",
                'description' => 'Derecho de producción energética con garantías y certificaciones oficiales.',
                'right_type' => 'solar_production_right',
                'total_capacity_kw' => $totalCapacity,
                'available_capacity_kw' => rand(0, $totalCapacity),
                'reserved_capacity_kw' => 0,
                'sold_capacity_kw' => 0,
                'estimated_annual_production_kwh' => $totalCapacity * rand(1200, 1800),
                'guaranteed_annual_production_kwh' => $totalCapacity * rand(1000, 1500),
                'valid_from' => Carbon::now()->addDays(rand(1, 30)),
                'valid_until' => Carbon::now()->addYears(rand(15, 25)),
                'duration_years' => rand(15, 25),
                'renewable_right' => rand(0, 1) == 1,
                'pricing_model' => 'fixed_price_kwh',
                'price_per_kwh' => rand(8, 15) / 100,
                'minimum_guaranteed_price' => rand(6, 12) / 100,
                'maximum_price_cap' => rand(15, 20) / 100,
                'upfront_payment' => rand(5000, 50000),
                'periodic_payment' => rand(100, 1000),
                'payment_frequency' => 'monthly',
                'security_deposit' => rand(1000, 10000),
                'production_guaranteed' => rand(0, 1) == 1,
                'production_guarantee_percentage' => rand(85, 95),
                'insurance_included' => rand(0, 1) == 1,
                'is_transferable' => rand(0, 1) == 1,
                'max_transfers' => rand(1, 5),
                'current_transfers' => 0,
                'transfer_fee_percentage' => rand(1, 5),
                'status' => 'available',
                'status_notes' => 'Derecho disponible para compra inmediata.',
                'current_month_production_kwh' => $totalCapacity * rand(80, 120),
                'ytd_production_kwh' => $totalCapacity * rand(500, 800),
                'lifetime_production_kwh' => $totalCapacity * rand(1000, 5000),
                'performance_ratio' => rand(85, 105),
                'regulatory_framework' => 'RD 244/2019',
                'grid_code_compliant' => rand(0, 1) == 1,
                'views_count' => rand(0, 1000),
                'inquiries_count' => rand(0, 50),
                'offers_received' => rand(0, 10),
                'highest_offer_price' => rand(8, 15) / 100,
                'average_market_price' => rand(10, 12) / 100,
                'is_active' => true,
                'is_featured' => rand(0, 1) == 1,
                'auto_accept_offers' => rand(0, 1) == 1,
                'auto_accept_threshold' => rand(8, 12) / 100,
                'allow_partial_sales' => rand(0, 1) == 1,
                'minimum_sale_capacity_kw' => rand(1, 5),
            ];

            ProductionRight::create($rightData);
            $createdCount++;
        }

        $this->command->info("✅ Derechos de producción creados: {$createdCount}");
        $this->command->info("📊 Total de derechos: " . ProductionRight::count());
        $this->command->info("🎯 Seeder de ProductionRight completado exitosamente!");
    }
}
