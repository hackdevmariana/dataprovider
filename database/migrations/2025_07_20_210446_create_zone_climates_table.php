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
        Schema::create('zone_climates', function (Blueprint $table) {
            $table->id();
            $table->string('climate_zone'); // e.g., C1, B4, etc.
            $table->string('description')->nullable();
            $table->decimal('average_heating_demand', 8, 2)->nullable(); // optional
            $table->decimal('average_cooling_demand', 8, 2)->nullable(); // optional
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zone_climates');
    }
};
