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
        Schema::create('social_comparisons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('comparison_type'); // energy_savings, carbon_reduction, community_participation, etc.
            $table->string('period'); // daily, weekly, monthly, yearly, all_time
            $table->string('scope'); // personal, cooperative, regional, national, global
            $table->unsignedBigInteger('scope_id')->nullable(); // ID of the scope entity
            $table->decimal('user_value', 15, 4); // User's value for comparison
            $table->string('unit'); // kWh, kg_co2, points, etc.
            $table->decimal('average_value', 15, 4)->nullable(); // Average value in comparison group
            $table->decimal('median_value', 15, 4)->nullable(); // Median value in comparison group
            $table->decimal('best_value', 15, 4)->nullable(); // Best value in comparison group
            $table->integer('user_rank')->nullable(); // User's rank in comparison
            $table->integer('total_participants'); // Total number of participants in comparison
            $table->decimal('percentile', 5, 2)->nullable(); // User's percentile (0-100)
            $table->json('breakdown')->nullable(); // Detailed breakdown of the comparison
            $table->json('metadata')->nullable(); // Additional comparison data
            $table->boolean('is_public')->default(true); // Whether comparison is visible to others
            $table->date('comparison_date'); // Date this comparison represents
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'comparison_type']);
            $table->index(['user_id', 'period']);
            $table->index(['comparison_type', 'scope']);
            $table->index(['comparison_date', 'comparison_type']);
            $table->index(['scope', 'scope_id']);
            $table->index(['user_rank', 'total_participants']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_comparisons');
    }
};