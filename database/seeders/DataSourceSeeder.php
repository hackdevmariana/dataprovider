<?php

namespace Database\Seeders;

use App\Models\DataSource;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DataSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fuentes de datos oficiales españolas
        $data = [
            [
                'name' => 'CNMC API',
                'type' => 'api',
                'url' => 'https://www.cnmc.es/api',
                'license' => 'ODbL',
            ],
            [
                'name' => 'REE API',
                'type' => 'api',
                'url' => 'https://www.ree.es/api',
                'license' => 'ODbL',
            ],
            [
                'name' => 'OMIE API',
                'type' => 'api',
                'url' => 'https://www.omie.es/api',
                'license' => 'ODbL',
            ],
            [
                'name' => 'AEMET API',
                'type' => 'api',
                'url' => 'https://www.aemet.es/api',
                'license' => 'ODbL',
            ],
            [
                'name' => 'INE API',
                'type' => 'api',
                'url' => 'https://www.ine.es/api',
                'license' => 'ODbL',
            ],
            [
                'name' => 'Ministerio Energía Scrap',
                'type' => 'scrap',
                'url' => 'https://www.miteco.gob.es',
                'license' => 'CC-BY',
            ],
            [
                'name' => 'IDAE CSV',
                'type' => 'csv',
                'url' => 'https://www.idae.es/datos',
                'license' => 'ODbL',
            ],
            [
                'name' => 'Datos Manuales',
                'type' => 'manual',
                'url' => null,
                'license' => 'CC-BY',
            ],
        ];

        foreach ($data as $item) {
            DataSource::updateOrCreate(
                ['name' => $item['name']],
                $item
            );
        }

        $this->command->info('Fuentes de datos creadas exitosamente.');
    }
}