<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saint_id')->constrained('catholic_saints')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('prayer_text')->nullable();
            $table->integer('novena_days')->nullable();
            $table->json('special_intentions')->nullable();
            $table->json('miracles')->nullable();
            $table->string('origin')->nullable();
            $table->string('popularity_level')->default('moderate');
            $table->json('practices')->nullable();
            $table->json('traditions')->nullable();
            $table->boolean('is_approved')->default(true);
            $table->timestamps();

            $table->index(['saint_id', 'popularity_level']);
            $table->index(['is_approved']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devotions');
    }
};
