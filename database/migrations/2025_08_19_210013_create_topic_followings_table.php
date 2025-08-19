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
        Schema::create('topic_followings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('topic_id')->constrained()->onDelete('cascade');
            $table->enum('follow_type', ['following', 'watching', 'ignoring'])->default('following');
            $table->boolean('notifications_enabled')->default(true);
            $table->json('notification_preferences')->nullable(); // What types of notifications to receive
            $table->timestamp('followed_at')->useCurrent();
            $table->timestamp('last_visited_at')->nullable();
            $table->integer('visit_count')->default(0);
            $table->timestamps();

            // Unique constraint
            $table->unique(['user_id', 'topic_id'], 'user_topic_follow_unique');

            // Indexes
            $table->index(['user_id', 'follow_type']);
            $table->index(['topic_id', 'follow_type']);
            $table->index(['followed_at']);
            $table->index(['notifications_enabled', 'follow_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topic_followings');
    }
};