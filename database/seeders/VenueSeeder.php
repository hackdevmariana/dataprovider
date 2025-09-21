<?php

namespace Database\Seeders;

use App\Models\Venue;
use App\Models\Municipality;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VenueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener algunos municipios para asignar a los venues
        $municipalities = Municipality::take(5)->get();
        
        if ($municipalities->isEmpty()) {
            $this->command->warn('No hay municipios disponibles. Saltando seeder de Venue.');
            return;
        }

        // Crear registros de ejemplo para Venue
        for ($i = 1; $i <= 5; $i++) {
            $data = [];
            
            // Generar datos según los campos disponibles
            $data['name'] = 'Venue ' . $i;
            $data['slug'] = 'venue-' . $i;
            $data['description'] = 'Descripción del Venue ' . $i;
            $data['address'] = 'Dirección ' . $i;
            $data['municipality_id'] = $municipalities->random()->id;

            // Usar el primer campo como identificador único
            $uniqueField = 'name';
            Venue::updateOrCreate(
                [$uniqueField => $data[$uniqueField]],
                $data
            );
        }

        $this->command->info('Venue creados exitosamente.');
    }
}