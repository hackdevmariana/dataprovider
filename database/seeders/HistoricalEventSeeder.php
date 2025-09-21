<?php

namespace Database\Seeders;

use App\Models\HistoricalEvent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HistoricalEventSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creando eventos históricos...');

        $historicalEvents = [
            [
                'title' => 'Caída del Imperio Romano de Occidente',
                'description' => 'El último emperador romano de Occidente, Rómulo Augústulo, fue depuesto por el líder germánico Odoacro.',
                'event_date' => '476-09-04',
                'era' => 'ancient',
                'category' => 'political',
                'location' => 'Roma',
                'country' => 'Italia',
                'key_figures' => ['Rómulo Augústulo', 'Odoacro'],
                'consequences' => ['Fin del Imperio Romano', 'Inicio de la Edad Media'],
                'significance_level' => 'critical',
                'sources' => ['Jordanes', 'Procopio de Cesarea'],
                'is_verified' => true,
                'related_events' => ['Saqueo de Roma (410)'],
            ],
            [
                'title' => 'Descubrimiento de América',
                'description' => 'Cristóbal Colón llegó a las costas de América, iniciando la era de la exploración europea.',
                'event_date' => '1492-10-12',
                'era' => 'renaissance',
                'category' => 'exploration',
                'location' => 'Guanahani',
                'country' => 'Bahamas',
                'key_figures' => ['Cristóbal Colón', 'Isabel I de Castilla'],
                'consequences' => ['Apertura de rutas comerciales', 'Colonización europea'],
                'significance_level' => 'critical',
                'sources' => ['Diario de Colón', 'Bartolomé de las Casas'],
                'is_verified' => true,
                'related_events' => ['Tratado de Tordesillas'],
            ],
            [
                'title' => 'Revolución Francesa',
                'description' => 'Movimiento que derrocó la monarquía absoluta y estableció los principios de libertad e igualdad.',
                'event_date' => '1789-07-14',
                'era' => 'modern',
                'category' => 'political',
                'location' => 'París',
                'country' => 'Francia',
                'key_figures' => ['Robespierre', 'Napoleón Bonaparte'],
                'consequences' => ['Fin del Antiguo Régimen', 'Declaración de Derechos'],
                'significance_level' => 'critical',
                'sources' => ['Declaración de Derechos', 'Actas de la Asamblea'],
                'is_verified' => true,
                'related_events' => ['Toma de la Bastilla'],
            ],
        ];

        $count = 0;
        foreach ($historicalEvents as $eventData) {
            HistoricalEvent::create($eventData);
            $count++;
        }

        // Crear eventos adicionales aleatorios
        $eras = ['ancient', 'medieval', 'renaissance', 'modern', 'contemporary'];
        $categories = ['political', 'military', 'cultural', 'religious', 'scientific'];
        $significanceLevels = ['critical', 'major', 'moderate', 'minor'];

        for ($i = 0; $i < 12; $i++) {
            $era = fake()->randomElement($eras);
            $year = $this->generateYearForEra($era);
            
            HistoricalEvent::create([
                'title' => fake()->sentence(4),
                'description' => fake()->paragraph(2),
                'event_date' => $year . '-' . sprintf('%02d', fake()->month()) . '-' . sprintf('%02d', fake()->dayOfMonth()),
                'era' => $era,
                'category' => fake()->randomElement($categories),
                'location' => fake()->city(),
                'country' => fake()->country(),
                'key_figures' => fake()->randomElements(['Líder histórico', 'General', 'Científico'], rand(1, 3)),
                'consequences' => fake()->randomElements(['Cambio político', 'Avance tecnológico'], rand(1, 2)),
                'significance_level' => fake()->randomElement($significanceLevels),
                'sources' => fake()->randomElements(['Documento histórico', 'Crónica'], rand(1, 2)),
                'is_verified' => fake()->boolean(70),
                'related_events' => fake()->randomElements(['Evento relacionado'], rand(0, 1)),
            ]);
            $count++;
        }

        $this->command->info("✅ Creados {$count} eventos históricos");
        $this->showStatistics();
    }

    private function generateYearForEra(string $era): int
    {
        return match ($era) {
            'ancient' => fake()->numberBetween(1, 476),
            'medieval' => fake()->numberBetween(476, 1453),
            'renaissance' => fake()->numberBetween(1453, 1600),
            'modern' => fake()->numberBetween(1600, 1900),
            'contemporary' => fake()->numberBetween(1900, now()->year),
            default => fake()->numberBetween(1000, now()->year),
        };
    }

    private function showStatistics(): void
    {
        $total = HistoricalEvent::count();
        $verified = HistoricalEvent::where('is_verified', true)->count();
        
        $this->command->info("\n📊 Estadísticas:");
        $this->command->info("   Total eventos: {$total}");
        $this->command->info("   Verificados: {$verified}");
        
        $eras = HistoricalEvent::selectRaw('era, COUNT(*) as count')->groupBy('era')->get();
        $this->command->info("\n🏛️ Por era:");
        foreach ($eras as $era) {
            $this->command->info("   {$era->era}: {$era->count}");
        }
    }
}