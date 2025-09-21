<?php

namespace Database\Seeders;

use App\Models\UserBadge;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserBadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear registros de ejemplo para UserBadge
        
        for ($i = 1; $i <= 5; $i++) {
            $data = [];
            
            // Generar datos según los campos disponibles
            $data['user_id'] = rand(1, 10);
            $data['badge_type'] = 'achievement';
            $data['category'] = 'General';
            $data['name'] = 'Badge ' . $i;
            $data['description'] = 'Descripción del UserBadge ' . $i;
            $data['icon_url'] = 'https://via.placeholder.com/64x64/1e40af/ffffff?text=B' . $i;
            $data['color'] = '#1e40af';
            $data['criteria'] = 'Completar ' . $i . ' tareas';
            $data['points_awarded'] = rand(10, 100);
            $data['is_public'] = true;
            $data['earned_at'] = now();

            // Usar el primer campo como identificador único
            $uniqueField = 'user_id';
            UserBadge::updateOrCreate(
                [$uniqueField => $data[$uniqueField]],
                $data
            );
        }

        $this->command->info('UserBadge creados exitosamente.');
    }
}