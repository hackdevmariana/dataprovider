<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CompanyType;

class CompanyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companyTypes = [
            [
                'name' => 'Comercializadora',
                'slug' => 'comercializadora',
                'description' => 'Empresa que vende energía eléctrica directamente a los consumidores finales. Se encarga de la facturación y atención al cliente.',
                'icon_url' => 'heroicon-o-currency-euro',
            ],
            [
                'name' => 'Distribuidora',
                'slug' => 'distribuidora',
                'description' => 'Empresa propietaria de las redes de distribución de energía eléctrica. Transporta la electricidad desde las redes de transporte hasta los puntos de consumo.',
                'icon_url' => 'heroicon-o-bolt',
            ],
            [
                'name' => 'Mixta',
                'slug' => 'mixta',
                'description' => 'Empresa que combina actividades de comercialización y distribución de energía eléctrica.',
                'icon_url' => 'heroicon-o-building-office',
            ],
            [
                'name' => 'Generadora',
                'slug' => 'generadora',
                'description' => 'Empresa dedicada a la producción de energía eléctrica mediante diferentes tecnologías (renovables, térmicas, nucleares, etc.).',
                'icon_url' => 'heroicon-o-sun',
            ],
            [
                'name' => 'Transportista',
                'slug' => 'transportista',
                'description' => 'Empresa propietaria y operadora de las redes de transporte de alta tensión. En España, Red Eléctrica de España (REE).',
                'icon_url' => 'heroicon-o-map',
            ],
            [
                'name' => 'Agregador',
                'slug' => 'agregador',
                'description' => 'Empresa que agrupa múltiples instalaciones de generación distribuida o consumidores para participar en los mercados energéticos.',
                'icon_url' => 'heroicon-o-squares-plus',
            ],
            [
                'name' => 'Instaladora',
                'slug' => 'instaladora',
                'description' => 'Empresa especializada en la instalación de sistemas de generación de energía renovable (paneles solares, aerogeneradores, etc.).',
                'icon_url' => 'heroicon-o-wrench-screwdriver',
            ],
            [
                'name' => 'Mantenimiento',
                'slug' => 'mantenimiento',
                'description' => 'Empresa dedicada al mantenimiento y operación de instalaciones energéticas.',
                'icon_url' => 'heroicon-o-cog-6-tooth',
            ],
            [
                'name' => 'Consultoría Energética',
                'slug' => 'consultoria-energetica',
                'description' => 'Empresa que ofrece servicios de consultoría en eficiencia energética, auditorías energéticas y optimización de consumos.',
                'icon_url' => 'heroicon-o-light-bulb',
            ],
            [
                'name' => 'Cooperativa Energética',
                'slug' => 'cooperativa-energetica',
                'description' => 'Cooperativa de consumidores que comercializa energía eléctrica, generalmente de origen renovable, a sus socios.',
                'icon_url' => 'heroicon-o-users',
            ],
            [
                'name' => 'Proveedor de Servicios',
                'slug' => 'proveedor-servicios',
                'description' => 'Empresa que ofrece servicios auxiliares al sector energético (software, monitorización, gestión de datos, etc.).',
                'icon_url' => 'heroicon-o-computer-desktop',
            ],
            [
                'name' => 'Certificadora',
                'slug' => 'certificadora',
                'description' => 'Empresa autorizada para emitir certificados energéticos (CEA, Garantías de Origen, etc.).',
                'icon_url' => 'heroicon-o-document-check',
            ],
        ];

        foreach ($companyTypes as $companyType) {
            CompanyType::firstOrCreate(
                ['slug' => $companyType['slug']],
                $companyType
            );
        }

        $this->command->info('CompanyType seeder completed: ' . count($companyTypes) . ' company types created/updated.');
    }
}