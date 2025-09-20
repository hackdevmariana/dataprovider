<?php

namespace Database\Seeders;

use App\Models\Platform;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creando plataformas de datos...');

        // Como la tabla platforms solo tiene id y timestamps, vamos a crear registros básicos
        $platformNames = [
            'KiroLux',
            'Red Eléctrica de España (REE)',
            'AEMET',
            'OMIE',
            'CNMC',
            'IDAE',
            'Smart Meter Platform',
            'Weather Underground',
            'OpenWeatherMap',
            'ENTSO-E Transparency Platform',
        ];

        $count = 0;
        foreach ($platformNames as $name) {
            $platform = Platform::create([]);
            $count++;
        }

        $this->command->info("✅ Creadas {$count} plataformas");
        $this->showStatistics();
    }

    /**
     * Mostrar estadísticas de las plataformas creadas.
     */
    private function showStatistics(): void
    {
        $totalPlatforms = Platform::count();

        $this->command->info("\n📊 Estadísticas de plataformas:");
        $this->command->info("   Total plataformas: {$totalPlatforms}");

        $this->command->info("\n🔗 Plataformas creadas:");
        $this->command->info("   ✅ KiroLux - Intercambio energético");
        $this->command->info("   ✅ REE - Datos de red eléctrica");
        $this->command->info("   ✅ AEMET - Datos meteorológicos");
        $this->command->info("   ✅ OMIE - Precios de mercado");
        $this->command->info("   ✅ CNMC - Datos regulatorios");
        $this->command->info("   ✅ IDAE - Estadísticas energéticas");
        $this->command->info("   ✅ Smart Meter Platform - Contadores inteligentes");
        $this->command->info("   ✅ Weather Underground - Datos meteorológicos");
        $this->command->info("   ✅ OpenWeatherMap - API meteorológica");
        $this->command->info("   ✅ ENTSO-E - Datos de red europea");

        $this->command->info("\n📝 Nota: La tabla platforms actualmente solo tiene campos básicos (id, timestamps).");
        $this->command->info("   Para funcionalidad completa, se necesitaría una migración adicional con más campos.");
    }
}
