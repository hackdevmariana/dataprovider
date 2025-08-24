<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProjectVerification;
use App\Models\ProjectProposal;
use App\Models\User;
use Carbon\Carbon;

class ProjectVerificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $proposals = ProjectProposal::all();
        
        if ($users->isEmpty() || $proposals->isEmpty()) {
            $this->command->warn('No hay usuarios o propuestas de proyecto disponibles. Ejecuta UserSeeder y ProjectProposalSeeder primero.');
            return;
        }

        $verificationTypes = ['basic', 'advanced', 'professional', 'enterprise'];
        $verificationStatuses = ['requested', 'in_review', 'approved', 'rejected', 'expired'];
        $currencies = ['EUR', 'USD', 'GBP'];

        $verifications = [];

        // Crear verificaciones básicas (solicitadas)
        for ($i = 0; $i < 8; $i++) {
            $type = $verificationTypes[array_rand($verificationTypes)];
            $status = 'requested';
            $requestedBy = $users->random();
            $proposal = $proposals->random();
            
            $verifications[] = [
                'project_proposal_id' => $proposal->id,
                'requested_by' => $requestedBy->id,
                'verified_by' => null,
                'type' => $type,
                'status' => $status,
                'fee' => $this->getFeeByType($type),
                'currency' => $currencies[array_rand($currencies)],
                'verification_criteria' => $this->getVerificationCriteria($type),
                'documents_required' => $this->getDocumentsRequired($type),
                'documents_provided' => null,
                'verification_results' => null,
                'verification_notes' => null,
                'rejection_reason' => null,
                'score' => null,
                'requested_at' => Carbon::now()->subDays(rand(1, 30)),
                'reviewed_at' => null,
                'verified_at' => null,
                'expires_at' => null,
                'is_public' => rand(0, 1),
                'certificate_number' => null,
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
                'updated_at' => Carbon::now()->subDays(rand(1, 30)),
            ];
        }

        // Crear verificaciones en revisión
        for ($i = 0; $i < 6; $i++) {
            $type = $verificationTypes[array_rand($verificationTypes)];
            $status = 'in_review';
            $requestedBy = $users->random();
            $verifiedBy = $users->where('id', '!=', $requestedBy->id)->random();
            $proposal = $proposals->random();
            $requestedAt = Carbon::now()->subDays(rand(5, 45));
            $reviewedAt = $requestedAt->copy()->addDays(rand(1, 10));
            
            $verifications[] = [
                'project_proposal_id' => $proposal->id,
                'requested_by' => $requestedBy->id,
                'verified_by' => $verifiedBy->id,
                'type' => $type,
                'status' => $status,
                'fee' => $this->getFeeByType($type),
                'currency' => $currencies[array_rand($currencies)],
                'verification_criteria' => $this->getVerificationCriteria($type),
                'documents_required' => $this->getDocumentsRequired($type),
                'documents_provided' => $this->getDocumentsProvided($type),
                'verification_results' => null,
                'verification_notes' => $this->getReviewNotes(),
                'rejection_reason' => null,
                'score' => null,
                'requested_at' => $requestedAt,
                'reviewed_at' => $reviewedAt,
                'verified_at' => null,
                'expires_at' => null,
                'is_public' => rand(0, 1),
                'certificate_number' => null,
                'created_at' => $requestedAt,
                'updated_at' => $reviewedAt,
            ];
        }

        // Crear verificaciones aprobadas
        for ($i = 0; $i < 12; $i++) {
            $type = $verificationTypes[array_rand($verificationTypes)];
            $status = 'approved';
            $requestedBy = $users->random();
            $verifiedBy = $users->where('id', '!=', $requestedBy->id)->random();
            $proposal = $proposals->random();
            $requestedAt = Carbon::now()->subDays(rand(10, 90));
            $reviewedAt = $requestedAt->copy()->addDays(rand(2, 15));
            $verifiedAt = $reviewedAt->copy()->addDays(rand(1, 7));
            $expiresAt = $this->calculateExpirationDate($type, $verifiedAt);
            $score = rand(70, 100);
            
            $verifications[] = [
                'project_proposal_id' => $proposal->id,
                'requested_by' => $requestedBy->id,
                'verified_by' => $verifiedBy->id,
                'type' => $type,
                'status' => $status,
                'fee' => $this->getFeeByType($type),
                'currency' => $currencies[array_rand($currencies)],
                'verification_criteria' => $this->getVerificationCriteria($type),
                'documents_required' => $this->getDocumentsRequired($type),
                'documents_provided' => $this->getDocumentsProvided($type),
                'verification_results' => $this->getVerificationResults($type, $score),
                'verification_notes' => $this->getApprovalNotes($score),
                'rejection_reason' => null,
                'score' => $score,
                'requested_at' => $requestedAt,
                'reviewed_at' => $reviewedAt,
                'verified_at' => $verifiedAt,
                'expires_at' => $expiresAt,
                'is_public' => rand(0, 1),
                'certificate_number' => $this->generateCertificateNumber($type, $i + 1),
                'created_at' => $requestedAt,
                'updated_at' => $verifiedAt,
            ];
        }

        // Crear verificaciones rechazadas
        for ($i = 0; $i < 4; $i++) {
            $type = $verificationTypes[array_rand($verificationTypes)];
            $status = 'rejected';
            $requestedBy = $users->random();
            $verifiedBy = $users->where('id', '!=', $requestedBy->id)->random();
            $proposal = $proposals->random();
            $requestedAt = Carbon::now()->subDays(rand(15, 60));
            $reviewedAt = $requestedAt->copy()->addDays(rand(3, 20));
            $verifiedAt = $reviewedAt->copy()->addDays(rand(1, 5));
            
            $verifications[] = [
                'project_proposal_id' => $proposal->id,
                'requested_by' => $requestedBy->id,
                'verified_by' => $verifiedBy->id,
                'type' => $type,
                'status' => $status,
                'fee' => $this->getFeeByType($type),
                'currency' => $currencies[array_rand($currencies)],
                'verification_criteria' => $this->getVerificationCriteria($type),
                'documents_required' => $this->getDocumentsRequired($type),
                'documents_provided' => $this->getDocumentsProvided($type),
                'verification_results' => $this->getRejectionResults($type),
                'verification_notes' => $this->getRejectionNotes(),
                'rejection_reason' => $this->getRejectionReason(),
                'score' => rand(30, 69),
                'requested_at' => $requestedAt,
                'reviewed_at' => $reviewedAt,
                'verified_at' => $verifiedAt,
                'expires_at' => null,
                'is_public' => rand(0, 1),
                'certificate_number' => null,
                'created_at' => $requestedAt,
                'updated_at' => $verifiedAt,
            ];
        }

        // Crear verificaciones expiradas
        for ($i = 0; $i < 3; $i++) {
            $type = $verificationTypes[array_rand($verificationTypes)];
            $status = 'expired';
            $requestedBy = $users->random();
            $verifiedBy = $users->where('id', '!=', $requestedBy->id)->random();
            $proposal = $proposals->random();
            $requestedAt = Carbon::now()->subDays(rand(400, 800));
            $reviewedAt = $requestedAt->copy()->addDays(rand(2, 15));
            $verifiedAt = $reviewedAt->copy()->addDays(rand(1, 7));
            $expiresAt = $this->calculateExpirationDate($type, $verifiedAt);
            $score = rand(70, 100);
            
            $verifications[] = [
                'project_proposal_id' => $proposal->id,
                'requested_by' => $requestedBy->id,
                'verified_by' => $verifiedBy->id,
                'type' => $type,
                'status' => $status,
                'fee' => $this->getFeeByType($type),
                'currency' => $currencies[array_rand($currencies)],
                'verification_criteria' => $this->getVerificationCriteria($type),
                'documents_required' => $this->getDocumentsRequired($type),
                'documents_provided' => $this->getDocumentsProvided($type),
                'verification_results' => $this->getVerificationResults($type, $score),
                'verification_notes' => $this->getApprovalNotes($score),
                'rejection_reason' => null,
                'score' => $score,
                'requested_at' => $requestedAt,
                'reviewed_at' => $reviewedAt,
                'verified_at' => $verifiedAt,
                'expires_at' => $expiresAt,
                'is_public' => rand(0, 1),
                'certificate_number' => $this->generateCertificateNumber($type, $i + 100),
                'created_at' => $requestedAt,
                'updated_at' => $verifiedAt,
            ];
        }

        // Insertar todas las verificaciones
        foreach ($verifications as $verification) {
            ProjectVerification::create($verification);
        }

        $this->command->info('✅ Se han creado ' . count($verifications) . ' verificaciones de proyecto.');
        $this->command->info('📊 Distribución por estado:');
        $this->command->info('   - Solicitadas: 8');
        $this->command->info('   - En revisión: 6');
        $this->command->info('   - Aprobadas: 12');
        $this->command->info('   - Rechazadas: 4');
        $this->command->info('   - Expiradas: 3');
        $this->command->info('🏷️ Tipos de verificación: Básica, Avanzada, Profesional, Enterprise');
        $this->command->info('💰 Tarifas: 199€, 499€, 999€, 1999€');
        $this->command->info('📅 Períodos de validez: 1-5 años según el tipo');
    }

    /**
     * Obtener tarifa por tipo de verificación
     */
    private function getFeeByType(string $type): float
    {
        return match ($type) {
            'basic' => 199.00,
            'advanced' => 499.00,
            'professional' => 999.00,
            'enterprise' => 1999.00,
            default => 199.00,
        };
    }

    /**
     * Obtener criterios de verificación por tipo
     */
    private function getVerificationCriteria(string $type): array
    {
        $baseCriteria = [
            'project_feasibility' => 'Viabilidad del proyecto',
            'financial_viability' => 'Viabilidad financiera',
            'legal_compliance' => 'Cumplimiento legal',
            'technical_specifications' => 'Especificaciones técnicas',
        ];

        return match ($type) {
            'basic' => $baseCriteria,
            'advanced' => array_merge($baseCriteria, [
                'environmental_impact' => 'Impacto ambiental',
                'risk_assessment' => 'Evaluación de riesgos',
                'timeline_analysis' => 'Análisis de cronograma',
            ]),
            'professional' => array_merge($baseCriteria, [
                'environmental_impact' => 'Impacto ambiental',
                'risk_assessment' => 'Evaluación de riesgos',
                'timeline_analysis' => 'Análisis de cronograma',
                'market_analysis' => 'Análisis de mercado',
                'competitive_advantage' => 'Ventaja competitiva',
                'scalability_potential' => 'Potencial de escalabilidad',
            ]),
            'enterprise' => array_merge($baseCriteria, [
                'environmental_impact' => 'Impacto ambiental',
                'risk_assessment' => 'Evaluación de riesgos',
                'timeline_analysis' => 'Análisis de cronograma',
                'market_analysis' => 'Análisis de mercado',
                'competitive_advantage' => 'Ventaja competitiva',
                'scalability_potential' => 'Potencial de escalabilidad',
                'regulatory_approval' => 'Aprobación regulatoria',
                'insurance_coverage' => 'Cobertura de seguros',
                'stakeholder_analysis' => 'Análisis de stakeholders',
            ]),
            default => $baseCriteria,
        };
    }

    /**
     * Obtener documentos requeridos por tipo
     */
    private function getDocumentsRequired(string $type): array
    {
        $baseDocuments = [
            'project_description' => 'Descripción del proyecto',
            'business_plan' => 'Plan de negocio',
            'financial_projections' => 'Proyecciones financieras',
            'technical_drawings' => 'Planos técnicos',
        ];

        return match ($type) {
            'basic' => $baseDocuments,
            'advanced' => array_merge($baseDocuments, [
                'environmental_study' => 'Estudio ambiental',
                'risk_analysis' => 'Análisis de riesgos',
                'permits_licenses' => 'Permisos y licencias',
            ]),
            'professional' => array_merge($baseDocuments, [
                'environmental_study' => 'Estudio ambiental',
                'risk_analysis' => 'Análisis de riesgos',
                'permits_licenses' => 'Permisos y licencias',
                'market_research' => 'Investigación de mercado',
                'competitive_analysis' => 'Análisis competitivo',
                'insurance_documentation' => 'Documentación de seguros',
            ]),
            'enterprise' => array_merge($baseDocuments, [
                'environmental_study' => 'Estudio ambiental',
                'risk_analysis' => 'Análisis de riesgos',
                'permits_licenses' => 'Permisos y licencias',
                'market_research' => 'Investigación de mercado',
                'competitive_analysis' => 'Análisis competitivo',
                'insurance_documentation' => 'Documentación de seguros',
                'regulatory_approvals' => 'Aprobaciones regulatorias',
                'stakeholder_agreements' => 'Acuerdos con stakeholders',
                'audit_reports' => 'Informes de auditoría',
            ]),
            default => $baseDocuments,
        };
    }

    /**
     * Obtener documentos proporcionados
     */
    private function getDocumentsProvided(string $type): array
    {
        $documents = $this->getDocumentsRequired($type);
        $provided = [];
        
        foreach ($documents as $key => $description) {
            $provided[$key] = [
                'status' => rand(0, 1) ? 'provided' : 'pending',
                'provided_at' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 30))->toISOString() : null,
                'notes' => rand(0, 1) ? $this->getDocumentNotes() : null,
            ];
        }
        
        return $provided;
    }

    /**
     * Obtener resultados de verificación
     */
    private function getVerificationResults(string $type, int $score): array
    {
        $criteria = $this->getVerificationCriteria($type);
        $results = [];
        
        foreach ($criteria as $key => $description) {
            $criteriaScore = max(1, $score - rand(0, 20));
            $results[$key] = [
                'score' => $criteriaScore,
                'status' => $criteriaScore >= 70 ? 'approved' : ($criteriaScore >= 50 ? 'conditional' : 'rejected'),
                'notes' => $this->getCriteriaNotes($criteriaScore),
                'verified_at' => Carbon::now()->subDays(rand(1, 30))->toISOString(),
            ];
        }
        
        return $results;
    }

    /**
     * Obtener resultados de rechazo
     */
    private function getRejectionResults(string $type): array
    {
        $criteria = $this->getVerificationCriteria($type);
        $results = [];
        
        foreach ($criteria as $key => $description) {
            $criteriaScore = rand(30, 69);
            $results[$key] = [
                'score' => $criteriaScore,
                'status' => 'rejected',
                'notes' => $this->getRejectionCriteriaNotes(),
                'verified_at' => Carbon::now()->subDays(rand(1, 30))->toISOString(),
            ];
        }
        
        return $results;
    }

    /**
     * Calcular fecha de expiración
     */
    private function calculateExpirationDate(string $type, Carbon $verifiedAt): Carbon
    {
        return match ($type) {
            'basic' => $verifiedAt->copy()->addYear(),
            'advanced' => $verifiedAt->copy()->addYears(2),
            'professional' => $verifiedAt->copy()->addYears(3),
            'enterprise' => $verifiedAt->copy()->addYears(5),
            default => $verifiedAt->copy()->addYear(),
        };
    }

    /**
     * Generar número de certificado
     */
    private function generateCertificateNumber(string $type, int $sequence): string
    {
        $prefix = strtoupper(substr($type, 0, 2));
        $year = now()->year;
        $sequenceStr = str_pad($sequence, 6, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$year}-{$sequenceStr}";
    }

    /**
     * Obtener notas de revisión
     */
    private function getReviewNotes(): string
    {
        $notes = [
            'Documentación recibida y en proceso de revisión.',
            'Análisis técnico en curso. Se requieren aclaraciones adicionales.',
            'Evaluación financiera en progreso. Pendiente de verificación de datos.',
            'Revisión de cumplimiento legal iniciada.',
            'Análisis de impacto ambiental en proceso.',
            'Evaluación de riesgos en curso.',
        ];
        
        return $notes[array_rand($notes)];
    }

    /**
     * Obtener notas de aprobación
     */
    private function getApprovalNotes(int $score): string
    {
        if ($score >= 90) {
            return 'Proyecto excepcional. Cumple todos los criterios con excelencia. Recomendado para implementación inmediata.';
        } elseif ($score >= 80) {
            return 'Proyecto de alta calidad. Cumple todos los criterios principales. Aprobado sin reservas.';
        } elseif ($score >= 70) {
            return 'Proyecto aprobado. Cumple los criterios mínimos requeridos. Se recomiendan mejoras menores.';
        }
        
        return 'Proyecto aprobado condicionalmente. Se requieren mejoras antes de la implementación completa.';
    }

    /**
     * Obtener notas de rechazo
     */
    private function getRejectionNotes(): string
    {
        $notes = [
            'El proyecto no cumple con los criterios mínimos de viabilidad financiera.',
            'Falta documentación técnica esencial para la evaluación.',
            'Los riesgos identificados superan los límites aceptables.',
            'El impacto ambiental no cumple con las regulaciones vigentes.',
            'La propuesta no demuestra viabilidad comercial suficiente.',
            'Se requieren mejoras significativas en el plan de implementación.',
        ];
        
        return $notes[array_rand($notes)];
    }

    /**
     * Obtener razón de rechazo
     */
    private function getRejectionReason(): string
    {
        $reasons = [
            'Viabilidad financiera insuficiente',
            'Documentación técnica incompleta',
            'Riesgos ambientales elevados',
            'Cumplimiento legal insuficiente',
            'Plan de implementación deficiente',
            'Análisis de mercado insuficiente',
        ];
        
        return $reasons[array_rand($reasons)];
    }

    /**
     * Obtener notas de criterios
     */
    private function getCriteriaNotes(int $score): string
    {
        if ($score >= 90) {
            return 'Excelente cumplimiento. Supera las expectativas.';
        } elseif ($score >= 80) {
            return 'Muy buen cumplimiento. Cumple todos los requisitos.';
        } elseif ($score >= 70) {
            return 'Buen cumplimiento. Cumple los requisitos mínimos.';
        } elseif ($score >= 50) {
            return 'Cumplimiento condicional. Se requieren mejoras.';
        } else {
            return 'Cumplimiento insuficiente. No cumple los requisitos.';
        }
    }

    /**
     * Obtener notas de criterios rechazados
     */
    private function getRejectionCriteriaNotes(): string
    {
        $notes = [
            'No cumple con los requisitos mínimos establecidos.',
            'Documentación insuficiente para la evaluación.',
            'Riesgos identificados superan los límites aceptables.',
            'Requiere mejoras significativas antes de la aprobación.',
            'No demuestra viabilidad según los criterios evaluados.',
        ];
        
        return $notes[array_rand($notes)];
    }

    /**
     * Obtener notas de documentos
     */
    private function getDocumentNotes(): string
    {
        $notes = [
            'Documento completo y bien estructurado.',
            'Información clara y detallada.',
            'Requiere aclaraciones menores.',
            'Formato adecuado para la evaluación.',
            'Contenido relevante y actualizado.',
        ];
        
        return $notes[array_rand($notes)];
    }
}
