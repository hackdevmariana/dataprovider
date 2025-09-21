<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear organizaciones específicas y realistas
        $organizations = [
            [
                'name' => 'Ministerio de Cultura de España',
                'slug' => 'ministerio-cultura-espana',
                'domain' => 'cultura.gob.es',
                'contact_email' => 'info@cultura.gob.es',
                'contact_phone' => '+34 91 701 70 00',
                'primary_color' => '#1e40af',
                'secondary_color' => '#3b82f6',
                'css_files' => ['ministerio-cultura.css'],
                'active' => true,
            ],
            [
                'name' => 'Academia de las Artes y las Ciencias Cinematográficas',
                'slug' => 'academia-artes-ciencias-cinematograficas',
                'domain' => 'academiadecine.com',
                'contact_email' => 'info@academiadecine.com',
                'contact_phone' => '+34 91 531 48 00',
                'primary_color' => '#dc2626',
                'secondary_color' => '#ef4444',
                'css_files' => ['academia-cine.css'],
                'active' => true,
            ],
            [
                'name' => 'Real Academia Española',
                'slug' => 'real-academia-espanola',
                'domain' => 'rae.es',
                'contact_email' => 'informacion@rae.es',
                'contact_phone' => '+34 91 420 14 78',
                'primary_color' => '#059669',
                'secondary_color' => '#10b981',
                'css_files' => ['rae.css'],
                'active' => true,
            ],
            [
                'name' => 'Fundación Telefónica',
                'slug' => 'fundacion-telefonica',
                'domain' => 'fundaciontelefonica.com',
                'contact_email' => 'info@fundaciontelefonica.com',
                'contact_phone' => '+34 91 584 04 00',
                'primary_color' => '#7c3aed',
                'secondary_color' => '#8b5cf6',
                'css_files' => ['fundacion-telefonica.css'],
                'active' => true,
            ],
            [
                'name' => 'Museo Nacional del Prado',
                'slug' => 'museo-nacional-prado',
                'domain' => 'museodelprado.es',
                'contact_email' => 'info@museodelprado.es',
                'contact_phone' => '+34 91 330 28 00',
                'primary_color' => '#b45309',
                'secondary_color' => '#d97706',
                'css_files' => ['museo-prado.css'],
                'active' => true,
            ],
            [
                'name' => 'Instituto Cervantes',
                'slug' => 'instituto-cervantes',
                'domain' => 'cervantes.es',
                'contact_email' => 'info@cervantes.es',
                'contact_phone' => '+34 91 436 75 00',
                'primary_color' => '#be123c',
                'secondary_color' => '#e11d48',
                'css_files' => ['instituto-cervantes.css'],
                'active' => true,
            ],
            [
                'name' => 'Fundación BBVA',
                'slug' => 'fundacion-bbva',
                'domain' => 'fbbva.es',
                'contact_email' => 'info@fbbva.es',
                'contact_phone' => '+34 91 374 52 00',
                'primary_color' => '#0f766e',
                'secondary_color' => '#14b8a6',
                'css_files' => ['fundacion-bbva.css'],
                'active' => true,
            ],
            [
                'name' => 'Centro de Arte Reina Sofía',
                'slug' => 'centro-arte-reina-sofia',
                'domain' => 'museoreinasofia.es',
                'contact_email' => 'info@museoreinasofia.es',
                'contact_phone' => '+34 91 774 10 00',
                'primary_color' => '#1f2937',
                'secondary_color' => '#374151',
                'css_files' => ['reina-sofia.css'],
                'active' => true,
            ],
        ];

        // Insertar las organizaciones
        foreach ($organizations as $organization) {
            Organization::updateOrCreate(
                ['slug' => $organization['slug']], // Buscar por slug único
                $organization
            );
        }

        // Crear algunas organizaciones adicionales
        $additionalOrganizations = [
            [
                'name' => 'Fundación La Caixa',
                'slug' => 'fundacion-la-caixa',
                'domain' => 'fundacionlacaixa.org',
                'contact_email' => 'info@fundacionlacaixa.org',
                'contact_phone' => '+34 93 404 60 00',
                'primary_color' => '#0f766e',
                'secondary_color' => '#14b8a6',
                'css_files' => ['fundacion-caixa.css'],
                'active' => true,
            ],
            [
                'name' => 'Museo Thyssen-Bornemisza',
                'slug' => 'museo-thyssen-bornemisza',
                'domain' => 'museothyssen.org',
                'contact_email' => 'info@museothyssen.org',
                'contact_phone' => '+34 91 369 01 51',
                'primary_color' => '#1f2937',
                'secondary_color' => '#374151',
                'css_files' => ['thyssen.css'],
                'active' => true,
            ],
        ];

        foreach ($additionalOrganizations as $org) {
            Organization::updateOrCreate(
                ['slug' => $org['slug']],
                $org
            );
        }

        $this->command->info('Organizaciones creadas exitosamente.');
    }
}