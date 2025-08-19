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
        Schema::create('leaderboards', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Name of the leaderboard
            $table->string('type'); // energy_savings, reputation, contributions, etc.
            $table->string('period'); // daily, weekly, monthly, yearly, all_time
            $table->string('scope'); // global, cooperative, regional, topic
            $table->unsignedBigInteger('scope_id')->nullable(); // ID of the scope entity
            $table->json('criteria'); // Criteria for ranking
            $table->json('rules')->nullable(); // Leaderboard rules and calculations
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(true);
            $table->integer('max_positions')->default(100); // Maximum positions to track
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamp('last_calculated_at')->nullable();
            $table->json('current_rankings')->nullable(); // Current top rankings (cached)
            $table->json('metadata')->nullable(); // Additional leaderboard data
            $table->timestamps();

            // Indexes
            $table->index(['type', 'period']);
            $table->index(['scope', 'scope_id']);
            $table->index(['is_active', 'is_public']);
            $table->index(['start_date', 'end_date']);
            $table->index('last_calculated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaderboards');
    }
};