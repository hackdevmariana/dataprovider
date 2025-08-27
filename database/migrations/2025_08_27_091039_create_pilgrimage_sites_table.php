<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pilgrimage_sites', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('saint_id')->nullable()->constrained('catholic_saints')->onDelete('set null');
            $table->string('location');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('country');
            $table->string('region')->nullable();
            $table->string('city')->nullable();
            $table->string('type')->default('shrine');
            $table->json('facilities')->nullable();
            $table->json('accommodation')->nullable();
            $table->json('transportation')->nullable();
            $table->string('best_time_to_visit')->nullable();
            $table->integer('annual_pilgrims')->nullable();
            $table->json('special_dates')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['type', 'country']);
            $table->index(['saint_id']);
            $table->index(['is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pilgrimage_sites');
    }
};
