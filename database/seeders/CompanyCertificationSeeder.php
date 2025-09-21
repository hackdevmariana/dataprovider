<?php

namespace Database\Seeders;

use App\Models\CompanyCertification;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanyCertificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creando certificaciones de empresas...');

        // Verificar que existen usuarios (usando como empresas)
        $companies = User::limit(10)->get();
        if ($companies->isEmpty()) {
            $this->command->warn('No hay usuarios disponibles. Creando usuarios de prueba...');
            for ($i = 1; $i <= 5; $i++) {
                User::create([
                    'name' => 'Empresa de Prueba ' . $i,
                    'email' => 'empresa' . $i . '@example.com',
                    'password' => bcrypt('password'),
                ]);
            }
            $companies = User::limit(10)->get();
        }

        $certificationTemplates = [
            [
                'certification_name' => 'ISO 9001:2015 - Sistema de Gestión de la Calidad',
                'issuing_body' => 'AENOR',
                'certification_type' => 'quality',
                'description' => 'Certificación internacional para sistemas de gestión de la calidad que garantiza la satisfacción del cliente y la mejora continua.',
                'scope' => ['Producción', 'Servicios', 'Gestión'],
                'requirements_met' => ['ISO 9001:2015', 'Política de Calidad', 'Objetivos de Calidad', 'Revisión por la Dirección'],
            ],
            [
                'certification_name' => 'ISO 14001:2015 - Sistema de Gestión Ambiental',
                'issuing_body' => 'AENOR',
                'certification_type' => 'environmental',
                'description' => 'Certificación para sistemas de gestión ambiental que ayuda a las organizaciones a minimizar su impacto en el medio ambiente.',
                'scope' => ['Gestión Ambiental', 'Sostenibilidad', 'Cumplimiento Legal'],
                'requirements_met' => ['ISO 14001:2015', 'Política Ambiental', 'Objetivos Ambientales', 'Evaluación de Aspectos'],
            ],
            [
                'certification_name' => 'ISO 45001:2018 - Sistema de Gestión de Seguridad y Salud en el Trabajo',
                'issuing_body' => 'AENOR',
                'certification_type' => 'safety',
                'description' => 'Certificación para sistemas de gestión de seguridad y salud ocupacional que protege a los trabajadores.',
                'scope' => ['Seguridad Laboral', 'Salud Ocupacional', 'Prevención de Riesgos'],
                'requirements_met' => ['ISO 45001:2018', 'Política de SST', 'Identificación de Peligros', 'Evaluación de Riesgos'],
            ],
            [
                'certification_name' => 'ISO 27001:2022 - Sistema de Gestión de Seguridad de la Información',
                'issuing_body' => 'AENOR',
                'certification_type' => 'information_security',
                'description' => 'Certificación para sistemas de gestión de seguridad de la información que protege los datos corporativos.',
                'scope' => ['Seguridad de la Información', 'Ciberseguridad', 'Protección de Datos'],
                'requirements_met' => ['ISO 27001:2022', 'Política de Seguridad', 'Análisis de Riesgos', 'Controles de Seguridad'],
            ],
            [
                'certification_name' => 'ISO 50001:2018 - Sistema de Gestión Energética',
                'issuing_body' => 'AENOR',
                'certification_type' => 'energy',
                'description' => 'Certificación para sistemas de gestión energética que ayuda a optimizar el consumo de energía.',
                'scope' => ['Gestión Energética', 'Eficiencia Energética', 'Sostenibilidad'],
                'requirements_met' => ['ISO 50001:2018', 'Política Energética', 'Objetivos Energéticos', 'Auditoría Energética'],
            ],
        ];

        $statuses = ['active', 'expired', 'pending', 'under_review', 'suspended'];
        $count = 0;

        foreach ($companies as $company) {
            $certificationsCount = rand(2, 6);
            
            for ($i = 0; $i < $certificationsCount; $i++) {
                $template = fake()->randomElement($certificationTemplates);
                $status = fake()->randomElement($statuses);
                
                $issuedDate = fake()->dateTimeBetween('-3 years', 'now');
                $expiryDate = null;
                
                if ($status === 'active') {
                    $expiryDate = fake()->dateTimeBetween('now', '+2 years');
                } elseif ($status === 'expired') {
                    $expiryDate = fake()->dateTimeBetween('-1 year', 'now');
                } else {
                    $expiryDate = fake()->optional(0.7)->dateTimeBetween('now', '+2 years');
                }

                CompanyCertification::create([
                    'company_id' => $company->id,
                    'certification_name' => $template['certification_name'],
                    'issuing_body' => $template['issuing_body'],
                    'certification_type' => $template['certification_type'],
                    'issued_date' => $issuedDate,
                    'expiry_date' => $expiryDate,
                    'certificate_number' => 'CERT-' . strtoupper(fake()->bothify('??####')),
                    'description' => $template['description'],
                    'scope' => json_encode($template['scope']),
                    'status' => $status,
                    'requirements_met' => json_encode($template['requirements_met']),
                    'is_verified' => fake()->boolean(80),
                ]);

                $count++;
            }
        }

        $this->command->info("✅ Creadas {$count} certificaciones de empresas");
        $this->showStatistics();
    }

    private function showStatistics(): void
    {
        $stats = [
            'Total certificaciones' => CompanyCertification::count(),
            'Certificaciones activas' => CompanyCertification::where('status', 'active')->count(),
            'Certificaciones expiradas' => CompanyCertification::where('status', 'expired')->count(),
            'Certificaciones pendientes' => CompanyCertification::where('status', 'pending')->count(),
            'Certificaciones verificadas' => CompanyCertification::where('is_verified', true)->count(),
        ];

        $this->command->info("\n📊 Estadísticas de certificaciones:");
        foreach ($stats as $type => $count) {
            $this->command->info("   {$type}: {$count}");
        }

        $typeStats = CompanyCertification::selectRaw('certification_type, COUNT(*) as count')
                                        ->groupBy('certification_type')
                                        ->orderBy('count', 'desc')
                                        ->get();

        if ($typeStats->isNotEmpty()) {
            $this->command->info("\n🏆 Tipos de certificación:");
            foreach ($typeStats as $stat) {
                $this->command->info("   {$stat->certification_type}: {$stat->count}");
            }
        }
    }
}