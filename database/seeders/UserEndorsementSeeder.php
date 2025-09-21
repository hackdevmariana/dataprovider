<?php

namespace Database\Seeders;

use App\Models\UserEndorsement;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserEndorsementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('👍 Sembrando endorsements de usuarios...');

        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('❌ No hay usuarios disponibles. Ejecuta UserSeeder primero.');
            return;
        }

        $skillCategories = [
            'solar_installation',
            'electrical_work',
            'project_management',
            'energy_consulting',
            'legal_advice',
            'financing',
            'maintenance',
            'design_engineering',
            'sales',
            'customer_service',
            'training',
            'research',
            'policy_analysis',
            'community_building',
            'technical_writing',
            'general_knowledge'
        ];

        $specificSkills = [
            'solar_installation' => ['panel_installation', 'inverter_setup', 'wiring', 'mounting_systems'],
            'electrical_work' => ['electrical_systems', 'grid_connection', 'electrical_safety', 'power_distribution'],
            'project_management' => ['project_planning', 'resource_coordination', 'timeline_management', 'risk_assessment'],
            'energy_consulting' => ['energy_audit', 'system_design', 'efficiency_analysis', 'renewable_assessment'],
            'legal_advice' => ['regulatory_compliance', 'contract_law', 'permit_management', 'legal_documentation'],
            'financing' => ['loan_processing', 'investment_analysis', 'financial_modeling', 'funding_strategies'],
            'maintenance' => ['preventive_maintenance', 'system_monitoring', 'performance_optimization', 'troubleshooting'],
            'design_engineering' => ['system_design', 'technical_drawings', 'engineering_analysis', 'specifications'],
            'sales' => ['client_acquisition', 'proposal_development', 'negotiation', 'relationship_building'],
            'customer_service' => ['client_support', 'issue_resolution', 'communication', 'satisfaction_management'],
            'training' => ['technical_training', 'safety_education', 'skill_development', 'certification_preparation'],
            'research' => ['market_research', 'technology_analysis', 'performance_studies', 'innovation_development'],
            'policy_analysis' => ['regulatory_analysis', 'policy_development', 'compliance_monitoring', 'legislative_review'],
            'community_building' => ['stakeholder_engagement', 'community_outreach', 'partnership_development', 'public_relations'],
            'technical_writing' => ['documentation', 'technical_reports', 'procedures', 'specifications'],
            'general_knowledge' => ['industry_knowledge', 'best_practices', 'emerging_technologies', 'market_trends']
        ];

        $relationshipContexts = [
            'colleague',
            'client',
            'supplier',
            'mentor',
            'mentee',
            'collaborator',
            'competitor',
            'community_member',
            'student',
            'teacher',
            'unknown'
        ];

        $projectContexts = [
            'solar_installation',
            'energy_consulting',
            'project_development',
            'technical_review',
            'financial_analysis',
            'legal_compliance',
            'environmental_assessment',
            'maintenance_planning',
            'team_management',
            'client_consultation'
        ];

        $createdCount = 0;

        foreach ($users as $endorser) {
            // Cada usuario puede hacer entre 5 y 15 endorsements
            $numEndorsements = fake()->numberBetween(5, 15);
            $endorsedUsers = $users->where('id', '!=', $endorser->id)->random($numEndorsements);
            
            foreach ($endorsedUsers as $endorsed) {
                $category = fake()->randomElement($skillCategories);
                $specificSkill = fake()->randomElement($specificSkills[$category]);
                $relationshipContext = fake()->randomElement($relationshipContexts);
                $projectContext = fake()->randomElement($projectContexts);
                
                // Verificar si ya existe un endorsement similar
                if (UserEndorsement::exists($endorser, $endorsed, $category, $specificSkill)) {
                    continue;
                }

                $endorsement = UserEndorsement::create([
                    'endorser_id' => $endorser->id,
                    'endorsed_id' => $endorsed->id,
                    'skill_category' => $category,
                    'specific_skill' => $specificSkill,
                    'endorsement_text' => $this->generateEndorsementText($category, $specificSkill),
                    'skill_rating' => fake()->randomFloat(1, 3.0, 5.0),
                    'relationship_context' => $relationshipContext,
                    'project_context' => $projectContext,
                    'collaboration_duration_months' => fake()->numberBetween(1, 36),
                    'is_verified' => fake()->boolean(70),
                    'trust_score' => fake()->randomFloat(2, 60, 100),
                    'helpful_votes' => fake()->numberBetween(0, 25),
                    'total_votes' => fake()->numberBetween(0, 30),
                    'is_public' => fake()->boolean(85),
                    'show_on_profile' => fake()->boolean(90),
                    'notify_endorsed' => fake()->boolean(80),
                    'is_mutual' => fake()->boolean(30),
                    'status' => fake()->randomElement(['active', 'pending', 'disputed']),
                    'disputed_by' => null,
                    'dispute_reason' => null,
                    'disputed_at' => null,
                ]);

                $createdCount++;
            }
        }

        $this->command->info("✅ Creados {$createdCount} endorsements de usuarios");
        $this->showStatistics();
    }

    private function generateEndorsementText(string $category, string $specificSkill): string
    {
        $templates = [
            'solar_installation' => [
                'Excelente conocimiento técnico en {skill}. Siempre aporta soluciones innovadoras y precisas.',
                'Dominio excepcional de {skill}. Sus instalaciones son siempre de alta calidad.',
                'Experto reconocido en {skill}. Su experiencia se refleja en la calidad de sus trabajos.',
                'Conocimiento profundo de {skill}. Sus instalaciones son muy profesionales.'
            ],
            'electrical_work' => [
                'Excelente trabajo eléctrico en {skill}. Siempre cumple con los estándares de seguridad.',
                'Dominio excepcional de {skill}. Sus conexiones eléctricas son impecables.',
                'Experto en {skill}. Su trabajo eléctrico es muy confiable.',
                'Conocimiento profundo de {skill}. Sus instalaciones eléctricas son de gran calidad.'
            ],
            'project_management' => [
                'Gestión excepcional de proyectos. Excelente capacidad de organización y liderazgo.',
                'Outstanding project management skills. Always delivers on time and within budget.',
                'Experto en {skill}. Su metodología y seguimiento son impecables.',
                'Liderazgo natural en gestión de proyectos. Excelente coordinación de equipos.'
            ],
            'energy_consulting' => [
                'Consultoría energética excepcional. Siempre aporta valor añadido.',
                'Experto en {skill}. Sus asesoramientos energéticos son muy valiosos.',
                'Conocimiento profundo de {skill}. Sus consultorías son de gran calidad.',
                'Excelente consultor energético. Sus recomendaciones son siempre acertadas.'
            ],
            'legal_advice' => [
                'Conocimiento legal excepcional. Siempre actualizado en normativas y regulaciones.',
                'Experto en {skill}. Sus asesoramientos legales son muy precisos y útiles.',
                'Dominio completo de {skill}. Sus recomendaciones legales son muy valiosas.',
                'Conocimiento profundo de normativas. Sus análisis de cumplimiento son excelentes.'
            ],
            'financing' => [
                'Análisis financiero muy sólido. Sus proyecciones son precisas y bien fundamentadas.',
                'Experto en {skill}. Sus análisis financieros son siempre detallados y acertados.',
                'Conocimiento profundo de {skill}. Sus recomendaciones financieras son muy valiosas.',
                'Excelente capacidad de análisis financiero. Sus estudios son muy completos.'
            ],
            'maintenance' => [
                'Planificación de mantenimiento muy sólida. Sus planes son eficientes y completos.',
                'Experto en {skill}. Sus estrategias de mantenimiento son muy efectivas.',
                'Conocimiento profundo de {skill}. Sus planes preventivos son excelentes.',
                'Excelente capacidad de {skill}. Sus protocolos de mantenimiento son muy completos.'
            ],
            'design_engineering' => [
                'Diseño e ingeniería excepcional. Sus proyectos son innovadores y funcionales.',
                'Experto en {skill}. Sus diseños son siempre precisos y eficientes.',
                'Conocimiento profundo de {skill}. Sus especificaciones técnicas son excelentes.',
                'Excelente capacidad de {skill}. Sus diseños son muy profesionales.'
            ],
            'sales' => [
                'Ventas excepcionales. Excelente capacidad de relación con clientes.',
                'Experto en {skill}. Sus técnicas de venta son muy efectivas.',
                'Conocimiento profundo de {skill}. Sus presentaciones son muy convincentes.',
                'Excelente vendedor. Siempre logra cerrar los mejores acuerdos.'
            ],
            'customer_service' => [
                'Atención al cliente excepcional. Excelente capacidad de resolución de problemas.',
                'Experto en {skill}. Su atención al cliente es impecable.',
                'Conocimiento profundo de {skill}. Su comunicación es muy profesional.',
                'Excelente capacidad de {skill}. Siempre logra la satisfacción del cliente.'
            ],
            'training' => [
                'Formación excepcional. Excelente capacidad de enseñanza y transmisión de conocimientos.',
                'Experto en {skill}. Sus cursos de formación son muy completos.',
                'Conocimiento profundo de {skill}. Su metodología de enseñanza es excelente.',
                'Excelente formador. Siempre logra que los alumnos aprendan efectivamente.'
            ],
            'research' => [
                'Investigación excepcional. Excelente capacidad de análisis y desarrollo.',
                'Experto en {skill}. Sus investigaciones son muy rigurosas.',
                'Conocimiento profundo de {skill}. Sus estudios son de gran calidad.',
                'Excelente investigador. Siempre aporta nuevos conocimientos al campo.'
            ],
            'policy_analysis' => [
                'Análisis de políticas excepcional. Excelente capacidad de comprensión regulatoria.',
                'Experto en {skill}. Sus análisis de políticas son muy precisos.',
                'Conocimiento profundo de {skill}. Sus evaluaciones son muy completas.',
                'Excelente analista de políticas. Siempre aporta perspectivas valiosas.'
            ],
            'community_building' => [
                'Construcción de comunidad excepcional. Excelente capacidad de engagement.',
                'Experto en {skill}. Su trabajo comunitario es muy efectivo.',
                'Conocimiento profundo de {skill}. Sus iniciativas son muy exitosas.',
                'Excelente constructor de comunidad. Siempre logra unir a las personas.'
            ],
            'technical_writing' => [
                'Redacción técnica excepcional. Excelente capacidad de documentación.',
                'Experto en {skill}. Sus documentos técnicos son muy claros.',
                'Conocimiento profundo de {skill}. Sus especificaciones son muy precisas.',
                'Excelente redactor técnico. Siempre produce documentación de calidad.'
            ],
            'general_knowledge' => [
                'Conocimiento general excepcional. Excelente capacidad de comprensión integral.',
                'Experto en {skill}. Su conocimiento es muy amplio y profundo.',
                'Conocimiento profundo de {skill}. Sus aportaciones son muy valiosas.',
                'Excelente conocedor general. Siempre aporta perspectivas únicas.'
            ]
        ];

        $template = fake()->randomElement($templates[$category] ?? $templates['solar_installation']);
        return str_replace('{skill}', $specificSkill, $template);
    }

    private function showStatistics(): void
    {
        $total = UserEndorsement::count();
        $active = UserEndorsement::where('status', 'active')->count();
        $verified = UserEndorsement::where('is_verified', true)->count();
        $mutual = UserEndorsement::where('is_mutual', true)->count();
        $public = UserEndorsement::where('is_public', true)->count();
        
        $avgRating = UserEndorsement::whereNotNull('skill_rating')->avg('skill_rating');
        $avgTrustScore = UserEndorsement::whereNotNull('trust_score')->avg('trust_score');
        
        $byCategory = UserEndorsement::selectRaw('skill_category, COUNT(*) as count')
            ->groupBy('skill_category')
            ->pluck('count', 'skill_category');
        
        $byStatus = UserEndorsement::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $this->command->info("\n📊 Estadísticas de endorsements de usuarios:");
        $this->command->info("   • Total de endorsements: {$total}");
        $this->command->info("   • Activos: {$active}");
        $this->command->info("   • Verificados: {$verified}");
        $this->command->info("   • Mutuos: {$mutual}");
        $this->command->info("   • Públicos: {$public}");
        $this->command->info("   • Valoración promedio: " . round($avgRating, 1) . "/5");
        $this->command->info("   • Score de confianza promedio: " . round($avgTrustScore, 1) . "%");

        $this->command->info("\n🎯 Por categoría:");
        foreach ($byCategory as $category => $count) {
            $categoryLabel = match($category) {
                'solar_installation' => 'Instalación Solar',
                'electrical_work' => 'Trabajo Eléctrico',
                'project_management' => 'Gestión de Proyectos',
                'energy_consulting' => 'Consultoría Energética',
                'legal_advice' => 'Asesoría Legal',
                'financing' => 'Financiación',
                'maintenance' => 'Mantenimiento',
                'design_engineering' => 'Ingeniería de Diseño',
                'sales' => 'Ventas',
                'customer_service' => 'Atención al Cliente',
                'training' => 'Formación',
                'research' => 'Investigación',
                'policy_analysis' => 'Análisis de Políticas',
                'community_building' => 'Construcción de Comunidad',
                'technical_writing' => 'Redacción Técnica',
                'general_knowledge' => 'Conocimiento General',
                default => ucfirst(str_replace('_', ' ', $category))
            };
            $this->command->info("   • {$categoryLabel}: {$count}");
        }

        $this->command->info("\n📈 Por estado:");
        foreach ($byStatus as $status => $count) {
            $statusLabel = match($status) {
                'active' => 'Activo',
                'pending' => 'Pendiente',
                'disputed' => 'En disputa',
                'rejected' => 'Rechazado',
                default => ucfirst($status)
            };
            $this->command->info("   • {$statusLabel}: {$count}");
        }

        // Mostrar algunos endorsements recientes
        $recentEndorsements = UserEndorsement::with(['endorser', 'endorsed'])
            ->latest()
            ->take(5)
            ->get();

        if ($recentEndorsements->isNotEmpty()) {
            $this->command->info("\n⭐ Últimos endorsements creados:");
            foreach ($recentEndorsements as $endorsement) {
                $endorserName = $endorsement->endorser ? $endorsement->endorser->name : 'Usuario Desconocido';
                $endorsedName = $endorsement->endorsed ? $endorsement->endorsed->name : 'Usuario Desconocido';
                $skillLabel = ucfirst(str_replace('_', ' ', $endorsement->specific_skill));
                $this->command->info("   • {$endorserName} endosó a {$endorsedName} en '{$skillLabel}' (" . round($endorsement->skill_rating, 1) . "/5)");
            }
        }
    }
}
