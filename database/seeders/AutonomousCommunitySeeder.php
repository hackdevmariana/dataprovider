<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AutonomousCommunity;
use App\Models\Country;
use App\Models\Timezone;

class AutonomousCommunitySeeder extends Seeder
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

        $communities = [
            ['name' => 'Andalucía', 'slug' => 'andalucia', 'code' => 'AN', 'latitude' => 37.3826, 'longitude' => -5.9963, 'area_km2' => 87144, 'altitude_m' => 600],
            ['name' => 'Aragón', 'slug' => 'aragon', 'code' => 'AR', 'latitude' => 41.6561, 'longitude' => -0.8773, 'area_km2' => 47719, 'altitude_m' => 400],
            ['name' => 'Asturias', 'slug' => 'asturias', 'code' => 'AS', 'latitude' => 43.3619, 'longitude' => -5.8494, 'area_km2' => 10604, 'altitude_m' => 150],
            ['name' => 'Islas Baleares', 'slug' => 'islas-baleares', 'code' => 'IB', 'latitude' => 39.6953, 'longitude' => 3.0176, 'area_km2' => 4992, 'altitude_m' => 120],
            ['name' => 'Canarias', 'slug' => 'canarias', 'code' => 'CN', 'latitude' => 28.2916, 'longitude' => -16.6291, 'area_km2' => 7447, 'altitude_m' => 200],
            ['name' => 'Cantabria', 'slug' => 'cantabria', 'code' => 'CB', 'latitude' => 43.1828, 'longitude' => -3.9874, 'area_km2' => 5321, 'altitude_m' => 150],
            ['name' => 'Castilla y León', 'slug' => 'castilla-y-leon', 'code' => 'CL', 'latitude' => 41.4996, 'longitude' => -4.0007, 'area_km2' => 94735, 'altitude_m' => 800],
            ['name' => 'Castilla-La Mancha', 'slug' => 'castilla-la-mancha', 'code' => 'CM', 'latitude' => 39.2636, 'longitude' => -3.4504, 'area_km2' => 79414, 'altitude_m' => 600],
            ['name' => 'Cataluña', 'slug' => 'cataluna', 'code' => 'CT', 'latitude' => 41.5912, 'longitude' => 1.5209, 'area_km2' => 32114, 'altitude_m' => 500],
            ['name' => 'Comunidad Valenciana', 'slug' => 'comunidad-valenciana', 'code' => 'VC', 'latitude' => 39.9971, 'longitude' => -0.0374, 'area_km2' => 23131, 'altitude_m' => 100],
            ['name' => 'Extremadura', 'slug' => 'extremadura', 'code' => 'EX', 'latitude' => 39.0456, 'longitude' => -6.9647, 'area_km2' => 41079, 'altitude_m' => 400],
            ['name' => 'Galicia', 'slug' => 'galicia', 'code' => 'GA', 'latitude' => 42.5751, 'longitude' => -8.1339, 'area_km2' => 29575, 'altitude_m' => 200],
            ['name' => 'La Rioja', 'slug' => 'la-rioja', 'code' => 'RI', 'latitude' => 42.4621, 'longitude' => -2.4451, 'area_km2' => 5048, 'altitude_m' => 400],
            ['name' => 'Comunidad de Madrid', 'slug' => 'madrid', 'code' => 'MD', 'latitude' => 40.4168, 'longitude' => -3.7038, 'area_km2' => 802, 'altitude_m' => 650],
            ['name' => 'Región de Murcia', 'slug' => 'murcia', 'code' => 'MC', 'latitude' => 37.9922, 'longitude' => -1.1307, 'area_km2' => 11313, 'altitude_m' => 50],
            ['name' => 'Navarra', 'slug' => 'navarra', 'code' => 'NC', 'latitude' => 42.6953, 'longitude' => -1.6761, 'area_km2' => 10391, 'altitude_m' => 400],
            ['name' => 'País Vasco', 'slug' => 'pais-vasco', 'code' => 'PV', 'latitude' => 43.0805, 'longitude' => -2.6787, 'area_km2' => 7234, 'altitude_m' => 300],
            // Ciudades Autónomas
            ['name' => 'Ceuta', 'slug' => 'ceuta', 'code' => 'CE', 'latitude' => 35.8894, 'longitude' => -5.3198, 'area_km2' => 18.5, 'altitude_m' => 20],
            ['name' => 'Melilla', 'slug' => 'melilla', 'code' => 'ML', 'latitude' => 35.2923, 'longitude' => -2.9381, 'area_km2' => 12.3, 'altitude_m' => 30],
        ];

        foreach ($communities as $community) {
            AutonomousCommunity::updateOrCreate(
                ['slug' => $community['slug'], 'country_id' => $spain->id],
                array_merge($community, ['country_id' => $spain->id, 'timezone_id' => $madridTz->id])
            );
            $this->command->info("Comunidad autónoma {$community['name']} creada o actualizada.");
        }
    }
}
