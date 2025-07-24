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
            [
                'name' => 'España',
                'slug' => 'espana',
                'iso_alpha2' => 'ES',
                'iso_alpha3' => 'ESP',
                'iso_numeric' => '724',
                'demonym' => 'español',
                'official_language' => 'es',
                'currency_code' => 'EUR',
                'phone_code' => '+34',
                'latitude' => 40.4168,
                'longitude' => -3.7038,
                'flag_url' => 'https://flagcdn.com/es.svg',
                'population' => 47450795,
                'gdp_usd' => 1600000000000,
                'region_group' => 'Europa',
                'area_km2' => 505944,
                'altitude_m' => 650, // promedio aproximado
                'timezone_name' => 'Europe/Madrid',
            ],
            [
                'name' => 'Andorra',
                'slug' => 'andorra',
                'iso_alpha2' => 'AD',
                'iso_alpha3' => 'AND',
                'iso_numeric' => '020',
                'demonym' => 'andorrano',
                'official_language' => 'ca, es',
                'currency_code' => 'EUR',
                'phone_code' => '+376',
                'latitude' => 42.5078,
                'longitude' => 1.5211,
                'flag_url' => 'https://flagcdn.com/ad.svg',
                'population' => 79824,
                'gdp_usd' => 3150000000,
                'region_group' => 'Europa',
                'area_km2' => 468,
                'altitude_m' => 1996, // promedio montañoso
                'timezone_name' => 'Europe/Andorra',
            ],
            [
                'name' => 'Guinea Ecuatorial',
                'slug' => 'guinea-ecuatorial',
                'iso_alpha2' => 'GQ',
                'iso_alpha3' => 'GNQ',
                'iso_numeric' => '226',
                'demonym' => 'ecuatoguineano',
                'official_language' => 'es, fr, pt',
                'currency_code' => 'XAF',
                'phone_code' => '+240',
                'latitude' => 1.6508,
                'longitude' => 10.2679,
                'flag_url' => 'https://flagcdn.com/gq.svg',
                'population' => 1590000,
                'gdp_usd' => 10400000000,
                'region_group' => 'África',
                'area_km2' => 28051,
                'altitude_m' => 577,
                'timezone_name' => 'Africa/Malabo',
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
