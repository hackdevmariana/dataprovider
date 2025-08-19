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
        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('badge_type'); // bronze, silver, gold, platinum, diamond
            $table->string('category'); // energy_saver, community_leader, expert_contributor, etc.
            $table->string('name');
            $table->text('description');
            $table->string('icon_url')->nullable();
            $table->string('color', 7)->default('#3B82F6'); // Hex color code
            $table->json('criteria'); // Criteria that was met to earn the badge
            $table->json('metadata')->nullable(); // Additional badge-specific data
            $table->integer('points_awarded')->default(0); // Points given for earning this badge
            $table->boolean('is_public')->default(true); // Whether badge is visible to others
            $table->boolean('is_featured')->default(false); // Featured on profile
            $table->timestamp('earned_at')->useCurrent();
            $table->timestamp('expires_at')->nullable(); // Some badges might expire
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'badge_type']);
            $table->index(['user_id', 'category']);
            $table->index(['badge_type', 'category']);
            $table->index('earned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_badges');
    }
};