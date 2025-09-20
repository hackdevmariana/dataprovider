<?php

namespace Database\Seeders;

use App\Models\QuoteCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuoteCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear registros de ejemplo para QuoteCategory
        
        $categories = [
            [
                'name' => 'Inspiración',
                'description' => 'Citas motivadoras e inspiradoras',
                'color' => '#3B82F6',
                'icon' => 'lightbulb',
                'quotes_count' => 0,
                'popularity_score' => 8.5,
                'is_active' => true,
            ],
            [
                'name' => 'Sostenibilidad',
                'description' => 'Citas sobre medio ambiente y sostenibilidad',
                'color' => '#10B981',
                'icon' => 'leaf',
                'quotes_count' => 0,
                'popularity_score' => 9.2,
                'is_active' => true,
            ],
            [
                'name' => 'Energía',
                'description' => 'Citas sobre energía renovable y eficiencia',
                'color' => '#F59E0B',
                'icon' => 'bolt',
                'quotes_count' => 0,
                'popularity_score' => 7.8,
                'is_active' => true,
            ],
            [
                'name' => 'Tecnología',
                'description' => 'Citas sobre innovación tecnológica',
                'color' => '#8B5CF6',
                'icon' => 'cpu',
                'quotes_count' => 0,
                'popularity_score' => 6.5,
                'is_active' => true,
            ],
            [
                'name' => 'Futuro',
                'description' => 'Citas sobre el futuro y la innovación',
                'color' => '#EF4444',
                'icon' => 'rocket',
                'quotes_count' => 0,
                'popularity_score' => 8.0,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $categoryData) {
            QuoteCategory::updateOrCreate(
                ['name' => $categoryData['name']],
                $categoryData
            );
        }

        $this->command->info('QuoteCategory creados exitosamente.');
    }
}