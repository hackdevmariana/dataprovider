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
        Schema::create('weather_and_solar_data', function (Blueprint $table) {
            $table->id();
            $table->dateTime('datetime');
            $table->string('location');
            $table->float('temperature')->nullable();
            $table->float('humidity')->nullable();
            $table->float('cloud_coverage')->nullable();
            $table->float('solar_irradiance')->nullable();
            $table->float('wind_speed')->nullable();
            $table->float('precipitation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_and_solar_data');
    }
};
