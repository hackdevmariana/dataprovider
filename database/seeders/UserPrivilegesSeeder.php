<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserPrivilege;
use App\Models\User;
use Carbon\Carbon;

class UserPrivilegesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::take(10)->get();
        
        if ($users->isEmpty()) {
            $this->command->warn('No hay usuarios disponibles. Ejecuta primero UserSeeder.');
            return;
        }

        $privileges = [
            [
                'user_id' => $users->random()->id,
                'privilege_type' => 'moderator',
                'scope' => 'global',
                'scope_id' => null,
                'level' => 3,
                'is_active' => true,
                'permissions' => [
                    'moderate_content' => true,
                    'ban_users' => true,
                    'delete_posts' => true,
                    'edit_posts' => true,
                    'manage_categories' => true,
                ],
                'limits' => [
                    'daily_actions' => 100,
                    'ban_duration_days' => 30,
                    'content_moderation_hours' => 8,
                ],
                'reputation_required' => 1000,
                'granted_at' => Carbon::now()->subDays(30),
                'expires_at' => Carbon::now()->addYear(),
                'granted_by' => 1,
                'reason' => 'Experiencia demostrada en moderación de contenido',
            ],
            [
                'user_id' => $users->random()->id,
                'privilege_type' => 'expert',
                'scope' => 'topic',
                'scope_id' => 1,
                'level' => 4,
                'is_active' => true,
                'permissions' => [
                    'verify_content' => true,
                    'create_expert_posts' => true,
                    'mentor_users' => true,
                    'access_expert_tools' => true,
                ],
                'limits' => [
                    'daily_verifications' => 50,
                    'mentoring_sessions' => 10,
                    'expert_posts_per_day' => 5,
                ],
                'reputation_required' => 2000,
                'granted_at' => Carbon::now()->subDays(60),
                'expires_at' => Carbon::now()->addYear(),
                'granted_by' => 1,
                'reason' => 'Certificación profesional en energía renovable',
            ],
            [
                'user_id' => $users->random()->id,
                'privilege_type' => 'contributor',
                'scope' => 'category',
                'scope_id' => 2,
                'level' => 2,
                'is_active' => true,
                'permissions' => [
                    'create_premium_content' => true,
                    'access_analytics' => true,
                    'priority_support' => true,
                ],
                'limits' => [
                    'premium_posts_per_month' => 20,
                    'analytics_access_hours' => 24,
                    'support_priority_level' => 2,
                ],
                'reputation_required' => 500,
                'granted_at' => Carbon::now()->subDays(15),
                'expires_at' => Carbon::now()->addMonths(6),
                'granted_by' => 1,
                'reason' => 'Contribuciones valiosas en categoría de sostenibilidad',
            ],
            [
                'user_id' => $users->random()->id,
                'privilege_type' => 'mentor',
                'scope' => 'global',
                'scope_id' => null,
                'level' => 3,
                'is_active' => true,
                'permissions' => [
                    'mentor_new_users' => true,
                    'create_tutorials' => true,
                    'moderate_mentoring' => true,
                ],
                'limits' => [
                    'mentoring_sessions_per_week' => 15,
                    'tutorials_per_month' => 5,
                    'mentee_limit' => 20,
                ],
                'reputation_required' => 1500,
                'granted_at' => Carbon::now()->subDays(45),
                'expires_at' => Carbon::now()->addYear(),
                'granted_by' => 1,
                'reason' => 'Experiencia demostrada en mentoría y enseñanza',
            ],
            [
                'user_id' => $users->random()->id,
                'privilege_type' => 'analyst',
                'scope' => 'platform',
                'scope_id' => null,
                'level' => 4,
                'is_active' => true,
                'permissions' => [
                    'access_platform_analytics' => true,
                    'generate_reports' => true,
                    'export_data' => true,
                ],
                'limits' => [
                    'reports_per_day' => 10,
                    'data_export_limit_mb' => 1000,
                    'analytics_access_hours' => 12,
                ],
                'reputation_required' => 3000,
                'granted_at' => Carbon::now()->subDays(90),
                'expires_at' => Carbon::now()->addYear(),
                'granted_by' => 1,
                'reason' => 'Certificación en análisis de datos y estadísticas',
            ],
            [
                'user_id' => $users->random()->id,
                'privilege_type' => 'content_creator',
                'scope' => 'category',
                'scope_id' => 3,
                'level' => 3,
                'is_active' => true,
                'permissions' => [
                    'create_sponsored_content' => true,
                    'access_creator_tools' => true,
                    'monetize_content' => true,
                ],
                'limits' => [
                    'sponsored_posts_per_month' => 10,
                    'monetization_threshold' => 1000,
                    'creator_tools_access' => true,
                ],
                'reputation_required' => 800,
                'granted_at' => Carbon::now()->subDays(20),
                'expires_at' => Carbon::now()->addMonths(8),
                'granted_by' => 1,
                'reason' => 'Calidad demostrada en creación de contenido',
            ],
            [
                'user_id' => $users->random()->id,
                'privilege_type' => 'community_manager',
                'scope' => 'topic',
                'scope_id' => 4,
                'level' => 3,
                'is_active' => true,
                'permissions' => [
                    'manage_community' => true,
                    'organize_events' => true,
                    'moderate_discussions' => true,
                ],
                'limits' => [
                    'events_per_month' => 5,
                    'community_members_limit' => 500,
                    'moderation_actions_per_day' => 200,
                ],
                'reputation_required' => 1200,
                'granted_at' => Carbon::now()->subDays(75),
                'expires_at' => Carbon::now()->addYear(),
                'granted_by' => 1,
                'reason' => 'Liderazgo demostrado en gestión de comunidad',
            ],
            [
                'user_id' => $users->random()->id,
                'privilege_type' => 'beta_tester',
                'scope' => 'platform',
                'scope_id' => null,
                'level' => 2,
                'is_active' => true,
                'permissions' => [
                    'access_beta_features' => true,
                    'provide_feedback' => true,
                    'report_bugs' => true,
                ],
                'limits' => [
                    'beta_features_access' => true,
                    'feedback_reports_per_week' => 20,
                    'bug_reports_per_day' => 10,
                ],
                'reputation_required' => 600,
                'granted_at' => Carbon::now()->subDays(10),
                'expires_at' => Carbon::now()->addMonths(3),
                'granted_by' => 1,
                'reason' => 'Participación activa en pruebas beta',
            ],
        ];

        foreach ($privileges as $privilegeData) {
            UserPrivilege::create($privilegeData);
        }
    }
}
