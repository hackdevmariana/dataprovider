<?php

/* Create:
php artisan make:seeder Municipality/MadridMunicipalitiesSeeder
*/
namespace Database\Seeders\Municipality;

use Illuminate\Database\Seeder;
use App\Models\Municipality;
use App\Models\Province;
use App\Models\AutonomousCommunity;
use App\Models\Country;
use App\Models\Timezone;
use Illuminate\Support\Str;

class MadridMunicipalitiesSeeder extends Seeder
{
    public function run(): void
    {
        $province = Province::where('slug', 'madrid')->firstOrFail();
        $community = $province->autonomousCommunity;
        $country = $province->country;
        $timezone = $province->timezone;

        $municipalities = [
            [
                'name' => 'Madrid',
                'ine_code' => '28079',
                'postal_code' => '28001',
                'population' => 3300000,
                'mayor_name' => 'José Luis Martínez-Almeida',
                'mayor_salary' => 108517.80,
                'latitude' => 40.4168,
                'longitude' => -3.7038,
                'area_km2' => 604.3,
                'altitude_m' => 667,
                'is_capital' => true,
                'tourism_info' => 'Capital de España, con una rica oferta cultural, gastronómica y patrimonial.',
            ],
            [
                'name' => 'Alcalá de Henares',
                'ine_code' => '28005',
                'postal_code' => '28801',
                'population' => 195000,
                'mayor_name' => 'Judith Piquet',
                'mayor_salary' => 68893.65,
                'latitude' => 40.4810,
                'longitude' => -3.3649,
                'area_km2' => 87.7,
                'altitude_m' => 594,
                'is_capital' => false,
                'tourism_info' => 'Ciudad universitaria, Patrimonio de la Humanidad, cuna de Cervantes.',
            ],
            [
                'name' => 'Móstoles',
                'ine_code' => '28092',
                'postal_code' => '28931',
                'population' => 210000,
                'mayor_name' => 'Manuel Bautista',
                'mayor_salary' => 73569.10,
                'latitude' => 40.3223,
                'longitude' => -3.8650,
                'area_km2' => 45.4,
                'altitude_m' => 660,
                'is_capital' => false,
                'tourism_info' => 'Importante núcleo urbano del sur de Madrid.',
            ],
        ];

        foreach ($municipalities as $data) {
            $data['slug'] = Str::slug($data['name']);
            $data['province_id'] = $province->id;
            $data['autonomous_community_id'] = $community->id;
            $data['country_id'] = $country->id;
            $data['timezone_id'] = $timezone->id;

            Municipality::updateOrCreate(
                ['ine_code' => $data['ine_code']],
                $data
            );
        }

        echo "Municipios de Madrid insertados correctamente.\n";
    }
}