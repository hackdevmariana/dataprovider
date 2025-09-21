<?php

namespace Database\Seeders;

use App\Models\OrganizationFeature;
use App\Models\Organization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganizationFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar que existan organizaciones antes de crear features
        $organizations = Organization::all();
        
        if ($organizations->isEmpty()) {
            $this->command->warn('No hay organizaciones disponibles. Ejecuta OrganizationSeeder primero.');
            return;
        }

        // Definir features comunes que pueden tener las organizaciones
        $commonFeatures = [
            'wallet' => 'Sistema de cartera digital',
            'surveys' => 'Encuestas y formularios',
            'events' => 'Gestión de eventos',
            'notifications' => 'Sistema de notificaciones',
            'analytics' => 'Panel de análisis y estadísticas',
            'reports' => 'Generación de reportes',
            'user_management' => 'Gestión de usuarios',
            'content_management' => 'Gestión de contenido',
            'api_access' => 'Acceso a API',
            'mobile_app' => 'Aplicación móvil',
            'social_login' => 'Inicio de sesión social',
            'email_marketing' => 'Marketing por email',
            'sms_notifications' => 'Notificaciones SMS',
            'file_storage' => 'Almacenamiento de archivos',
            'calendar' => 'Calendario y programación',
        ];

        $createdCount = 0;

        foreach ($organizations as $organization) {
            // Cada organización tendrá entre 3 y 8 features aleatorias
            $featuresCount = rand(3, 8);
            $selectedFeatures = array_rand($commonFeatures, $featuresCount);
            
            // Asegurar que $selectedFeatures sea un array
            if (!is_array($selectedFeatures)) {
                $selectedFeatures = [$selectedFeatures];
            }

            foreach ($selectedFeatures as $featureKey) {
                $featureData = [
                    'organization_id' => $organization->id,
                    'feature_key' => $featureKey,
                    'enabled_dashboard' => rand(0, 1) == 1, // 50% probabilidad
                    'enabled_web' => rand(0, 1) == 1, // 50% probabilidad
                    'notes' => rand(0, 1) == 1 ? $commonFeatures[$featureKey] : null, // 50% probabilidad de tener notas
                ];

                // Usar updateOrCreate para evitar duplicados
                OrganizationFeature::updateOrCreate(
                    [
                        'organization_id' => $organization->id,
                        'feature_key' => $featureKey,
                    ],
                    $featureData
                );
                
                $createdCount++;
            }
        }

        $this->command->info("OrganizationFeature creados exitosamente. Total: {$createdCount} registros.");
    }
}