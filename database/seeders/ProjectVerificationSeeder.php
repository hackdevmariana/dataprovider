<?php

namespace Database\Seeders;

use App\Models\ProjectVerification;
use App\Models\ProjectProposal;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectVerificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔍 Sembrando verificaciones de proyectos...');

        $projectProposals = ProjectProposal::all();
        $users = User::all();

        if ($projectProposals->isEmpty() || $users->isEmpty()) {
            $this->command->warn('❌ No hay proyectos o usuarios disponibles. Asegúrate de ejecutar ProjectProposalSeeder y UserSeeder primero.');
            return;
        }

        $verificationTypes = ['basic', 'advanced', 'professional', 'enterprise'];
        $statuses = ['requested', 'in_review', 'approved', 'rejected', 'expired'];
        $currencies = ['EUR', 'USD'];

        $createdCount = 0;

        foreach ($projectProposals as $project) {
            $numVerifications = fake()->numberBetween(0, 2);
            
            for ($i = 0; $i < $numVerifications; $i++) {
                $type = fake()->randomElement($verificationTypes);
                $status = fake()->randomElement($statuses);
                $requestedBy = $users->random();
                $verifiedBy = $status !== 'requested' ? $users->random() : null;
                
                $requestedAt = fake()->dateTimeBetween('-1 year', 'now');
                $reviewedAt = $status !== 'requested' ? fake()->dateTimeBetween($requestedAt, 'now') : null;
                $verifiedAt = in_array($status, ['approved', 'rejected']) ? fake()->dateTimeBetween($requestedAt, 'now') : null;

                $verification = ProjectVerification::create([
                    'project_proposal_id' => $project->id,
                    'requested_by' => $requestedBy->id,
                    'verified_by' => $verifiedBy?->id,
                    'type' => $type,
                    'status' => $status,
                    'fee' => ProjectVerification::getFeeByType($type),
                    'currency' => fake()->randomElement($currencies),
                    'verification_criteria' => ProjectVerification::getDefaultCriteria($type),
                    'documents_required' => ProjectVerification::getDefaultDocuments($type),
                    'documents_provided' => $this->generateDocumentsProvided($type, $status),
                    'verification_results' => $this->generateVerificationResults($type, $status),
                    'verification_notes' => $this->generateVerificationNotes($type, $status),
                    'rejection_reason' => $status === 'rejected' ? fake()->randomElement([
                        'Documentación insuficiente',
                        'Criterios técnicos no cumplidos',
                        'Información financiera incompleta',
                        'Problemas legales identificados',
                        'Viabilidad técnica cuestionable'
                    ]) : null,
                    'score' => $status === 'approved' ? fake()->numberBetween(70, 100) : null,
                    'requested_at' => $requestedAt,
                    'reviewed_at' => $reviewedAt,
                    'verified_at' => $verifiedAt,
                    'expires_at' => $status === 'approved' ? $this->calculateExpirationDate($type, $verifiedAt) : null,
                    'is_public' => fake()->boolean(70),
                    'certificate_number' => $status === 'approved' ? $this->generateCertificateNumber($type) : null,
                ]);

                $createdCount++;
            }
        }

        $this->command->info("✅ Creadas {$createdCount} verificaciones de proyectos");
        $this->showStatistics();
    }

    private function generateDocumentsProvided(string $type, string $status): array
    {
        if ($status === 'requested') {
            return [];
        }

        $allDocuments = ProjectVerification::getDefaultDocuments($type);
        $providedCount = $status === 'approved' ? count($allDocuments) : fake()->numberBetween(1, count($allDocuments) - 1);
        
        return array_slice($allDocuments, 0, $providedCount);
    }

    private function generateVerificationResults(string $type, string $status): array
    {
        if (!in_array($status, ['approved', 'rejected'])) {
            return [];
        }

        $baseResults = [
            'project_feasibility' => fake()->randomFloat(1, 3, 5),
            'financial_viability' => fake()->randomFloat(1, 3, 5),
            'legal_compliance' => fake()->randomFloat(1, 3, 5),
            'technical_specifications' => fake()->randomFloat(1, 3, 5),
        ];

        if ($type === 'advanced' || $type === 'professional' || $type === 'enterprise') {
            $baseResults = array_merge($baseResults, [
                'environmental_impact' => fake()->randomFloat(1, 3, 5),
                'risk_assessment' => fake()->randomFloat(1, 3, 5),
                'timeline_analysis' => fake()->randomFloat(1, 3, 5),
            ]);
        }

        if ($type === 'professional' || $type === 'enterprise') {
            $baseResults = array_merge($baseResults, [
                'market_analysis' => fake()->randomFloat(1, 3, 5),
                'competitive_advantage' => fake()->randomFloat(1, 3, 5),
                'scalability_potential' => fake()->randomFloat(1, 3, 5),
            ]);
        }

        if ($type === 'enterprise') {
            $baseResults = array_merge($baseResults, [
                'regulatory_approval' => fake()->randomFloat(1, 3, 5),
                'insurance_coverage' => fake()->randomFloat(1, 3, 5),
                'stakeholder_analysis' => fake()->randomFloat(1, 3, 5),
            ]);
        }

        return $baseResults;
    }

    private function generateVerificationNotes(string $type, string $status): string
    {
        return match($status) {
            'approved' => match($type) {
                'basic' => 'Proyecto cumple con todos los criterios básicos de viabilidad técnica y financiera.',
                'advanced' => 'Proyecto presenta excelente potencial con análisis ambiental y de riesgos satisfactorios.',
                'professional' => 'Proyecto altamente viable con análisis de mercado y ventajas competitivas sólidas.',
                'enterprise' => 'Proyecto enterprise con todas las aprobaciones regulatorias y análisis de stakeholders completos.',
                default => 'Proyecto verificado exitosamente.'
            },
            'rejected' => match($type) {
                'basic' => 'Proyecto no cumple con los criterios mínimos de viabilidad.',
                'advanced' => 'Proyecto presenta deficiencias significativas en análisis ambiental o de riesgos.',
                'professional' => 'Análisis de mercado insuficiente o ventajas competitivas no demostradas.',
                'enterprise' => 'Faltan aprobaciones regulatorias o análisis de stakeholders incompleto.',
                default => 'Proyecto no cumple con los criterios establecidos.'
            },
            'in_review' => 'Verificación en proceso de revisión por parte del equipo técnico.',
            'expired' => 'Verificación ha expirado y requiere renovación.',
            default => 'Estado de verificación pendiente.'
        };
    }

    private function calculateExpirationDate(string $type, $verifiedAt): \Carbon\Carbon
    {
        $expirationYears = match($type) {
            'basic' => 1,
            'advanced' => 2,
            'professional' => 3,
            'enterprise' => 5,
            default => 1
        };

        return \Carbon\Carbon::parse($verifiedAt)->addYears($expirationYears);
    }

    private function generateCertificateNumber(string $type): string
    {
        $prefix = strtoupper(substr($type, 0, 2));
        $year = now()->year;
        $sequence = str_pad(fake()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$year}-{$sequence}";
    }

    private function showStatistics(): void
    {
        $total = ProjectVerification::count();
        $requested = ProjectVerification::requested()->count();
        $inReview = ProjectVerification::inReview()->count();
        $approved = ProjectVerification::approved()->count();
        $rejected = ProjectVerification::rejected()->count();
        $expired = ProjectVerification::expired()->count();
        
        $totalFees = ProjectVerification::sum('fee');
        $avgScore = ProjectVerification::whereNotNull('score')->avg('score');
        
        $byType = ProjectVerification::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type');
        
        $byStatus = ProjectVerification::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $this->command->info("\n📊 Estadísticas de verificaciones de proyectos:");
        $this->command->info("   • Total de verificaciones: {$total}");
        $this->command->info("   • Solicitadas: {$requested}");
        $this->command->info("   • En revisión: {$inReview}");
        $this->command->info("   • Aprobadas: {$approved}");
        $this->command->info("   • Rechazadas: {$rejected}");
        $this->command->info("   • Expiradas: {$expired}");
        $this->command->info("   • Ingresos totales: €" . number_format($totalFees, 2, ',', '.'));
        $this->command->info("   • Puntuación promedio: " . round($avgScore, 1));

        $this->command->info("\n🔍 Por tipo:");
        foreach ($byType as $type => $count) {
            $typeLabel = match($type) {
                'basic' => 'Básica',
                'advanced' => 'Avanzada',
                'professional' => 'Profesional',
                'enterprise' => 'Enterprise',
                default => ucfirst($type)
            };
            $this->command->info("   • {$typeLabel}: {$count}");
        }

        $this->command->info("\n📈 Por estado:");
        foreach ($byStatus as $status => $count) {
            $statusLabel = match($status) {
                'requested' => 'Solicitada',
                'in_review' => 'En revisión',
                'approved' => 'Aprobada',
                'rejected' => 'Rechazada',
                'expired' => 'Expirada',
                default => ucfirst($status)
            };
            $this->command->info("   • {$statusLabel}: {$count}");
        }

        // Mostrar algunas verificaciones recientes
        $recentVerifications = ProjectVerification::with(['projectProposal', 'requester', 'verifier'])
            ->latest()
            ->take(5)
            ->get();

        if ($recentVerifications->isNotEmpty()) {
            $this->command->info("\n⭐ Últimas verificaciones creadas:");
            foreach ($recentVerifications as $verification) {
                $requesterName = $verification->requester ? $verification->requester->name : 'Usuario Desconocido';
                $projectTitle = $verification->projectProposal ? $verification->projectProposal->title : 'Proyecto Desconocido';
                $this->command->info("   • {$requesterName} solicitó verificación '{$verification->getTypeLabel()}' para '{$projectTitle}' (Estado: {$verification->getStatusLabel()})");
            }
        }
    }
}
