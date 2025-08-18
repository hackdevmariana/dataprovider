<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cooperative;
use App\Models\Municipality;
use Carbon\Carbon;

class CooperativeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar municipios o crear algunos si no existen
        $madrid = Municipality::where('name', 'Madrid')->first() ?? 
                 Municipality::factory()->create(['name' => 'Madrid']);
                 
        $barcelona = Municipality::where('name', 'Barcelona')->first() ?? 
                    Municipality::factory()->create(['name' => 'Barcelona']);
                    
        $girona = Municipality::where('name', 'Girona')->first() ?? 
                 Municipality::factory()->create(['name' => 'Girona']);
                 
        $bilbao = Municipality::where('name', 'Bilbao')->first() ?? 
                 Municipality::factory()->create(['name' => 'Bilbao']);

        // Cooperativas energéticas reales españolas
        $energyCooperatives = [
            [
                'name' => 'Som Energia',
                'slug' => 'som-energia',
                'legal_name' => 'Som Energia SCCL',
                'cooperative_type' => 'energy',
                'scope' => 'national',
                'nif' => 'F55091367',
                'founded_at' => Carbon::createFromDate(2010, 12, 1),
                'phone' => '872 202 550',
                'email' => 'info@somenergia.coop',
                'website' => 'https://www.somenergia.coop',
                'municipality_id' => $girona->id,
                'address' => 'Pic de Peguera, 15, 17003 Girona',
                'latitude' => 41.9794,
                'longitude' => 2.8214,
                'description' => 'Som Energia es una cooperativa de consumo de energía verde sin ánimo de lucro. Su objetivo es cambiar el modelo energético actual hacia un modelo 100% renovable fomentando el uso eficiente de la energía, la generación renovable, y la comercialización de energía 100% renovable.',
                'number_of_members' => 65000,
                'main_activity' => 'Comercialización de energía 100% renovable',
                'is_open_to_new_members' => true,
                'source' => 'manual',
                'has_energy_market_access' => true,
                'legal_form' => 'Sociedad Cooperativa Catalana Limitada',
                'statutes_url' => 'https://www.somenergia.coop/es/conoce-som-energia/estatutos/',
                'accepts_new_installations' => true,
            ],
            [
                'name' => 'Goiener',
                'slug' => 'goiener',
                'legal_name' => 'Goiener SCoop',
                'cooperative_type' => 'energy',
                'scope' => 'national',
                'nif' => 'F20893883',
                'founded_at' => Carbon::createFromDate(2012, 6, 1),
                'phone' => '944 264 007',
                'email' => 'info@goiener.com',
                'website' => 'https://www.goiener.com',
                'municipality_id' => $bilbao->id,
                'address' => 'Beato Tomás de Zumárraga, 71, 48013 Bilbao',
                'latitude' => 43.2603,
                'longitude' => -2.9326,
                'description' => 'Goiener es una cooperativa de energía verde que comercializa electricidad de origen 100% renovable. Nacida en Euskadi, tiene como objetivo democratizar el sector energético y promover el autoconsumo y la eficiencia energética.',
                'number_of_members' => 18000,
                'main_activity' => 'Energía verde del País Vasco',
                'is_open_to_new_members' => true,
                'source' => 'manual',
                'has_energy_market_access' => true,
                'legal_form' => 'Sociedad Cooperativa',
                'statutes_url' => 'https://www.goiener.com/estatutos',
                'accepts_new_installations' => true,
            ],
            [
                'name' => 'Zencer',
                'slug' => 'zencer',
                'legal_name' => 'Zencer SCoop',
                'cooperative_type' => 'energy',
                'scope' => 'national',
                'nif' => 'F87654321',
                'founded_at' => Carbon::createFromDate(2017, 3, 1),
                'phone' => '910 858 676',
                'email' => 'hola@zencer.es',
                'website' => 'https://zencer.es',
                'municipality_id' => $madrid->id,
                'address' => 'Calle de la Princesa, 31, 28008 Madrid',
                'latitude' => 40.4296,
                'longitude' => -3.7129,
                'description' => 'Zencer es una cooperativa digital de energía verde que utiliza tecnología blockchain para crear un marketplace energético descentralizado. Ofrece energía 100% renovable y promueve el autoconsumo inteligente.',
                'number_of_members' => 5000,
                'main_activity' => 'Cooperativa digital con app móvil',
                'is_open_to_new_members' => true,
                'source' => 'manual',
                'has_energy_market_access' => true,
                'legal_form' => 'Sociedad Cooperativa',
                'statutes_url' => null,
                'accepts_new_installations' => true,
            ],
            [
                'name' => 'Energética Coop',
                'slug' => 'energetica-coop',
                'legal_name' => 'Energética Cooperativa Andaluza',
                'cooperative_type' => 'energy',
                'scope' => 'regional',
                'nif' => 'F41123456',
                'founded_at' => Carbon::createFromDate(2015, 9, 1),
                'phone' => '954 123 456',
                'email' => 'info@energeticacoop.es',
                'website' => 'https://www.energeticacoop.es',
                'municipality_id' => $madrid->id, // Placeholder
                'address' => 'Calle Sierpes, 45, 41004 Sevilla',
                'latitude' => 37.3886,
                'longitude' => -5.9953,
                'description' => 'Cooperativa energética andaluza especializada en autoconsumo solar y eficiencia energética. Promueve la transición energética en el sur de España a través de proyectos comunitarios y servicios energéticos integrales.',
                'number_of_members' => 1200,
                'main_activity' => 'Autoconsumo solar comunitario en Andalucía',
                'is_open_to_new_members' => true,
                'source' => 'manual',
                'has_energy_market_access' => false,
                'legal_form' => 'Sociedad Cooperativa Andaluza',
                'statutes_url' => null,
                'accepts_new_installations' => true,
            ],
        ];

        // Cooperativas de vivienda
        $housingCooperatives = [
            [
                'name' => 'La Borda',
                'slug' => 'la-borda',
                'legal_name' => 'Cooperativa La Borda SCCL',
                'cooperative_type' => 'housing',
                'scope' => 'local',
                'nif' => 'F66123456',
                'founded_at' => Carbon::createFromDate(2012, 1, 1),
                'phone' => '934 567 890',
                'email' => 'info@laborda.coop',
                'website' => 'https://www.laborda.coop',
                'municipality_id' => $barcelona->id,
                'address' => 'Carrer de les Tres Torres, 49, 08024 Barcelona',
                'latitude' => 41.4036,
                'longitude' => 2.1323,
                'description' => 'La Borda es una cooperativa de vivienda en cesión de uso que ha construido un edificio de 28 viviendas en Barcelona. Es un proyecto pionero en España que demuestra la viabilidad de modelos habitacionales alternativos.',
                'number_of_members' => 65,
                'main_activity' => 'Vivienda cooperativa en cesión de uso',
                'is_open_to_new_members' => false,
                'source' => 'manual',
                'has_energy_market_access' => false,
                'legal_form' => 'Sociedad Cooperativa Catalana Limitada',
                'statutes_url' => 'https://www.laborda.coop/estatutos',
                'accepts_new_installations' => false,
            ],
            [
                'name' => 'Cooperativa de Viviendas Entrepatios',
                'slug' => 'entrepatios',
                'legal_name' => 'Entrepatios Sociedad Cooperativa Madrileña',
                'cooperative_type' => 'housing',
                'scope' => 'local',
                'nif' => 'F28789012',
                'founded_at' => Carbon::createFromDate(2014, 4, 1),
                'phone' => '914 567 890',
                'email' => 'info@entrepatios.es',
                'website' => 'https://www.entrepatios.es',
                'municipality_id' => $madrid->id,
                'address' => 'Ronda de Segovia, 50, 28005 Madrid',
                'latitude' => 40.4053,
                'longitude' => -3.7109,
                'description' => 'Entrepatios es una cooperativa de viviendas en cesión de uso ubicada en Madrid. El proyecto incluye 17 viviendas diseñadas con criterios de sostenibilidad, cohousing y participación comunitaria.',
                'number_of_members' => 40,
                'main_activity' => 'Cohousing y vivienda sostenible',
                'is_open_to_new_members' => false,
                'source' => 'manual',
                'has_energy_market_access' => false,
                'legal_form' => 'Sociedad Cooperativa Madrileña',
                'statutes_url' => null,
                'accepts_new_installations' => true, // Para placas solares propias
            ],
        ];

        // Combinar todas las cooperativas
        $allCooperatives = array_merge($energyCooperatives, $housingCooperatives);

        foreach ($allCooperatives as $cooperativeData) {
            Cooperative::firstOrCreate(
                ['slug' => $cooperativeData['slug']],
                $cooperativeData
            );
        }

        // Crear algunas cooperativas adicionales usando la factory
        Cooperative::factory(5)->energy()->create();
        Cooperative::factory(3)->housing()->create();
        Cooperative::factory(2)->agriculture()->create();

        $total = Cooperative::count();
        $energy = Cooperative::energy()->count();
        $housing = Cooperative::where('cooperative_type', 'housing')->count();
        $totalMembers = Cooperative::sum('number_of_members');

        $this->command->info("Cooperative seeder completed: {$total} cooperatives created.");
        $this->command->info("- Energy cooperatives: {$energy}");
        $this->command->info("- Housing cooperatives: {$housing}");
        $this->command->info("- Total members: {$totalMembers}");
        $this->command->info('Data includes:');
        $this->command->info('- Real Spanish cooperatives (Som Energia, Goiener, Zencer, La Borda, Entrepatios)');
        $this->command->info('- Energy, housing and agriculture cooperatives');
        $this->command->info('- Various scopes: local, regional, national');
        $this->command->info('- Realistic member counts and activities');
    }
}