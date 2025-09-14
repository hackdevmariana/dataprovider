<?php

namespace Database\Seeders;

use App\Models\SocialAccount;
use App\Models\Person;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SocialAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar que existan personas antes de crear cuentas sociales
        $people = Person::all();
        
        if ($people->isEmpty()) {
            $this->command->warn('No hay personas disponibles. Ejecuta PersonSeeder primero.');
            return;
        }

        // Definir plataformas sociales disponibles
        $platforms = ['twitter', 'instagram', 'youtube', 'tiktok', 'other'];
        
        // Generar handles y URLs realistas para cada plataforma
        $platformHandles = [
            'twitter' => [
                'handles' => ['@johndoe', '@janedoe', '@artist123', '@musician456', '@actor789', '@director101', '@writer202'],
                'urls' => ['https://twitter.com/johndoe', 'https://twitter.com/janedoe', 'https://twitter.com/artist123']
            ],
            'instagram' => [
                'handles' => ['@johndoe_official', '@janedoe_art', '@artist_life', '@music_creator', '@actor_studio'],
                'urls' => ['https://instagram.com/johndoe_official', 'https://instagram.com/janedoe_art']
            ],
            'youtube' => [
                'handles' => ['JohnDoe Channel', 'JaneDoe Music', 'Artist Studio', 'Actor Talks', 'Director Insights'],
                'urls' => ['https://youtube.com/@johndoe', 'https://youtube.com/@janedoe']
            ],
            'tiktok' => [
                'handles' => ['@johndoe', '@janedoe', '@artist_tiktok', '@actor_clips', '@music_vibes'],
                'urls' => ['https://tiktok.com/@johndoe', 'https://tiktok.com/@janedoe']
            ],
            'other' => [
                'handles' => ['johndoe_official', 'janedoe_art', 'artist_portfolio', 'actor_website'],
                'urls' => ['https://johndoe.com', 'https://janedoe.art', 'https://artist-portfolio.com']
            ]
        ];

        $createdCount = 0;

        foreach ($people as $person) {
            // Cada persona tendrá entre 1 y 4 cuentas sociales aleatorias
            $accountsCount = rand(1, 4);
            $selectedPlatforms = array_rand($platforms, min($accountsCount, count($platforms)));
            
            // Asegurar que $selectedPlatforms sea un array
            if (!is_array($selectedPlatforms)) {
                $selectedPlatforms = [$selectedPlatforms];
            }

            foreach ($selectedPlatforms as $platformIndex) {
                $platform = $platforms[$platformIndex];
                $handles = $platformHandles[$platform]['handles'];
                $urls = $platformHandles[$platform]['urls'];
                
                $handle = $handles[array_rand($handles)];
                $url = $urls[array_rand($urls)];

                $accountData = [
                    'person_id' => $person->id,
                    'platform' => $platform,
                    'handle' => $handle,
                    'url' => $url,
                    'followers_count' => rand(100, 1000000), // Entre 100 y 1M seguidores
                    'verified' => rand(0, 1) == 1, // 50% probabilidad de estar verificado
                    'is_public' => rand(0, 1) == 1, // 50% probabilidad de ser público
                ];

                // Usar updateOrCreate para evitar duplicados
                SocialAccount::updateOrCreate(
                    [
                        'person_id' => $person->id,
                        'platform' => $platform,
                    ],
                    $accountData
                );
                
                $createdCount++;
            }
        }

        $this->command->info("SocialAccount creados exitosamente. Total: {$createdCount} registros.");
    }
}