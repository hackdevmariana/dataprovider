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
        Schema::create('topic_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_post_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('topic_comments')->onDelete('cascade');
            
            // Contenido del comentario
            $table->longText('body'); // Contenido del comentario (Markdown)
            $table->text('excerpt')->nullable(); // Resumen para notificaciones
            
            // Jerarquía de comentarios
            $table->integer('depth')->default(0); // Profundidad en la jerarquía (0 = raíz)
            $table->string('thread_path')->nullable(); // Ruta completa del hilo (ej: "1/5/12")
            $table->integer('sort_order')->default(0); // Orden de visualización
            $table->integer('children_count')->default(0); // Número de respuestas directas
            $table->integer('descendants_count')->default(0); // Número total de descendientes
            
            // Tipo de comentario
            $table->enum('comment_type', [
                'comment',              // Comentario regular
                'answer',               // Respuesta a pregunta
                'solution',             // Solución marcada como correcta
                'clarification',        // Aclaración del autor original
                'moderator_note',       // Nota de moderador
                'bot_response',         // Respuesta automática
                'system_message'        // Mensaje del sistema
            ])->default('comment');
            
            // Estados especiales
            $table->boolean('is_best_answer')->default(false); // Marcado como mejor respuesta
            $table->boolean('is_author_reply')->default(false); // Respuesta del autor del post
            $table->boolean('is_moderator_reply')->default(false); // Respuesta de moderador
            $table->boolean('is_pinned')->default(false); // Comentario fijado
            $table->boolean('is_highlighted')->default(false); // Comentario destacado
            $table->boolean('is_edited')->default(false); // Ha sido editado
            $table->boolean('is_deleted')->default(false); // Eliminado (soft delete)
            
            // Métricas de engagement
            $table->integer('upvotes_count')->default(0);
            $table->integer('downvotes_count')->default(0);
            $table->integer('score')->default(0); // upvotes - downvotes
            $table->integer('replies_count')->default(0); // Respuestas directas
            $table->integer('likes_count')->default(0);
            $table->integer('reports_count')->default(0);
            $table->integer('helpful_votes')->default(0); // Votos de "útil"
            $table->integer('not_helpful_votes')->default(0); // Votos de "no útil"
            
            // Métricas de calidad
            $table->decimal('quality_score', 8, 2)->default(100);
            $table->decimal('helpfulness_score', 8, 2)->default(0);
            $table->decimal('relevance_score', 8, 2)->default(100);
            $table->integer('read_time_seconds')->nullable();
            $table->decimal('engagement_rate', 5, 2)->default(0);
            
            // Contenido multimedia
            $table->json('images')->nullable(); // URLs de imágenes
            $table->json('attachments')->nullable(); // Archivos adjuntos
            $table->json('links')->nullable(); // Enlaces mencionados
            $table->json('code_snippets')->nullable(); // Fragmentos de código
            
            // Información de moderación
            $table->enum('status', [
                'published',            // Publicado
                'pending',              // Pendiente de aprobación
                'approved',             // Aprobado
                'hidden',               // Oculto por moderador
                'deleted',              // Eliminado
                'spam',                 // Marcado como spam
                'flagged'               // Reportado
            ])->default('published');
            
            $table->json('moderation_flags')->nullable();
            $table->text('moderation_notes')->nullable();
            $table->foreignId('moderated_by')->nullable()->constrained('users');
            $table->timestamp('moderated_at')->nullable();
            
            // Información de edición
            $table->timestamp('last_edited_at')->nullable();
            $table->foreignId('last_edited_by')->nullable()->constrained('users');
            $table->text('edit_reason')->nullable();
            $table->integer('edit_count')->default(0);
            $table->json('edit_history')->nullable(); // Historial de ediciones
            
            // Etiquetado y menciones
            $table->json('mentioned_users')->nullable(); // Usuarios mencionados
            $table->json('tags')->nullable(); // Etiquetas específicas
            $table->string('language', 5)->default('es'); // Idioma del comentario
            
            // Información de contexto
            $table->text('quote_text')->nullable(); // Texto citado de otro comentario
            $table->foreignId('quoted_comment_id')->nullable()->constrained('topic_comments');
            $table->json('context_data')->nullable(); // Datos de contexto adicionales
            
            // Configuración de notificaciones
            $table->boolean('notify_parent_author')->default(true); // Notificar al autor del comentario padre
            $table->boolean('notify_post_author')->default(true); // Notificar al autor del post
            $table->boolean('notify_followers')->default(true); // Notificar a seguidores
            
            // Información de actividad
            $table->timestamp('last_activity_at')->nullable(); // Última actividad en este comentario
            $table->integer('views_count')->default(0); // Visualizaciones del comentario
            $table->integer('unique_views_count')->default(0);
            
            // Información de algoritmo
            $table->decimal('ranking_score', 10, 2)->default(0); // Score para ordenamiento
            $table->decimal('controversy_score', 8, 2)->default(0); // Nivel de controversia
            $table->timestamp('hot_until')->nullable(); // Hasta cuándo se considera "hot"
            
            // Información técnica
            $table->string('source')->nullable(); // web, mobile_app, api
            $table->string('user_agent')->nullable();
            $table->json('creation_metadata')->nullable();
            $table->decimal('author_reputation_at_time', 8, 2)->default(0);
            
            // Información de threading avanzado
            $table->integer('root_comment_id')->nullable(); // ID del comentario raíz del hilo
            $table->json('thread_participants')->nullable(); // Participantes en el hilo
            $table->boolean('breaks_thread')->default(false); // Si rompe el hilo de conversación
            $table->timestamp('thread_last_activity')->nullable();
            
            // Configuración de visualización
            $table->boolean('collapsed_by_default')->default(false); // Colapsado por defecto
            $table->boolean('show_score')->default(true); // Mostrar puntuación
            $table->boolean('allow_replies')->default(true); // Permitir respuestas
            $table->integer('max_reply_depth')->nullable(); // Profundidad máxima de respuestas
            
            $table->timestamps();
            
            // Índices para optimización
            $table->index(['topic_post_id', 'status', 'created_at']);
            $table->index(['topic_post_id', 'parent_id', 'sort_order']);
            $table->index(['user_id', 'status', 'created_at']);
            $table->index(['parent_id', 'score', 'created_at']);
            $table->index(['status', 'score', 'created_at']);
            $table->index(['is_best_answer', 'score']);
            $table->index(['thread_path', 'sort_order']);
            $table->index(['root_comment_id', 'depth']);
            $table->index(['last_activity_at', 'status']);
            $table->index(['ranking_score', 'created_at']);
            $table->index(['comment_type', 'status']);
            $table->fullText(['body', 'excerpt']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topic_comments');
    }
};
