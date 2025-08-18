<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ScrapingSource;

class ScrapingSourceSeeder extends Seeder
{
    /**
     * Ejecutar el seeder para fuentes de scraping.
     */
    public function run(): void
    {
        $this->command->info('Creando fuentes de scraping para medios españoles...');

        // Crear fuentes principales activas
        $mainSources = ScrapingSource::factory()
            ->count(15)
            ->active()
            ->create();

        $this->command->info("✅ Creadas {$mainSources->count()} fuentes principales activas");

        // Crear fuentes especializadas en sostenibilidad
        $sustainabilitySources = ScrapingSource::factory()
            ->count(8)
            ->sustainability()
            ->active()
            ->create();

        $this->command->info("✅ Creadas {$sustainabilitySources->count()} fuentes especializadas en sostenibilidad");

        // Crear fuentes de alta frecuencia
        $highFrequencySources = ScrapingSource::factory()
            ->count(5)
            ->highFrequency()
            ->create();

        $this->command->info("✅ Creadas {$highFrequencySources->count()} fuentes de alta frecuencia");

        // Crear algunas fuentes inactivas (para testing)
        $inactiveSources = ScrapingSource::factory()
            ->count(3)
            ->inactive()
            ->create();

        $this->command->info("✅ Creadas {$inactiveSources->count()} fuentes inactivas (para testing)");

        $totalSources = ScrapingSource::count();
        $this->command->info("🎉 Total de fuentes de scraping creadas: {$totalSources}");
        
        // Mostrar estadísticas
        $this->showStatistics();
    }

    /**
     * Mostrar estadísticas de las fuentes creadas.
     */
    private function showStatistics(): void
    {
        $stats = [
            'Total fuentes' => ScrapingSource::count(),
            'Fuentes activas' => ScrapingSource::where('is_active', true)->count(),
            'Fuentes inactivas' => ScrapingSource::where('is_active', false)->count(),
            'Scraping diario' => ScrapingSource::where('frequency', 'daily')->count(),
            'Scraping semanal' => ScrapingSource::where('frequency', 'weekly')->count(),
            'Scraping mensual' => ScrapingSource::where('frequency', 'monthly')->count(),
            'Blogs' => ScrapingSource::where('type', 'blog')->count(),
            'Periódicos' => ScrapingSource::where('type', 'newspaper')->count(),
            'Otros tipos' => ScrapingSource::where('type', 'other')->count(),
            'Con scraping reciente' => ScrapingSource::where('last_scraped_at', '>', now()->subDays(7))->count(),
        ];

        $this->command->info("\n📊 Estadísticas de fuentes de scraping:");
        foreach ($stats as $type => $count) {
            $this->command->info("   {$type}: {$count}");
        }

        // Mostrar fuentes más activas
        $dailySources = ScrapingSource::where('is_active', true)
                                     ->where('frequency', 'daily')
                                     ->count();
        
        if ($dailySources > 0) {
            $this->command->info("\n⚡ Fuentes de scraping diario configuradas: {$dailySources}");
        }

        // Información sobre cobertura temática
        $sustainabilitySources = ScrapingSource::where('source_type_description', 'LIKE', '%sostenibilidad%')
                                              ->orWhere('source_type_description', 'LIKE', '%medio ambiente%')
                                              ->orWhere('source_type_description', 'LIKE', '%energía%')
                                              ->count();
        
        $this->command->info("🌱 Fuentes especializadas en sostenibilidad: {$sustainabilitySources}");
    }
}
