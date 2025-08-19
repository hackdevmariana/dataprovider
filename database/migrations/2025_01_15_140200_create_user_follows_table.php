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
        Schema::create('user_follows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('follower_id')->constrained('users')->onDelete('cascade'); // Quien sigue
            $table->foreignId('following_id')->constrained('users')->onDelete('cascade'); // A quien sigue
            
            // Tipo de seguimiento
            $table->enum('follow_type', [
                'general',              // Seguimiento general
                'expertise',            // Por expertise específica
                'projects',             // Solo proyectos
                'achievements',         // Solo logros
                'energy_activity',      // Solo actividad energética
                'installations',        // Solo instalaciones
                'investments',          // Solo inversiones
                'content',              // Solo contenido publicado
                'community'             // Actividad comunitaria
            ])->default('general');
            
            // Configuración de notificaciones
            $table->boolean('notify_new_activity')->default(true); // Notificar nueva actividad
            $table->boolean('notify_achievements')->default(true); // Notificar logros
            $table->boolean('notify_projects')->default(true); // Notificar proyectos
            $table->boolean('notify_investments')->default(false); // Notificar inversiones
            $table->boolean('notify_milestones')->default(true); // Notificar hitos importantes
            $table->boolean('notify_content')->default(true); // Notificar contenido nuevo
            
            // Configuración de frecuencia
            $table->enum('notification_frequency', [
                'instant',              // Instantáneo
                'daily_digest',         // Resumen diario
                'weekly_digest',        // Resumen semanal
                'monthly_digest',       // Resumen mensual
                'never'                 // Nunca notificar
            ])->default('instant');
            
            // Configuración de feed
            $table->boolean('show_in_main_feed')->default(true); // Mostrar en feed principal
            $table->boolean('prioritize_in_feed')->default(false); // Priorizar en feed
            $table->integer('feed_weight')->default(100); // Peso en algoritmo de feed (0-100)
            
            // Contexto del seguimiento
            $table->text('follow_reason')->nullable(); // Razón del seguimiento
            $table->json('interests')->nullable(); // Intereses específicos en este usuario
            $table->json('tags')->nullable(); // Etiquetas personalizadas
            
            // Información de reciprocidad
            $table->boolean('is_mutual')->default(false); // Si es seguimiento mutuo
            $table->timestamp('mutual_since')->nullable(); // Desde cuándo es mutuo
            
            // Métricas de engagement
            $table->integer('interactions_count')->default(0); // Interacciones con contenido del seguido
            $table->timestamp('last_interaction_at')->nullable(); // Última interacción
            $table->decimal('engagement_score', 8, 2)->default(0); // Score de engagement
            $table->integer('content_views')->default(0); // Contenido visto del seguido
            
            // Configuración de privacidad
            $table->boolean('is_public')->default(true); // Si el seguimiento es público
            $table->boolean('show_to_followed')->default(true); // Mostrar al usuario seguido
            $table->boolean('allow_followed_to_see_activity')->default(true); // Permitir ver mi actividad
            
            // Configuración de filtros
            $table->json('content_filters')->nullable(); // Filtros de contenido
            $table->json('activity_filters')->nullable(); // Filtros de actividad
            $table->decimal('minimum_relevance_score', 5, 2)->default(0); // Score mínimo de relevancia
            
            // Estados y moderación
            $table->enum('status', [
                'active',               // Activo
                'paused',               // Pausado temporalmente
                'muted',                // Silenciado (sin notificaciones)
                'blocked',              // Bloqueado
                'requested',            // Solicitado (para cuentas privadas)
                'rejected'              // Rechazado
            ])->default('active');
            
            $table->timestamp('status_changed_at')->nullable(); // Cuándo cambió el estado
            $table->text('status_reason')->nullable(); // Razón del cambio de estado
            
            // Información temporal
            $table->timestamp('followed_at')->useCurrent(); // Cuándo empezó a seguir
            $table->timestamp('last_seen_activity_at')->nullable(); // Última actividad vista
            $table->integer('days_following')->default(0); // Días siguiendo
            
            // Configuración de algoritmo
            $table->decimal('relevance_decay_rate', 5, 2)->default(1.0); // Tasa de decaimiento de relevancia
            $table->json('algorithm_preferences')->nullable(); // Preferencias para algoritmo
            
            $table->timestamps();
            
            // Constraint único para evitar seguimientos duplicados
            $table->unique(['follower_id', 'following_id'], 'unique_follow_relationship');
            
            // Índices para optimización
            $table->index(['follower_id', 'status', 'created_at'], 'uf_follower_status');
            $table->index(['following_id', 'status', 'created_at'], 'uf_following_status');
            $table->index(['follow_type', 'status'], 'uf_type_status');
            $table->index(['is_mutual', 'mutual_since'], 'uf_mutual');
            $table->index(['notification_frequency', 'notify_new_activity'], 'uf_notif_freq');
            $table->index(['engagement_score', 'last_interaction_at'], 'uf_engagement');
            $table->index(['show_in_main_feed', 'feed_weight'], 'uf_feed_weight');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_follows');
    }
};
