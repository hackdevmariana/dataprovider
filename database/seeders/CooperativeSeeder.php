<?php

namespace Database\Seeders;

use App\Models\Cooperative;
use App\Models\Municipality;
use App\Models\DataSource;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CooperativeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener algunos municipios y fuentes de datos existentes
        $municipalities = Municipality::limit(20)->get();
        $dataSources = DataSource::limit(5)->get();
        
        if ($municipalities->isEmpty()) {
            $this->command->warn('No hay municipios disponibles. Creando municipios de ejemplo...');
            $municipalities = Municipality::factory()->count(10)->create();
        }

        // Crear cooperativas específicas y realistas
        $cooperatives = [
            // Cooperativas energéticas
            [
                'name' => 'Som Energia',
                'slug' => 'som-energia',
                'legal_name' => 'Som Energia SCCL',
                'cooperative_type' => 'energy',
                'scope' => 'national',
                'nif' => 'G55034080',
                'founded_at' => '2010-12-01',
                'phone' => '972 183 380',
                'email' => 'info@somenergia.coop',
                'website' => 'https://www.somenergia.coop',
                'logo_url' => 'https://www.somenergia.coop/wp-content/uploads/2019/07/logo-som-energia.png',
                'municipality_id' => $municipalities->random()->id,
                'address' => 'Carrer de la Pau, 8, 17002 Girona',
                'latitude' => 41.9794,
                'longitude' => 2.8214,
                'description' => 'Primera cooperativa de consumo de energía 100% renovable de España. Ofrecemos electricidad verde y participamos en la transición energética.',
                'number_of_members' => 85000,
                'main_activity' => 'Comercialización de energía renovable',
                'is_open_to_new_members' => true,
                'source' => 'manual',
                'data_source_id' => $dataSources->random()->id ?? null,
                'has_energy_market_access' => true,
                'legal_form' => 'Sociedad Cooperativa',
                'statutes_url' => 'https://www.somenergia.coop/es/estatutos/',
                'accepts_new_installations' => true,
            ],
            [
                'name' => 'GoiEner',
                'slug' => 'goiener',
                'legal_name' => 'GoiEner S. Coop.',
                'cooperative_type' => 'energy',
                'scope' => 'regional',
                'nif' => 'G75123456',
                'founded_at' => '2012-03-15',
                'phone' => '943 123 456',
                'email' => 'info@goiener.com',
                'website' => 'https://www.goiener.com',
                'municipality_id' => $municipalities->random()->id,
                'address' => 'Calle Mayor, 15, 20001 Donostia-San Sebastián',
                'latitude' => 43.3183,
                'longitude' => -1.9812,
                'description' => 'Cooperativa vasca de energía renovable que promueve el autoconsumo y la soberanía energética.',
                'number_of_members' => 12000,
                'main_activity' => 'Autoconsumo colectivo y comercialización',
                'is_open_to_new_members' => true,
                'source' => 'manual',
                'has_energy_market_access' => true,
                'legal_form' => 'Sociedad Cooperativa Andaluza',
                'accepts_new_installations' => true,
            ],
            [
                'name' => 'Enercoop',
                'slug' => 'enercoop',
                'legal_name' => 'Enercoop S. Coop.',
                'cooperative_type' => 'energy',
                'scope' => 'local',
                'nif' => 'G12345678',
                'founded_at' => '2015-06-20',
                'phone' => '91 234 5678',
                'email' => 'contacto@enercoop.es',
                'website' => 'https://www.enercoop.es',
                'municipality_id' => $municipalities->random()->id,
                'address' => 'Calle de la Energía, 25, 28001 Madrid',
                'latitude' => 40.4168,
                'longitude' => -3.7038,
                'description' => 'Cooperativa madrileña especializada en instalaciones fotovoltaicas y eficiencia energética.',
                'number_of_members' => 2500,
                'main_activity' => 'Instalaciones fotovoltaicas',
                'is_open_to_new_members' => true,
                'source' => 'manual',
                'has_energy_market_access' => false,
                'legal_form' => 'Sociedad Cooperativa',
                'accepts_new_installations' => true,
            ],
            // Cooperativas de vivienda
            [
                'name' => 'Sostre Cívic',
                'slug' => 'sostre-civic',
                'legal_name' => 'Sostre Cívic SCCL',
                'cooperative_type' => 'housing',
                'scope' => 'regional',
                'nif' => 'G55123456',
                'founded_at' => '2010-01-10',
                'phone' => '93 123 4567',
                'email' => 'info@sostrecivic.coop',
                'website' => 'https://www.sostrecivic.coop',
                'municipality_id' => $municipalities->random()->id,
                'address' => 'Carrer de la Pau, 12, 08002 Barcelona',
                'latitude' => 41.3851,
                'longitude' => 2.1734,
                'description' => 'Cooperativa de vivienda en cesión de uso que promueve el derecho a la vivienda digna.',
                'number_of_members' => 800,
                'main_activity' => 'Vivienda colaborativa',
                'is_open_to_new_members' => true,
                'source' => 'manual',
                'has_energy_market_access' => false,
                'legal_form' => 'Sociedad Cooperativa',
                'accepts_new_installations' => false,
            ],
            // Cooperativas agrícolas
            [
                'name' => 'La Verde',
                'slug' => 'la-verde',
                'legal_name' => 'La Verde S. Coop.',
                'cooperative_type' => 'agriculture',
                'scope' => 'local',
                'nif' => 'G98765432',
                'founded_at' => '2008-04-15',
                'phone' => '95 678 9012',
                'email' => 'info@laverde.coop',
                'website' => 'https://www.laverde.coop',
                'municipality_id' => $municipalities->random()->id,
                'address' => 'Finca La Verde, s/n, 41000 Sevilla',
                'latitude' => 37.3891,
                'longitude' => -5.9845,
                'description' => 'Cooperativa agrícola ecológica especializada en producción de hortalizas y frutas.',
                'number_of_members' => 45,
                'main_activity' => 'Producción agrícola ecológica',
                'is_open_to_new_members' => false,
                'source' => 'manual',
                'has_energy_market_access' => false,
                'legal_form' => 'Sociedad Cooperativa Andaluza',
                'accepts_new_installations' => true,
            ],
        ];

        // Insertar las cooperativas específicas
        foreach ($cooperatives as $cooperative) {
            Cooperative::updateOrCreate(
                ['slug' => $cooperative['slug']], // Buscar por slug único
                $cooperative
            );
        }

        // Crear cooperativas adicionales usando el factory
        Cooperative::factory()
            ->count(15)
            ->create();

        $this->command->info('Cooperativas creadas exitosamente.');
    }
}