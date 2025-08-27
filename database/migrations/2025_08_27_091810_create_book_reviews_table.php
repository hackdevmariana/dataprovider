<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('rating');
            $table->text('review_text')->nullable();
            $table->string('title')->nullable();
            $table->boolean('is_verified_purchase')->default(false);
            $table->boolean('is_helpful')->default(false);
            $table->integer('helpful_votes')->default(0);
            $table->integer('not_helpful_votes')->default(0);
            $table->json('pros')->nullable();
            $table->json('cons')->nullable();
            $table->boolean('is_public')->default(true);
            $table->timestamps();

            $table->unique(['book_id', 'user_id']);
            $table->index(['rating', 'is_helpful']);
            $table->index(['is_verified_purchase']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_reviews');
    }
};
