<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('topic_memberships', function (Blueprint $table) {
            $table->id();
            
            // Relaciones principales
            $table->foreignId('topic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Información básica de membresía
            $table->string('role')->default('member'); // member, contributor, moderator, admin, creator
            $table->string('status')->default('active'); // active, pending, banned, muted, inactive
            $table->timestamp('joined_at')->nullable();
            
            // Configuración de notificaciones
            $table->boolean('notifications_enabled')->default(true);
            $table->boolean('email_notifications')->default(true);
            $table->boolean('push_notifications')->default(false);
            $table->boolean('digest_notifications')->default(true);
            $table->string('notification_frequency')->default('daily'); // immediate, daily, weekly, never
            $table->json('notification_preferences')->nullable();
            
            // Tipos específicos de notificaciones
            $table->boolean('notify_new_posts')->default(true);
            $table->boolean('notify_replies')->default(true);
            $table->boolean('notify_mentions')->default(true);
            $table->boolean('notify_trending')->default(false);
            $table->boolean('notify_announcements')->default(true);
            $table->boolean('notify_events')->default(false);
            
            // Configuración de feed
            $table->boolean('show_in_main_feed')->default(true);
            $table->boolean('prioritize_in_feed')->default(false);
            $table->decimal('feed_weight', 3, 2)->default(1.00);
            
            // Estadísticas de actividad
            $table->unsignedInteger('posts_count')->default(0);
            $table->unsignedInteger('comments_count')->default(0);
            $table->integer('upvotes_received')->default(0);
            $table->integer('downvotes_received')->default(0);
            $table->integer('reputation_score')->default(0);
            $table->unsignedInteger('helpful_answers_count')->default(0);
            $table->unsignedInteger('best_answers_count')->default(0);
            $table->unsignedInteger('days_active')->default(0);
            $table->unsignedInteger('consecutive_days_active')->default(0);
            $table->unsignedInteger('posts_this_week')->default(0);
            $table->unsignedInteger('posts_this_month')->default(0);
            $table->decimal('avg_post_score', 5, 2)->default(0.00);
            $table->decimal('participation_rate', 5, 2)->default(0.00);
            
            // Timestamps de actividad
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('last_post_at')->nullable();
            $table->timestamp('last_comment_at')->nullable();
            $table->timestamp('last_visit_at')->nullable();
            
            // Estadísticas de visitas
            $table->unsignedInteger('total_visits')->default(0);
            $table->unsignedInteger('total_time_spent_minutes')->default(0);
            
            // Permisos de moderación
            $table->json('moderation_permissions')->nullable();
            $table->boolean('can_pin_posts')->default(false);
            $table->boolean('can_feature_posts')->default(false);
            $table->boolean('can_delete_posts')->default(false);
            $table->boolean('can_ban_users')->default(false);
            $table->boolean('can_edit_topic')->default(false);
            
            // Sistema de moderación
            $table->text('ban_reason')->nullable();
            $table->timestamp('banned_until')->nullable();
            $table->foreignId('banned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('muted_until')->nullable();
            $table->foreignId('muted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('moderation_notes')->nullable();
            
            // Configuración de privacidad
            $table->boolean('show_activity_publicly')->default(true);
            $table->boolean('allow_direct_messages')->default(true);
            $table->boolean('show_online_status')->default(true);
            
            // Badges y reconocimientos
            $table->json('topic_badges')->nullable();
            $table->unsignedInteger('featured_posts_count')->default(0);
            $table->unsignedInteger('trending_posts_count')->default(0);
            
            // Promociones
            $table->timestamp('became_contributor_at')->nullable();
            $table->timestamp('became_moderator_at')->nullable();
            
            // Configuración personalizada
            $table->json('custom_settings')->nullable();
            $table->string('custom_title')->nullable();
            $table->string('custom_flair')->nullable();
            $table->json('interests_in_topic')->nullable();
            
            // Información de invitación
            $table->foreignId('invited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('join_source')->nullable(); // direct, invitation, referral, etc.
            $table->json('join_metadata')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->unique(['topic_id', 'user_id']);
            $table->index(['topic_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['role']);
            $table->index(['reputation_score']);
            $table->index(['last_activity_at']);
            $table->index(['joined_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topic_memberships');
    }
};