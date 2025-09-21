<?php

namespace Database\Seeders;

use App\Models\UserFollow;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserFollowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('👥 Sembrando seguimientos de usuarios...');

        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('❌ No hay usuarios disponibles. Ejecuta UserSeeder primero.');
            return;
        }

        $followTypes = ['general', 'expertise', 'projects', 'achievements', 'energy_activity', 'installations', 'investments', 'content', 'community'];
        $notificationFrequencies = ['instant', 'daily_digest', 'weekly_digest', 'never'];
        $statuses = ['active', 'paused', 'muted', 'blocked'];

        $createdCount = 0;

        foreach ($users as $follower) {
            // Cada usuario puede seguir entre 10 y 50 usuarios
            $numFollows = fake()->numberBetween(10, 50);
            $followingUsers = $users->where('id', '!=', $follower->id)->random($numFollows);
            
            foreach ($followingUsers as $following) {
                // Verificar si ya existe un seguimiento entre estos usuarios
                if (UserFollow::where('follower_id', $follower->id)
                              ->where('following_id', $following->id)
                              ->exists()) {
                    continue;
                }
                
                $followType = fake()->randomElement($followTypes);
                $status = fake()->randomElement($statuses);
                $notificationFrequency = fake()->randomElement($notificationFrequencies);
                
                $followedAt = now()->subDays(fake()->numberBetween(1, 730));
                $lastInteractionAt = fake()->boolean(70) ? $followedAt->copy()->addDays(fake()->numberBetween(1, 100)) : null;
                $lastSeenActivityAt = fake()->boolean(80) ? $followedAt->copy()->addDays(fake()->numberBetween(1, 200)) : null;

                $follow = UserFollow::create([
                    'follower_id' => $follower->id,
                    'following_id' => $following->id,
                    'follow_type' => $followType,
                    'notify_new_activity' => fake()->boolean(70),
                    'notify_achievements' => fake()->boolean(60),
                    'notify_projects' => fake()->boolean(80),
                    'notify_investments' => fake()->boolean(40),
                    'notify_milestones' => fake()->boolean(50),
                    'notify_content' => fake()->boolean(60),
                    'notification_frequency' => $notificationFrequency,
                    'show_in_main_feed' => fake()->boolean(85),
                    'prioritize_in_feed' => fake()->boolean(20),
                    'feed_weight' => fake()->numberBetween(10, 100),
                    'follow_reason' => $this->generateFollowReason($followType),
                    'interests' => $this->generateInterests($followType),
                    'tags' => $this->generateTags($followType),
                    'is_mutual' => fake()->boolean(30),
                    'mutual_since' => fake()->boolean(30) && fake()->boolean(50) ? $followedAt->copy()->addDays(fake()->numberBetween(1, 30)) : null,
                    'interactions_count' => fake()->numberBetween(0, 100),
                    'last_interaction_at' => $lastInteractionAt,
                    'engagement_score' => fake()->randomFloat(2, 0, 100),
                    'content_views' => fake()->numberBetween(0, 500),
                    'is_public' => fake()->boolean(90),
                    'show_to_followed' => fake()->boolean(85),
                    'allow_followed_to_see_activity' => fake()->boolean(70),
                    'content_filters' => $this->generateContentFilters(),
                    'activity_filters' => $this->generateActivityFilters(),
                    'minimum_relevance_score' => fake()->randomFloat(2, 0.1, 1.0),
                    'status' => $status,
                    'status_changed_at' => $status !== 'active' ? $followedAt->copy()->addDays(fake()->numberBetween(1, 365)) : null,
                    'status_reason' => $status !== 'active' ? $this->generateStatusReason($status) : null,
                    'followed_at' => $followedAt,
                    'last_seen_activity_at' => $lastSeenActivityAt,
                    'days_following' => fake()->numberBetween(1, 730),
                    'relevance_decay_rate' => fake()->randomFloat(2, 0.01, 0.1),
                    'algorithm_preferences' => $this->generateAlgorithmPreferences(),
                ]);

                $createdCount++;
            }
        }

        $this->command->info("✅ Creados {$createdCount} seguimientos de usuarios");
        $this->showStatistics();
    }

    private function generateFollowReason(string $followType): string
    {
        return match($followType) {
            'general' => fake()->randomElement([
                'Seguimiento general de actividades',
                'Interés en todas sus actividades',
                'Seguimiento completo de perfil',
                'Interés general en su trabajo'
            ]),
            'expertise' => fake()->randomElement([
                'Interesado en su experiencia técnica',
                'Seguimiento por expertise específica',
                'Interés en conocimientos especializados',
                'Seguimiento por competencias técnicas'
            ]),
            'projects' => fake()->randomElement([
                'Seguimiento de proyectos específicos',
                'Interés en desarrollo de proyectos',
                'Colaboración en proyectos',
                'Seguimiento de progreso de proyectos'
            ]),
            'achievements' => fake()->randomElement([
                'Seguimiento de logros y reconocimientos',
                'Interés en sus logros profesionales',
                'Seguimiento de avances y éxitos',
                'Interés en sus logros técnicos'
            ]),
            'energy_activity' => fake()->randomElement([
                'Interés en actividad energética',
                'Seguimiento de producción energética',
                'Interés en eficiencia energética',
                'Seguimiento de impacto energético'
            ]),
            'installations' => fake()->randomElement([
                'Seguimiento de instalaciones',
                'Interés en proyectos de instalación',
                'Seguimiento de trabajos de instalación',
                'Interés en supervisión de instalaciones'
            ]),
            'investments' => fake()->randomElement([
                'Seguimiento de inversiones',
                'Interés en oportunidades de inversión',
                'Seguimiento de proyectos de inversión',
                'Interés en análisis de inversiones'
            ]),
            'content' => fake()->randomElement([
                'Seguimiento de contenido publicado',
                'Interés en sus publicaciones',
                'Seguimiento de artículos y posts',
                'Interés en contenido técnico'
            ]),
            'community' => fake()->randomElement([
                'Interés en actividad comunitaria',
                'Seguimiento de participación comunitaria',
                'Interés en construcción de comunidad',
                'Seguimiento de iniciativas comunitarias'
            ]),
            default => 'Seguimiento general'
        };
    }

    private function generateInterests(string $followType): array
    {
        $baseInterests = [
            'solar_energy',
            'renewable_energy',
            'energy_efficiency',
            'sustainability',
            'green_technology'
        ];

        $specificInterests = match($followType) {
            'general' => ['general_interest', 'comprehensive_following', 'overall_activity'],
            'expertise' => ['technical_expertise', 'specialized_knowledge', 'professional_skills'],
            'projects' => ['project_development', 'project_management', 'project_collaboration'],
            'achievements' => ['achievements', 'milestones', 'recognition', 'success'],
            'energy_activity' => ['energy_production', 'energy_savings', 'energy_efficiency'],
            'installations' => ['solar_installations', 'installation_work', 'installation_supervision'],
            'investments' => ['investment_opportunities', 'financial_analysis', 'investment_strategies'],
            'content' => ['content_creation', 'technical_writing', 'educational_content'],
            'community' => ['community_building', 'stakeholder_engagement', 'community_outreach'],
            default => []
        };

        return array_merge($baseInterests, $specificInterests);
    }

    private function generateTags(string $followType): array
    {
        return match($followType) {
            'general' => ['general', 'comprehensive', 'overall', 'complete'],
            'expertise' => ['expertise', 'technical', 'professional', 'skills'],
            'projects' => ['projects', 'development', 'collaboration', 'progress'],
            'achievements' => ['achievements', 'success', 'milestones', 'recognition'],
            'energy_activity' => ['energy', 'production', 'savings', 'efficiency'],
            'installations' => ['installations', 'installation_work', 'supervision', 'technical'],
            'investments' => ['investments', 'financial', 'opportunities', 'analysis'],
            'content' => ['content', 'publications', 'writing', 'education'],
            'community' => ['community', 'engagement', 'outreach', 'building'],
            default => ['general', 'follow']
        };
    }

    private function generateContentFilters(): array
    {
        $filters = [];
        
        if (fake()->boolean(30)) {
            $filters[] = 'spam';
        }
        
        if (fake()->boolean(20)) {
            $filters[] = 'low_quality';
        }
        
        if (fake()->boolean(15)) {
            $filters[] = 'off_topic';
        }

        return $filters;
    }

    private function generateActivityFilters(): array
    {
        $filters = [];
        
        if (fake()->boolean(25)) {
            $filters[] = 'minor_updates';
        }
        
        if (fake()->boolean(20)) {
            $filters[] = 'automated_posts';
        }
        
        if (fake()->boolean(15)) {
            $filters[] = 'system_notifications';
        }

        return $filters;
    }

    private function generateStatusReason(string $status): string
    {
        return match($status) {
            'paused' => fake()->randomElement([
                'Pausa temporal por sobrecarga de información',
                'Pausa para reorganizar seguimientos',
                'Pausa temporal por cambio de intereses'
            ]),
            'muted' => fake()->randomElement([
                'Silenciado por exceso de notificaciones',
                'Silenciado temporalmente',
                'Silenciado por cambio de prioridades'
            ]),
            'blocked' => fake()->randomElement([
                'Bloqueado por contenido inapropiado',
                'Bloqueado por spam',
                'Bloqueado por comportamiento inadecuado'
            ]),
            default => null
        };
    }

    private function generateAlgorithmPreferences(): array
    {
        return [
            'prioritize_recent' => fake()->boolean(70),
            'prioritize_high_engagement' => fake()->boolean(60),
            'prioritize_mutual_connections' => fake()->boolean(40),
            'prioritize_verified_users' => fake()->boolean(30),
            'prioritize_expert_content' => fake()->boolean(50),
            'prioritize_project_updates' => fake()->boolean(80),
            'prioritize_achievement_notifications' => fake()->boolean(60),
            'prioritize_investment_opportunities' => fake()->boolean(40),
        ];
    }

    private function showStatistics(): void
    {
        $total = UserFollow::count();
        $active = UserFollow::active()->count();
        $mutual = UserFollow::mutual()->count();
        $public = UserFollow::public()->count();
        $withNotifications = UserFollow::withNotifications()->count();
        $highEngagement = UserFollow::highEngagement()->count();
        
        $avgEngagementScore = UserFollow::whereNotNull('engagement_score')->avg('engagement_score');
        $avgInteractions = UserFollow::whereNotNull('interactions_count')->avg('interactions_count');
        
        $byType = UserFollow::selectRaw('follow_type, COUNT(*) as count')
            ->groupBy('follow_type')
            ->pluck('count', 'follow_type');
        
        $byStatus = UserFollow::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');
        
        $byNotificationFrequency = UserFollow::selectRaw('notification_frequency, COUNT(*) as count')
            ->groupBy('notification_frequency')
            ->pluck('count', 'notification_frequency');

        $this->command->info("\n📊 Estadísticas de seguimientos de usuarios:");
        $this->command->info("   • Total de seguimientos: {$total}");
        $this->command->info("   • Activos: {$active}");
        $this->command->info("   • Mutuos: {$mutual}");
        $this->command->info("   • Públicos: {$public}");
        $this->command->info("   • Con notificaciones: {$withNotifications}");
        $this->command->info("   • Alta interacción: {$highEngagement}");
        $this->command->info("   • Score de engagement promedio: " . round($avgEngagementScore, 1));
        $this->command->info("   • Interacciones promedio: " . round($avgInteractions, 1));

        $this->command->info("\n👥 Por tipo:");
        foreach ($byType as $type => $count) {
            $typeLabel = match($type) {
                'general' => 'General',
                'expertise' => 'Expertise',
                'projects' => 'Proyectos',
                'achievements' => 'Logros',
                'energy_activity' => 'Actividad Energética',
                'installations' => 'Instalaciones',
                'investments' => 'Inversiones',
                'content' => 'Contenido',
                'community' => 'Comunidad',
                default => ucfirst($type)
            };
            $this->command->info("   • {$typeLabel}: {$count}");
        }

        $this->command->info("\n📈 Por estado:");
        foreach ($byStatus as $status => $count) {
            $statusLabel = match($status) {
                'active' => 'Activo',
                'paused' => 'Pausado',
                'muted' => 'Silenciado',
                'blocked' => 'Bloqueado',
                default => ucfirst($status)
            };
            $this->command->info("   • {$statusLabel}: {$count}");
        }

        $this->command->info("\n🔔 Por frecuencia de notificación:");
        foreach ($byNotificationFrequency as $frequency => $count) {
            $frequencyLabel = match($frequency) {
                'instant' => 'Instantánea',
                'daily_digest' => 'Resumen diario',
                'weekly_digest' => 'Resumen semanal',
                'never' => 'Nunca',
                default => ucfirst($frequency)
            };
            $this->command->info("   • {$frequencyLabel}: {$count}");
        }

        // Mostrar algunos seguimientos recientes
        $recentFollows = UserFollow::with(['follower', 'following'])
            ->latest('followed_at')
            ->take(5)
            ->get();

        if ($recentFollows->isNotEmpty()) {
            $this->command->info("\n⭐ Últimos seguimientos creados:");
            foreach ($recentFollows as $follow) {
                $followerName = $follow->follower ? $follow->follower->name : 'Usuario Desconocido';
                $followingName = $follow->following ? $follow->following->name : 'Usuario Desconocido';
                $typeLabel = match($follow->follow_type) {
                    'general' => 'General',
                    'expertise' => 'Expertise',
                    'projects' => 'Proyectos',
                    'achievements' => 'Logros',
                    'energy_activity' => 'Actividad Energética',
                    'installations' => 'Instalaciones',
                    'investments' => 'Inversiones',
                    'content' => 'Contenido',
                    'community' => 'Comunidad',
                    default => ucfirst($follow->follow_type)
                };
                $this->command->info("   • {$followerName} siguió a {$followingName} ({$typeLabel})");
            }
        }
    }
}
