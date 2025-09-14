<?php

namespace Database\Seeders;

use App\Models\BookEdition;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookEditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear registros de ejemplo para BookEdition
        
        for ($i = 1; $i <= 5; $i++) {
            $data = [];
            
            // Generar datos según los campos disponibles
            $data['book_id'] = rand(1, 5);
            $data['edition_number'] = $i;
            $data['format'] = 'Tapa blanda';
            $data['publisher'] = 'Editorial ' . $i;
            $data['publication_date'] = now()->subDays(rand(1, 365));
            $data['isbn'] = '978-84-376-0' . str_pad($i, 3, '0', STR_PAD_LEFT);
            $data['pages'] = rand(100, 800);
            $data['cover_type'] = 'Blanda';
            $data['price'] = rand(10, 50);
            $data['currency'] = 'EUR';

            // Usar el primer campo como identificador único
            $uniqueField = 'book_id';
            BookEdition::updateOrCreate(
                [$uniqueField => $data[$uniqueField]],
                $data
            );
        }

        $this->command->info('BookEdition creados exitosamente.');
    }
}