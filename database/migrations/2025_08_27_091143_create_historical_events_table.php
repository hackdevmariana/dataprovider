<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historical_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->date('event_date');
            $table->string('era')->nullable();
            $table->string('category');
            $table->string('location')->nullable();
            $table->string('country')->nullable();
            $table->json('key_figures')->nullable();
            $table->json('consequences')->nullable();
            $table->string('significance_level')->default('moderate');
            $table->json('sources')->nullable();
            $table->boolean('is_verified')->default(true);
            $table->json('related_events')->nullable();
            $table->timestamps();

            $table->index(['event_date']);
            $table->index(['category', 'significance_level']);
            $table->index(['country']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historical_events');
    }
};
