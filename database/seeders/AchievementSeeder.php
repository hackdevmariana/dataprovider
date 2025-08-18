<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Achievement;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creando logros de gamificación para KiroLux...');

        $achievements = [
            // Ahorro energético
            [
                'name' => 'Primer Ahorro', 'slug' => 'primer-ahorro',
                'description' => 'Ahorra tu primera unidad de energía usando KiroLux.',
                'icon' => 'bolt', 'badge_color' => '#22C55E', 'category' => 'energy_saving',
                'type' => 'single', 'difficulty' => 'bronze', 'points' => 10,
                'required_value' => 1, 'required_unit' => 'kWh', 'is_active' => true,
                'is_hidden' => false, 'sort_order' => 1,
                'conditions' => ['energy_saved' => ['min' => 1, 'unit' => 'kWh']],
            ],
            [
                'name' => 'Ahorrador Novato', 'slug' => 'ahorrador-novato',
                'description' => 'Ahorra 50 kWh de energía en total.',
                'icon' => 'lightning-bolt', 'badge_color' => '#22C55E', 'category' => 'energy_saving',
                'type' => 'single', 'difficulty' => 'bronze', 'points' => 50,
                'required_value' => 50, 'required_unit' => 'kWh', 'is_active' => true,
                'is_hidden' => false, 'sort_order' => 2,
                'conditions' => ['energy_saved' => ['min' => 50, 'unit' => 'kWh']],
            ],
            [
                'name' => 'Maestro del Ahorro', 'slug' => 'maestro-ahorro',
                'description' => 'Ahorra 500 kWh de energía en total.',
                'icon' => 'star', 'badge_color' => '#D97706', 'category' => 'energy_saving',
                'type' => 'single', 'difficulty' => 'gold', 'points' => 200,
                'required_value' => 500, 'required_unit' => 'kWh', 'is_active' => true,
                'is_hidden' => false, 'sort_order' => 3,
                'conditions' => ['energy_saved' => ['min' => 500, 'unit' => 'kWh']],
            ],

            // Producción solar
            [
                'name' => 'Primera Luz', 'slug' => 'primera-luz',
                'description' => 'Genera tu primer kWh de energía solar.',
                'icon' => 'sun', 'badge_color' => '#FCD34D', 'category' => 'solar_production',
                'type' => 'single', 'difficulty' => 'bronze', 'points' => 15,
                'required_value' => 1, 'required_unit' => 'kWh', 'is_active' => true,
                'is_hidden' => false, 'sort_order' => 10,
                'conditions' => ['solar_produced' => ['min' => 1, 'unit' => 'kWh']],
            ],
            [
                'name' => 'Cosechador Solar', 'slug' => 'cosechador-solar',
                'description' => 'Genera 100 kWh de energía solar.',
                'icon' => 'sun', 'badge_color' => '#FCD34D', 'category' => 'solar_production',
                'type' => 'single', 'difficulty' => 'silver', 'points' => 75,
                'required_value' => 100, 'required_unit' => 'kWh', 'is_active' => true,
                'is_hidden' => false, 'sort_order' => 11,
                'conditions' => ['solar_produced' => ['min' => 100, 'unit' => 'kWh']],
            ],

            // Cooperativismo
            [
                'name' => 'Nuevo Cooperativista', 'slug' => 'nuevo-cooperativista',
                'description' => 'Únete a tu primera cooperativa energética.',
                'icon' => 'user-group', 'badge_color' => '#3B82F6', 'category' => 'cooperation',
                'type' => 'single', 'difficulty' => 'bronze', 'points' => 25,
                'required_value' => 1, 'required_unit' => 'cooperativas', 'is_active' => true,
                'is_hidden' => false, 'sort_order' => 20,
                'conditions' => ['cooperatives_joined' => ['min' => 1]],
            ],
            [
                'name' => 'Compartidor Generoso', 'slug' => 'compartidor-generoso',
                'description' => 'Comparte 50 kWh de energía con tu cooperativa.',
                'icon' => 'heart', 'badge_color' => '#3B82F6', 'category' => 'cooperation',
                'type' => 'single', 'difficulty' => 'silver', 'points' => 100,
                'required_value' => 50, 'required_unit' => 'kWh', 'is_active' => true,
                'is_hidden' => false, 'sort_order' => 21,
                'conditions' => ['energy_shared' => ['min' => 50, 'unit' => 'kWh']],
            ],

            // Sostenibilidad
            [
                'name' => 'Guardián Verde', 'slug' => 'guardian-verde',
                'description' => 'Evita 10 kg de CO2 usando energía renovable.',
                'icon' => 'leaf', 'badge_color' => '#10B981', 'category' => 'sustainability',
                'type' => 'single', 'difficulty' => 'bronze', 'points' => 30,
                'required_value' => 10, 'required_unit' => 'kg CO2', 'is_active' => true,
                'is_hidden' => false, 'sort_order' => 30,
                'conditions' => ['co2_avoided' => ['min' => 10, 'unit' => 'kg']],
            ],
            [
                'name' => 'Eco Guerrero', 'slug' => 'eco-guerrero',
                'description' => 'Evita 100 kg de CO2 con tus acciones sostenibles.',
                'icon' => 'globe-alt', 'badge_color' => '#10B981', 'category' => 'sustainability',
                'type' => 'single', 'difficulty' => 'silver', 'points' => 150,
                'required_value' => 100, 'required_unit' => 'kg CO2', 'is_active' => true,
                'is_hidden' => false, 'sort_order' => 31,
                'conditions' => ['co2_avoided' => ['min' => 100, 'unit' => 'kg']],
            ],

            // Participación
            [
                'name' => 'Primera Conexión', 'slug' => 'primera-conexion',
                'description' => 'Inicia sesión en KiroLux por primera vez.',
                'icon' => 'login', 'badge_color' => '#6B7280', 'category' => 'engagement',
                'type' => 'single', 'difficulty' => 'bronze', 'points' => 5,
                'required_value' => 1, 'required_unit' => 'sesiones', 'is_active' => true,
                'is_hidden' => false, 'sort_order' => 40,
                'conditions' => ['logins' => ['min' => 1]],
            ],
            [
                'name' => 'Usuario Activo', 'slug' => 'usuario-activo',
                'description' => 'Usa KiroLux durante 7 días consecutivos.',
                'icon' => 'calendar', 'badge_color' => '#8B5CF6', 'category' => 'engagement',
                'type' => 'single', 'difficulty' => 'silver', 'points' => 75,
                'required_value' => 7, 'required_unit' => 'días', 'is_active' => true,
                'is_hidden' => false, 'sort_order' => 41,
                'conditions' => ['consecutive_days' => ['min' => 7]],
            ],

            // Hitos económicos
            [
                'name' => 'Primer Céntimo', 'slug' => 'primer-centimo',
                'description' => 'Gana tu primer euro ahorrando energía.',
                'icon' => 'currency-euro', 'badge_color' => '#F59E0B', 'category' => 'milestone',
                'type' => 'single', 'difficulty' => 'bronze', 'points' => 20,
                'required_value' => 1, 'required_unit' => 'EUR', 'is_active' => true,
                'is_hidden' => false, 'sort_order' => 50,
                'conditions' => ['money_saved' => ['min' => 1, 'unit' => 'EUR']],
            ],

            // Logros secretos
            [
                'name' => 'Explorador Nocturno', 'slug' => 'explorador-nocturno',
                'description' => 'Usa KiroLux entre las 12:00 AM y 6:00 AM.',
                'icon' => 'moon', 'badge_color' => '#374151', 'category' => 'engagement',
                'type' => 'single', 'difficulty' => 'silver', 'points' => 50,
                'required_value' => 1, 'required_unit' => 'sesiones', 'is_active' => true,
                'is_hidden' => true, 'sort_order' => 100,
                'conditions' => ['night_usage' => ['min' => 1]],
            ],

            // Logro legendario
            [
                'name' => 'Leyenda de KiroLux', 'slug' => 'leyenda-kirolux',
                'description' => 'Completa 20 logros diferentes en KiroLux.',
                'icon' => 'trophy', 'badge_color' => '#8B5CF6', 'category' => 'milestone',
                'type' => 'single', 'difficulty' => 'legendary', 'points' => 1000,
                'required_value' => 20, 'required_unit' => 'logros', 'is_active' => true,
                'is_hidden' => false, 'sort_order' => 200,
                'conditions' => ['achievements_completed' => ['min' => 20]],
            ],
        ];

        $createdCount = 0;
        foreach ($achievements as $achievementData) {
            $achievement = Achievement::firstOrCreate(
                ['slug' => $achievementData['slug']],
                $achievementData
            );
            
            if ($achievement->wasRecentlyCreated) {
                $createdCount++;
            }
        }

        $this->command->info("✅ Creados {$createdCount} logros de gamificación");
        $this->showStatistics();
    }

    private function showStatistics(): void
    {
        $stats = [
            'Total logros' => Achievement::count(),
            'Activos' => Achievement::where('is_active', true)->count(),
            'Secretos' => Achievement::where('is_hidden', true)->count(),
            'Bronce' => Achievement::where('difficulty', 'bronze')->count(),
            'Plata' => Achievement::where('difficulty', 'silver')->count(),
            'Oro' => Achievement::where('difficulty', 'gold')->count(),
            'Legendarios' => Achievement::where('difficulty', 'legendary')->count(),
        ];

        $this->command->info("\n📊 Estadísticas de logros:");
        foreach ($stats as $type => $count) {
            $this->command->info("   {$type}: {$count}");
        }

        $totalPoints = Achievement::where('is_active', true)->sum('points');
        $this->command->info("\n⚡ Para KiroLux:");
        $this->command->info("   🎯 Total puntos disponibles: {$totalPoints}");
        $this->command->info("   🌟 Logros únicos de energía renovable");
        $this->command->info("   🤝 Enfoque cooperativo y sostenible");
        $this->command->info("   🎮 Gamificación completa implementada");
    }
}