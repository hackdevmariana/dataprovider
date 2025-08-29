<?php

namespace Database\Seeders;

use App\Models\TrendingTopic;
use Illuminate\Database\Seeder;

class TrendingTopicSeeder extends Seeder
{
    public function run(): void
    {
        $topics = [
            [
                'topic' => 'Energías Renovables',
                'trending_score' => 95.50,
                'mentions_count' => 15420,
                'growth_rate' => 12.30,
                'geographic_spread' => 'global',
                'category' => 'energy',
                'related_keywords' => json_encode(['solar', 'eólica', 'hidrógeno', 'sostenibilidad', 'verde']),
                'geographic_data' => json_encode([
                    'countries' => ['España', 'Alemania', 'Países Bajos', 'Francia'],
                    'regions' => ['Europa', 'América del Norte', 'Asia']
                ]),
                'peak_time' => now()->subDays(5),
                'peak_score' => 98,
                'trend_analysis' => json_encode([
                    'sentiment' => 'positive',
                    'confidence' => 0.94,
                    'demographics' => ['25-34', '35-44', '45-54'],
                    'interests' => ['tecnología', 'medio ambiente', 'sostenibilidad']
                ]),
                'is_breaking' => false,
            ],
            [
                'topic' => 'Crisis Energética',
                'trending_score' => 87.20,
                'mentions_count' => 12850,
                'growth_rate' => 8.70,
                'geographic_spread' => 'european',
                'category' => 'energy',
                'related_keywords' => json_encode(['crisis', 'precios', 'suministro', 'gas', 'electricidad']),
                'geographic_data' => json_encode([
                    'countries' => ['España', 'Italia', 'Francia', 'Alemania'],
                    'regions' => ['Europa Occidental', 'Europa del Sur']
                ]),
                'peak_time' => now()->subDays(3),
                'peak_score' => 92,
                'trend_analysis' => json_encode([
                    'sentiment' => 'negative',
                    'confidence' => 0.89,
                    'demographics' => ['35-44', '45-54', '55-64'],
                    'interests' => ['economía', 'política', 'actualidad']
                ]),
                'is_breaking' => true,
            ],
            [
                'topic' => 'Transición Energética',
                'trending_score' => 82.10,
                'mentions_count' => 9870,
                'growth_rate' => 15.20,
                'geographic_spread' => 'global',
                'category' => 'energy',
                'related_keywords' => json_encode(['transición', 'descarbonización', 'neutralidad', '2030', '2050']),
                'geographic_data' => json_encode([
                    'countries' => ['España', 'UE', 'Estados Unidos', 'Japón'],
                    'regions' => ['Europa', 'América del Norte', 'Asia']
                ]),
                'peak_time' => now()->subDays(2),
                'peak_score' => 85,
                'trend_analysis' => json_encode([
                    'sentiment' => 'positive',
                    'confidence' => 0.91,
                    'demographics' => ['25-34', '35-44', '45-54'],
                    'interests' => ['política', 'medio ambiente', 'futuro']
                ]),
                'is_breaking' => false,
            ],
            [
                'topic' => 'Eficiencia Energética',
                'trending_score' => 76.80,
                'mentions_count' => 7650,
                'growth_rate' => 6.40,
                'geographic_spread' => 'national',
                'category' => 'energy',
                'related_keywords' => json_encode(['eficiencia', 'ahorro', 'consumo', 'edificios', 'hogares']),
                'geographic_data' => json_encode([
                    'countries' => ['España'],
                    'regions' => ['Madrid', 'Barcelona', 'Valencia', 'Andalucía']
                ]),
                'peak_time' => now()->subDays(1),
                'peak_score' => 78,
                'trend_analysis' => json_encode([
                    'sentiment' => 'positive',
                    'confidence' => 0.85,
                    'demographics' => ['25-34', '35-44', '45-54', '55-64'],
                    'interests' => ['hogar', 'tecnología', 'ahorro', 'sostenibilidad']
                ]),
                'is_breaking' => false,
            ],
            [
                'topic' => 'Hidrógeno Verde',
                'trending_score' => 71.50,
                'mentions_count' => 5430,
                'growth_rate' => 22.80,
                'geographic_spread' => 'international',
                'category' => 'energy',
                'related_keywords' => json_encode(['hidrógeno', 'verde', 'H2', 'combustible', 'futuro']),
                'geographic_data' => json_encode([
                    'countries' => ['España', 'Alemania', 'Japón', 'Australia'],
                    'regions' => ['Europa', 'Asia', 'Oceanía']
                ]),
                'peak_time' => now(),
                'peak_score' => 72,
                'trend_analysis' => json_encode([
                    'sentiment' => 'positive',
                    'confidence' => 0.87,
                    'demographics' => ['25-34', '35-44'],
                    'interests' => ['tecnología', 'innovación', 'futuro', 'ciencia']
                ]),
                'is_breaking' => true,
            ],
            [
                'topic' => 'Precios de la Electricidad',
                'trending_score' => 68.90,
                'mentions_count' => 12340,
                'growth_rate' => 4.20,
                'geographic_spread' => 'national',
                'category' => 'energy',
                'related_keywords' => json_encode(['precios', 'electricidad', 'factura', 'OMIE', 'mercado']),
                'geographic_data' => json_encode([
                    'countries' => ['España'],
                    'regions' => ['Madrid', 'Barcelona', 'Andalucía', 'Valencia']
                ]),
                'peak_time' => now()->subDays(10),
                'peak_score' => 75,
                'trend_analysis' => json_encode([
                    'sentiment' => 'negative',
                    'confidence' => 0.96,
                    'demographics' => ['25-34', '35-44', '45-54', '55-64', '65+'],
                    'interests' => ['economía', 'hogar', 'actualidad', 'política']
                ]),
                'is_breaking' => false,
            ],
            [
                'topic' => 'Energía Nuclear',
                'trending_score' => 65.30,
                'mentions_count' => 8760,
                'growth_rate' => 3.80,
                'geographic_spread' => 'european',
                'category' => 'energy',
                'related_keywords' => json_encode(['nuclear', 'centrales', 'fisión', 'uranio', 'residuos']),
                'geographic_data' => json_encode([
                    'countries' => ['España', 'Francia', 'Alemania', 'Bélgica'],
                    'regions' => ['Europa Occidental']
                ]),
                'peak_time' => now()->subDays(4),
                'peak_score' => 68,
                'trend_analysis' => json_encode([
                    'sentiment' => 'neutral',
                    'confidence' => 0.82,
                    'demographics' => ['35-44', '45-54', '55-64'],
                    'interests' => ['política', 'ciencia', 'debate', 'actualidad']
                ]),
                'is_breaking' => false,
            ],
            [
                'topic' => 'Smart Grids',
                'trending_score' => 59.70,
                'mentions_count' => 4320,
                'growth_rate' => 18.50,
                'geographic_spread' => 'international',
                'category' => 'energy',
                'related_keywords' => json_encode(['smart grid', 'red inteligente', 'IoT', 'tecnología', 'futuro']),
                'geographic_data' => json_encode([
                    'countries' => ['España', 'Estados Unidos', 'Corea del Sur', 'Japón'],
                    'regions' => ['Europa', 'América del Norte', 'Asia']
                ]),
                'peak_time' => now(),
                'peak_score' => 60,
                'trend_analysis' => json_encode([
                    'sentiment' => 'positive',
                    'confidence' => 0.84,
                    'demographics' => ['25-34', '35-44'],
                    'interests' => ['tecnología', 'innovación', 'IoT', 'futuro']
                ]),
                'is_breaking' => false,
            ],
        ];

        foreach ($topics as $topic) {
            TrendingTopic::create($topic);
        }

        $this->command->info('✅ Creados ' . count($topics) . ' temas de tendencia');
    }
}
