<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Challenge;
use Carbon\Carbon;

class ChallengeSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creando retos de gamificación para KiroLux...');

        $challenges = [
            // Retos individuales
            [
                'name' => 'Semana Verde', 'slug' => 'semana-verde',
                'description' => 'Ahorra al menos 20 kWh de energía durante una semana.',
                'instructions' => 'Usa dispositivos eficientes, apaga luces innecesarias y optimiza tu consumo energético durante 7 días consecutivos.',
                'icon' => 'leaf', 'banner_color' => '#22C55E', 'type' => 'individual',
                'category' => 'energy_saving', 'difficulty' => 'easy',
                'start_date' => Carbon::now()->addDays(1),
                'end_date' => Carbon::now()->addDays(8),
                'goals' => ['energy_saved' => 20, 'unit' => 'kWh'],
                'rewards' => ['points' => 100, 'badge' => 'Ahorrador Semanal'],
                'max_participants' => 1000, 'min_participants' => 1,
                'entry_fee' => 0, 'prize_pool' => 0, 'is_active' => true,
                'is_featured' => true, 'auto_join' => false, 'sort_order' => 1,
            ],
            [
                'name' => 'Desafío Solar 30', 'slug' => 'desafio-solar-30',
                'description' => 'Genera 100 kWh de energía solar en 30 días.',
                'instructions' => 'Maximiza la producción de tus paneles solares durante un mes completo.',
                'icon' => 'sun', 'banner_color' => '#FCD34D', 'type' => 'individual',
                'category' => 'solar_production', 'difficulty' => 'medium',
                'start_date' => Carbon::now()->addDays(3),
                'end_date' => Carbon::now()->addDays(33),
                'goals' => ['solar_produced' => 100, 'unit' => 'kWh'],
                'rewards' => ['points' => 300, 'money' => 25, 'badge' => 'Maestro Solar'],
                'max_participants' => 500, 'min_participants' => 1,
                'entry_fee' => 0, 'prize_pool' => 0, 'is_active' => true,
                'is_featured' => true, 'auto_join' => false, 'sort_order' => 2,
            ],
            [
                'name' => 'Maratón de Ahorro', 'slug' => 'maraton-ahorro',
                'description' => 'Reduce tu consumo energético en un 25% durante 2 semanas.',
                'instructions' => 'Compara tu consumo actual con el mes anterior y logra una reducción significativa.',
                'icon' => 'trending-down', 'banner_color' => '#10B981', 'type' => 'individual',
                'category' => 'energy_saving', 'difficulty' => 'hard',
                'start_date' => Carbon::now()->addDays(7),
                'end_date' => Carbon::now()->addDays(21),
                'goals' => ['energy_reduction' => 25, 'unit' => 'percent'],
                'rewards' => ['points' => 500, 'money' => 50, 'achievement' => 'Maestro del Ahorro'],
                'max_participants' => 200, 'min_participants' => 1,
                'entry_fee' => 0, 'prize_pool' => 0, 'is_active' => true,
                'is_featured' => false, 'auto_join' => false, 'sort_order' => 3,
            ],

            // Retos comunitarios
            [
                'name' => 'Comunidad Sostenible', 'slug' => 'comunidad-sostenible',
                'description' => 'Unamos fuerzas para ahorrar 10,000 kWh como comunidad.',
                'instructions' => 'Participa junto a otros usuarios de tu zona para alcanzar el objetivo colectivo.',
                'icon' => 'users', 'banner_color' => '#3B82F6', 'type' => 'community',
                'category' => 'cooperation', 'difficulty' => 'medium',
                'start_date' => Carbon::now()->addDays(2),
                'end_date' => Carbon::now()->addDays(32),
                'goals' => ['community_energy_saved' => 10000, 'unit' => 'kWh'],
                'rewards' => ['points' => 200, 'community_badge' => 'Comunidad Verde'],
                'max_participants' => null, 'min_participants' => 50,
                'entry_fee' => 0, 'prize_pool' => 0, 'is_active' => true,
                'is_featured' => true, 'auto_join' => true, 'sort_order' => 10,
            ],
            [
                'name' => 'Revolución Solar', 'slug' => 'revolucion-solar',
                'description' => 'Generemos 50,000 kWh de energía solar entre todos.',
                'instructions' => 'Cada kWh solar que generes cuenta para el objetivo global de la comunidad.',
                'icon' => 'lightning-bolt', 'banner_color' => '#F59E0B', 'type' => 'community',
                'category' => 'solar_production', 'difficulty' => 'hard',
                'start_date' => Carbon::now()->addDays(5),
                'end_date' => Carbon::now()->addDays(65),
                'goals' => ['community_solar_produced' => 50000, 'unit' => 'kWh'],
                'rewards' => ['points' => 400, 'money' => 10, 'community_achievement' => 'Revolución Solar'],
                'max_participants' => null, 'min_participants' => 100,
                'entry_fee' => 0, 'prize_pool' => 1000, 'is_active' => true,
                'is_featured' => true, 'auto_join' => false, 'sort_order' => 11,
            ],

            // Retos cooperativos
            [
                'name' => 'Liga de Cooperativas', 'slug' => 'liga-cooperativas',
                'description' => 'Competencia entre cooperativas para ver quién ahorra más energía.',
                'instructions' => 'Representa a tu cooperativa y ayuda a que sea la más eficiente energéticamente.',
                'icon' => 'shield', 'banner_color' => '#8B5CF6', 'type' => 'cooperative',
                'category' => 'cooperation', 'difficulty' => 'expert',
                'start_date' => Carbon::now()->addDays(10),
                'end_date' => Carbon::now()->addDays(40),
                'goals' => ['cooperative_efficiency' => 30, 'unit' => 'percent_improvement'],
                'rewards' => ['points' => 750, 'money' => 100, 'trophy' => 'Copa de Cooperativas'],
                'max_participants' => 10, 'min_participants' => 3,
                'entry_fee' => 50, 'prize_pool' => 500, 'is_active' => true,
                'is_featured' => true, 'auto_join' => false, 'sort_order' => 20,
            ],

            // Retos educativos
            [
                'name' => 'Academia Energética', 'slug' => 'academia-energetica',
                'description' => 'Aprende sobre energías renovables completando módulos educativos.',
                'instructions' => 'Completa 5 módulos sobre energía solar, eólica y eficiencia energética.',
                'icon' => 'academic-cap', 'banner_color' => '#06B6D4', 'type' => 'individual',
                'category' => 'education', 'difficulty' => 'easy',
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addDays(60),
                'goals' => ['modules_completed' => 5, 'unit' => 'modules'],
                'rewards' => ['points' => 150, 'certificate' => 'Experto en Energías Renovables'],
                'max_participants' => null, 'min_participants' => 1,
                'entry_fee' => 0, 'prize_pool' => 0, 'is_active' => true,
                'is_featured' => false, 'auto_join' => true, 'sort_order' => 30,
            ],

            // Retos estacionales
            [
                'name' => 'Verano Solar', 'slug' => 'verano-solar',
                'description' => 'Aprovecha al máximo el sol de verano para generar energía.',
                'instructions' => 'Durante los meses de mayor irradiación solar, maximiza tu producción fotovoltaica.',
                'icon' => 'fire', 'banner_color' => '#EF4444', 'type' => 'individual',
                'category' => 'solar_production', 'difficulty' => 'medium',
                'start_date' => Carbon::createFromDate(null, 6, 21), // 21 de junio
                'end_date' => Carbon::createFromDate(null, 9, 21),   // 21 de septiembre
                'goals' => ['summer_solar_production' => 500, 'unit' => 'kWh'],
                'rewards' => ['points' => 600, 'money' => 75, 'seasonal_badge' => 'Rey del Verano Solar'],
                'max_participants' => 1000, 'min_participants' => 1,
                'entry_fee' => 0, 'prize_pool' => 0, 'is_active' => false, // Activar en temporada
                'is_featured' => false, 'auto_join' => false, 'sort_order' => 40,
            ],

            // Retos de emergencia/especiales
            [
                'name' => 'Hora del Planeta KiroLux', 'slug' => 'hora-planeta-kirolux',
                'description' => 'Únete al evento global reduciendo tu consumo al mínimo durante una hora.',
                'instructions' => 'Apaga todos los dispositivos no esenciales durante la Hora del Planeta.',
                'icon' => 'globe', 'banner_color' => '#374151', 'type' => 'community',
                'category' => 'sustainability', 'difficulty' => 'easy',
                'start_date' => Carbon::createFromDate(null, 3, 30)->setTime(20, 30), // 30 marzo 20:30
                'end_date' => Carbon::createFromDate(null, 3, 30)->setTime(21, 30),   // 30 marzo 21:30
                'goals' => ['energy_reduction' => 90, 'unit' => 'percent'],
                'rewards' => ['points' => 100, 'special_badge' => 'Guardián del Planeta'],
                'max_participants' => null, 'min_participants' => 100,
                'entry_fee' => 0, 'prize_pool' => 0, 'is_active' => false, // Activar para el evento
                'is_featured' => false, 'auto_join' => true, 'sort_order' => 50,
            ],
        ];

        $createdCount = 0;
        foreach ($challenges as $challengeData) {
            $challenge = Challenge::firstOrCreate(
                ['slug' => $challengeData['slug']],
                $challengeData
            );
            
            if ($challenge->wasRecentlyCreated) {
                $createdCount++;
            }
        }

        $this->command->info("✅ Creados {$createdCount} retos de gamificación");
        $this->showStatistics();
    }

    private function showStatistics(): void
    {
        $stats = [
            'Total retos' => Challenge::count(),
            'Activos' => Challenge::where('is_active', true)->count(),
            'Destacados' => Challenge::where('is_featured', true)->count(),
            'Individuales' => Challenge::where('type', 'individual')->count(),
            'Comunitarios' => Challenge::where('type', 'community')->count(),
            'Cooperativos' => Challenge::where('type', 'cooperative')->count(),
            'En curso' => Challenge::ongoing()->count(),
            'Próximos' => Challenge::upcoming()->count(),
        ];

        $this->command->info("\n📊 Estadísticas de retos:");
        foreach ($stats as $type => $count) {
            $this->command->info("   {$type}: {$count}");
        }

        $totalPrizePool = Challenge::where('is_active', true)->sum('prize_pool');
        $this->command->info("\n⚡ Para KiroLux:");
        $this->command->info("   💰 Premio total disponible: €{$totalPrizePool}");
        $this->command->info("   🎯 Retos específicos de energía renovable");
        $this->command->info("   🤝 Cooperación y competencia equilibradas");
        $this->command->info("   📚 Componente educativo integrado");
        $this->command->info("   🌍 Eventos especiales y estacionales");
    }
}