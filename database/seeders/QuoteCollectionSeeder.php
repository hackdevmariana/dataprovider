<?php

namespace Database\Seeders;

use App\Models\QuoteCollection;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuoteCollectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear registros de ejemplo para QuoteCollection
        
        for ($i = 1; $i <= 5; $i++) {
            $data = [];
            
            // Generar datos según los campos disponibles
            $data['name'] = 'QuoteCollection ' . $i;
            $data['slug'] = 'titulo-' . $i;
            $data['description'] = 'Descripción del QuoteCollection ' . $i;
            $data['user_id'] = rand(1, 10);

            // Usar el primer campo como identificador único
            $uniqueField = 'name';
            QuoteCollection::updateOrCreate(
                [$uniqueField => $data[$uniqueField]],
                $data
            );
        }

        $this->command->info('QuoteCollection creados exitosamente.');
    }
}