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
        Schema::table('user_generated_contents', function (Blueprint $table) {
            // Cambiar 'type' por 'content_type' para coincidir con el modelo
            $table->renameColumn('type', 'content_type');
            
            // Agregar campos faltantes del modelo
            $table->string('title')->nullable()->after('content');
            $table->text('excerpt')->nullable()->after('title');
            $table->string('language')->default('es')->after('excerpt');
            $table->enum('visibility', ['public', 'private', 'unlisted'])->default('public')->after('status');
            $table->unsignedBigInteger('parent_id')->nullable()->after('visibility');
            $table->decimal('rating', 2, 1)->nullable()->after('parent_id');
            $table->json('metadata')->nullable()->after('rating');
            $table->json('media_attachments')->nullable()->after('metadata');
            $table->string('user_name')->nullable()->after('media_attachments');
            $table->string('user_email')->nullable()->after('user_name');
            $table->string('user_ip')->nullable()->after('user_email');
            $table->text('user_agent')->nullable()->after('user_ip');
            $table->boolean('is_anonymous')->default(false)->after('user_agent');
            $table->boolean('is_verified')->default(false)->after('is_anonymous');
            $table->boolean('is_featured')->default(false)->after('is_verified');
            $table->boolean('is_spam')->default(false)->after('is_featured');
            $table->boolean('needs_moderation')->default(true)->after('is_spam');
            $table->unsignedInteger('likes_count')->default(0)->after('needs_moderation');
            $table->unsignedInteger('dislikes_count')->default(0)->after('likes_count');
            $table->unsignedInteger('replies_count')->default(0)->after('dislikes_count');
            $table->unsignedInteger('reports_count')->default(0)->after('replies_count');
            $table->decimal('sentiment_score', 3, 2)->nullable()->after('reports_count');
            $table->string('sentiment_label')->nullable()->after('sentiment_score');
            $table->json('moderation_notes')->nullable()->after('sentiment_label');
            $table->json('auto_tags')->nullable()->after('moderation_notes');
            $table->unsignedBigInteger('moderator_id')->nullable()->after('auto_tags');
            $table->string('location_name')->nullable()->after('moderator_id');
            $table->decimal('latitude', 10, 8)->nullable()->after('location_name');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->timestamp('published_at')->nullable()->after('longitude');
            $table->timestamp('moderated_at')->nullable()->after('published_at');
            $table->timestamp('featured_until')->nullable()->after('moderated_at');
            
            // Agregar índices para mejorar el rendimiento (solo si no existen)
            if (!Schema::hasIndex('user_generated_contents', 'user_generated_contents_content_type_status_index')) {
                $table->index(['content_type', 'status']);
            }
            if (!Schema::hasIndex('user_generated_contents', 'user_generated_contents_user_id_created_at_index')) {
                $table->index(['user_id', 'created_at']);
            }
            // El índice related_type_related_id ya existe por morphs()
            if (!Schema::hasIndex('user_generated_contents', 'user_generated_contents_parent_id_index')) {
                $table->index(['parent_id']);
            }
            if (!Schema::hasIndex('user_generated_contents', 'user_generated_contents_is_featured_featured_until_index')) {
                $table->index(['is_featured', 'featured_until']);
            }
            if (!Schema::hasIndex('user_generated_contents', 'user_generated_contents_needs_moderation_status_index')) {
                $table->index(['needs_moderation', 'status']);
            }
            if (!Schema::hasIndex('user_generated_contents', 'user_generated_contents_sentiment_score_index')) {
                $table->index(['sentiment_score']);
            }
            if (!Schema::hasIndex('user_generated_contents', 'user_generated_contents_rating_index')) {
                $table->index(['rating']);
            }
            
            // Agregar foreign keys
            $table->foreign('parent_id')->references('id')->on('user_generated_contents')->onDelete('cascade');
            $table->foreign('moderator_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_generated_contents', function (Blueprint $table) {
            // Revertir cambios
            $table->renameColumn('content_type', 'type');
            
            // Eliminar campos agregados
            $table->dropColumn([
                'title', 'excerpt', 'language', 'visibility', 'parent_id', 'rating',
                'metadata', 'media_attachments', 'user_name', 'user_email', 'user_ip',
                'user_agent', 'is_anonymous', 'is_verified', 'is_featured', 'is_spam',
                'needs_moderation', 'likes_count', 'dislikes_count', 'replies_count',
                'reports_count', 'sentiment_score', 'sentiment_label', 'moderation_notes',
                'auto_tags', 'moderator_id', 'location_name', 'latitude', 'longitude',
                'published_at', 'moderated_at', 'featured_until'
            ]);
            
            // Eliminar índices
            $table->dropIndex(['content_type', 'status']);
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['related_type', 'related_id']);
            $table->dropIndex(['parent_id']);
            $table->dropIndex(['is_featured', 'featured_until']);
            $table->dropIndex(['needs_moderation', 'status']);
            $table->dropIndex(['sentiment_score']);
            $table->dropIndex(['rating']);
            
            // Eliminar foreign keys
            $table->dropForeign(['parent_id']);
            $table->dropForeign(['moderator_id']);
        });
    }
};
