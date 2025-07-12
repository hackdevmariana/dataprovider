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
        Schema::create('works', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('type', ['book', 'movie', 'tv_show', 'theatre_play', 'article']);
            $table->text('description')->nullable();
            $table->year('release_year')->nullable();
            $table->foreignId('person_id')->nullable()->constrained()->nullOnDelete(); // autor, director, etc.
            $table->string('genre')->nullable();
            $table->foreignId('language_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('link_id')->nullable()->constrained('links')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('works');
    }
};
