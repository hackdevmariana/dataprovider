<?php

namespace Database\Seeders;

use App\Models\ListItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ListItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear registros de ejemplo para ListItem
        
        for ($i = 1; $i <= 5; $i++) {
            $data = [];
            
            // Generar datos según los campos disponibles
            $data['list_id'] = rand(1, 5);
            $data['title'] = 'Título ' . $i;
            $data['description'] = 'Descripción del ListItem ' . $i;
            $data['position'] = $i;

            // Usar el primer campo como identificador único
            $uniqueField = 'list_id';
            ListItem::updateOrCreate(
                [$uniqueField => $data[$uniqueField]],
                $data
            );
        }

        $this->command->info('ListItem creados exitosamente.');
    }
}