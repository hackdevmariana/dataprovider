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
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('topic_comments')->onDelete('cascade');
            $table->text('content');
            
            // Contenido enriquecido
            $table->json('images')->nullable();
            $table->json('attachments')->nullable();
            $table->json('links')->nullable();
            
            // Moderación
            $table->enum('status', ['published', 'pending', 'rejected', 'edited'])->default('published');
            $table->text('edit_reason')->nullable();
            $table->timestamp('edited_at')->nullable();
            
            // Métricas
            $table->integer('likes_count')->default(0);
            $table->integer('replies_count')->default(0);
            $table->boolean('is_solution')->default(false); // Marcado como solución
            $table->boolean('is_pinned')->default(false);
            
            $table->timestamps();
            
            $table->index(['topic_post_id', 'status', 'created_at']);
            $table->index(['author_id', 'status']);
            $table->index(['parent_id', 'created_at']);
            $table->index('is_solution');
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

