<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EnergyCompany;

class EnergyCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            // Grandes comercializadoras tradicionales
            [
                'name' => 'Iberdrola Clientes',
                'slug' => 'iberdrola-clientes',
                'website' => 'https://www.iberdrola.es',
                'phone_customer' => '900 171 171',
                'phone_commercial' => '800 760 909',
                'email_customer' => 'atencion.cliente@iberdrola.com',
                'email_commercial' => 'comercial@iberdrola.com',
                'highlighted_offer' => 'Plan Estable: tarifa fija 12 meses',
                'cnmc_id' => '0031',
                'company_type' => 'comercializadora',
                'address' => 'Plaza Euskadi, 5, 48009 Bilbao',
                'coverage_scope' => 'nacional',
                'municipality_id' => null, // Bilbao
            ],
            [
                'name' => 'Endesa Energía',
                'slug' => 'endesa-energia',
                'website' => 'https://www.endesa.com',
                'phone_customer' => '800 760 909',
                'phone_commercial' => '800 760 266',
                'email_customer' => 'atencion.cliente@endesa.es',
                'email_commercial' => 'comercial@endesa.es',
                'highlighted_offer' => 'Tempo Happy: descuentos en franjas horarias',
                'cnmc_id' => '0032',
                'company_type' => 'comercializadora',
                'address' => 'Calle Ribera del Loira, 60, 28042 Madrid',
                'coverage_scope' => 'nacional',
                'municipality_id' => 3, // Madrid
            ],
            [
                'name' => 'Naturgy Iberia',
                'slug' => 'naturgy-iberia',
                'website' => 'https://www.naturgy.es',
                'phone_customer' => '900 100 251',
                'phone_commercial' => '902 546 333',
                'email_customer' => 'atencion.cliente@naturgy.com',
                'email_commercial' => 'comercial@naturgy.com',
                'highlighted_offer' => 'Tarifa Online: gestión 100% digital',
                'cnmc_id' => '0033',
                'company_type' => 'comercializadora',
                'address' => 'Avenida de San Luis, 77, 28033 Madrid',
                'coverage_scope' => 'nacional',
                'municipality_id' => 3, // Madrid
            ],
            [
                'name' => 'EDP España',
                'slug' => 'edp-espana',
                'website' => 'https://www.edpenergia.es',
                'phone_customer' => '800 37 37 37',
                'phone_commercial' => '900 848 848',
                'email_customer' => 'atencion.cliente@edpenergia.es',
                'email_commercial' => 'comercial@edpenergia.es',
                'highlighted_offer' => 'EDP Solar: autoconsumo + comercialización',
                'cnmc_id' => '0034',
                'company_type' => 'comercializadora',
                'address' => 'Plaza de la Gesta, 2, 33007 Oviedo',
                'coverage_scope' => 'nacional',
                'municipality_id' => null, // Oviedo
            ],

            // Cooperativas energéticas
            [
                'name' => 'Som Energia',
                'slug' => 'som-energia',
                'website' => 'https://www.somenergia.coop',
                'phone_customer' => '872 202 550',
                'email_customer' => 'info@somenergia.coop',
                'email_commercial' => 'comercial@somenergia.coop',
                'highlighted_offer' => '100% energía renovable certificada',
                'cnmc_id' => '0441',
                'company_type' => 'cooperativa',
                'address' => 'Pic de Peguera, 15, 17003 Girona',
                'coverage_scope' => 'nacional',
                'municipality_id' => null, // Girona
            ],
            [
                'name' => 'Goiener',
                'slug' => 'goiener',
                'website' => 'https://www.goiener.com',
                'phone_customer' => '944 264 007',
                'email_customer' => 'info@goiener.com',
                'email_commercial' => 'socios@goiener.com',
                'highlighted_offer' => 'Energía verde del País Vasco',
                'cnmc_id' => '0442',
                'company_type' => 'cooperativa',
                'address' => 'Beato Tomás de Zumárraga, 71, 48013 Bilbao',
                'coverage_scope' => 'nacional',
                'municipality_id' => null, // Bilbao
            ],
            [
                'name' => 'Zencer',
                'slug' => 'zencer',
                'website' => 'https://zencer.es',
                'phone_customer' => '910 858 676',
                'email_customer' => 'hola@zencer.es',
                'email_commercial' => 'comercial@zencer.es',
                'highlighted_offer' => 'Cooperativa digital con app móvil',
                'cnmc_id' => '0443',
                'company_type' => 'cooperativa',
                'address' => 'Calle de la Princesa, 31, 28008 Madrid',
                'coverage_scope' => 'nacional',
                'municipality_id' => 3, // Madrid
            ],

            // Comercializadoras independientes
            [
                'name' => 'Holaluz',
                'slug' => 'holaluz',
                'website' => 'https://www.holaluz.com',
                'phone_customer' => '900 850 000',
                'email_customer' => 'hola@holaluz.com',
                'email_commercial' => 'comercial@holaluz.com',
                'highlighted_offer' => 'Tarifa plana sin permanencia',
                'cnmc_id' => '0135',
                'company_type' => 'comercializadora',
                'address' => 'Avinguda del Paral·lel, 148, 08015 Barcelona',
                'coverage_scope' => 'nacional',
                'municipality_id' => null, // Barcelona
            ],
            [
                'name' => 'Factor Energía',
                'slug' => 'factor-energia',
                'website' => 'https://www.factorenergia.com',
                'phone_customer' => '900 850 000',
                'email_customer' => 'atencion.cliente@factorenergia.com',
                'email_commercial' => 'comercial@factorenergia.com',
                'highlighted_offer' => 'Precio fijo 12 meses sin sorpresas',
                'cnmc_id' => '0136',
                'company_type' => 'comercializadora',
                'address' => 'Carrer de Pamplona, 92-94, 08018 Barcelona',
                'coverage_scope' => 'nacional',
                'municipality_id' => null, // Barcelona
            ],
            [
                'name' => 'Lucera',
                'slug' => 'lucera',
                'website' => 'https://lucera.es',
                'phone_customer' => '900 907 408',
                'email_customer' => 'info@lucera.es',
                'email_commercial' => 'comercial@lucera.es',
                'highlighted_offer' => 'Tarifa indexada al PVPC con descuento',
                'cnmc_id' => '0137',
                'company_type' => 'comercializadora',
                'address' => 'Calle Velázquez, 157, 28002 Madrid',
                'coverage_scope' => 'nacional',
                'municipality_id' => 3, // Madrid
            ],
            [
                'name' => 'Podo',
                'slug' => 'podo',
                'website' => 'https://podo.es',
                'phone_customer' => '911 234 455',
                'email_customer' => 'hola@podo.es',
                'email_commercial' => 'comercial@podo.es',
                'highlighted_offer' => 'App inteligente para optimizar consumo',
                'cnmc_id' => '0138',
                'company_type' => 'comercializadora',
                'address' => 'Calle de Alcalá, 476, 28027 Madrid',
                'coverage_scope' => 'nacional',
                'municipality_id' => 3, // Madrid
            ],

            // Distribuidoras principales
            [
                'name' => 'e-distribución (Endesa)',
                'slug' => 'e-distribucion',
                'website' => 'https://www.e-distribucion.com',
                'phone_customer' => '800 760 266',
                'email_customer' => 'atencion.cliente@e-distribucion.com',
                'company_type' => 'distribuidora',
                'address' => 'Calle Ribera del Loira, 60, 28042 Madrid',
                'coverage_scope' => 'regional',
                'municipality_id' => 3, // Madrid
            ],
            [
                'name' => 'i-DE (Iberdrola)',
                'slug' => 'i-de',
                'website' => 'https://www.i-de.es',
                'phone_customer' => '900 171 171',
                'email_customer' => 'atencion.cliente@i-de.es',
                'company_type' => 'distribuidora',
                'address' => 'Plaza Euskadi, 5, 48009 Bilbao',
                'coverage_scope' => 'regional',
                'municipality_id' => null, // Bilbao
            ],
            [
                'name' => 'UFD (Naturgy)',
                'slug' => 'ufd',
                'website' => 'https://www.ufd.es',
                'phone_customer' => '900 100 251',
                'email_customer' => 'atencion.cliente@ufd.es',
                'company_type' => 'distribuidora',
                'address' => 'Avenida de San Luis, 77, 28033 Madrid',
                'coverage_scope' => 'regional',
                'municipality_id' => 3, // Madrid
            ],

            // Empresas autonómicas
            [
                'name' => 'Energía XXI',
                'slug' => 'energia-xxi',
                'website' => 'https://www.energiaxxi.com',
                'phone_customer' => '985 270 705',
                'email_customer' => 'atencion.cliente@energiaxxi.com',
                'email_commercial' => 'comercial@energiaxxi.com',
                'highlighted_offer' => 'Especializada en Asturias',
                'cnmc_id' => '0200',
                'company_type' => 'comercializadora',
                'address' => 'Calle Uría, 58, 33003 Oviedo',
                'coverage_scope' => 'regional',
                'municipality_id' => null, // Oviedo
            ],
        ];

        foreach ($companies as $company) {
            EnergyCompany::firstOrCreate(
                ['slug' => $company['slug']],
                $company
            );
        }

        $this->command->info('EnergyCompany seeder completed: ' . count($companies) . ' companies created/updated.');
    }
}