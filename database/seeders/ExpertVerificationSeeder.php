<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ExpertVerification;
use App\Models\User;
use Carbon\Carbon;

class ExpertVerificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->warn('No hay usuarios disponibles. Ejecuta UserSeeder primero.');
            return;
        }

        $verifications = [];

        // Áreas de experiencia disponibles
        $expertiseAreas = [
            'solar_energy' => [
                'name' => 'Energía Solar',
                'description' => 'Instalación, mantenimiento y optimización de sistemas solares',
                'levels' => ['basic', 'advanced', 'professional', 'expert'],
            ],
            'wind_energy' => [
                'name' => 'Energía Eólica',
                'description' => 'Diseño, instalación y mantenimiento de turbinas eólicas',
                'levels' => ['basic', 'advanced', 'professional', 'expert'],
            ],
            'legal_consulting' => [
                'name' => 'Consultoría Legal',
                'description' => 'Asesoramiento legal en proyectos energéticos y cooperativas',
                'levels' => ['advanced', 'professional', 'expert'],
            ],
            'financial_consulting' => [
                'name' => 'Consultoría Financiera',
                'description' => 'Financiamiento, inversiones y análisis económico de proyectos',
                'levels' => ['advanced', 'professional', 'expert'],
            ],
            'technical_engineering' => [
                'name' => 'Ingeniería Técnica',
                'description' => 'Diseño técnico y supervisión de proyectos energéticos',
                'levels' => ['basic', 'advanced', 'professional', 'expert'],
            ],
            'energy_efficiency' => [
                'name' => 'Eficiencia Energética',
                'description' => 'Auditorías energéticas y optimización de consumo',
                'levels' => ['basic', 'advanced', 'professional'],
            ],
            'battery_storage' => [
                'name' => 'Almacenamiento de Baterías',
                'description' => 'Sistemas de almacenamiento y gestión de energía',
                'levels' => ['advanced', 'professional', 'expert'],
            ],
            'smart_grid' => [
                'name' => 'Redes Inteligentes',
                'description' => 'Tecnologías de red inteligente y gestión de demanda',
                'levels' => ['professional', 'expert'],
            ],
        ];

        // Crear verificaciones para cada usuario
        foreach ($users as $user) {
            // Seleccionar áreas de experiencia para este usuario
            $userExpertiseAreas = array_rand($expertiseAreas, rand(1, 3));
            if (!is_array($userExpertiseAreas)) {
                $userExpertiseAreas = [$userExpertiseAreas];
            }

            foreach ($userExpertiseAreas as $areaKey) {
                $area = $expertiseAreas[$areaKey];
                $level = $area['levels'][array_rand($area['levels'])];
                $status = $this->getRandomStatus();
                $submittedAt = Carbon::now()->subDays(rand(5, 120));
                
                $verifications[] = [
                    'user_id' => $user->id,
                    'expertise_area' => $areaKey,
                    'verification_level' => $level,
                    'status' => $status,
                    'credentials' => $this->getCredentials($areaKey, $level),
                    'verification_documents' => $this->getVerificationDocuments($areaKey),
                    'expertise_description' => $this->getExpertiseDescription($areaKey, $level),
                    'years_experience' => $this->getYearsExperience($level),
                    'certifications' => $this->getCertifications($areaKey, $level),
                    'education' => $this->getEducation($areaKey, $level),
                    'work_history' => $this->getWorkHistory($areaKey, $level),
                    'verification_fee' => $this->getVerificationFee($level),
                    'verified_by' => $status === 'approved' ? $users->where('id', '!=', $user->id)->random()->id : null,
                    'submitted_at' => $submittedAt,
                    'reviewed_at' => $status !== 'pending' ? $submittedAt->copy()->addDays(rand(1, 14)) : null,
                    'verified_at' => $status === 'approved' ? $submittedAt->copy()->addDays(rand(2, 21)) : null,
                    'expires_at' => $status === 'approved' ? $submittedAt->copy()->addDays(rand(2, 21))->addYear() : null,
                    'verification_notes' => $this->getVerificationNotes($status, $areaKey, $level),
                    'rejection_reason' => $status === 'rejected' ? $this->getRejectionReason($areaKey) : null,
                    'verification_score' => $status === 'approved' ? rand(75, 100) : null,
                    'is_public' => rand(0, 1),
                    'created_at' => $submittedAt,
                    'updated_at' => $status !== 'pending' ? $submittedAt->copy()->addDays(rand(1, 21)) : $submittedAt,
                ];
            }
        }

        // Insertar todas las verificaciones
        foreach ($verifications as $verification) {
            ExpertVerification::create($verification);
        }

        $this->command->info('✅ Se han creado ' . count($verifications) . ' verificaciones de expertos.');
        $this->command->info('📊 Distribución por estado:');
        $this->command->info('   - Pendientes: ' . count(array_filter($verifications, fn($v) => $v['status'] === 'pending')));
        $this->command->info('   - En Revisión: ' . count(array_filter($verifications, fn($v) => $v['status'] === 'under_review')));
        $this->command->info('   - Aprobadas: ' . count(array_filter($verifications, fn($v) => $v['status'] === 'approved')));
        $this->command->info('   - Rechazadas: ' . count(array_filter($verifications, fn($v) => $v['status'] === 'rejected')));
        $this->command->info('   - Expiradas: ' . count(array_filter($verifications, fn($v) => $v['status'] === 'expired')));
        $this->command->info('🏷️ Áreas: Solar, Eólica, Legal, Financiera, Técnica, Eficiencia, Baterías, Redes Inteligentes');
        $this->command->info('📊 Niveles: Básico, Avanzado, Profesional, Experto');
        $this->command->info('🔒 Estados: Pendiente, En Revisión, Aprobado, Rechazado, Expirado');
        $this->command->info('💰 Tarifas: Según nivel de verificación');
    }

    /**
     * Obtener estado aleatorio con distribución realista
     */
    private function getRandomStatus(): string
    {
        $statuses = ['pending', 'pending', 'under_review', 'under_review', 'approved', 'approved', 'approved', 'rejected', 'expired'];
        return $statuses[array_rand($statuses)];
    }

    /**
     * Obtener credenciales según área y nivel
     */
    private function getCredentials(string $area, string $level): array
    {
        $baseCredentials = [
            'identification' => 'Documento de identidad verificado',
            'professional_photo' => 'Foto profesional reciente',
            'cv_updated' => 'CV actualizado y verificado',
        ];

        $areaCredentials = match ($area) {
            'solar_energy' => [
                'solar_certification' => 'Certificación en instalación solar',
                'safety_training' => 'Entrenamiento en seguridad eléctrica',
                'installation_photos' => 'Fotos de instalaciones realizadas',
            ],
            'wind_energy' => [
                'wind_certification' => 'Certificación en energía eólica',
                'height_safety' => 'Certificación de seguridad en altura',
                'project_portfolio' => 'Portafolio de proyectos eólicos',
            ],
            'legal_consulting' => [
                'law_degree' => 'Título de abogado',
                'bar_association' => 'Colegio de abogados',
                'specialization_certificate' => 'Certificado de especialización',
            ],
            'financial_consulting' => [
                'finance_degree' => 'Título en finanzas o economía',
                'cfa_certification' => 'Certificación CFA',
                'financial_analysis_samples' => 'Muestras de análisis financiero',
            ],
            'technical_engineering' => [
                'engineering_degree' => 'Título de ingeniero',
                'technical_certifications' => 'Certificaciones técnicas',
                'project_drawings' => 'Planos y diseños de proyectos',
            ],
            'energy_efficiency' => [
                'energy_auditor_certification' => 'Certificación de auditor energético',
                'efficiency_reports' => 'Reportes de auditorías realizadas',
                'energy_savings_proof' => 'Pruebas de ahorro energético',
            ],
            'battery_storage' => [
                'battery_certification' => 'Certificación en sistemas de baterías',
                'electrical_safety' => 'Certificación de seguridad eléctrica',
                'installation_examples' => 'Ejemplos de instalaciones',
            ],
            'smart_grid' => [
                'smart_grid_certification' => 'Certificación en redes inteligentes',
                'iot_knowledge' => 'Conocimientos en IoT',
                'grid_analysis_samples' => 'Muestras de análisis de red',
            ],
            default => [],
        };

        $levelCredentials = match ($level) {
            'basic' => ['basic_training' => 'Entrenamiento básico completado'],
            'advanced' => ['advanced_course' => 'Curso avanzado completado'],
            'professional' => ['professional_exam' => 'Examen profesional aprobado'],
            'expert' => ['expert_assessment' => 'Evaluación de experto aprobada'],
            default => [],
        };

        return array_merge($baseCredentials, $areaCredentials, $levelCredentials);
    }

    /**
     * Obtener documentos de verificación
     */
    private function getVerificationDocuments(string $area): array
    {
        return [
            'diploma' => 'https://example.com/documents/diploma.pdf',
            'certification' => 'https://example.com/documents/certification.pdf',
            'work_samples' => 'https://example.com/documents/work_samples.pdf',
            'references' => 'https://example.com/documents/references.pdf',
            'portfolio' => 'https://example.com/documents/portfolio.pdf',
        ];
    }

    /**
     * Obtener descripción de experiencia
     */
    private function getExpertiseDescription(string $area, string $level): string
    {
        $descriptions = match ($area) {
            'solar_energy' => [
                'basic' => 'Instalador de paneles solares con experiencia en sistemas residenciales',
                'advanced' => 'Técnico especializado en instalaciones solares comerciales e industriales',
                'professional' => 'Ingeniero solar con amplia experiencia en proyectos de gran escala',
                'expert' => 'Consultor experto en energía solar con 15+ años liderando proyectos internacionales',
            ],
            'wind_energy' => [
                'basic' => 'Técnico en mantenimiento de turbinas eólicas',
                'advanced' => 'Especialista en operación y mantenimiento de parques eólicos',
                'professional' => 'Ingeniero eólico con experiencia en diseño y optimización',
                'expert' => 'Consultor líder en energía eólica con proyectos en múltiples países',
            ],
            'legal_consulting' => [
                'advanced' => 'Abogado especializado en derecho energético y ambiental',
                'professional' => 'Consultor legal senior en proyectos de energía renovable',
                'expert' => 'Abogado experto en regulación energética internacional',
            ],
            'financial_consulting' => [
                'advanced' => 'Analista financiero especializado en proyectos energéticos',
                'professional' => 'Consultor financiero senior en inversiones verdes',
                'expert' => 'Director financiero experto en financiamiento de energías renovables',
            ],
            'technical_engineering' => [
                'basic' => 'Técnico en sistemas energéticos',
                'advanced' => 'Ingeniero técnico especializado en energías renovables',
                'professional' => 'Ingeniero senior con amplia experiencia en proyectos complejos',
                'expert' => 'Ingeniero consultor experto en diseño y optimización de sistemas',
            ],
            'energy_efficiency' => [
                'basic' => 'Auditor energético certificado para edificios residenciales',
                'advanced' => 'Consultor en eficiencia energética para edificios comerciales',
                'professional' => 'Especialista senior en optimización energética industrial',
            ],
            'battery_storage' => [
                'advanced' => 'Técnico especializado en sistemas de almacenamiento',
                'professional' => 'Ingeniero en sistemas de baterías para redes eléctricas',
                'expert' => 'Consultor experto en soluciones de almacenamiento energético',
            ],
            'smart_grid' => [
                'professional' => 'Ingeniero en redes inteligentes y gestión de demanda',
                'expert' => 'Consultor líder en tecnologías de red inteligente',
            ],
            default => 'Especialista en el área con experiencia demostrable',
        };

        return $descriptions[$level] ?? $descriptions['basic'] ?? 'Especialista en el área con experiencia demostrable';
    }

    /**
     * Obtener años de experiencia según el nivel
     */
    private function getYearsExperience(string $level): int
    {
        return match ($level) {
            'basic' => rand(1, 3),
            'advanced' => rand(3, 7),
            'professional' => rand(7, 12),
            'expert' => rand(12, 25),
            default => rand(1, 5),
        };
    }

    /**
     * Obtener certificaciones según área y nivel
     */
    private function getCertifications(string $area, string $level): array
    {
        $baseCertifications = [
            'safety_training' => [
                'name' => 'Entrenamiento en Seguridad',
                'issuer' => 'Instituto de Seguridad Laboral',
                'year' => date('Y') - rand(1, 5),
                'valid_until' => date('Y') + rand(1, 3),
            ],
        ];

        $areaCertifications = match ($area) {
            'solar_energy' => [
                'solar_installer' => [
                    'name' => 'Instalador Solar Certificado',
                    'issuer' => 'Asociación Solar Española',
                    'year' => date('Y') - rand(1, 3),
                    'valid_until' => date('Y') + rand(2, 4),
                ],
                'electrical_safety' => [
                    'name' => 'Seguridad Eléctrica',
                    'issuer' => 'Colegio de Ingenieros',
                    'year' => date('Y') - rand(1, 4),
                    'valid_until' => date('Y') + rand(1, 3),
                ],
            ],
            'wind_energy' => [
                'wind_technician' => [
                    'name' => 'Técnico Eólico',
                    'issuer' => 'Asociación Eólica',
                    'year' => date('Y') - rand(1, 3),
                    'valid_until' => date('Y') + rand(2, 4),
                ],
                'height_safety' => [
                    'name' => 'Trabajo en Altura',
                    'issuer' => 'Instituto de Prevención',
                    'year' => date('Y') - rand(1, 2),
                    'valid_until' => date('Y') + rand(1, 2),
                ],
            ],
            'legal_consulting' => [
                'energy_law' => [
                    'name' => 'Derecho Energético',
                    'issuer' => 'Colegio de Abogados',
                    'year' => date('Y') - rand(2, 5),
                    'valid_until' => null,
                ],
                'environmental_law' => [
                    'name' => 'Derecho Ambiental',
                    'issuer' => 'Universidad Complutense',
                    'year' => date('Y') - rand(1, 4),
                    'valid_until' => null,
                ],
            ],
            'financial_consulting' => [
                'cfa' => [
                    'name' => 'Chartered Financial Analyst',
                    'issuer' => 'CFA Institute',
                    'year' => date('Y') - rand(3, 8),
                    'valid_until' => null,
                ],
                'green_finance' => [
                    'name' => 'Finanzas Verdes',
                    'issuer' => 'Instituto de Finanzas Sostenibles',
                    'year' => date('Y') - rand(1, 3),
                    'valid_until' => date('Y') + rand(2, 4),
                ],
            ],
            'technical_engineering' => [
                'renewable_energy' => [
                    'name' => 'Ingeniería en Energías Renovables',
                    'issuer' => 'Colegio de Ingenieros',
                    'year' => date('Y') - rand(2, 6),
                    'valid_until' => null,
                ],
                'project_management' => [
                    'name' => 'Gestión de Proyectos',
                    'issuer' => 'PMI',
                    'year' => date('Y') - rand(1, 4),
                    'valid_until' => date('Y') + rand(1, 3),
                ],
            ],
            default => [],
        };

        $levelCertifications = match ($level) {
            'basic' => [],
            'advanced' => [
                'advanced_training' => [
                    'name' => 'Formación Avanzada',
                    'issuer' => 'Instituto de Formación',
                    'year' => date('Y') - rand(1, 2),
                    'valid_until' => date('Y') + rand(1, 2),
                ],
            ],
            'professional' => [
                'professional_certification' => [
                    'name' => 'Certificación Profesional',
                    'issuer' => 'Organismo Certificador',
                    'year' => date('Y') - rand(2, 5),
                    'valid_until' => date('Y') + rand(2, 5),
                ],
            ],
            'expert' => [
                'expert_assessment' => [
                    'name' => 'Evaluación de Experto',
                    'issuer' => 'Comité de Expertos',
                    'year' => date('Y') - rand(1, 3),
                    'valid_until' => date('Y') + rand(3, 5),
                ],
            ],
            default => [],
        };

        return array_merge($baseCertifications, $areaCertifications, $levelCertifications);
    }

    /**
     * Obtener educación según área y nivel
     */
    private function getEducation(string $area, string $level): array
    {
        $baseEducation = [
            'high_school' => [
                'degree' => 'Bachillerato',
                'institution' => 'Instituto de Educación Secundaria',
                'year' => date('Y') - rand(15, 25),
                'field' => 'Ciencias',
            ],
        ];

        $areaEducation = match ($area) {
            'solar_energy', 'wind_energy', 'technical_engineering' => [
                'technical_degree' => [
                    'degree' => 'Grado en Ingeniería Técnica',
                    'institution' => 'Universidad Politécnica',
                    'year' => date('Y') - rand(8, 15),
                    'field' => 'Ingeniería Industrial',
                ],
            ],
            'legal_consulting' => [
                'law_degree' => [
                    'degree' => 'Grado en Derecho',
                    'institution' => 'Universidad Complutense',
                    'year' => date('Y') - rand(10, 20),
                    'field' => 'Derecho',
                ],
            ],
            'financial_consulting' => [
                'finance_degree' => [
                    'degree' => 'Grado en Economía',
                    'institution' => 'Universidad Autónoma',
                    'year' => date('Y') - rand(8, 18),
                    'field' => 'Economía y Finanzas',
                ],
            ],
            default => [],
        };

        $levelEducation = match ($level) {
            'basic' => [],
            'advanced' => [
                'advanced_course' => [
                    'degree' => 'Curso Avanzado',
                    'institution' => 'Instituto de Formación',
                    'year' => date('Y') - rand(2, 5),
                    'field' => 'Especialización',
                ],
            ],
            'professional' => [
                'master_degree' => [
                    'degree' => 'Máster',
                    'institution' => 'Universidad',
                    'year' => date('Y') - rand(3, 8),
                    'field' => 'Energías Renovables',
                ],
            ],
            'expert' => [
                'phd' => [
                    'degree' => 'Doctorado',
                    'institution' => 'Universidad',
                    'year' => date('Y') - rand(5, 12),
                    'field' => 'Energía Sostenible',
                ],
            ],
            default => [],
        };

        return array_merge($baseEducation, $areaEducation, $levelEducation);
    }

    /**
     * Obtener historial laboral
     */
    private function getWorkHistory(string $area, string $level): array
    {
        $yearsExperience = $this->getYearsExperience($level);
        $history = [];
        
        for ($i = 0; $i < min(3, $yearsExperience); $i++) {
            $yearsAgo = $yearsExperience - $i;
            $history["job_" . ($i + 1)] = [
                'position' => $this->getJobPosition($area, $level, $i),
                'company' => $this->getCompanyName($area),
                'start_date' => date('Y-m-d', strtotime("-{$yearsAgo} years")),
                'end_date' => $i === 0 ? null : date('Y-m-d', strtotime("-" . ($yearsAgo - 1) . " years")),
                'responsibilities' => $this->getJobResponsibilities($area, $level),
            ];
        }

        return $history;
    }

    /**
     * Obtener cargo según área, nivel e índice
     */
    private function getJobPosition(string $area, string $level, int $index): string
    {
        $positions = match ($area) {
            'solar_energy' => match ($level) {
                'basic' => ['Instalador Solar', 'Técnico de Mantenimiento', 'Operador de Planta'],
                'advanced' => ['Supervisor de Instalación', 'Técnico Senior', 'Coordinador de Proyectos'],
                'professional' => ['Ingeniero de Proyectos', 'Gerente Técnico', 'Consultor Senior'],
                'expert' => ['Director Técnico', 'Consultor Experto', 'Director de Operaciones'],
                default => ['Técnico', 'Supervisor', 'Gerente'],
            },
            'wind_energy' => match ($level) {
                'basic' => ['Técnico de Turbinas', 'Operador de Planta', 'Mantenimiento Básico'],
                'advanced' => ['Técnico Senior', 'Supervisor de Mantenimiento', 'Coordinador de Operaciones'],
                'professional' => ['Ingeniero de Mantenimiento', 'Gerente de Planta', 'Consultor Técnico'],
                'expert' => ['Director de Operaciones', 'Consultor Experto', 'Director Técnico'],
                default => ['Técnico', 'Supervisor', 'Gerente'],
            },
            'legal_consulting' => match ($level) {
                'advanced' => ['Abogado Junior', 'Asesor Legal', 'Consultor'],
                'professional' => ['Abogado Senior', 'Consultor Legal', 'Socio'],
                'expert' => ['Socio Director', 'Consultor Experto', 'Director Legal'],
                default => ['Abogado', 'Consultor', 'Director'],
            },
            'financial_consulting' => match ($level) {
                'advanced' => ['Analista Financiero', 'Consultor Junior', 'Asesor'],
                'professional' => ['Consultor Senior', 'Analista Senior', 'Gerente'],
                'expert' => ['Director Financiero', 'Consultor Experto', 'Socio'],
                default => ['Analista', 'Consultor', 'Director'],
            },
            default => match ($level) {
                'basic' => ['Técnico', 'Operador', 'Asistente'],
                'advanced' => ['Especialista', 'Supervisor', 'Coordinador'],
                'professional' => ['Ingeniero', 'Gerente', 'Consultor'],
                'expert' => ['Director', 'Consultor Experto', 'Socio'],
                default => ['Técnico', 'Supervisor', 'Gerente'],
            },
        };

        return $positions[$index] ?? $positions[0] ?? 'Profesional';
    }

    /**
     * Obtener nombre de empresa
     */
    private function getCompanyName(string $area): string
    {
        $companies = match ($area) {
            'solar_energy' => ['SolarTech Solutions', 'Green Energy Corp', 'SunPower Systems'],
            'wind_energy' => ['WindForce Energy', 'EcoWind Solutions', 'Renewable Power Co'],
            'legal_consulting' => ['Energy Law Partners', 'Green Legal Services', 'Renewable Law Group'],
            'financial_consulting' => ['Green Finance Advisors', 'Sustainable Capital', 'Renewable Investment Co'],
            'technical_engineering' => ['Technical Solutions Inc', 'Engineering Partners', 'Innovation Tech'],
            'energy_efficiency' => ['Efficiency Experts', 'Green Building Solutions', 'Sustainable Systems'],
            'battery_storage' => ['Storage Solutions', 'Battery Tech Corp', 'Energy Storage Co'],
            'smart_grid' => ['Smart Grid Solutions', 'Intelligent Energy', 'Grid Tech Partners'],
            default => ['Innovation Corp', 'Tech Solutions', 'Professional Services'],
        };

        return $companies[array_rand($companies)];
    }

    /**
     * Obtener responsabilidades del trabajo
     */
    private function getJobResponsibilities(string $area, string $level): array
    {
        $baseResponsibilities = ['Coordinación de equipos', 'Gestión de proyectos', 'Análisis técnico'];

        $areaResponsibilities = match ($area) {
            'solar_energy' => ['Instalación de paneles', 'Mantenimiento preventivo', 'Optimización de sistemas'],
            'wind_energy' => ['Mantenimiento de turbinas', 'Operación de parques', 'Análisis de rendimiento'],
            'legal_consulting' => ['Asesoramiento legal', 'Revisión de contratos', 'Cumplimiento normativo'],
            'financial_consulting' => ['Análisis financiero', 'Estructuración de proyectos', 'Gestión de inversiones'],
            'technical_engineering' => ['Diseño técnico', 'Supervisión de obras', 'Control de calidad'],
            'energy_efficiency' => ['Auditorías energéticas', 'Implementación de mejoras', 'Monitoreo de consumo'],
            'battery_storage' => ['Diseño de sistemas', 'Instalación y configuración', 'Mantenimiento'],
            'smart_grid' => ['Implementación de tecnologías', 'Gestión de redes', 'Análisis de datos'],
            default => ['Gestión técnica', 'Coordinación', 'Análisis'],
        };

        return array_merge($baseResponsibilities, $areaResponsibilities);
    }

    /**
     * Obtener tarifa de verificación según el nivel
     */
    private function getVerificationFee(string $level): float
    {
        return match ($level) {
            'basic' => rand(50, 100),
            'advanced' => rand(100, 200),
            'professional' => rand(200, 400),
            'expert' => rand(400, 800),
            default => rand(75, 150),
        };
    }

    /**
     * Obtener notas de verificación
     */
    private function getVerificationNotes(string $status, string $area, string $level): ?string
    {
        if ($status !== 'approved') {
            return null;
        }

        $notes = match ($level) {
            'basic' => "Verificación básica aprobada. El candidato demuestra conocimientos fundamentales en {$area}.",
            'advanced' => "Verificación avanzada aprobada. El candidato muestra competencia técnica sólida y experiencia práctica.",
            'professional' => "Verificación profesional aprobada. El candidato demuestra experiencia significativa y liderazgo técnico.",
            'expert' => "Verificación de experto aprobada. El candidato es reconocido como autoridad en el campo con contribuciones destacadas.",
            default => "Verificación aprobada para el nivel solicitado.",
        };

        return $notes;
    }

    /**
     * Obtener razón de rechazo
     */
    private function getRejectionReason(string $area): string
    {
        $reasons = [
            'Documentación insuficiente o no verificable',
            'Experiencia no cumple con los requisitos del nivel solicitado',
            'Certificaciones no válidas o expiradas',
            'Referencias laborales no verificables',
            'Falta de evidencia de proyectos realizados',
            'Nivel de educación no cumple con los estándares',
        ];

        return $reasons[array_rand($reasons)];
    }
}
