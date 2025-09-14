<?php

namespace Database\Seeders;

use App\Models\BookReview;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear registros de ejemplo para BookReview
        
        for ($i = 1; $i <= 5; $i++) {
            $data = [];
            
            // Generar datos según los campos disponibles
            $data['book_id'] = rand(1, 5);
            $data['user_id'] = rand(1, 10);
            $data['rating'] = rand(1, 5);
            $data['title'] = 'Título ' . $i;

            // Usar el primer campo como identificador único
            $uniqueField = 'book_id';
            BookReview::updateOrCreate(
                [$uniqueField => $data[$uniqueField]],
                $data
            );
        }

        $this->command->info('BookReview creados exitosamente.');
    }
}