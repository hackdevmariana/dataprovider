<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MediaOutlet;
use App\Models\Municipality;

class MediaOutletSeeder extends Seeder
{
    /**
     * Ejecutar el seeder para medios de comunicación.
     */
    public function run(): void
    {
        $this->command->info('Creando medios de comunicación españoles...');

        // Verificar que existan municipios
        $municipalitiesCount = Municipality::count();
        if ($municipalitiesCount === 0) {
            $this->command->warn('No se encontraron municipios. Los medios se crearán sin asignación de municipio.');
        }

        // Crear medios principales españoles usando el factory
        $mediaOutlets = MediaOutlet::factory()
            ->count(15)
            ->verified()
            ->create();

        $this->command->info("✅ Creados {$mediaOutlets->count()} medios de comunicación principales");

        // Crear medios especializados en sostenibilidad
        $sustainabilityMedia = MediaOutlet::factory()
            ->count(8)
            ->sustainabilityFocused()
            ->verified()
            ->create();

        $this->command->info("✅ Creados {$sustainabilityMedia->count()} medios especializados en sostenibilidad");

        // Crear medios regionales y locales adicionales
        $regionalMedia = MediaOutlet::factory()
            ->count(12)
            ->create([
                'coverage_scope' => 'regional',
                'circulation' => fake()->numberBetween(5000, 50000),
                'monthly_pageviews' => fake()->numberBetween(100000, 2000000),
            ]);

        $this->command->info("✅ Creados {$regionalMedia->count()} medios regionales");

        // Crear blogs y medios digitales nativos
        $digitalMedia = MediaOutlet::factory()
            ->count(10)
            ->create([
                'is_digital_native' => true,
                'type' => 'blog',
                'media_category' => 'digital',
                'circulation' => null,
                'monthly_pageviews' => fake()->numberBetween(50000, 5000000),
            ]);

        $this->command->info("✅ Creados {$digitalMedia->count()} medios digitales nativos");

        $totalMedia = MediaOutlet::count();
        $this->command->info("🎉 Total de medios de comunicación creados: {$totalMedia}");
        
        // Mostrar estadísticas
        $this->showStatistics();
    }

    /**
     * Mostrar estadísticas de los medios creados.
     */
    private function showStatistics(): void
    {
        $stats = [
            'Periódicos' => MediaOutlet::where('type', 'newspaper')->count(),
            'Revistas' => MediaOutlet::where('type', 'magazine')->count(),
            'Blogs/Digitales' => MediaOutlet::where('type', 'blog')->count(),
            'TV' => MediaOutlet::where('type', 'tv')->count(),
            'Radio' => MediaOutlet::where('type', 'radio')->count(),
            'Verificados' => MediaOutlet::where('is_verified', true)->count(),
            'Nativos digitales' => MediaOutlet::where('is_digital_native', true)->count(),
            'Con enfoque sostenible' => MediaOutlet::where('covers_sustainability', true)->count(),
        ];

        $this->command->info("\n📊 Estadísticas de medios creados:");
        foreach ($stats as $type => $count) {
            $this->command->info("   {$type}: {$count}");
        }
    }
}