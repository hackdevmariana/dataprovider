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
        Schema::create('activity_feeds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Usuario que genera la actividad
            
            // Tipo de actividad energética
            $table->enum('activity_type', [
                'energy_saved',          // Ahorro energético
                'solar_generated',       // Energía solar generada
                'achievement_unlocked',  // Logro desbloqueado
                'project_funded',        // Proyecto financiado
                'installation_completed', // Instalación completada
                'cooperative_joined',    // Se unió a cooperativa
                'roof_published',        // Publicó techo en marketplace
                'investment_made',       // Realizó inversión
                'production_right_sold', // Vendió derecho de producción
                'challenge_completed',   // Completó desafío
                'milestone_reached',     // Alcanzó hito
                'content_published',     // Publicó contenido
                'expert_verified',       // Verificado como experto
                'review_published',      // Publicó review
                'topic_created',         // Creó tema de discusión
                'community_contribution', // Contribución comunitaria
                'carbon_milestone',      // Hito de reducción CO2
                'efficiency_improvement', // Mejora de eficiencia
                'grid_contribution',     // Contribución a la red
                'sustainability_goal',   // Meta de sostenibilidad
                'other'                  // Otra actividad
            ]);
            
            // Relación polimórfica con el objeto relacionado
            $table->morphs('related'); // related_type + related_id
            
            // Datos específicos de la actividad (JSON flexible)
            $table->json('activity_data'); // Detalles específicos por tipo de actividad
            $table->text('description')->nullable(); // Descripción legible de la actividad
            $table->text('summary')->nullable(); // Resumen corto para notificaciones
            
            // Métricas de la actividad
            $table->decimal('energy_amount_kwh', 10, 2)->nullable(); // Cantidad de energía (kWh)
            $table->decimal('cost_savings_eur', 8, 2)->nullable(); // Ahorro económico (€)
            $table->decimal('co2_savings_kg', 8, 2)->nullable(); // Ahorro CO2 (kg)
            $table->decimal('investment_amount_eur', 10, 2)->nullable(); // Cantidad invertida (€)
            $table->integer('community_impact_score')->nullable(); // Puntuación impacto comunitario
            
            // Configuración de visibilidad
            $table->enum('visibility', [
                'public',               // Visible para todos
                'cooperative',          // Solo miembros de cooperativa
                'followers',            // Solo seguidores
                'private'               // Solo el usuario
            ])->default('public');
            
            // Configuración de contenido
            $table->boolean('is_featured')->default(false); // Actividad destacada
            $table->boolean('is_milestone')->default(false); // Es un hito importante
            $table->boolean('notify_followers')->default(true); // Notificar a seguidores
            $table->boolean('show_in_feed')->default(true); // Mostrar en feed público
            $table->boolean('allow_interactions')->default(true); // Permitir likes/comentarios
            
            // Métricas de engagement
            $table->integer('engagement_score')->default(0); // Score total de engagement
            $table->integer('likes_count')->default(0); // Número de likes
            $table->integer('loves_count')->default(0); // Número de loves
            $table->integer('wow_count')->default(0); // Número de wow
            $table->integer('comments_count')->default(0); // Número de comentarios
            $table->integer('shares_count')->default(0); // Número de shares
            $table->integer('bookmarks_count')->default(0); // Número de bookmarks
            $table->integer('views_count')->default(0); // Número de visualizaciones
            
            // Geolocalización (opcional)
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('location_name')->nullable(); // Nombre del lugar
            
            // Información temporal
            $table->timestamp('activity_occurred_at')->nullable(); // Cuándo ocurrió la actividad real
            $table->boolean('is_real_time')->default(true); // Si es actividad en tiempo real
            
            // Agrupación de actividades
            $table->string('activity_group')->nullable(); // Grupo de actividades relacionadas
            $table->foreignId('parent_activity_id')->nullable()->constrained('activity_feeds'); // Actividad padre
            
            // Configuración de algoritmo
            $table->decimal('relevance_score', 8, 2)->default(100); // Score de relevancia
            $table->timestamp('boost_until')->nullable(); // Fecha hasta la cual impulsar
            $table->json('algorithm_data')->nullable(); // Datos para algoritmo de feed
            
            // Estados y moderación
            $table->enum('status', [
                'active',               // Activa
                'hidden',               // Oculta
                'flagged',              // Reportada
                'archived',             // Archivada
                'deleted'               // Eliminada
            ])->default('active');
            
            $table->integer('flags_count')->default(0); // Número de reportes
            $table->json('flag_reasons')->nullable(); // Razones de reportes
            $table->foreignId('moderated_by')->nullable()->constrained('users');
            $table->timestamp('moderated_at')->nullable();
            
            $table->timestamps();
            
            // Índices para optimización
            $table->index(['user_id', 'created_at']);
            $table->index(['activity_type', 'created_at']);
            $table->index(['visibility', 'status', 'created_at']);
            $table->index(['engagement_score', 'created_at']);
            $table->index(['is_featured', 'relevance_score']);
            $table->index(['activity_occurred_at']);
            $table->index(['related_type', 'related_id'], 'activity_related_idx');
            $table->index(['activity_group', 'created_at']);
            $table->index(['latitude', 'longitude']); // Para búsquedas geoespaciales
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_feeds');
    }
};
