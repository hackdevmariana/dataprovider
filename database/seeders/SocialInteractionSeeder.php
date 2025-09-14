<?php

namespace Database\Seeders;

use App\Models\SocialInteraction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SocialInteractionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear registros de ejemplo para SocialInteraction
        
        for ($i = 1; $i <= 5; $i++) {
            $data = [];
            
            // Generar datos según los campos disponibles
            $data['user_id'] = rand(1, 10);
            $data['target_id'] = rand(1, 10);
            $data['interaction_type'] = 'like';
            $data['platform'] = 'web';

            // Usar el primer campo como identificador único
            $uniqueField = 'user_id';
            SocialInteraction::updateOrCreate(
                [$uniqueField => $data[$uniqueField]],
                $data
            );
        }

        $this->command->info('SocialInteraction creados exitosamente.');
    }
}