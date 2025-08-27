<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->text('text');
            $table->string('author')->nullable();
            $table->string('source')->nullable();
            $table->string('language')->default('es');
            $table->string('category')->nullable();
            $table->json('tags')->nullable();
            $table->string('mood')->nullable();
            $table->string('difficulty_level')->default('easy');
            $table->integer('word_count');
            $table->integer('character_count');
            $table->decimal('popularity_score', 5, 2)->default(0.00);
            $table->integer('usage_count')->default(0);
            $table->json('translations')->nullable();
            $table->boolean('is_verified')->default(true);
            $table->timestamps();

            $table->index(['category', 'popularity_score']);
            $table->index(['language', 'mood']);
            $table->index(['is_verified']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
