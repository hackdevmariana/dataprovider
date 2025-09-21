<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ExpertVerification;
use App\Models\User;
use Carbon\Carbon;

class ExpertVerificationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::take(15)->get();
        
        if ($users->isEmpty()) {
            $this->command->warn('No hay usuarios disponibles. Ejecuta primero UserSeeder.');
            return;
        }

        $verifications = [
            [
                'user_id' => $users->random()->id,
                'expertise_area' => 'renewable_energy',
                'verification_level' => 'expert',
                'status' => 'approved',
                'credentials' => [
                    'degree' => 'Ingeniería en Energías Renovables',
                    'university' => 'Universidad Politécnica de Madrid',
                    'graduation_year' => '2018',
                    'gpa' => '8.5',
                ],
                'verification_documents' => [
                    'diploma' => 'diploma_renewable_energy_2018.pdf',
                    'transcript' => 'transcript_renewable_energy.pdf',
                    'certification' => 'cert_renewable_energy_2023.pdf',
                ],
                'expertise_description' => 'Especialista en sistemas fotovoltaicos y eólicos con más de 5 años de experiencia en diseño e implementación de proyectos de energía renovable.',
                'years_experience' => 5,
                'certifications' => [
                    'PV_Design_Certification' => 'Certificación en Diseño de Sistemas Fotovoltaicos',
                    'Wind_Energy_Specialist' => 'Especialista en Energía Eólica',
                    'Energy_Auditor' => 'Auditor Energético Certificado',
                ],
                'education' => [
                    'bachelor' => 'Ingeniería en Energías Renovables',
                    'master' => 'Máster en Eficiencia Energética',
                    'phd' => null,
                ],
                'work_history' => [
                    'current_position' => 'Ingeniero Senior en Energías Renovables',
                    'company' => 'SolarTech Solutions',
                    'years_at_company' => 3,
                    'previous_companies' => ['GreenEnergy Corp', 'Renewable Systems'],
                ],
                'verification_fee' => 150.00,
                'verified_by' => 1,
                'submitted_at' => Carbon::now()->subDays(30),
                'reviewed_at' => Carbon::now()->subDays(25),
                'verified_at' => Carbon::now()->subDays(20),
                'expires_at' => Carbon::now()->addYear(),
                'verification_notes' => 'Verificación completa con documentación válida y experiencia demostrada.',
                'rejection_reason' => null,
                'verification_score' => 95,
                'is_public' => true,
            ],
            [
                'user_id' => $users->random()->id,
                'expertise_area' => 'sustainability',
                'verification_level' => 'expert',
                'status' => 'approved',
                'credentials' => [
                    'degree' => 'Ciencias Ambientales',
                    'university' => 'Universidad de Barcelona',
                    'graduation_year' => '2016',
                    'gpa' => '9.2',
                ],
                'verification_documents' => [
                    'diploma' => 'diploma_environmental_sciences_2016.pdf',
                    'certification' => 'cert_sustainability_expert_2022.pdf',
                    'portfolio' => 'portfolio_sustainability_projects.pdf',
                ],
                'expertise_description' => 'Consultor senior en sostenibilidad empresarial con amplia experiencia en certificaciones LEED y análisis de huella de carbono.',
                'years_experience' => 7,
                'certifications' => [
                    'LEED_AP' => 'LEED Accredited Professional',
                    'Carbon_Footprint_Analyst' => 'Analista de Huella de Carbono',
                    'Sustainability_Consultant' => 'Consultor en Sostenibilidad',
                ],
                'education' => [
                    'bachelor' => 'Ciencias Ambientales',
                    'master' => 'Máster en Gestión Ambiental',
                    'phd' => 'Doctorado en Sostenibilidad',
                ],
                'work_history' => [
                    'current_position' => 'Consultor Senior en Sostenibilidad',
                    'company' => 'EcoConsulting Group',
                    'years_at_company' => 4,
                    'previous_companies' => ['Green Solutions', 'Environmental Partners'],
                ],
                'verification_fee' => 200.00,
                'verified_by' => 1,
                'submitted_at' => Carbon::now()->subDays(45),
                'reviewed_at' => Carbon::now()->subDays(40),
                'verified_at' => Carbon::now()->subDays(35),
                'expires_at' => Carbon::now()->addYear(),
                'verification_notes' => 'Excelente perfil profesional con certificaciones reconocidas internacionalmente.',
                'rejection_reason' => null,
                'verification_score' => 98,
                'is_public' => true,
            ],
            [
                'user_id' => $users->random()->id,
                'expertise_area' => 'energy_efficiency',
                'verification_level' => 'professional',
                'status' => 'under_review',
                'credentials' => [
                    'degree' => 'Ingeniería Industrial',
                    'university' => 'Universidad de Valencia',
                    'graduation_year' => '2020',
                    'gpa' => '8.8',
                ],
                'verification_documents' => [
                    'diploma' => 'diploma_industrial_engineering_2020.pdf',
                    'certification' => 'cert_energy_efficiency_2023.pdf',
                ],
                'expertise_description' => 'Especialista en auditorías energéticas y optimización de sistemas industriales.',
                'years_experience' => 3,
                'certifications' => [
                    'Energy_Auditor' => 'Auditor Energético Certificado',
                    'ISO_50001' => 'Especialista en ISO 50001',
                ],
                'education' => [
                    'bachelor' => 'Ingeniería Industrial',
                    'master' => 'Máster en Eficiencia Energética',
                    'phd' => null,
                ],
                'work_history' => [
                    'current_position' => 'Ingeniero de Eficiencia Energética',
                    'company' => 'EnergyOptimize Solutions',
                    'years_at_company' => 2,
                    'previous_companies' => ['Industrial Systems'],
                ],
                'verification_fee' => 120.00,
                'verified_by' => null,
                'submitted_at' => Carbon::now()->subDays(10),
                'reviewed_at' => null,
                'verified_at' => null,
                'expires_at' => null,
                'verification_notes' => null,
                'rejection_reason' => null,
                'verification_score' => null,
                'is_public' => false,
            ],
            [
                'user_id' => $users->random()->id,
                'expertise_area' => 'climate_change',
                'verification_level' => 'expert',
                'status' => 'approved',
                'credentials' => [
                    'degree' => 'Ciencias del Clima',
                    'university' => 'Universidad Complutense de Madrid',
                    'graduation_year' => '2015',
                    'gpa' => '9.0',
                ],
                'verification_documents' => [
                    'diploma' => 'diploma_climate_sciences_2015.pdf',
                    'research_papers' => 'research_papers_climate_change.pdf',
                    'certification' => 'cert_climate_expert_2023.pdf',
                ],
                'expertise_description' => 'Investigador especializado en cambio climático y políticas ambientales con publicaciones en revistas científicas.',
                'years_experience' => 8,
                'certifications' => [
                    'Climate_Researcher' => 'Investigador en Cambio Climático',
                    'Policy_Analyst' => 'Analista de Políticas Ambientales',
                ],
                'education' => [
                    'bachelor' => 'Ciencias del Clima',
                    'master' => 'Máster en Cambio Climático',
                    'phd' => 'Doctorado en Ciencias Atmosféricas',
                ],
                'work_history' => [
                    'current_position' => 'Investigador Senior en Cambio Climático',
                    'company' => 'Climate Research Institute',
                    'years_at_company' => 5,
                    'previous_companies' => ['Environmental Research Center'],
                ],
                'verification_fee' => 180.00,
                'verified_by' => 1,
                'submitted_at' => Carbon::now()->subDays(60),
                'reviewed_at' => Carbon::now()->subDays(55),
                'verified_at' => Carbon::now()->subDays(50),
                'expires_at' => Carbon::now()->addYear(),
                'verification_notes' => 'Perfil excepcional con contribuciones significativas a la investigación climática.',
                'rejection_reason' => null,
                'verification_score' => 100,
                'is_public' => true,
            ],
            [
                'user_id' => $users->random()->id,
                'expertise_area' => 'green_technology',
                'verification_level' => 'basic',
                'status' => 'rejected',
                'credentials' => [
                    'degree' => 'Ingeniería de Software',
                    'university' => 'Universidad de Sevilla',
                    'graduation_year' => '2021',
                    'gpa' => '7.5',
                ],
                'verification_documents' => [
                    'diploma' => 'diploma_software_engineering_2021.pdf',
                ],
                'expertise_description' => 'Desarrollador de software con interés en tecnologías verdes.',
                'years_experience' => 2,
                'certifications' => [
                    'Software_Developer' => 'Desarrollador de Software Certificado',
                ],
                'education' => [
                    'bachelor' => 'Ingeniería de Software',
                    'master' => null,
                    'phd' => null,
                ],
                'work_history' => [
                    'current_position' => 'Desarrollador de Software',
                    'company' => 'TechSolutions',
                    'years_at_company' => 2,
                    'previous_companies' => [],
                ],
                'verification_fee' => 100.00,
                'verified_by' => 1,
                'submitted_at' => Carbon::now()->subDays(20),
                'reviewed_at' => Carbon::now()->subDays(15),
                'verified_at' => null,
                'expires_at' => null,
                'verification_notes' => null,
                'rejection_reason' => 'Experiencia insuficiente en el área de tecnología verde solicitada.',
                'verification_score' => 45,
                'is_public' => false,
            ],
        ];

        foreach ($verifications as $verificationData) {
            ExpertVerification::create($verificationData);
        }
    }
}
