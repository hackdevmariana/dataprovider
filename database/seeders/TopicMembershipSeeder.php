<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TopicMembership;
use App\Models\Topic;
use App\Models\User;
use Carbon\Carbon;

class TopicMembershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $topics = Topic::all();
        
        if ($users->isEmpty() || $topics->isEmpty()) {
            $this->command->warn('No hay usuarios o temas disponibles. Ejecuta UserSeeder y TopicSeeder primero.');
            return;
        }

        $memberships = [];

        // Crear membresías para cada tema
        foreach ($topics as $topic) {
            // Asignar usuarios a este tema
            $topicUsers = $users->random(min(3, $users->count()));
            
            foreach ($topicUsers as $index => $user) {
                $role = $this->getRoleForIndex($index);
                $joinedAt = Carbon::now()->subDays(rand(5, 90));
                
                $memberships[] = [
                    'topic_id' => $topic->id,
                    'user_id' => $user->id,
                    'role' => $role,
                    'status' => 'active',
                    'notifications_enabled' => true,
                    'email_notifications' => rand(0, 1),
                    'push_notifications' => true,
                    'digest_notifications' => rand(0, 1),
                    'notification_frequency' => $this->getRandomNotificationFrequency(),
                    'notification_preferences' => $this->getNotificationPreferences(),
                    'notify_new_posts' => rand(0, 1),
                    'notify_replies' => true,
                    'notify_mentions' => true,
                    'notify_trending' => rand(0, 1),
                    'notify_announcements' => true,
                    'notify_events' => rand(0, 1),
                    'show_in_main_feed' => true,
                    'prioritize_in_feed' => $role === 'creator' || $role === 'moderator',
                    'feed_weight' => $this->getFeedWeight($role),
                    'posts_count' => $this->getPostsCount($role),
                    'comments_count' => $this->getCommentsCount($role),
                    'upvotes_received' => $this->getUpvotesCount($role),
                    'downvotes_received' => rand(0, 5),
                    'reputation_score' => $this->getReputationScore($role),
                    'helpful_answers_count' => $this->getHelpfulAnswersCount($role),
                    'best_answers_count' => $this->getBestAnswersCount($role),
                    'days_active' => rand(5, 45),
                    'consecutive_days_active' => rand(1, 10),
                    'posts_this_week' => rand(0, 3),
                    'posts_this_month' => rand(1, 8),
                    'avg_post_score' => rand(60, 90) / 10,
                    'participation_rate' => rand(50, 90) / 10,
                    'joined_at' => $joinedAt,
                    'last_activity_at' => Carbon::now()->subHours(rand(2, 72)),
                    'last_post_at' => Carbon::now()->subDays(rand(1, 10)),
                    'last_comment_at' => Carbon::now()->subHours(rand(4, 96)),
                    'last_visit_at' => Carbon::now()->subHours(rand(2, 48)),
                    'total_visits' => rand(10, 100),
                    'total_time_spent_minutes' => rand(100, 800),
                    'moderation_permissions' => $this->getModerationPermissions($role),
                    'can_pin_posts' => in_array($role, ['creator', 'moderator']),
                    'can_feature_posts' => in_array($role, ['creator', 'moderator']),
                    'can_delete_posts' => in_array($role, ['creator', 'moderator']),
                    'can_ban_users' => in_array($role, ['creator', 'moderator']),
                    'can_edit_topic' => $role === 'creator',
                    'ban_reason' => null,
                    'banned_until' => null,
                    'banned_by' => null,
                    'muted_until' => null,
                    'muted_by' => null,
                    'moderation_notes' => null,
                    'show_activity_publicly' => rand(0, 1),
                    'allow_direct_messages' => true,
                    'show_online_status' => rand(0, 1),
                    'topic_badges' => $this->getTopicBadges($role),
                    'featured_posts_count' => $this->getFeaturedPostsCount($role),
                    'trending_posts_count' => $this->getTrendingPostsCount($role),
                    'became_contributor_at' => $role === 'contributor' ? $joinedAt->copy()->addDays(rand(1, 20)) : null,
                    'became_moderator_at' => $role === 'moderator' ? $joinedAt->copy()->addDays(rand(5, 30)) : null,
                    'custom_settings' => $this->getCustomSettings(),
                    'custom_title' => $this->getCustomTitle($role),
                    'custom_flair' => $this->getCustomFlair($role),
                    'interests_in_topic' => $this->getInterestsInTopic(),
                    'invited_by' => $index > 0 ? $topicUsers->first()->id : null,
                    'join_source' => $this->getJoinSource($index),
                    'join_metadata' => $this->getJoinMetadata($this->getJoinSource($index)),
                    'created_at' => $joinedAt,
                    'updated_at' => Carbon::now()->subHours(rand(2, 72)),
                ];
            }
        }

        // Insertar todas las membresías
        foreach ($memberships as $membership) {
            TopicMembership::create($membership);
        }

        $this->command->info('✅ Se han creado ' . count($memberships) . ' membresías de temas.');
        $this->command->info('📊 Distribución por tema:');
        $this->command->info('   - Temas: ' . $topics->count());
        $this->command->info('   - Usuarios por tema: ' . min(3, $users->count()));
        $this->command->info('🏷️ Roles: Miembro, Contribuidor, Moderador, Creador');
        $this->command->info('📊 Estados: Activo, Pendiente, Baneado, Silenciado');
        $this->command->info('🔔 Notificaciones: Instantáneas, Horarias, Diarias, Semanales, Nunca');
        $this->command->info('📈 Métricas: Posts, comentarios, reputación, participación');
    }

    /**
     * Obtener rol según el índice del usuario en el tema
     */
    private function getRoleForIndex(int $index): string
    {
        return match ($index) {
            0 => 'creator',
            1 => 'moderator',
            default => 'member',
        };
    }

    /**
     * Obtener peso en el feed según el rol
     */
    private function getFeedWeight(string $role): float
    {
        return match ($role) {
            'creator' => 9.99,
            'moderator' => 8.50,
            'contributor' => 7.00,
            default => 5.00,
        };
    }

    /**
     * Obtener conteo de posts según el rol
     */
    private function getPostsCount(string $role): int
    {
        return match ($role) {
            'creator' => rand(15, 40),
            'moderator' => rand(10, 25),
            'contributor' => rand(5, 15),
            default => rand(0, 8),
        };
    }

    /**
     * Obtener conteo de comentarios según el rol
     */
    private function getCommentsCount(string $role): int
    {
        return match ($role) {
            'creator' => rand(25, 80),
            'moderator' => rand(15, 50),
            'contributor' => rand(8, 30),
            default => rand(0, 20),
        };
    }

    /**
     * Obtener conteo de upvotes según el rol
     */
    private function getUpvotesCount(string $role): int
    {
        return match ($role) {
            'creator' => rand(40, 150),
            'moderator' => rand(25, 100),
            'contributor' => rand(15, 60),
            default => rand(0, 30),
        };
    }

    /**
     * Obtener score de reputación según el rol
     */
    private function getReputationScore(string $role): int
    {
        return match ($role) {
            'creator' => rand(200, 400),
            'moderator' => rand(150, 300),
            'contributor' => rand(100, 200),
            default => rand(0, 100),
        };
    }

    /**
     * Obtener conteo de respuestas útiles según el rol
     */
    private function getHelpfulAnswersCount(string $role): int
    {
        return match ($role) {
            'creator' => rand(8, 20),
            'moderator' => rand(5, 15),
            'contributor' => rand(3, 10),
            default => rand(0, 3),
        };
    }

    /**
     * Obtener conteo de mejores respuestas según el rol
     */
    private function getBestAnswersCount(string $role): int
    {
        return match ($role) {
            'creator' => rand(3, 10),
            'moderator' => rand(2, 6),
            'contributor' => rand(1, 4),
            default => 0,
        };
    }

    /**
     * Obtener conteo de posts destacados según el rol
     */
    private function getFeaturedPostsCount(string $role): int
    {
        return match ($role) {
            'creator' => rand(2, 8),
            'moderator' => rand(1, 4),
            default => 0,
        };
    }

    /**
     * Obtener conteo de posts trending según el rol
     */
    private function getTrendingPostsCount(string $role): int
    {
        return match ($role) {
            'creator' => rand(1, 5),
            'moderator' => rand(1, 3),
            default => 0,
        };
    }

    /**
     * Obtener título personalizado según el rol
     */
    private function getCustomTitle(string $role): ?string
    {
        return match ($role) {
            'creator' => 'Fundador',
            'moderator' => 'Moderador',
            'contributor' => 'Contribuidor',
            default => null,
        };
    }

    /**
     * Obtener flair personalizado según el rol
     */
    private function getCustomFlair(string $role): ?string
    {
        return match ($role) {
            'creator' => '🌟',
            'moderator' => '🛡️',
            'contributor' => '⭐',
            default => null,
        };
    }

    /**
     * Obtener fuente de unión según el índice
     */
    private function getJoinSource(int $index): string
    {
        return match ($index) {
            0 => 'created',
            1 => 'invitation',
            default => 'search',
        };
    }

    /**
     * Obtener preferencias de notificación
     */
    private function getNotificationPreferences(): array
    {
        return [
            'quiet_hours' => [
                'enabled' => rand(0, 1),
                'start' => '22:00',
                'end' => '08:00',
            ],
            'priority_levels' => [
                'high' => ['announcements', 'mentions'],
                'medium' => ['new_posts', 'replies'],
                'low' => ['trending', 'events'],
            ],
            'channels' => [
                'email' => rand(0, 1),
                'push' => true,
                'in_app' => true,
                'sms' => false,
            ],
        ];
    }

    /**
     * Obtener permisos de moderación por rol
     */
    private function getModerationPermissions(string $role): array
    {
        return match ($role) {
            'creator' => [
                'can_pin_posts' => true,
                'can_feature_posts' => true,
                'can_delete_posts' => true,
                'can_ban_users' => true,
                'can_edit_topic' => true,
                'can_manage_moderators' => true,
                'can_view_analytics' => true,
            ],
            'moderator' => [
                'can_pin_posts' => true,
                'can_feature_posts' => true,
                'can_delete_posts' => true,
                'can_ban_users' => true,
                'can_edit_topic' => false,
                'can_manage_moderators' => false,
                'can_view_analytics' => true,
            ],
            default => [
                'can_pin_posts' => false,
                'can_feature_posts' => false,
                'can_delete_posts' => false,
                'can_ban_users' => false,
                'can_edit_topic' => false,
                'can_manage_moderators' => false,
                'can_view_analytics' => false,
            ],
        };
    }

    /**
     * Obtener badges del tema por rol
     */
    private function getTopicBadges(string $role): array
    {
        $baseBadges = [
            'early_adopter' => [
                'name' => 'Adoptador Temprano',
                'description' => 'Se unió al tema en sus inicios',
                'earned_at' => Carbon::now()->subDays(rand(30, 180))->toISOString(),
            ],
        ];

        $roleBadges = match ($role) {
            'creator' => [
                'founder' => [
                    'name' => 'Fundador',
                    'description' => 'Creador del tema',
                    'earned_at' => Carbon::now()->subDays(rand(30, 180))->toISOString(),
                ],
                'visionary' => [
                    'name' => 'Visionario',
                    'description' => 'Líder de la comunidad',
                    'earned_at' => Carbon::now()->subDays(rand(20, 120))->toISOString(),
                ],
            ],
            'moderator' => [
                'guardian' => [
                    'name' => 'Guardian',
                    'description' => 'Protector de la comunidad',
                    'earned_at' => Carbon::now()->subDays(rand(15, 90))->toISOString(),
                ],
                'peacekeeper' => [
                    'name' => 'Mantenedor de Paz',
                    'description' => 'Moderador activo',
                    'earned_at' => Carbon::now()->subDays(rand(10, 60))->toISOString(),
                ],
            ],
            'contributor' => [
                'knowledge_sharer' => [
                    'name' => 'Compartidor de Conocimiento',
                    'description' => 'Contribuye activamente',
                    'earned_at' => Carbon::now()->subDays(rand(5, 45))->toISOString(),
                ],
                'helpful_member' => [
                    'name' => 'Miembro Útil',
                    'description' => 'Respuestas útiles',
                    'earned_at' => Carbon::now()->subDays(rand(3, 30))->toISOString(),
                ],
            ],
            default => [
                'active_member' => [
                    'name' => 'Miembro Activo',
                    'description' => 'Participación regular',
                    'earned_at' => Carbon::now()->subDays(rand(1, 20))->toISOString(),
                ],
            ],
        };

        return array_merge($baseBadges, $roleBadges);
    }

    /**
     * Obtener configuración personalizada
     */
    private function getCustomSettings(): array
    {
        return [
            'theme_preference' => ['light', 'dark', 'auto'][array_rand(['light', 'dark', 'auto'])],
            'language' => ['es', 'en', 'fr'][array_rand(['es', 'en', 'fr'])],
            'auto_subscribe' => rand(0, 1),
            'show_signatures' => rand(0, 1),
            'compact_view' => rand(0, 1),
            'auto_refresh' => rand(0, 1),
            'notifications_sound' => rand(0, 1),
            'email_digest' => rand(0, 1),
        ];
    }

    /**
     * Obtener intereses en el tema
     */
    private function getInterestsInTopic(): array
    {
        $interests = [
            'technical_discussions',
            'news_and_updates',
            'community_events',
            'help_and_support',
            'showcase_and_projects',
            'learning_resources',
            'networking',
            'industry_trends',
        ];

        $selectedInterests = array_rand($interests, rand(3, 6));
        if (!is_array($selectedInterests)) {
            $selectedInterests = [$selectedInterests];
        }

        $result = [];
        foreach ($selectedInterests as $index) {
            $result[$interests[$index]] = [
                'level' => ['beginner', 'intermediate', 'advanced'][array_rand(['beginner', 'intermediate', 'advanced'])],
                'interest_score' => rand(60, 100),
                'added_at' => Carbon::now()->subDays(rand(1, 30))->toISOString(),
            ];
        }

        return $result;
    }

    /**
     * Obtener metadatos de unión
     */
    private function getJoinMetadata(string $source): array
    {
        return match ($source) {
            'created' => [
                'method' => 'topic_creation',
                'referrer' => null,
                'campaign' => null,
                'user_agent' => 'Seeder Generated',
                'ip_address' => '127.0.0.1',
                'location' => 'Madrid, España',
            ],
            'invitation' => [
                'method' => 'direct_invitation',
                'referrer' => 'moderator_invite',
                'campaign' => 'community_growth',
                'user_agent' => 'Seeder Generated',
                'ip_address' => '127.0.0.1',
                'location' => 'Madrid, España',
            ],
            'search' => [
                'method' => 'search_discovery',
                'referrer' => 'google_search',
                'campaign' => null,
                'user_agent' => 'Seeder Generated',
                'ip_address' => '127.0.0.1',
                'location' => 'Madrid, España',
            ],
            default => [
                'method' => 'unknown',
                'referrer' => null,
                'campaign' => null,
                'user_agent' => 'Seeder Generated',
                'ip_address' => '127.0.0.1',
                'location' => 'Madrid, España',
            ],
        };
    }

    /**
     * Obtener frecuencia de notificación aleatoria
     */
    private function getRandomNotificationFrequency(): string
    {
        $frequencies = ['instant', 'hourly', 'daily', 'weekly', 'never'];
        return $frequencies[array_rand($frequencies)];
    }
}
