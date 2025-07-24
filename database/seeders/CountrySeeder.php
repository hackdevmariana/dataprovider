<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\Timezone;

class CountrySeeder extends Seeder
{
    public function run()
    {
        $countries = [
            [
                'name' => 'Argentina',
                'slug' => 'argentina',
                'iso_alpha2' => 'AR',
                'iso_alpha3' => 'ARG',
                'iso_numeric' => '032',
                'demonym' => 'argentino',
                'official_language' => 'es',
                'currency_code' => 'ARS',
                'phone_code' => '+54',
                'latitude' => -34.6037,
                'longitude' => -58.3816,
                'flag_url' => 'https://flagcdn.com/ar.svg',
                'population' => 45376763,
                'gdp_usd' => 491000000000,
                'region_group' => 'Sudamérica',
                'area_km2' => 2780400,
                'altitude_m' => 595,
                'timezone_name' => 'America/Argentina/Buenos_Aires',
            ],
            [
                'name' => 'México',
                'slug' => 'mexico',
                'iso_alpha2' => 'MX',
                'iso_alpha3' => 'MEX',
                'iso_numeric' => '484',
                'demonym' => 'mexicano',
                'official_language' => 'es',
                'currency_code' => 'MXN',
                'phone_code' => '+52',
                'latitude' => 19.4326,
                'longitude' => -99.1332,
                'flag_url' => 'https://flagcdn.com/mx.svg',
                'population' => 126014024,
                'gdp_usd' => 1270000000000,
                'region_group' => 'Norteamérica',
                'area_km2' => 1964375,
                'altitude_m' => 2250,
                'timezone_name' => 'America/Mexico_City',
            ],
            [
                'name' => 'Colombia',
                'slug' => 'colombia',
                'iso_alpha2' => 'CO',
                'iso_alpha3' => 'COL',
                'iso_numeric' => '170',
                'demonym' => 'colombiano',
                'official_language' => 'es',
                'currency_code' => 'COP',
                'phone_code' => '+57',
                'latitude' => 4.7110,
                'longitude' => -74.0721,
                'flag_url' => 'https://flagcdn.com/co.svg',
                'population' => 51609000,
                'gdp_usd' => 314000000000,
                'region_group' => 'Sudamérica',
                'area_km2' => 1141748,
                'altitude_m' => 2640,
                'timezone_name' => 'America/Bogota',
            ],
        ];

        foreach ($countries as $data) {
            $timezone = Timezone::where('name', $data['timezone_name'])->first();

            if (!$timezone) {
                echo "No se encontró el timezone: {$data['timezone_name']} para {$data['name']}\n";
                continue;
            }

            $data['timezone_id'] = $timezone->id;
            unset($data['timezone_name']); // ya no se necesita

            Country::create($data);
            echo "País creado: {$data['name']}\n";
        }
    }
}
