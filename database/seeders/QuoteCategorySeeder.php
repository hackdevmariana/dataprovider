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
        
        for ($i = 1; $i <= 5; $i++) {
            $data = [];
            
            // Generar datos según los campos disponibles
            $data['name'] = 'QuoteCategory ' . $i;
            $data['slug'] = 'titulo-' . $i;
            $data['description'] = 'Descripción del QuoteCategory ' . $i;
            $data['color'] = '#1e40af';

            // Usar el primer campo como identificador único
            $uniqueField = 'name';
            QuoteCategory::updateOrCreate(
                [$uniqueField => $data[$uniqueField]],
                $data
            );
        }

        $this->command->info('QuoteCategory creados exitosamente.');
    }
}