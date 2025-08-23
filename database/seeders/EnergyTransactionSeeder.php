<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EnergyTransaction;
use App\Models\User;
use App\Models\EnergyInstallation;
use Carbon\Carbon;

class EnergyTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('⚡ Sembrando transacciones energéticas...');

        // Obtener usuarios disponibles
        $users = User::take(10)->get();
        if ($users->isEmpty()) {
            $this->command->error('❌ No se encontraron usuarios. Ejecuta primero el UserSeeder.');
            return;
        }

        // Obtener instalaciones disponibles
        $installations = EnergyInstallation::take(20)->get();
        if ($installations->isEmpty()) {
            $this->command->error('❌ No se encontraron instalaciones. Ejecuta primero el EnergyInstallationSeeder.');
            return;
        }

        $this->command->info("👥 Usuarios disponibles: {$users->count()}");
        $this->command->info("⚡ Instalaciones disponibles: {$installations->count()}");

        // Crear transacciones simuladas
        $transactions = $this->generateEnergyTransactions($users, $installations);

        $createdCount = 0;
        $updatedCount = 0;

        foreach ($transactions as $transactionData) {
            $transaction = EnergyTransaction::updateOrCreate(
                [
                    'producer_id' => $transactionData['producer_id'],
                    'consumer_id' => $transactionData['consumer_id'],
                    'installation_id' => $transactionData['installation_id'],
                    'transaction_datetime' => $transactionData['transaction_datetime'],
                ],
                $transactionData
            );

            if ($transaction->wasRecentlyCreated) {
                $createdCount++;
            } else {
                $updatedCount++;
            }
        }

        // Mostrar estadísticas
        $this->command->info("✅ Transacciones creadas: {$createdCount}");
        $this->command->info("🔄 Transacciones actualizadas: {$updatedCount}");
        $this->command->info("📊 Total de transacciones: " . EnergyTransaction::count());

        // Mostrar resumen por tipo de instalación
        $this->command->info("\n📋 Resumen por tipo de instalación:");
        $installationsByType = EnergyTransaction::with('installation')
            ->get()
            ->groupBy('installation.type');
        
        foreach ($installationsByType as $type => $transactions) {
            $totalKwh = $transactions->sum('amount_kwh');
            $avgPrice = $transactions->avg('price_per_kwh');
            $this->command->info("  {$type}: {$transactions->count()} transacciones, {$totalKwh} kWh, precio medio: " . number_format($avgPrice, 4) . " €/kWh");
        }

        // Mostrar algunos ejemplos
        $this->command->info("\n🔬 Ejemplos de transacciones creadas:");
        $sampleTransactions = EnergyTransaction::with(['producer', 'consumer', 'installation'])->take(5)->get();
        foreach ($sampleTransactions as $transaction) {
            $this->command->info("  📋 ID: {$transaction->id}");
            $this->command->info("     👤 Productor: " . ($transaction->producer?->name ?? 'N/A'));
            $this->command->info("     👥 Consumidor: " . ($transaction->consumer?->name ?? 'N/A'));
            $this->command->info("     ⚡ Instalación: " . ($transaction->installation?->name ?? 'N/A'));
            $this->command->info("     💰 Cantidad: {$transaction->amount_kwh} kWh");
            $this->command->info("     💵 Precio: {$transaction->price_per_kwh} €/kWh");
            $this->command->info("     📅 Fecha: " . $transaction->transaction_datetime->format('d/m/Y H:i'));
            $this->command->info("     💸 Total: " . number_format($transaction->amount_kwh * $transaction->price_per_kwh, 2) . " €");
            $this->command->info("     ---");
        }

        $this->command->info("\n🎯 Seeder de EnergyTransaction completado exitosamente!");
    }

    /**
     * Genera transacciones energéticas simuladas
     */
    private function generateEnergyTransactions($users, $installations): array
    {
        $transactions = [];
        $now = Carbon::now();
        
        // Generar transacciones para los últimos 6 meses
        for ($i = 0; $i < 50; $i++) {
            // Seleccionar usuarios aleatorios (productor y consumidor diferentes)
            $producer = $users->random();
            $consumer = $users->where('id', '!=', $producer->id)->random();
            
            // Seleccionar instalación aleatoria
            $installation = $installations->random();
            
            // Generar fecha aleatoria en los últimos 6 meses
            $transactionDate = $now->copy()->subDays(rand(0, 180))->subHours(rand(0, 23))->subMinutes(rand(0, 59));
            
            // Generar cantidad de energía realista basada en la capacidad de la instalación
            $maxDailyProduction = $installation->capacity_kw * 24 * 0.8; // 80% de eficiencia
            $amountKwh = $this->generateRealisticEnergyAmount($installation, $transactionDate);
            
            // Generar precio realista basado en el tipo de instalación y la fecha
            $pricePerKwh = $this->generateRealisticPrice($installation, $transactionDate);
            
            $transactions[] = [
                'producer_id' => $producer->id,
                'consumer_id' => $consumer->id,
                'installation_id' => $installation->id,
                'amount_kwh' => $amountKwh,
                'price_per_kwh' => $pricePerKwh,
                'transaction_datetime' => $transactionDate,
            ];
        }

        // Generar transacciones específicas para diferentes escenarios
        $transactions = array_merge($transactions, $this->generateSpecificScenarios($users, $installations, $now));

        return $transactions;
    }

    /**
     * Genera cantidad de energía realista basada en el tipo de instalación
     */
    private function generateRealisticEnergyAmount($installation, $date): float
    {
        $hour = $date->hour;
        $month = $date->month;
        $capacity = $installation->capacity_kw;
        
        // Factores estacionales y horarios
        $seasonalFactor = $this->getSeasonalFactor($month);
        $hourlyFactor = $this->getHourlyFactor($hour, $installation->type);
        
        // Producción base diaria (considerando eficiencia)
        $baseProduction = $capacity * 24 * 0.8; // 80% de eficiencia promedio
        
        // Aplicar factores
        $production = $baseProduction * $seasonalFactor * $hourlyFactor;
        
        // Agregar variabilidad realista (±20%)
        $variability = rand(80, 120) / 100;
        
        return round($production * $variability, 2);
    }

    /**
     * Factor estacional basado en el mes
     */
    private function getSeasonalFactor(int $month): float
    {
        return match($month) {
            12, 1, 2 => 0.6,  // Invierno - menor producción
            3, 4, 5 => 0.8,   // Primavera - producción media
            6, 7, 8 => 1.0,   // Verano - máxima producción
            9, 10, 11 => 0.7, // Otoño - producción media-baja
            default => 0.8,
        };
    }

    /**
     * Factor horario basado en el tipo de instalación
     */
    private function getHourlyFactor(int $hour, string $type): float
    {
        return match($type) {
            'solar' => match(true) {
                $hour >= 6 && $hour <= 18 => 1.0,  // Día - máxima producción
                $hour >= 5 && $hour <= 19 => 0.7,  // Amanecer/atardecer
                default => 0.0,                     // Noche - sin producción
            },
            'wind' => match(true) {
                $hour >= 22 || $hour <= 6 => 1.2,  // Noche - mayor viento
                $hour >= 7 && $hour <= 21 => 0.8,  // Día - menor viento
                default => 1.0,
            },
            'hydro' => 1.0,  // Hidroeléctrica - producción constante
            'biomass' => 1.0, // Biomasa - producción constante
            default => 0.8,   // Otros tipos
        };
    }

    /**
     * Genera precio realista basado en el tipo de instalación y la fecha
     */
    private function generateRealisticPrice($installation, $date): float
    {
        $basePrice = $this->getBasePriceByType($installation->type);
        $date = Carbon::parse($date);
        
        // Factores de precio
        $seasonalPriceFactor = $this->getSeasonalPriceFactor($date->month);
        $hourlyPriceFactor = $this->getHourlyPriceFactor($date->hour);
        
        // Precio base con factores aplicados
        $price = $basePrice * $seasonalPriceFactor * $hourlyPriceFactor;
        
        // Agregar pequeña variabilidad (±10%)
        $variability = rand(90, 110) / 100;
        
        return round($price * $variability, 4);
    }

    /**
     * Precio base por tipo de instalación
     */
    private function getBasePriceByType(string $type): float
    {
        return match($type) {
            'solar' => 0.08,      // Solar - más barato
            'wind' => 0.10,       // Eólica - precio medio
            'hydro' => 0.12,      // Hidroeléctrica - precio medio-alto
            'biomass' => 0.15,    // Biomasa - más caro
            default => 0.11,      // Otros tipos
        };
    }

    /**
     * Factor de precio estacional
     */
    private function getSeasonalPriceFactor(int $month): float
    {
        return match($month) {
            12, 1, 2 => 1.2,  // Invierno - precios más altos
            3, 4, 5 => 1.0,   // Primavera - precios normales
            6, 7, 8 => 0.9,   // Verano - precios más bajos
            9, 10, 11 => 1.1, // Otoño - precios ligeramente altos
            default => 1.0,
        };
    }

    /**
     * Factor de precio horario
     */
    private function getHourlyPriceFactor(int $hour): float
    {
        return match(true) {
            $hour >= 8 && $hour <= 10 => 1.3,   // Hora pico mañana
            $hour >= 18 && $hour <= 21 => 1.4,  // Hora pico tarde
            $hour >= 0 && $hour <= 6 => 0.7,    // Hora valle noche
            default => 1.0,                      // Hora normal
        };
    }

    /**
     * Genera transacciones para escenarios específicos
     */
    private function generateSpecificScenarios($users, $installations, $now): array
    {
        $scenarios = [];
        
        // Escenario 1: Transacciones de alta producción solar en verano
        $solarInstallations = $installations->where('type', 'solar')->take(3);
        foreach ($solarInstallations as $installation) {
            $producer = $users->random();
            $consumer = $users->where('id', '!=', $producer->id)->random();
            
            $scenarios[] = [
                'producer_id' => $producer->id,
                'consumer_id' => $consumer->id,
                'installation_id' => $installation->id,
                'amount_kwh' => round($installation->capacity_kw * 8 * 0.9, 2), // 8 horas de sol, 90% eficiencia
                'price_per_kwh' => 0.075, // Precio reducido por alta producción
                'transaction_datetime' => $now->copy()->subDays(rand(1, 30))->setHour(14)->setMinute(rand(0, 59)),
            ];
        }
        
        // Escenario 2: Transacciones nocturnas de eólica
        $windInstallations = $installations->where('type', 'wind')->take(2);
        foreach ($windInstallations as $installation) {
            $producer = $users->random();
            $consumer = $users->where('id', '!=', $producer->id)->random();
            
            $scenarios[] = [
                'producer_id' => $producer->id,
                'consumer_id' => $consumer->id,
                'installation_id' => $installation->id,
                'amount_kwh' => round($installation->capacity_kw * 6 * 1.1, 2), // 6 horas nocturnas, 110% eficiencia
                'price_per_kwh' => 0.085, // Precio ligeramente alto por demanda nocturna
                'transaction_datetime' => $now->copy()->subDays(rand(1, 30))->setHour(2)->setMinute(rand(0, 59)),
            ];
        }
        
        // Escenario 3: Transacciones de biomasa constantes
        $biomassInstallations = $installations->where('type', 'biomass')->take(2);
        foreach ($biomassInstallations as $installation) {
            $producer = $users->random();
            $consumer = $users->where('id', '!=', $producer->id)->random();
            
            $scenarios[] = [
                'producer_id' => $producer->id,
                'consumer_id' => $consumer->id,
                'installation_id' => $installation->id,
                'amount_kwh' => round($installation->capacity_kw * 12 * 0.95, 2), // 12 horas, 95% eficiencia
                'price_per_kwh' => 0.145, // Precio alto por producción constante
                'transaction_datetime' => $now->copy()->subDays(rand(1, 30))->setHour(rand(8, 20))->setMinute(rand(0, 59)),
            ];
        }
        
        // Escenario 4: Transacciones de hidroeléctrica
        $hydroInstallations = $installations->where('type', 'hydro')->take(2);
        foreach ($hydroInstallations as $installation) {
            $producer = $users->random();
            $consumer = $users->where('id', '!=', $producer->id)->random();
            
            $scenarios[] = [
                'producer_id' => $producer->id,
                'consumer_id' => $consumer->id,
                'installation_id' => $installation->id,
                'amount_kwh' => round($installation->capacity_kw * 16 * 0.98, 2), // 16 horas, 98% eficiencia
                'price_per_kwh' => 0.115, // Precio medio por producción estable
                'transaction_datetime' => $now->copy()->subDays(rand(1, 30))->setHour(rand(0, 23))->setMinute(rand(0, 59)),
            ];
        }
        
        return $scenarios;
    }
}
