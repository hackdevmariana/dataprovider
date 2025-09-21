<?php

namespace Database\Seeders;

use App\Models\Municipality;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MunicipalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Municipios españoles con todos los campos requeridos
        $data = [
            [
                'name' => 'Madrid',
                'slug' => 'madrid',
                'province_id' => 1,
                'autonomous_community_id' => 1,
                'country_id' => 1,
                'population' => 3223334,
                'postal_code' => '28001',
                'is_capital' => true,
            ],
            [
                'name' => 'Barcelona',
                'slug' => 'barcelona',
                'province_id' => 2,
                'autonomous_community_id' => 2,
                'country_id' => 1,
                'population' => 1636762,
                'postal_code' => '08001',
                'is_capital' => true,
            ],
            [
                'name' => 'Valencia',
                'slug' => 'valencia',
                'province_id' => 3,
                'autonomous_community_id' => 3,
                'country_id' => 1,
                'population' => 800215,
                'postal_code' => '46001',
                'is_capital' => true,
            ],
            [
                'name' => 'Sevilla',
                'slug' => 'sevilla',
                'province_id' => 4,
                'autonomous_community_id' => 4,
                'country_id' => 1,
                'population' => 688711,
                'postal_code' => '41001',
                'is_capital' => true,
            ],
            [
                'name' => 'Zaragoza',
                'slug' => 'zaragoza',
                'province_id' => 5,
                'autonomous_community_id' => 5,
                'country_id' => 1,
                'population' => 675301,
                'postal_code' => '50001',
                'is_capital' => true,
            ],
            [
                'name' => 'Málaga',
                'slug' => 'malaga',
                'province_id' => 6,
                'autonomous_community_id' => 4,
                'country_id' => 1,
                'population' => 577405,
                'postal_code' => '29001',
                'is_capital' => false,
            ],
            [
                'name' => 'Murcia',
                'slug' => 'murcia',
                'province_id' => 7,
                'autonomous_community_id' => 6,
                'country_id' => 1,
                'population' => 459403,
                'postal_code' => '30001',
                'is_capital' => true,
            ],
            [
                'name' => 'Palma',
                'slug' => 'palma',
                'province_id' => 8,
                'autonomous_community_id' => 7,
                'country_id' => 1,
                'population' => 419366,
                'postal_code' => '07001',
                'is_capital' => true,
            ],
            [
                'name' => 'Las Palmas',
                'slug' => 'las-palmas',
                'province_id' => 9,
                'autonomous_community_id' => 8,
                'country_id' => 1,
                'population' => 381123,
                'postal_code' => '35001',
                'is_capital' => true,
            ],
            [
                'name' => 'Bilbao',
                'slug' => 'bilbao',
                'province_id' => 10,
                'autonomous_community_id' => 9,
                'country_id' => 1,
                'population' => 346843,
                'postal_code' => '48001',
                'is_capital' => false,
            ],
        ];

        foreach ($data as $item) {
            Municipality::updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );
        }

        $this->command->info('Municipios españoles creados exitosamente.');
    }
}