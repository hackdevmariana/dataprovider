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
            $table->enum('role', ['member', 'moderator', 'admin'])->default('member');
            $table->enum('status', ['active', 'banned', 'muted'])->default('active');
            
            // Configuraciones personales
            $table->boolean('notifications_enabled')->default(true);
            $table->boolean('email_notifications')->default(false);
            $table->json('notification_preferences')->nullable();
            
            // Métricas del usuario en el tema
            $table->integer('posts_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->integer('reputation_score')->default(0); // Reputación en este tema
            $table->timestamp('last_activity_at')->nullable();
            
            $table->timestamps();
            
            $table->unique(['topic_id', 'user_id']);
            $table->index(['topic_id', 'role']);
            $table->index(['user_id', 'status']);
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
