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
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->enum('type', [
                'discussion',     // Discusión general
                'question',       // Pregunta
                'tutorial',       // Tutorial/Guía
                'news',          // Noticia
                'poll',          // Encuesta
                'showcase',      // Mostrar proyecto
                'help',          // Pedir ayuda
                'announcement'   // Anuncio
            ])->default('discussion');
            
            // Contenido enriquecido
            $table->json('images')->nullable(); // URLs de imágenes
            $table->json('attachments')->nullable(); // Documentos adjuntos
            $table->json('links')->nullable(); // Enlaces externos
            $table->json('poll_data')->nullable(); // Datos de encuesta si type=poll
            
            // Etiquetas y clasificación
            $table->json('tags')->nullable(); // Tags específicos del post
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced'])->nullable();
            $table->decimal('estimated_cost', 10, 2)->nullable(); // Coste estimado si aplica
            $table->string('location')->nullable(); // Ubicación si es relevante
            
            // Moderación y estado
            $table->enum('status', ['published', 'pending', 'rejected', 'archived'])->default('published');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Métricas de engagement
            $table->integer('views_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->integer('shares_count')->default(0);
            $table->integer('bookmarks_count')->default(0);
            $table->decimal('engagement_score', 8, 2)->default(0);
            
            // Configuración
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false); // No permite más comentarios
            $table->boolean('is_featured')->default(false);
            $table->boolean('allow_comments')->default(true);
            $table->boolean('notify_on_comment')->default(true);
            
            $table->timestamps();
            
            $table->index(['topic_id', 'status', 'created_at']);
            $table->index(['author_id', 'status']);
            $table->index(['type', 'status']);
            $table->index(['engagement_score', 'created_at']);
            $table->index('is_pinned');
            $table->fullText(['title', 'content']);
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

