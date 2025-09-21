<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Achievement;

class AchievementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $achievements = [
            [
                'name' => 'Primer Ahorro',
                'slug' => 'primer-ahorro',
                'description' => 'Ahorra tu primera cantidad de energía',
                'icon' => 'energy-saving',
                'badge_color' => '#4CAF50',
                'category' => 'energy_saving',
                'type' => 'single',
                'difficulty' => 'bronze',
                'conditions' => ['energy_saved_kwh' => 10],
                'points' => 10,
                'required_value' => 10,
                'required_unit' => 'kWh',
                'is_active' => true,
                'is_hidden' => false,
                'sort_order' => 1,
            ],
            [
                'name' => 'Productor Solar',
                'slug' => 'productor-solar',
                'description' => 'Produce energía solar por primera vez',
                'icon' => 'solar-panel',
                'badge_color' => '#FF9800',
                'category' => 'solar_production',
                'type' => 'single',
                'difficulty' => 'silver',
                'conditions' => ['solar_produced_kwh' => 50],
                'points' => 25,
                'required_value' => 50,
                'required_unit' => 'kWh',
                'is_active' => true,
                'is_hidden' => false,
                'sort_order' => 2,
            ],
            [
                'name' => 'Eficiencia Máxima',
                'slug' => 'eficiencia-maxima',
                'description' => 'Mejora la eficiencia energética en un 20%',
                'icon' => 'efficiency',
                'badge_color' => '#2196F3',
                'category' => 'energy_saving',
                'type' => 'progressive',
                'difficulty' => 'gold',
                'conditions' => ['efficiency_improvement_percent' => 20],
                'points' => 50,
                'required_value' => 20,
                'required_unit' => '%',
                'is_active' => true,
                'is_hidden' => false,
                'sort_order' => 3,
            ],
            [
                'name' => 'Campeón de Retos',
                'slug' => 'campeon-retos',
                'description' => 'Completa 10 retos exitosamente',
                'icon' => 'trophy',
                'badge_color' => '#9C27B0',
                'category' => 'engagement',
                'type' => 'progressive',
                'difficulty' => 'platinum',
                'conditions' => ['challenges_completed' => 10],
                'points' => 100,
                'required_value' => 10,
                'required_unit' => 'retos',
                'is_active' => true,
                'is_hidden' => false,
                'sort_order' => 4,
            ],
            [
                'name' => 'Leyenda Verde',
                'slug' => 'leyenda-verde',
                'description' => 'Logro legendario por sostenibilidad excepcional',
                'icon' => 'leaf',
                'badge_color' => '#4CAF50',
                'category' => 'sustainability',
                'type' => 'single',
                'difficulty' => 'legendary',
                'conditions' => ['sustainability_score' => 95],
                'points' => 500,
                'required_value' => 95,
                'required_unit' => 'puntos',
                'is_active' => true,
                'is_hidden' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($achievements as $achievementData) {
            Achievement::firstOrCreate(
                ['slug' => $achievementData['slug']], // Condición de búsqueda
                $achievementData // Datos a crear si no existe
            );
        }
    }
}
