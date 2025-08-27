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
        Schema::create('topic_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Contenido del post
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('body'); // Contenido principal (Markdown)
            $table->text('excerpt')->nullable(); // Resumen automático o manual
            $table->text('summary')->nullable(); // Resumen corto para feeds
            
            // Tipo de post
            $table->enum('post_type', [
                'discussion',           // Discusión general
                'question',             // Pregunta que busca respuesta
                'tutorial',             // Tutorial o guía
                'news',                 // Noticia
                'announcement',         // Anuncio importante
                'poll',                 // Encuesta
                'showcase',             // Mostrar proyecto/instalación
                'help',                 // Solicitud de ayuda
                'review',               // Reseña de producto/servicio
                'resource',             // Recurso útil (link, documento)
                'event',                // Evento
                'job',                  // Oferta de trabajo
                'marketplace',          // Compra/venta
                'case_study',           // Caso de estudio
                'research'              // Investigación
            ])->default('discussion');
            
            // Configuración del post
            $table->boolean('is_pinned')->default(false); // Fijado en la parte superior
            $table->boolean('is_locked')->default(false); // No se pueden añadir comentarios
            $table->boolean('is_featured')->default(false); // Destacado
            $table->boolean('is_announcement')->default(false); // Es un anuncio
            $table->boolean('is_nsfw')->default(false); // Contenido no apto para trabajo
            $table->boolean('is_spoiler')->default(false); // Contiene spoilers
            $table->boolean('requires_approval')->default(false); // Requiere aprobación
            $table->boolean('allow_comments')->default(true); // Permitir comentarios
            $table->boolean('notify_replies')->default(true); // Notificar respuestas al autor
            
            // Contenido multimedia
            $table->json('images')->nullable(); // URLs de imágenes
            $table->json('videos')->nullable(); // URLs de videos
            $table->json('attachments')->nullable(); // Archivos adjuntos
            $table->json('links')->nullable(); // Enlaces externos
            $table->string('thumbnail_url')->nullable(); // Miniatura del post
            
            // Métricas de engagement
            $table->integer('views_count')->default(0);
            $table->integer('unique_views_count')->default(0);
            $table->integer('upvotes_count')->default(0);
            $table->integer('downvotes_count')->default(0);
            $table->integer('score')->default(0); // upvotes - downvotes
            $table->integer('comments_count')->default(0);
            $table->integer('shares_count')->default(0);
            $table->integer('bookmarks_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->integer('reports_count')->default(0);
            
            // Métricas de calidad
            $table->decimal('quality_score', 8, 2)->default(100); // Score de calidad del contenido
            $table->decimal('helpfulness_score', 8, 2)->default(0); // Qué tan útil es el post
            $table->decimal('engagement_rate', 5, 2)->default(0); // Tasa de engagement
            $table->integer('read_time_seconds')->nullable(); // Tiempo estimado de lectura
            $table->decimal('completion_rate', 5, 2)->default(0); // % de usuarios que lo leen completo
            
            // Información de trending y algoritmo
            $table->decimal('trending_score', 10, 2)->default(0);
            $table->decimal('hot_score', 10, 2)->default(0); // Score para "hot" posts
            $table->decimal('relevance_score', 8, 2)->default(100);
            $table->integer('controversy_score')->default(0); // Nivel de controversia
            $table->timestamp('trending_until')->nullable();
            
            // Estados y moderación
            $table->enum('status', [
                'published',            // Publicado
                'draft',                // Borrador
                'pending',              // Pendiente de aprobación
                'approved',             // Aprobado por moderador
                'rejected',             // Rechazado
                'hidden',               // Oculto
                'deleted',              // Eliminado
                'archived',             // Archivado
                'spam'                  // Marcado como spam
            ])->default('published');
            
            $table->json('moderation_flags')->nullable(); // Flags de moderación
            $table->text('moderation_notes')->nullable();
            $table->foreignId('moderated_by')->nullable()->constrained('users');
            $table->timestamp('moderated_at')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Configuración específica por tipo
            $table->json('poll_options')->nullable(); // Para posts tipo poll
            $table->timestamp('poll_expires_at')->nullable();
            $table->boolean('poll_multiple_choice')->default(false);
            $table->json('event_details')->nullable(); // Para posts tipo event
            $table->timestamp('event_start_at')->nullable();
            $table->timestamp('event_end_at')->nullable();
            
            // Información de ubicación (para showcases, eventos)
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('location_name')->nullable();
            $table->string('location_region')->nullable();
            
            // Etiquetado y categorización
            $table->json('tags')->nullable(); // Etiquetas del post
            $table->json('mentioned_users')->nullable(); // Usuarios mencionados
            $table->json('related_posts')->nullable(); // Posts relacionados
            $table->string('language', 5)->default('es'); // Idioma del contenido
            
            // Información de edición
            $table->boolean('is_edited')->default(false);
            $table->timestamp('last_edited_at')->nullable();
            $table->foreignId('last_edited_by')->nullable()->constrained('users');
            $table->text('edit_reason')->nullable();
            $table->integer('edit_count')->default(0);
            
            // Información de actividad
            $table->timestamp('last_activity_at')->nullable(); // Última actividad (comentario, voto, etc.)
            $table->timestamp('last_comment_at')->nullable();
            $table->foreignId('last_comment_by')->nullable()->constrained('users');
            $table->timestamp('bumped_at')->nullable(); // Para mantener posts activos arriba
            
            // SEO y metadatos
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('structured_data')->nullable(); // Datos estructurados para SEO
            
            // Configuración de algoritmo personalizada
            $table->json('algorithm_data')->nullable(); // Datos para algoritmo de ranking
            $table->decimal('author_reputation_at_time', 8, 2)->default(0); // Reputación del autor al momento del post
            
            // Información de origen
            $table->string('source')->nullable(); // web, mobile_app, api
            $table->string('user_agent')->nullable();
            $table->json('creation_metadata')->nullable();
            
            $table->timestamps();
            
            // Índices para optimización
            $table->index(['topic_id', 'status', 'created_at']);
            $table->index(['topic_id', 'is_pinned', 'score']);
            $table->index(['user_id', 'status', 'created_at']);
            $table->index(['post_type', 'status', 'created_at']);
            $table->index(['status', 'trending_score']);
            $table->index(['status', 'hot_score']);
            $table->index(['is_featured', 'score']);
            $table->index(['last_activity_at', 'status']);
            $table->index(['views_count', 'status']);
            $table->index(['score', 'created_at']);
            $table->index(['quality_score', 'status']);
            $table->fullText(['title', 'body', 'excerpt']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topic_posts');
    }
};
