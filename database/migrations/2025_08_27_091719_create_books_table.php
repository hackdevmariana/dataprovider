<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('original_title')->nullable();
            $table->text('synopsis')->nullable();
            $table->string('author');
            $table->string('isbn')->nullable();
            $table->string('publisher')->nullable();
            $table->date('publication_date')->nullable();
            $table->string('language')->default('es');
            $table->string('genre')->nullable();
            $table->integer('pages')->nullable();
            $table->string('format')->default('paperback');
            $table->decimal('rating', 3, 2)->nullable();
            $table->integer('ratings_count')->default(0);
            $table->integer('reviews_count')->default(0);
            $table->json('awards')->nullable();
            $table->json('tags')->nullable();
            $table->string('cover_image')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            $table->index(['title', 'author']);
            $table->index(['genre', 'rating']);
            $table->index(['publication_date']);
            $table->index(['isbn']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
