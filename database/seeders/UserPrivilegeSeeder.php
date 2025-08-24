<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserPrivilege;
use App\Models\User;
use Carbon\Carbon;

class UserPrivilegeSeeder extends Seeder
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

        $privileges = [];

        // Crear privilegios para cada usuario
        foreach ($users as $user) {
            // Privilegios globales (nivel alto)
            $privileges[] = [
                'user_id' => $user->id,
                'privilege_type' => 'posting',
                'scope' => 'global',
                'scope_id' => null,
                'level' => 3,
                'is_active' => true,
                'permissions' => $this->getPermissions('posting', 3),
                'limits' => $this->getLimits('posting', 3),
                'reputation_required' => 100,
                'granted_at' => Carbon::now()->subDays(rand(30, 180)),
                'expires_at' => null,
                'granted_by' => $user->id, // Auto-concedido
                'reason' => 'Usuario activo con buena reputación',
                'created_at' => Carbon::now()->subDays(rand(30, 180)),
                'updated_at' => Carbon::now()->subDays(rand(1, 30)),
            ];

            $privileges[] = [
                'user_id' => $user->id,
                'privilege_type' => 'voting',
                'scope' => 'global',
                'scope_id' => null,
                'level' => 2,
                'is_active' => true,
                'permissions' => $this->getPermissions('voting', 2),
                'limits' => $this->getLimits('voting', 2),
                'reputation_required' => 50,
                'granted_at' => Carbon::now()->subDays(rand(20, 120)),
                'expires_at' => null,
                'granted_by' => $user->id,
                'reason' => 'Participación activa en votaciones',
                'created_at' => Carbon::now()->subDays(rand(20, 120)),
                'updated_at' => Carbon::now()->subDays(rand(1, 20)),
            ];

            $privileges[] = [
                'user_id' => $user->id,
                'privilege_type' => 'content_creation',
                'scope' => 'global',
                'scope_id' => null,
                'level' => 2,
                'is_active' => true,
                'permissions' => $this->getPermissions('content_creation', 2),
                'limits' => $this->getLimits('content_creation', 2),
                'reputation_required' => 75,
                'granted_at' => Carbon::now()->subDays(rand(15, 90)),
                'expires_at' => null,
                'granted_by' => $user->id,
                'reason' => 'Creador de contenido de calidad',
                'created_at' => Carbon::now()->subDays(rand(15, 90)),
                'updated_at' => Carbon::now()->subDays(rand(1, 15)),
            ];

            // Privilegios específicos por tema (scope: topic)
            $privileges[] = [
                'user_id' => $user->id,
                'privilege_type' => 'posting',
                'scope' => 'topic',
                'scope_id' => rand(1, 3), // IDs de temas existentes
                'level' => rand(2, 3),
                'is_active' => true,
                'permissions' => $this->getPermissions('posting', rand(2, 3)),
                'limits' => $this->getLimits('posting', rand(2, 3)),
                'reputation_required' => rand(50, 150),
                'granted_at' => Carbon::now()->subDays(rand(10, 60)),
                'expires_at' => null,
                'granted_by' => $users->where('id', '!=', $user->id)->random()->id,
                'reason' => 'Experto en el tema específico',
                'created_at' => Carbon::now()->subDays(rand(10, 60)),
                'updated_at' => Carbon::now()->subDays(rand(1, 10)),
            ];

            // Privilegios de moderación (solo para algunos usuarios)
            if (rand(0, 2) === 0) { // 33% de probabilidad
                $privileges[] = [
                    'user_id' => $user->id,
                    'privilege_type' => 'moderation',
                    'scope' => 'topic',
                    'scope_id' => rand(1, 3),
                    'level' => rand(2, 4),
                    'is_active' => true,
                    'permissions' => $this->getPermissions('moderation', rand(2, 4)),
                    'limits' => $this->getLimits('moderation', rand(2, 4)),
                    'reputation_required' => rand(200, 500),
                    'granted_at' => Carbon::now()->subDays(rand(5, 45)),
                    'expires_at' => null,
                    'granted_by' => $users->where('id', '!=', $user->id)->random()->id,
                    'reason' => 'Moderador confiable del tema',
                    'created_at' => Carbon::now()->subDays(rand(5, 45)),
                    'updated_at' => Carbon::now()->subDays(rand(1, 5)),
                ];
            }

            // Privilegios de verificación (solo para usuarios con alta reputación)
            if (rand(0, 3) === 0) { // 25% de probabilidad
                $privileges[] = [
                    'user_id' => $user->id,
                    'privilege_type' => 'verification',
                    'scope' => 'cooperative',
                    'scope_id' => rand(1, 2),
                    'level' => rand(3, 5),
                    'is_active' => true,
                    'permissions' => $this->getPermissions('verification', rand(3, 5)),
                    'limits' => $this->getLimits('verification', rand(3, 5)),
                    'reputation_required' => rand(300, 800),
                    'granted_at' => Carbon::now()->subDays(rand(3, 30)),
                    'expires_at' => null,
                    'granted_by' => $users->where('id', '!=', $user->id)->random()->id,
                    'reason' => 'Verificador experto de la cooperativa',
                    'created_at' => Carbon::now()->subDays(rand(3, 30)),
                    'updated_at' => Carbon::now()->subDays(rand(1, 3)),
                ];
            }

            // Privilegios de respuestas de experto
            if (rand(0, 2) === 0) { // 33% de probabilidad
                $privileges[] = [
                    'user_id' => $user->id,
                    'privilege_type' => 'expert_answers',
                    'scope' => 'topic',
                    'scope_id' => rand(1, 3),
                    'level' => rand(3, 4),
                    'is_active' => true,
                    'permissions' => $this->getPermissions('expert_answers', rand(3, 4)),
                    'limits' => $this->getLimits('expert_answers', rand(3, 4)),
                    'reputation_required' => rand(250, 600),
                    'granted_at' => Carbon::now()->subDays(rand(7, 40)),
                    'expires_at' => null,
                    'granted_by' => $users->where('id', '!=', $user->id)->random()->id,
                    'reason' => 'Experto reconocido en el tema',
                    'created_at' => Carbon::now()->subDays(rand(7, 40)),
                ];
            }

            // Privilegios de aprobación de proyectos
            if (rand(0, 4) === 0) { // 20% de probabilidad
                $privileges[] = [
                    'user_id' => $user->id,
                    'privilege_type' => 'project_approval',
                    'scope' => 'project',
                    'scope_id' => rand(1, 5),
                    'level' => rand(3, 5),
                    'is_active' => true,
                    'permissions' => $this->getPermissions('project_approval', rand(3, 5)),
                    'limits' => $this->getLimits('project_approval', rand(3, 5)),
                    'reputation_required' => rand(400, 1000),
                    'granted_at' => Carbon::now()->subDays(rand(2, 20)),
                    'expires_at' => null,
                    'granted_by' => $users->where('id', '!=', $user->id)->random()->id,
                    'reason' => 'Aprobador de proyectos energéticos',
                    'created_at' => Carbon::now()->subDays(rand(2, 20)),
                ];
            }

            // Privilegios temporales (con fecha de expiración)
            if (rand(0, 3) === 0) { // 25% de probabilidad
                $privileges[] = [
                    'user_id' => $user->id,
                    'privilege_type' => 'posting',
                    'scope' => 'cooperative',
                    'scope_id' => rand(1, 2),
                    'level' => 1,
                    'is_active' => true,
                    'permissions' => $this->getPermissions('posting', 1),
                    'limits' => $this->getLimits('posting', 1),
                    'reputation_required' => 25,
                    'granted_at' => Carbon::now()->subDays(rand(1, 10)),
                    'expires_at' => Carbon::now()->addDays(rand(7, 30)), // Privilegio temporal
                    'granted_by' => $users->where('id', '!=', $user->id)->random()->id,
                    'reason' => 'Privilegio temporal para proyecto específico',
                    'created_at' => Carbon::now()->subDays(rand(1, 10)),
                ];
            }

            // Privilegios inactivos (para simular revocaciones)
            if (rand(0, 4) === 0) { // 20% de probabilidad
                $privileges[] = [
                    'user_id' => $user->id,
                    'privilege_type' => 'voting',
                    'scope' => 'topic',
                    'scope_id' => rand(1, 3),
                    'level' => 1,
                    'is_active' => false,
                    'permissions' => $this->getPermissions('voting', 1),
                    'limits' => $this->getLimits('voting', 1),
                    'reputation_required' => 30,
                    'granted_at' => Carbon::now()->subDays(rand(50, 200)),
                    'expires_at' => null,
                    'granted_by' => $users->where('id', '!=', $user->id)->random()->id,
                    'reason' => 'Privilegio revocado por mal comportamiento',
                    'created_at' => Carbon::now()->subDays(rand(50, 200)),
                ];
            }
        }

        // Insertar todos los privilegios
        foreach ($privileges as $privilege) {
            UserPrivilege::create($privilege);
        }

        $this->command->info('✅ Se han creado ' . count($privileges) . ' privilegios de usuario.');
        $this->command->info('📊 Distribución por tipo:');
        $this->command->info('   - Posting: ' . count(array_filter($privileges, fn($p) => $p['privilege_type'] === 'posting')));
        $this->command->info('   - Voting: ' . count(array_filter($privileges, fn($p) => $p['privilege_type'] === 'voting')));
        $this->command->info('   - Moderation: ' . count(array_filter($privileges, fn($p) => $p['privilege_type'] === 'moderation')));
        $this->command->info('   - Verification: ' . count(array_filter($privileges, fn($p) => $p['privilege_type'] === 'verification')));
        $this->command->info('   - Expert Answers: ' . count(array_filter($privileges, fn($p) => $p['privilege_type'] === 'expert_answers')));
        $this->command->info('   - Project Approval: ' . count(array_filter($privileges, fn($p) => $p['privilege_type'] === 'project_approval')));
        $this->command->info('🏷️ Tipos: Posting, Voting, Moderation, Verification, Administration, Content Creation, Expert Answers, Project Approval');
        $this->command->info('📋 Scopes: Global, Topic, Cooperative, Project, Region');
        $this->command->info('📊 Niveles: 1-5 (básico a avanzado)');
        $this->command->info('🔒 Estados: Activo, Inactivo, Temporal, Permanente');
    }

    /**
     * Obtener permisos según el tipo y nivel de privilegio
     */
    private function getPermissions(string $type, int $level): array
    {
        return match ($type) {
            'posting' => match ($level) {
                1 => ['create_post', 'edit_own_post', 'delete_own_post'],
                2 => ['create_post', 'edit_own_post', 'delete_own_post', 'create_topic', 'pin_own_post'],
                3 => ['create_post', 'edit_own_post', 'delete_own_post', 'create_topic', 'pin_own_post', 'edit_others_post', 'feature_post'],
                default => ['create_post', 'edit_own_post', 'delete_own_post'],
            },
            'voting' => match ($level) {
                1 => ['vote_post', 'vote_comment', 'vote_project'],
                2 => ['vote_post', 'vote_comment', 'vote_project', 'vote_topic', 'vote_cooperative'],
                3 => ['vote_post', 'vote_comment', 'vote_project', 'vote_topic', 'vote_cooperative', 'vote_privilege', 'vote_ban'],
                default => ['vote_post', 'vote_comment'],
            },
            'moderation' => match ($level) {
                2 => ['warn_user', 'hide_post', 'flag_content'],
                3 => ['warn_user', 'hide_post', 'flag_content', 'delete_post', 'ban_user_temporary'],
                4 => ['warn_user', 'hide_post', 'flag_content', 'delete_post', 'ban_user_temporary', 'ban_user_permanent', 'edit_topic'],
                5 => ['warn_user', 'hide_post', 'flag_content', 'delete_post', 'ban_user_temporary', 'ban_user_permanent', 'edit_topic', 'manage_moderators'],
                default => ['warn_user', 'hide_post'],
            },
            'verification' => match ($level) {
                3 => ['verify_content', 'verify_project', 'approve_installation'],
                4 => ['verify_content', 'verify_project', 'approve_installation', 'verify_expert', 'revoke_verification'],
                5 => ['verify_content', 'verify_project', 'approve_installation', 'verify_expert', 'revoke_verification', 'manage_verifiers'],
                default => ['verify_content'],
            },
            'administration' => match ($level) {
                4 => ['manage_users', 'manage_topics', 'manage_cooperatives', 'view_analytics'],
                5 => ['manage_users', 'manage_topics', 'manage_cooperatives', 'view_analytics', 'manage_system', 'grant_privileges'],
                default => ['view_analytics'],
            },
            'content_creation' => match ($level) {
                1 => ['create_article', 'create_guide', 'create_tutorial'],
                2 => ['create_article', 'create_guide', 'create_tutorial', 'create_video', 'create_podcast'],
                3 => ['create_article', 'create_guide', 'create_tutorial', 'create_video', 'create_podcast', 'create_course', 'publish_book'],
                default => ['create_article'],
            },
            'expert_answers' => match ($level) {
                3 => ['answer_question', 'verify_answer', 'edit_answer'],
                4 => ['answer_question', 'verify_answer', 'edit_answer', 'pin_answer', 'delete_answer'],
                5 => ['answer_question', 'verify_answer', 'edit_answer', 'pin_answer', 'delete_answer', 'manage_experts'],
                default => ['answer_question'],
            },
            'project_approval' => match ($level) {
                3 => ['review_project', 'approve_project', 'request_changes'],
                4 => ['review_project', 'approve_project', 'request_changes', 'reject_project', 'expedite_approval'],
                5 => ['review_project', 'approve_project', 'request_changes', 'reject_project', 'expedite_approval', 'manage_approvers'],
                default => ['review_project'],
            },
            default => ['basic_access'],
        };
    }

    /**
     * Obtener límites según el tipo y nivel de privilegio
     */
    private function getLimits(string $type, int $level): array
    {
        return match ($type) {
            'posting' => match ($level) {
                1 => ['posts_per_day' => 5, 'posts_per_week' => 20, 'characters_per_post' => 1000],
                2 => ['posts_per_day' => 10, 'posts_per_week' => 50, 'characters_per_post' => 2000],
                3 => ['posts_per_day' => 20, 'posts_per_week' => 100, 'characters_per_post' => 5000],
                default => ['posts_per_day' => 3, 'posts_per_week' => 10, 'characters_per_post' => 500],
            },
            'voting' => match ($level) {
                1 => ['votes_per_hour' => 10, 'votes_per_day' => 50, 'votes_per_week' => 200],
                2 => ['votes_per_hour' => 20, 'votes_per_day' => 100, 'votes_per_week' => 500],
                3 => ['votes_per_hour' => 50, 'votes_per_day' => 200, 'votes_per_week' => 1000],
                default => ['votes_per_hour' => 5, 'votes_per_day' => 20, 'votes_per_week' => 100],
            },
            'moderation' => match ($level) {
                2 => ['warnings_per_day' => 5, 'hidden_posts_per_day' => 10, 'flags_per_day' => 20],
                3 => ['warnings_per_day' => 10, 'hidden_posts_per_day' => 25, 'flags_per_day' => 50, 'bans_per_day' => 3],
                4 => ['warnings_per_day' => 20, 'hidden_posts_per_day' => 50, 'flags_per_day' => 100, 'bans_per_day' => 10],
                5 => ['warnings_per_day' => 50, 'hidden_posts_per_day' => 100, 'flags_per_day' => 200, 'bans_per_day' => 25],
                default => ['warnings_per_day' => 2, 'hidden_posts_per_day' => 5, 'flags_per_day' => 10],
            },
            'verification' => match ($level) {
                3 => ['verifications_per_day' => 10, 'projects_per_day' => 5, 'installations_per_day' => 3],
                4 => ['verifications_per_day' => 25, 'projects_per_day' => 15, 'installations_per_day' => 8],
                5 => ['verifications_per_day' => 50, 'projects_per_day' => 30, 'installations_per_day' => 15],
                default => ['verifications_per_day' => 5, 'projects_per_day' => 2, 'installations_per_day' => 1],
            },
            'content_creation' => match ($level) {
                1 => ['articles_per_week' => 2, 'guides_per_month' => 1, 'tutorials_per_month' => 1],
                2 => ['articles_per_week' => 5, 'guides_per_month' => 3, 'tutorials_per_month' => 2, 'videos_per_month' => 2],
                3 => ['articles_per_week' => 10, 'guides_per_month' => 5, 'tutorials_per_month' => 4, 'videos_per_month' => 5, 'courses_per_year' => 2],
                default => ['articles_per_week' => 1, 'guides_per_month' => 1, 'tutorials_per_month' => 1],
            },
            'expert_answers' => match ($level) {
                3 => ['answers_per_day' => 10, 'verifications_per_day' => 5, 'edits_per_day' => 15],
                4 => ['answers_per_day' => 25, 'verifications_per_day' => 15, 'edits_per_day' => 30, 'pins_per_day' => 5],
                5 => ['answers_per_day' => 50, 'verifications_per_day' => 30, 'edits_per_day' => 60, 'pins_per_day' => 10],
                default => ['answers_per_day' => 5, 'verifications_per_day' => 2, 'edits_per_day' => 8],
            },
            'project_approval' => match ($level) {
                3 => ['reviews_per_day' => 5, 'approvals_per_day' => 3, 'changes_per_day' => 8],
                4 => ['reviews_per_day' => 15, 'approvals_per_day' => 10, 'changes_per_day' => 20, 'rejections_per_day' => 5],
                5 => ['reviews_per_day' => 30, 'approvals_per_day' => 20, 'changes_per_day' => 40, 'rejections_per_day' => 15],
                default => ['reviews_per_day' => 2, 'approvals_per_day' => 1, 'changes_per_day' => 3],
            },
            default => ['requests_per_day' => 10],
        };
    }
}
