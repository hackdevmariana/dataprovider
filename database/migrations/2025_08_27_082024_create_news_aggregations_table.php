<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_aggregations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_id')->constrained('news_sources')->onDelete('cascade');
            $table->foreignId('article_id')->constrained('news_articles')->onDelete('cascade');
            $table->timestamp('aggregated_at');
            $table->string('processing_status')->default('pending');
            $table->boolean('duplicate_check')->default(false);
            $table->decimal('quality_score', 3, 2)->nullable();
            $table->json('processing_metadata')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->text('processing_notes')->nullable();
            $table->timestamps();

            $table->unique(['source_id', 'article_id']);
            $table->index(['processing_status']);
            $table->index(['aggregated_at']);
            $table->index(['quality_score']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_aggregations');
    }
};
