<?php

namespace Database\Seeders;

use App\Models\PilgrimageSite;
use App\Models\CatholicSaint;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PilgrimageSiteSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creando lugares de peregrinación...');

        // Verificar que existen santos
        $saints = CatholicSaint::limit(10)->get();
        if ($saints->isEmpty()) {
            $this->command->warn('No hay santos disponibles. Creando santos de prueba...');
            for ($i = 1; $i <= 5; $i++) {
                CatholicSaint::create([
                    'name' => 'Santo de Prueba ' . $i,
                    'feast_day' => fake()->date(),
                    'description' => 'Descripción del santo de prueba ' . $i,
                ]);
            }
            $saints = CatholicSaint::limit(10)->get();
        }

        $pilgrimageSites = [
            [
                'name' => 'Santiago de Compostela',
                'description' => 'Ciudad santa y destino del Camino de Santiago, una de las peregrinaciones más importantes del cristianismo.',
                'saint_id' => $saints->random()->id,
                'location' => 'Santiago de Compostela',
                'latitude' => 42.8805,
                'longitude' => -8.5456,
                'country' => 'España',
                'region' => 'Galicia',
                'city' => 'Santiago de Compostela',
                'type' => 'cathedral',
                'facilities' => ['Albergues', 'Restaurantes', 'Farmacias', 'Oficina de turismo'],
                'accommodation' => ['Albergues públicos', 'Hoteles', 'Casas rurales'],
                'transportation' => ['Autobús', 'Tren', 'Aeropuerto'],
                'best_time_to_visit' => 'Mayo a octubre',
                'annual_pilgrims' => 300000,
                'special_dates' => ['25 de julio - Día de Santiago', 'Semana Santa'],
                'is_active' => true,
            ],
            [
                'name' => 'Lourdes',
                'description' => 'Santuario mariano donde la Virgen María se apareció a Santa Bernadette Soubirous en 1858.',
                'saint_id' => $saints->random()->id,
                'location' => 'Lourdes',
                'latitude' => 43.0975,
                'longitude' => -0.0458,
                'country' => 'Francia',
                'region' => 'Occitania',
                'city' => 'Lourdes',
                'type' => 'shrine',
                'facilities' => ['Hospitalidad', 'Centro médico', 'Tiendas religiosas'],
                'accommodation' => ['Hoteles', 'Albergues', 'Casas de acogida'],
                'transportation' => ['Tren', 'Autobús', 'Aeropuerto'],
                'best_time_to_visit' => 'Todo el año',
                'annual_pilgrims' => 6000000,
                'special_dates' => ['11 de febrero - Aparición', '15 de agosto - Asunción'],
                'is_active' => true,
            ],
            [
                'name' => 'Fátima',
                'description' => 'Santuario donde la Virgen María se apareció a tres pastorcitos en 1917.',
                'saint_id' => $saints->random()->id,
                'location' => 'Fátima',
                'latitude' => 39.6200,
                'longitude' => -8.6700,
                'country' => 'Portugal',
                'region' => 'Centro',
                'city' => 'Fátima',
                'type' => 'shrine',
                'facilities' => ['Capilla de las Apariciones', 'Basílica', 'Museo'],
                'accommodation' => ['Hoteles', 'Casas de acogida'],
                'transportation' => ['Autobús', 'Tren'],
                'best_time_to_visit' => 'Mayo a octubre',
                'annual_pilgrims' => 4000000,
                'special_dates' => ['13 de mayo - Primera aparición', '13 de octubre - Milagro del sol'],
                'is_active' => true,
            ],
            [
                'name' => 'Basílica de San Pedro',
                'description' => 'Basílica principal del Vaticano y centro espiritual del catolicismo mundial.',
                'saint_id' => $saints->random()->id,
                'location' => 'Ciudad del Vaticano',
                'latitude' => 41.9022,
                'longitude' => 12.4539,
                'country' => 'Vaticano',
                'region' => 'Lacio',
                'city' => 'Roma',
                'type' => 'basilica',
                'facilities' => ['Museos Vaticanos', 'Capilla Sixtina', 'Tumbas papales'],
                'accommodation' => ['Hoteles en Roma'],
                'transportation' => ['Metro', 'Autobús', 'Taxi'],
                'best_time_to_visit' => 'Todo el año',
                'annual_pilgrims' => 7000000,
                'special_dates' => ['Navidad', 'Pascua', 'Audiencias papales'],
                'is_active' => true,
            ],
            [
                'name' => 'Montserrat',
                'description' => 'Monasterio benedictino en la montaña de Montserrat, hogar de la Moreneta.',
                'saint_id' => $saints->random()->id,
                'location' => 'Montserrat',
                'latitude' => 41.5956,
                'longitude' => 1.8381,
                'country' => 'España',
                'region' => 'Cataluña',
                'city' => 'Monistrol de Montserrat',
                'type' => 'monastery',
                'facilities' => ['Monasterio', 'Museo', 'Restaurante'],
                'accommodation' => ['Hotel del monasterio'],
                'transportation' => ['Funicular', 'Autobús'],
                'best_time_to_visit' => 'Primavera y otoño',
                'annual_pilgrims' => 2500000,
                'special_dates' => ['27 de abril - Día de Montserrat'],
                'is_active' => true,
            ],
        ];

        $count = 0;
        foreach ($pilgrimageSites as $siteData) {
            PilgrimageSite::create($siteData);
            $count++;
        }

        // Crear sitios adicionales aleatorios
        $types = ['shrine', 'basilica', 'cathedral', 'church', 'monastery', 'convent', 'hermitage', 'chapel'];
        $countries = ['España', 'Francia', 'Italia', 'Portugal', 'México', 'Brasil', 'Argentina', 'Colombia'];

        for ($i = 0; $i < 10; $i++) {
            PilgrimageSite::create([
                'name' => fake()->city() . ' - ' . fake()->randomElement(['Santuario', 'Basílica', 'Monasterio']),
                'description' => fake()->paragraph(2),
                'saint_id' => fake()->optional(0.8)->randomElement($saints->pluck('id')->toArray()),
                'location' => fake()->city(),
                'latitude' => fake()->latitude(),
                'longitude' => fake()->longitude(),
                'country' => fake()->randomElement($countries),
                'region' => fake()->state(),
                'city' => fake()->city(),
                'type' => fake()->randomElement($types),
                'facilities' => fake()->randomElements(['Albergues', 'Restaurantes', 'Farmacias', 'Tiendas'], rand(1, 4)),
                'accommodation' => fake()->randomElements(['Hoteles', 'Albergues', 'Casas rurales'], rand(1, 3)),
                'transportation' => fake()->randomElements(['Autobús', 'Tren', 'Aeropuerto'], rand(1, 3)),
                'best_time_to_visit' => fake()->randomElement(['Todo el año', 'Primavera', 'Verano', 'Otoño']),
                'annual_pilgrims' => fake()->numberBetween(1000, 500000),
                'special_dates' => fake()->randomElements(['Fiesta patronal', 'Semana Santa', 'Navidad'], rand(1, 2)),
                'is_active' => fake()->boolean(90),
            ]);
            $count++;
        }

        $this->command->info("✅ Creados {$count} lugares de peregrinación");
        $this->showStatistics();
    }

    private function showStatistics(): void
    {
        $total = PilgrimageSite::count();
        $active = PilgrimageSite::where('is_active', true)->count();
        
        $this->command->info("\n📊 Estadísticas:");
        $this->command->info("   Total sitios: {$total}");
        $this->command->info("   Activos: {$active}");
        
        $types = PilgrimageSite::selectRaw('type, COUNT(*) as count')->groupBy('type')->get();
        $this->command->info("\n🏛️ Por tipo:");
        foreach ($types as $type) {
            $this->command->info("   {$type->type}: {$type->count}");
        }
        
        $countries = PilgrimageSite::selectRaw('country, COUNT(*) as count')->groupBy('country')->get();
        $this->command->info("\n🌍 Por país:");
        foreach ($countries as $country) {
            $this->command->info("   {$country->country}: {$country->count}");
        }
    }
}