<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EnergyInstallation;
use App\Models\User;
use Carbon\Carbon;

class EnergyInstallationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear algunos usuarios propietarios si no existen
        $users = User::count() > 0 ? User::all() : User::factory(5)->create();

        // Instalaciones solares residenciales (las más comunes en España)
        $solarInstallations = [
            [
                'name' => 'Autoconsumo Solar Residencial Madrid',
                'type' => 'solar',
                'capacity_kw' => 5.5,
                'location' => 'Madrid, Comunidad de Madrid',
                'commissioned_at' => Carbon::now()->subMonths(6),
            ],
            [
                'name' => 'Placas Solares Vivienda Barcelona',
                'type' => 'solar',
                'capacity_kw' => 4.2,
                'location' => 'Barcelona, Cataluña',
                'commissioned_at' => Carbon::now()->subYear(),
            ],
            [
                'name' => 'Instalación Fotovoltaica Valencia',
                'type' => 'solar',
                'capacity_kw' => 6.8,
                'location' => 'Valencia, Comunidad Valenciana',
                'commissioned_at' => Carbon::now()->subMonths(8),
            ],
            [
                'name' => 'Autoconsumo Solar Sevilla',
                'type' => 'solar',
                'capacity_kw' => 7.2,
                'location' => 'Sevilla, Andalucía',
                'commissioned_at' => Carbon::now()->subMonths(3),
            ],
            [
                'name' => 'Planta Solar Comunitaria Zaragoza',
                'type' => 'solar',
                'capacity_kw' => 15.0,
                'location' => 'Zaragoza, Aragón',
                'commissioned_at' => Carbon::now()->subMonths(2),
            ],
        ];

        // Instalaciones eólicas (mini-eólica)
        $windInstallations = [
            [
                'name' => 'Aerogenerador Rural Galicia',
                'type' => 'wind',
                'capacity_kw' => 25.0,
                'location' => 'Lugo, Galicia',
                'commissioned_at' => Carbon::now()->subYears(2),
            ],
            [
                'name' => 'Mini-Eólica Castilla-La Mancha',
                'type' => 'wind',
                'capacity_kw' => 45.0,
                'location' => 'Albacete, Castilla-La Mancha',
                'commissioned_at' => Carbon::now()->subYear(),
            ],
        ];

        // Instalaciones hidráulicas (mini-hidráulica)
        $hydroInstallations = [
            [
                'name' => 'Mini-Hidráulica Río Pirenaico',
                'type' => 'hydro',
                'capacity_kw' => 15.5,
                'location' => 'Huesca, Aragón',
                'commissioned_at' => Carbon::now()->subYears(3),
            ],
            [
                'name' => 'Turbina Hidráulica Asturias',
                'type' => 'hydro',
                'capacity_kw' => 22.0,
                'location' => 'Oviedo, Asturias',
                'commissioned_at' => Carbon::now()->subMonths(10),
            ],
        ];

        // Instalaciones de biomasa
        $biomassInstallations = [
            [
                'name' => 'Planta de Biomasa Agrícola',
                'type' => 'biomass',
                'capacity_kw' => 80.0,
                'location' => 'Lleida, Cataluña',
                'commissioned_at' => Carbon::now()->subMonths(5),
            ],
            [
                'name' => 'Generador de Biomasa Forestal',
                'type' => 'biomass',
                'capacity_kw' => 120.0,
                'location' => 'Soria, Castilla y León',
                'commissioned_at' => Carbon::now()->subMonths(7),
            ],
        ];

        // Instalaciones en desarrollo (sin comisionar)
        $developmentInstallations = [
            [
                'name' => 'Proyecto Solar Comunitario Málaga',
                'type' => 'solar',
                'capacity_kw' => 25.0,
                'location' => 'Málaga, Andalucía',
                'commissioned_at' => null, // En planificación
            ],
            [
                'name' => 'Futuro Parque Eólico Navarra',
                'type' => 'wind',
                'capacity_kw' => 150.0,
                'location' => 'Pamplona, Navarra',
                'commissioned_at' => Carbon::now()->addMonths(6), // En construcción
            ],
            [
                'name' => 'Instalación Solar Industrial Murcia',
                'type' => 'solar',
                'capacity_kw' => 35.0,
                'location' => 'Murcia, Región de Murcia',
                'commissioned_at' => Carbon::now()->addMonths(3), // En construcción
            ],
        ];

        // Combinar todas las instalaciones
        $allInstallations = array_merge(
            $solarInstallations,
            $windInstallations,
            $hydroInstallations,
            $biomassInstallations,
            $developmentInstallations
        );

        foreach ($allInstallations as $installationData) {
            // Asignar un propietario aleatorio
            $installationData['owner_id'] = $users->random()->id;
            
            EnergyInstallation::firstOrCreate(
                ['name' => $installationData['name']],
                $installationData
            );
        }

        // Crear algunas instalaciones adicionales usando la factory
        EnergyInstallation::factory(10)->create([
            'owner_id' => $users->random()->id,
        ]);

        // Crear instalaciones específicas por tipo
        EnergyInstallation::factory(5)->solar()->commissioned()->create([
            'owner_id' => $users->random()->id,
        ]);

        EnergyInstallation::factory(3)->wind()->commissioned()->create([
            'owner_id' => $users->random()->id,
        ]);

        EnergyInstallation::factory(2)->inDevelopment()->create([
            'owner_id' => $users->random()->id,
        ]);

        $total = EnergyInstallation::count();
        $commissioned = EnergyInstallation::commissioned()->count();
        $inDevelopment = EnergyInstallation::inDevelopment()->count();
        $totalCapacity = EnergyInstallation::sum('capacity_kw');

        $this->command->info("EnergyInstallation seeder completed: {$total} installations created.");
        $this->command->info("- Commissioned: {$commissioned}");
        $this->command->info("- In development: {$inDevelopment}");
        $this->command->info("- Total capacity: {$totalCapacity} kW");
        $this->command->info('Data includes:');
        $this->command->info('- Realistic Spanish renewable energy installations');
        $this->command->info('- Different types: solar, wind, hydro, biomass');
        $this->command->info('- Various stages: operational, in construction, in planning');
        $this->command->info('- Capacity ranges typical for each technology');
    }
}