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
        Schema::create('cooperative_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cooperative_id')->constrained()->onDelete('cascade');
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->enum('post_type', ['announcement', 'news', 'event', 'discussion', 'update'])->default('announcement');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->enum('visibility', ['public', 'members_only', 'board_only'])->default('members_only');
            $table->json('attachments')->nullable(); // File attachments
            $table->json('metadata')->nullable(); // Additional post data
            $table->boolean('comments_enabled')->default(true);
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->integer('views_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('pinned_until')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['cooperative_id', 'status']);
            $table->index(['cooperative_id', 'post_type']);
            $table->index(['author_id', 'status']);
            $table->index(['status', 'published_at']);
            $table->index(['visibility', 'published_at']);
            $table->index(['is_pinned', 'pinned_until']);
            $table->index(['is_featured', 'published_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cooperative_posts');
    }
};