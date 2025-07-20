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
        Schema::create('plant_species', function (Blueprint $table) {
            $table->id();
            $table->string('common_name');
            $table->string('scientific_name')->nullable();
            $table->decimal('co2_absorption_kg_per_year', 8, 2)->comment('Approx. annual CO₂ absorption');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plant_species');
    }
};
