<?php

namespace Database\Seeders;

use App\Models\TopicFollowing;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TopicFollowingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear registros de ejemplo para TopicFollowing
        
        for ($i = 1; $i <= 5; $i++) {
            $data = [];
            
            // Generar datos según los campos disponibles
            $data['user_id'] = rand(1, 10);
            $data['topic_id'] = rand(1, 5);
            $data['notifications_enabled'] = true;

            // Usar el primer campo como identificador único
            $uniqueField = 'user_id';
            TopicFollowing::updateOrCreate(
                [$uniqueField => $data[$uniqueField]],
                $data
            );
        }

        $this->command->info('TopicFollowing creados exitosamente.');
    }
}