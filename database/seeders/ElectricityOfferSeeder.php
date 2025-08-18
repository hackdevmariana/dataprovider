<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ElectricityOffer;
use App\Models\EnergyCompany;
use App\Models\PriceUnit;

class ElectricityOfferSeeder extends Seeder
{
    /**
     * Ejecutar el seeder para ofertas eléctricas.
     */
    public function run(): void
    {
        $this->command->info('Creando ofertas eléctricas españolas...');

        // Verificar que existan compañías energéticas
        $energyCompanies = EnergyCompany::all();
        if ($energyCompanies->isEmpty()) {
            $this->command->warn('No se encontraron compañías energéticas. Creando algunas...');
            EnergyCompany::factory()->count(5)->create();
            $energyCompanies = EnergyCompany::all();
        }

        // Verificar que existan unidades de precio
        $priceUnits = PriceUnit::all();
        if ($priceUnits->isEmpty()) {
            $this->command->warn('No se encontraron unidades de precio. Las ofertas se crearán sin unidad específica.');
        }

        // Crear ofertas principales para cada compañía
        $totalOffers = 0;
        
        foreach ($energyCompanies as $company) {
            $offersCount = $this->getOffersCountForCompany($company);
            
            // Ofertas fijas
            $fixedOffers = ElectricityOffer::factory()
                ->count($offersCount['fixed'])
                ->fixed()
                ->create(['energy_company_id' => $company->id]);
            
            // Ofertas variables
            $variableOffers = ElectricityOffer::factory()
                ->count($offersCount['variable'])
                ->variable()
                ->create(['energy_company_id' => $company->id]);
            
            // Ofertas híbridas
            $hybridOffers = ElectricityOffer::factory()
                ->count($offersCount['hybrid'])
                ->hybrid()
                ->create(['energy_company_id' => $company->id]);
            
            $companyTotal = $fixedOffers->count() + $variableOffers->count() + $hybridOffers->count();
            $totalOffers += $companyTotal;
            
            $this->command->info("✅ {$company->name}: {$companyTotal} ofertas");
        }

        // Crear ofertas especializadas
        $renewableOffers = ElectricityOffer::factory()
            ->count(15)
            ->renewable()
            ->create();
        
        $totalOffers += $renewableOffers->count();
        $this->command->info("✅ Creadas {$renewableOffers->count()} ofertas renovables especializadas");

        // Crear ofertas para autoconsumo
        $selfConsumptionOffers = ElectricityOffer::factory()
            ->count(8)
            ->selfConsumption()
            ->create();
        
        $totalOffers += $selfConsumptionOffers->count();
        $this->command->info("✅ Creadas {$selfConsumptionOffers->count()} ofertas para autoconsumo");

        // Crear ofertas con contador inteligente
        $smartMeterOffers = ElectricityOffer::factory()
            ->count(12)
            ->smartMeter()
            ->create();
        
        $totalOffers += $smartMeterOffers->count();
        $this->command->info("✅ Creadas {$smartMeterOffers->count()} ofertas con contador inteligente");

        $this->command->info("🎉 Total de ofertas eléctricas creadas: {$totalOffers}");
        
        // Mostrar estadísticas
        $this->showStatistics();
    }

    /**
     * Determinar cuántas ofertas crear para cada compañía.
     */
    private function getOffersCountForCompany(EnergyCompany $company): array
    {
        // Compañías grandes tienen más ofertas
        $isLargeCompany = in_array(strtolower($company->name), [
            'iberdrola', 'endesa', 'naturgy', 'repsol', 'totalenergies'
        ]);

        if ($isLargeCompany) {
            return [
                'fixed' => fake()->numberBetween(3, 6),
                'variable' => fake()->numberBetween(2, 4),
                'hybrid' => fake()->numberBetween(1, 3),
            ];
        } else {
            return [
                'fixed' => fake()->numberBetween(1, 3),
                'variable' => fake()->numberBetween(1, 2),
                'hybrid' => fake()->numberBetween(0, 2),
            ];
        }
    }

    /**
     * Mostrar estadísticas de las ofertas creadas.
     */
    private function showStatistics(): void
    {
        $stats = [
            'Total ofertas' => ElectricityOffer::count(),
            'Ofertas fijas' => ElectricityOffer::where('offer_type', 'fixed')->count(),
            'Ofertas variables' => ElectricityOffer::where('offer_type', 'variable')->count(),
            'Ofertas híbridas' => ElectricityOffer::where('offer_type', 'hybrid')->count(),
            'Con energía renovable' => ElectricityOffer::where('renewable_origin_certified', true)->count(),
            'Requieren contador inteligente' => ElectricityOffer::where('requires_smart_meter', true)->count(),
            'Contratos a 12 meses' => ElectricityOffer::where('contract_length_months', 12)->count(),
            'Contratos a 24 meses' => ElectricityOffer::where('contract_length_months', 24)->count(),
            'Sin permanencia' => ElectricityOffer::whereNull('contract_length_months')->count(),
        ];

        $this->command->info("\n📊 Estadísticas de ofertas eléctricas:");
        foreach ($stats as $type => $count) {
            $this->command->info("   {$type}: {$count}");
        }

        // Estadísticas de precios
        $avgFixedPrice = ElectricityOffer::whereNotNull('price_variable_eur_kwh')
                                       ->avg('price_variable_eur_kwh');
        
        $avgMonthlyPrice = ElectricityOffer::whereNotNull('price_fixed_eur_month')
                                         ->avg('price_fixed_eur_month');

        if ($avgFixedPrice) {
            $this->command->info("   Precio medio kWh: " . round($avgFixedPrice, 4) . " €/kWh");
        }
        
        if ($avgMonthlyPrice) {
            $this->command->info("   Precio medio mensual: " . round($avgMonthlyPrice, 2) . " €/mes");
        }

        // Compañías con más ofertas
        $topCompanies = EnergyCompany::withCount('electricityOffers')
                                    ->orderBy('electricity_offers_count', 'desc')
                                    ->limit(5)
                                    ->get();

        if ($topCompanies->isNotEmpty()) {
            $this->command->info("\n🏢 Compañías con más ofertas:");
            foreach ($topCompanies as $company) {
                $this->command->info("   {$company->name}: {$company->electricity_offers_count} ofertas");
            }
        }

        // Porcentaje de ofertas sostenibles
        $renewablePercentage = ElectricityOffer::count() > 0 ? 
            round((ElectricityOffer::where('renewable_origin_certified', true)->count() / ElectricityOffer::count()) * 100, 1) : 0;
        
        $this->command->info("\n🌱 Porcentaje de ofertas renovables: {$renewablePercentage}%");
    }
}
