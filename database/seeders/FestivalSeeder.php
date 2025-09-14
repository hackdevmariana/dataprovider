<?php

namespace Database\Seeders;

use App\Models\Festival;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FestivalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear registros de ejemplo para Festival
        
        for ($i = 1; $i <= 5; $i++) {
            $data = [];
            
            // Generar datos según los campos disponibles
            $data['name'] = 'Festival ' . $i;
            $data['slug'] = 'festival-' . $i;
            $data['description'] = 'Descripción del Festival ' . $i;
            $data['month'] = rand(1, 12);
            $data['usual_days'] = rand(1, 7);
            $data['recurring'] = true;
            $data['location_id'] = rand(1, 10);
            $data['logo_url'] = 'https://via.placeholder.com/200x200/1e40af/ffffff?text=Festival+' . $i;
            $data['color_theme'] = '#1e40af';

            // Usar el primer campo como identificador único
            $uniqueField = 'name';
            Festival::updateOrCreate(
                [$uniqueField => $data[$uniqueField]],
                $data
            );
        }

        $this->command->info('Festival creados exitosamente.');
    }
}