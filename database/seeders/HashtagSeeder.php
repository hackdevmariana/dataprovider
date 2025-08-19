<?php

namespace Database\Seeders;

use App\Models\Hashtag;
use App\Models\User;
use Illuminate\Database\Seeder;

class HashtagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏷️ Creando hashtags del sector energético...');

        // Hashtags oficiales verificados
        $officialHashtags = [
            [
                'name' => 'energiasolar',
                'description' => 'Todo sobre energía solar fotovoltaica',
                'category' => 'technology',
                'color' => '#F59E0B',
                'icon' => 'solar-panel',
                'is_verified' => true,
                'is_trending' => true,
            ],
            [
                'name' => 'autoconsumo',
                'description' => 'Autoconsumo energético y excedentes',
                'category' => 'general',
                'color' => '#10B981',
                'icon' => 'home',
                'is_verified' => true,
                'is_trending' => true,
            ],
            [
                'name' => 'cooperativa',
                'description' => 'Cooperativas energéticas y comunidades',
                'category' => 'cooperative',
                'color' => '#3B82F6',
                'icon' => 'users',
                'is_verified' => true,
            ],
            [
                'name' => 'rd244',
                'description' => 'Real Decreto 244/2019 de autoconsumo',
                'category' => 'legislation',
                'color' => '#8B5CF6',
                'icon' => 'book-open',
                'is_verified' => true,
            ],
            [
                'name' => 'subvenciones',
                'description' => 'Ayudas y subvenciones para instalaciones',
                'category' => 'financing',
                'color' => '#EF4444',
                'icon' => 'gift',
                'is_verified' => true,
            ],
        ];

        $creator = User::first();

        foreach ($officialHashtags as $hashtagData) {
            $hashtag = Hashtag::create(array_merge($hashtagData, [
                'slug' => \Str::slug($hashtagData['name']),
                'usage_count' => fake()->numberBetween(500, 2000),
                'posts_count' => fake()->numberBetween(200, 800),
                'followers_count' => fake()->numberBetween(100, 500),
                'trending_score' => fake()->randomFloat(2, 200, 1000),
                'created_by' => $creator?->id,
            ]));

            $this->command->info("✅ Creado hashtag oficial: #{$hashtag->name}");
        }

        // Hashtags por categoría
        $categories = [
            'technology' => 15,
            'legislation' => 8,
            'financing' => 10,
            'installation' => 12,
            'cooperative' => 8,
            'market' => 10,
            'sustainability' => 12,
            'location' => 15,
            'general' => 10,
        ];

        foreach ($categories as $category => $count) {
            Hashtag::factory()
                  ->category($category)
                  ->count($count)
                  ->create();
        }

        // Hashtags trending adicionales
        Hashtag::factory()
              ->trending()
              ->count(10)
              ->create();

        // Estadísticas finales
        $total = Hashtag::count();
        $trending = Hashtag::where('is_trending', true)->count();
        $verified = Hashtag::where('is_verified', true)->count();
        $byCategory = Hashtag::selectRaw('category, COUNT(*) as count')
                            ->groupBy('category')
                            ->pluck('count', 'category')
                            ->toArray();

        $this->command->info("📊 Estadísticas de Hashtags:");
        $this->command->info("   Total: {$total}");
        $this->command->info("   Trending: {$trending}");
        $this->command->info("   Verificados: {$verified}");
        
        foreach ($byCategory as $category => $count) {
            $this->command->info("   {$category}: {$count}");
        }

        $this->command->info('🎉 Hashtags creados exitosamente!');
    }
}
