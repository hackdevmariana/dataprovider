<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Challenge;
use Carbon\Carbon;

class ChallengesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $challenges = [
            [
                'name' => 'Desafío Solar Semanal',
                'slug' => 'desafio-solar-semanal',
                'description' => 'Produce la mayor cantidad de energía solar en una semana',
                'instructions' => 'Instala paneles solares y comparte tu producción diaria',
                'icon' => 'solar-panel',
                'banner_color' => '#FF9800',
                'type' => 'individual',
                'category' => 'solar_production',
                'difficulty' => 'medium',
                'start_date' => Carbon::now()->subWeek(),
                'end_date' => Carbon::now()->addWeek(),
                'goals' => [
                    'primary' => 'Producir 100 kWh en una semana',
                    'secondary' => 'Compartir datos diarios',
                ],
                'rewards' => [
                    'points' => 100,
                    'badge' => 'Solar Champion',
                    'prize' => 'Descuento en equipos solares',
                ],
                'max_participants' => 50,
                'min_participants' => 5,
                'entry_fee' => 0.00,
                'prize_pool' => 500.00,
                'is_active' => true,
                'is_featured' => true,
                'auto_join' => false,
                'sort_order' => 1,
            ],
            [
                'name' => 'Reto de Eficiencia Comunitaria',
                'slug' => 'reto-eficiencia-comunitaria',
                'description' => 'Mejora la eficiencia energética de tu comunidad',
                'instructions' => 'Organiza actividades de eficiencia energética en tu barrio',
                'icon' => 'community',
                'banner_color' => '#4CAF50',
                'type' => 'community',
                'category' => 'energy_saving',
                'difficulty' => 'hard',
                'start_date' => Carbon::now()->subMonth(),
                'end_date' => Carbon::now()->addMonth(),
                'goals' => [
                    'primary' => 'Reducir consumo energético en 15%',
                    'secondary' => 'Involucrar a 10 vecinos',
                ],
                'rewards' => [
                    'points' => 250,
                    'badge' => 'Community Leader',
                    'prize' => 'Kit de eficiencia energética',
                ],
                'max_participants' => 20,
                'min_participants' => 3,
                'entry_fee' => 10.00,
                'prize_pool' => 1000.00,
                'is_active' => true,
                'is_featured' => false,
                'auto_join' => false,
                'sort_order' => 2,
            ],
            [
                'name' => 'Cooperativa Verde',
                'slug' => 'cooperativa-verde',
                'description' => 'Reto cooperativo de sostenibilidad',
                'instructions' => 'Trabaja en equipo para lograr objetivos sostenibles',
                'icon' => 'cooperative',
                'banner_color' => '#2196F3',
                'type' => 'cooperative',
                'category' => 'sustainability',
                'difficulty' => 'expert',
                'start_date' => Carbon::now()->subMonths(2),
                'end_date' => Carbon::now()->addMonths(2),
                'goals' => [
                    'primary' => 'Lograr certificación verde',
                    'secondary' => 'Reducir huella de carbono en 30%',
                ],
                'rewards' => [
                    'points' => 500,
                    'badge' => 'Green Cooperative',
                    'prize' => 'Fondo para proyectos verdes',
                ],
                'max_participants' => 100,
                'min_participants' => 10,
                'entry_fee' => 25.00,
                'prize_pool' => 5000.00,
                'is_active' => true,
                'is_featured' => true,
                'auto_join' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($challenges as $challengeData) {
            Challenge::create($challengeData);
        }
    }
}
