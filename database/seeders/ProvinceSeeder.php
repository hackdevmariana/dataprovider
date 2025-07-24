<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Province;
use App\Models\AutonomousCommunity;
use App\Models\Country;
use App\Models\Timezone;

class ProvinceSeeder extends Seeder
{
    public function run()
    {
        $spain = Country::where('slug', 'espana')->first();
        if (!$spain) {
            $this->command->error('No se encontró el país España. Ejecuta primero el CountrySeeder.');
            return;
        }

        $madridTz = Timezone::where('name', 'Europe/Madrid')->first();
        if (!$madridTz) {
            $this->command->error('No se encontró el timezone Europe/Madrid. Ejecuta primero el TimezoneSeeder.');
            return;
        }

        $provinces = [
            // Andalucía
            ['name' => 'Almería', 'slug' => 'almeria', 'ine_code' => '04', 'community_slug' => 'andalucia', 'latitude' => 36.834, 'longitude' => -2.463, 'area_km2' => 8772, 'altitude_m' => 80],
            ['name' => 'Cádiz', 'slug' => 'cadiz', 'ine_code' => '11', 'community_slug' => 'andalucia', 'latitude' => 36.527, 'longitude' => -6.288, 'area_km2' => 7475, 'altitude_m' => 50],
            ['name' => 'Córdoba', 'slug' => 'cordoba', 'ine_code' => '14', 'community_slug' => 'andalucia', 'latitude' => 37.889, 'longitude' => -4.779, 'area_km2' => 13804, 'altitude_m' => 110],
            ['name' => 'Granada', 'slug' => 'granada', 'ine_code' => '18', 'community_slug' => 'andalucia', 'latitude' => 37.188, 'longitude' => -3.606, 'area_km2' => 12639, 'altitude_m' => 600],
            ['name' => 'Huelva', 'slug' => 'huelva', 'ine_code' => '21', 'community_slug' => 'andalucia', 'latitude' => 37.261, 'longitude' => -6.944, 'area_km2' => 10559, 'altitude_m' => 80],
            ['name' => 'Jaén', 'slug' => 'jaen', 'ine_code' => '23', 'community_slug' => 'andalucia', 'latitude' => 37.766, 'longitude' => -3.785, 'area_km2' => 13499, 'altitude_m' => 600],
            ['name' => 'Málaga', 'slug' => 'malaga', 'ine_code' => '29', 'community_slug' => 'andalucia', 'latitude' => 36.721, 'longitude' => -4.421, 'area_km2' => 7308, 'altitude_m' => 30],
            ['name' => 'Sevilla', 'slug' => 'sevilla', 'ine_code' => '41', 'community_slug' => 'andalucia', 'latitude' => 37.389, 'longitude' => -5.982, 'area_km2' => 14062, 'altitude_m' => 7],

            // Aragón
            ['name' => 'Huesca', 'slug' => 'huesca', 'ine_code' => '22', 'community_slug' => 'aragon', 'latitude' => 42.140, 'longitude' => -0.408, 'area_km2' => 15691, 'altitude_m' => 523],
            ['name' => 'Teruel', 'slug' => 'teruel', 'ine_code' => '44', 'community_slug' => 'aragon', 'latitude' => 40.344, 'longitude' => -1.106, 'area_km2' => 14144, 'altitude_m' => 912],
            ['name' => 'Zaragoza', 'slug' => 'zaragoza', 'ine_code' => '50', 'community_slug' => 'aragon', 'latitude' => 41.648, 'longitude' => -0.889, 'area_km2' => 17281, 'altitude_m' => 199],

            // Asturias
            ['name' => 'Asturias', 'slug' => 'asturias', 'ine_code' => '33', 'community_slug' => 'asturias', 'latitude' => 43.361, 'longitude' => -5.849, 'area_km2' => 10604, 'altitude_m' => 150],

            // Islas Baleares
            ['name' => 'Islas Baleares', 'slug' => 'islas-baleares', 'ine_code' => '07', 'community_slug' => 'islas-baleares', 'latitude' => 39.695, 'longitude' => 3.018, 'area_km2' => 4992, 'altitude_m' => 120],

            // Canarias
            ['name' => 'Las Palmas', 'slug' => 'las-palmas', 'ine_code' => '35', 'community_slug' => 'canarias', 'latitude' => 28.123, 'longitude' => -15.436, 'area_km2' => 4066, 'altitude_m' => 33],
            ['name' => 'Santa Cruz de Tenerife', 'slug' => 'santa-cruz-de-tenerife', 'ine_code' => '38', 'community_slug' => 'canarias', 'latitude' => 28.468, 'longitude' => -16.254, 'area_km2' => 3383, 'altitude_m' => 20],

            // Cantabria
            ['name' => 'Cantabria', 'slug' => 'cantabria', 'ine_code' => '39', 'community_slug' => 'cantabria', 'latitude' => 43.182, 'longitude' => -3.987, 'area_km2' => 5321, 'altitude_m' => 150],

            // Castilla y León
            ['name' => 'Ávila', 'slug' => 'avila', 'ine_code' => '05', 'community_slug' => 'castilla-y-leon', 'latitude' => 40.656, 'longitude' => -4.699, 'area_km2' => 8040, 'altitude_m' => 1100],
            ['name' => 'Burgos', 'slug' => 'burgos', 'ine_code' => '09', 'community_slug' => 'castilla-y-leon', 'latitude' => 42.345, 'longitude' => -3.700, 'area_km2' => 14034, 'altitude_m' => 856],
            ['name' => 'León', 'slug' => 'leon', 'ine_code' => '24', 'community_slug' => 'castilla-y-leon', 'latitude' => 42.598, 'longitude' => -5.567, 'area_km2' => 15578, 'altitude_m' => 837],
            ['name' => 'Palencia', 'slug' => 'palencia', 'ine_code' => '34', 'community_slug' => 'castilla-y-leon', 'latitude' => 42.010, 'longitude' => -4.530, 'area_km2' => 8058, 'altitude_m' => 735],
            ['name' => 'Salamanca', 'slug' => 'salamanca', 'ine_code' => '37', 'community_slug' => 'castilla-y-leon', 'latitude' => 40.967, 'longitude' => -5.663, 'area_km2' => 12305, 'altitude_m' => 800],
            ['name' => 'Segovia', 'slug' => 'segovia', 'ine_code' => '40', 'community_slug' => 'castilla-y-leon', 'latitude' => 40.947, 'longitude' => -4.118, 'area_km2' => 6921, 'altitude_m' => 1000],
            ['name' => 'Soria', 'slug' => 'soria', 'ine_code' => '42', 'community_slug' => 'castilla-y-leon', 'latitude' => 41.765, 'longitude' => -2.466, 'area_km2' => 10328, 'altitude_m' => 1063],
            ['name' => 'Valladolid', 'slug' => 'valladolid', 'ine_code' => '47', 'community_slug' => 'castilla-y-leon', 'latitude' => 41.655, 'longitude' => -4.723, 'area_km2' => 8117, 'altitude_m' => 700],
            ['name' => 'Zamora', 'slug' => 'zamora', 'ine_code' => '49', 'community_slug' => 'castilla-y-leon', 'latitude' => 41.503, 'longitude' => -5.743, 'area_km2' => 10553, 'altitude_m' => 700],

            // Castilla-La Mancha
            ['name' => 'Albacete', 'slug' => 'albacete', 'ine_code' => '02', 'community_slug' => 'castilla-la-mancha', 'latitude' => 38.994, 'longitude' => -1.858, 'area_km2' => 14915, 'altitude_m' => 700],
            ['name' => 'Ciudad Real', 'slug' => 'ciudad-real', 'ine_code' => '13', 'community_slug' => 'castilla-la-mancha', 'latitude' => 38.984, 'longitude' => -3.927, 'area_km2' => 19813, 'altitude_m' => 630],
            ['name' => 'Cuenca', 'slug' => 'cuenca', 'ine_code' => '16', 'community_slug' => 'castilla-la-mancha', 'latitude' => 40.068, 'longitude' => -2.137, 'area_km2' => 17102, 'altitude_m' => 900],
            ['name' => 'Guadalajara', 'slug' => 'guadalajara', 'ine_code' => '19', 'community_slug' => 'castilla-la-mancha', 'latitude' => 40.632, 'longitude' => -3.170, 'area_km2' => 12431, 'altitude_m' => 700],
            ['name' => 'Toledo', 'slug' => 'toledo', 'ine_code' => '45', 'community_slug' => 'castilla-la-mancha', 'latitude' => 39.862, 'longitude' => -4.027, 'area_km2' => 15370, 'altitude_m' => 500],

            // Cataluña
            ['name' => 'Barcelona', 'slug' => 'barcelona', 'ine_code' => '08', 'community_slug' => 'cataluna', 'latitude' => 41.385, 'longitude' => 2.173, 'area_km2' => 7764, 'altitude_m' => 12],
            ['name' => 'Girona', 'slug' => 'girona', 'ine_code' => '17', 'community_slug' => 'cataluna', 'latitude' => 41.979, 'longitude' => 2.821, 'area_km2' => 5835, 'altitude_m' => 100],
            ['name' => 'Lleida', 'slug' => 'lleida', 'ine_code' => '25', 'community_slug' => 'cataluna', 'latitude' => 41.616, 'longitude' => 0.622, 'area_km2' => 12212, 'altitude_m' => 155],
            ['name' => 'Tarragona', 'slug' => 'tarragona', 'ine_code' => '43', 'community_slug' => 'cataluna', 'latitude' => 41.118, 'longitude' => 1.244, 'area_km2' => 6313, 'altitude_m' => 30],

            // Comunidad Valenciana
            ['name' => 'Alicante', 'slug' => 'alicante', 'ine_code' => '03', 'community_slug' => 'comunidad-valenciana', 'latitude' => 38.345, 'longitude' => -0.483, 'area_km2' => 5807, 'altitude_m' => 30],
            ['name' => 'Castellón', 'slug' => 'castellon', 'ine_code' => '12', 'community_slug' => 'comunidad-valenciana', 'latitude' => 39.986, 'longitude' => -0.051, 'area_km2' => 6616, 'altitude_m' => 300],
            ['name' => 'Valencia', 'slug' => 'valencia', 'ine_code' => '46', 'community_slug' => 'comunidad-valenciana', 'latitude' => 39.469, 'longitude' => -0.377, 'area_km2' => 7922, 'altitude_m' => 15],

            // Extremadura
            ['name' => 'Badajoz', 'slug' => 'badajoz', 'ine_code' => '06', 'community_slug' => 'extremadura', 'latitude' => 38.879, 'longitude' => -6.970, 'area_km2' => 21731, 'altitude_m' => 185],
            ['name' => 'Cáceres', 'slug' => 'caceres', 'ine_code' => '10', 'community_slug' => 'extremadura', 'latitude' => 39.476, 'longitude' => -6.372, 'area_km2' => 19813, 'altitude_m' => 445],

            // Galicia
            ['name' => 'A Coruña', 'slug' => 'a-coruna', 'ine_code' => '15', 'community_slug' => 'galicia', 'latitude' => 43.362, 'longitude' => -8.411, 'area_km2' => 7956, 'altitude_m' => 20],
            ['name' => 'Lugo', 'slug' => 'lugo', 'ine_code' => '27', 'community_slug' => 'galicia', 'latitude' => 43.012, 'longitude' => -7.555, 'area_km2' => 9756, 'altitude_m' => 400],
            ['name' => 'Ourense', 'slug' => 'ourense', 'ine_code' => '32', 'community_slug' => 'galicia', 'latitude' => 42.335, 'longitude' => -7.863, 'area_km2' => 7367, 'altitude_m' => 250],
            ['name' => 'Pontevedra', 'slug' => 'pontevedra', 'ine_code' => '36', 'community_slug' => 'galicia', 'latitude' => 42.433, 'longitude' => -8.644, 'area_km2' => 4490, 'altitude_m' => 50],

            // La Rioja
            ['name' => 'La Rioja', 'slug' => 'la-rioja', 'ine_code' => '26', 'community_slug' => 'la-rioja', 'latitude' => 42.462, 'longitude' => -2.445, 'area_km2' => 5048, 'altitude_m' => 400],

            // Comunidad de Madrid
            ['name' => 'Madrid', 'slug' => 'madrid', 'ine_code' => '28', 'community_slug' => 'madrid', 'latitude' => 40.416, 'longitude' => -3.703, 'area_km2' => 802, 'altitude_m' => 650],

            // Región de Murcia
            ['name' => 'Murcia', 'slug' => 'murcia', 'ine_code' => '30', 'community_slug' => 'murcia', 'latitude' => 37.992, 'longitude' => -1.130, 'area_km2' => 11313, 'altitude_m' => 50],

            // Navarra
            ['name' => 'Navarra', 'slug' => 'navarra', 'ine_code' => '31', 'community_slug' => 'navarra', 'latitude' => 42.695, 'longitude' => -1.676, 'area_km2' => 10391, 'altitude_m' => 400],

            // País Vasco
            ['name' => 'Álava', 'slug' => 'alava', 'ine_code' => '01', 'community_slug' => 'pais-vasco', 'latitude' => 42.851, 'longitude' => -2.672, 'area_km2' => 3034, 'altitude_m' => 600],
            ['name' => 'Guipúzcoa', 'slug' => 'guipuzcoa', 'ine_code' => '20', 'community_slug' => 'pais-vasco', 'latitude' => 43.263, 'longitude' => -2.928, 'area_km2' => 1980, 'altitude_m' => 350],
            ['name' => 'Vizcaya', 'slug' => 'vizcaya', 'ine_code' => '48', 'community_slug' => 'pais-vasco', 'latitude' => 43.263, 'longitude' => -2.934, 'area_km2' => 2238, 'altitude_m' => 400],

            // Ceuta
            ['name' => 'Ceuta', 'slug' => 'ceuta', 'ine_code' => '51', 'community_slug' => 'ceuta', 'latitude' => 35.889, 'longitude' => -5.321, 'area_km2' => 20, 'altitude_m' => 0],

            // Melilla
            ['name' => 'Melilla', 'slug' => 'melilla', 'ine_code' => '52', 'community_slug' => 'melilla', 'latitude' => 35.291, 'longitude' => -2.938, 'area_km2' => 12, 'altitude_m' => 0],
        ];

        foreach ($provinces as $provinceData) {
            $community = AutonomousCommunity::where('slug', $provinceData['community_slug'])->first();
            if (!$community) {
                $this->command->warn("No se encontró la comunidad autónoma {$provinceData['community_slug']} para la provincia {$provinceData['name']}");
                continue;
            }

            Province::updateOrCreate(
                ['ine_code' => $provinceData['ine_code']],
                [
                    'name' => $provinceData['name'],
                    'slug' => $provinceData['slug'],
                    'autonomous_community_id' => $community->id,
                    'country_id' => $spain->id,
                    'latitude' => $provinceData['latitude'],
                    'longitude' => $provinceData['longitude'],
                    'area_km2' => $provinceData['area_km2'],
                    'altitude_m' => $provinceData['altitude_m'],
                    'timezone_id' => $madridTz->id,
                ]
            );
        }
    }
}
