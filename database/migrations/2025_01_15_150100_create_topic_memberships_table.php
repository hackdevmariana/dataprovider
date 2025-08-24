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
            $table->foreignId('topic_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Rol del usuario en el tema
            $table->enum('role', [
                'member',               // Miembro regular
                'contributor',          // Contribuidor frecuente
                'moderator',            // Moderador
                'admin',                // Administrador
                'creator'               // Creador del tema
            ])->default('member');
            
            // Estado de la membresía
            $table->enum('status', [
                'active',               // Activo
                'pending',              // Pendiente de aprobación
                'banned',               // Baneado del tema
                'muted',                // Silenciado temporalmente
                'suspended',            // Suspendido
                'left',                 // Abandonó el tema
                'removed'               // Removido por moderador
            ])->default('active');
            
            // Configuraciones de notificaciones
            $table->boolean('notifications_enabled')->default(true);
            $table->boolean('email_notifications')->default(false);
            $table->boolean('push_notifications')->default(true);
            $table->boolean('digest_notifications')->default(true);
            
            $table->enum('notification_frequency', [
                'instant',              // Notificaciones instantáneas
                'hourly',               // Resumen cada hora
                'daily',                // Resumen diario
                'weekly',               // Resumen semanal
                'never'                 // Sin notificaciones
            ])->default('daily');
            
            // Preferencias de contenido
            $table->json('notification_preferences')->nullable(); // Configuraciones detalladas
            $table->boolean('notify_new_posts')->default(true);
            $table->boolean('notify_replies')->default(true);
            $table->boolean('notify_mentions')->default(true);
            $table->boolean('notify_trending')->default(false);
            $table->boolean('notify_announcements')->default(true);
            $table->boolean('notify_events')->default(true);
            
            // Configuración de feed
            $table->boolean('show_in_main_feed')->default(true);
            $table->boolean('prioritize_in_feed')->default(false);
            $table->integer('feed_weight')->default(100); // Peso en algoritmo de feed
            
            // Métricas del usuario en el tema
            $table->integer('posts_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->integer('upvotes_received')->default(0);
            $table->integer('downvotes_received')->default(0);
            $table->integer('reputation_score')->default(0); // Reputación en este tema específico
            $table->integer('helpful_answers_count')->default(0);
            $table->integer('best_answers_count')->default(0);
            
            // Estadísticas de participación
            $table->integer('days_active')->default(0); // Días activos en el tema
            $table->integer('consecutive_days_active')->default(0);
            $table->integer('posts_this_week')->default(0);
            $table->integer('posts_this_month')->default(0);
            $table->decimal('avg_post_score', 8, 2)->default(0);
            $table->decimal('participation_rate', 5, 2)->default(0); // % de participación
            
            // Información de actividad
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('last_post_at')->nullable();
            $table->timestamp('last_comment_at')->nullable();
            $table->timestamp('last_visit_at')->nullable();
            $table->integer('total_visits')->default(0);
            $table->integer('total_time_spent_minutes')->default(0);
            
            // Configuración de moderación
            $table->json('moderation_permissions')->nullable(); // Permisos específicos de moderación
            $table->boolean('can_pin_posts')->default(false);
            $table->boolean('can_feature_posts')->default(false);
            $table->boolean('can_delete_posts')->default(false);
            $table->boolean('can_ban_users')->default(false);
            $table->boolean('can_edit_topic')->default(false);
            
            // Información de moderación
            $table->text('ban_reason')->nullable();
            $table->timestamp('banned_until')->nullable();
            $table->foreignId('banned_by')->nullable()->constrained('users');
            $table->timestamp('muted_until')->nullable();
            $table->foreignId('muted_by')->nullable()->constrained('users');
            $table->text('moderation_notes')->nullable();
            
            // Configuración de privacidad
            $table->boolean('show_activity_publicly')->default(true);
            $table->boolean('allow_direct_messages')->default(true);
            $table->boolean('show_online_status')->default(true);
            
            // Logros y reconocimientos en el tema
            $table->json('topic_badges')->nullable(); // Badges específicos del tema
            $table->integer('featured_posts_count')->default(0);
            $table->integer('trending_posts_count')->default(0);
            $table->timestamp('became_contributor_at')->nullable();
            $table->timestamp('became_moderator_at')->nullable();
            
            // Configuración personalizada
            $table->json('custom_settings')->nullable(); // Configuraciones personalizadas
            $table->string('custom_title')->nullable(); // Título personalizado en el tema
            $table->string('custom_flair')->nullable(); // Flair personalizado
            $table->json('interests_in_topic')->nullable(); // Intereses específicos en este tema
            
            // Información de referencia
            $table->foreignId('invited_by')->nullable()->constrained('users'); // Quien lo invitó
            $table->string('join_source')->nullable(); // Cómo se unió (search, invitation, etc.)
            $table->json('join_metadata')->nullable(); // Metadatos del momento de unión
            
            $table->timestamps();
            
            // Constraint único para evitar membresías duplicadas
            $table->unique(['topic_id', 'user_id'], 'unique_topic_membership');
            
            // Índices para optimización
            $table->index(['topic_id', 'role', 'status']);
            $table->index(['user_id', 'status', 'joined_at']);
            $table->index(['topic_id', 'status', 'last_activity_at']);
            $table->index(['topic_id', 'reputation_score']);
            $table->index(['role', 'status']);
            $table->index(['last_activity_at', 'status']);
            $table->index(['notifications_enabled', 'notification_frequency'], 'idx_notif_enabled_freq');
            $table->index(['show_in_main_feed', 'feed_weight'], 'idx_feed_show_weight');
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
