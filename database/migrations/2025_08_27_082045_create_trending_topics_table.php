<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trending_topics', function (Blueprint $table) {
            $table->id();
            $table->string('topic');
            $table->decimal('trending_score', 5, 2)->default(0.00);
            $table->integer('mentions_count')->default(0);
            $table->decimal('growth_rate', 5, 2)->default(0.00);
            $table->string('geographic_spread')->default('local');
            $table->string('category')->nullable();
            $table->json('related_keywords')->nullable();
            $table->json('geographic_data')->nullable();
            $table->timestamp('peak_time')->nullable();
            $table->integer('peak_score')->nullable();
            $table->json('trend_analysis')->nullable();
            $table->boolean('is_breaking')->default(false);
            $table->timestamps();

            $table->unique('topic');
            $table->index(['trending_score']);
            $table->index(['category', 'trending_score']);
            $table->index(['is_breaking']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trending_topics');
    }
};
