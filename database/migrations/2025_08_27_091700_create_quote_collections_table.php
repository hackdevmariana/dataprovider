<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quote_collections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('theme')->nullable();
            $table->json('tags')->nullable();
            $table->integer('quotes_count')->default(0);
            $table->boolean('is_public')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('views_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->timestamps();

            $table->index(['created_by', 'is_public']);
            $table->index(['theme', 'is_featured']);
            $table->index(['views_count']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_collections');
    }
};
