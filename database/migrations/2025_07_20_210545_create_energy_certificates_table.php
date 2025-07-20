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
        Schema::create('energy_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('building_type'); // e.g., 'residential', 'office'
            $table->string('energy_rating'); // e.g., A, B, C...
            $table->decimal('annual_energy_consumption_kwh', 10, 2);
            $table->decimal('annual_emissions_kg_co2e', 10, 2);
            $table->foreignId('zone_climate_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('energy_certificates');
    }
};
