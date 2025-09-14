<?php

namespace Database\Seeders;

use App\Models\Link;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Enlaces del sistema
        $data = [
            [
                'url' => 'https://www.cnmc.es',
                'label' => 'CNMC',
                'related_type' => 'App\Models\DataSource',
                'related_id' => 1,
                'type' => 'official',
                'is_primary' => true,
                'opens_in_new_tab' => true,
            ],
            [
                'url' => 'https://www.ree.es',
                'label' => 'REE',
                'related_type' => 'App\Models\DataSource',
                'related_id' => 2,
                'type' => 'official',
                'is_primary' => true,
                'opens_in_new_tab' => true,
            ],
            [
                'url' => 'https://www.omie.es',
                'label' => 'OMIE',
                'related_type' => 'App\Models\DataSource',
                'related_id' => 3,
                'type' => 'official',
                'is_primary' => true,
                'opens_in_new_tab' => true,
            ],
            [
                'url' => 'https://www.aemet.es',
                'label' => 'AEMET',
                'related_type' => 'App\Models\DataSource',
                'related_id' => 4,
                'type' => 'official',
                'is_primary' => true,
                'opens_in_new_tab' => true,
            ],
            [
                'url' => 'https://www.ine.es',
                'label' => 'INE',
                'related_type' => 'App\Models\DataSource',
                'related_id' => 5,
                'type' => 'official',
                'is_primary' => true,
                'opens_in_new_tab' => true,
            ],
        ];

        foreach ($data as $item) {
            Link::updateOrCreate(
                ['url' => $item['url']], // Usar URL como campo único
                $item
            );
        }

        $this->command->info('Enlaces del sistema creados exitosamente.');
    }
}