<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('liturgical_calendars', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('liturgical_season');
            $table->string('feast_day');
            $table->foreignId('saint_id')->nullable()->constrained('catholic_saints')->onDelete('set null');
            $table->string('celebration_level')->default('memorial');
            $table->json('readings')->nullable();
            $table->json('prayers')->nullable();
            $table->json('traditions')->nullable();
            $table->string('color')->nullable();
            $table->text('description')->nullable();
            $table->json('special_observances')->nullable();
            $table->boolean('is_holiday')->default(false);
            $table->timestamps();

            $table->unique('date');
            $table->index(['liturgical_season']);
            $table->index(['celebration_level']);
            $table->index(['saint_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('liturgical_calendars');
    }
};
