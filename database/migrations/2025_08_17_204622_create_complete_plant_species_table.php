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
        Schema::dropIfExists('plant_species');
        
        Schema::create('plant_species', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('scientific_name')->nullable();
            $table->string('family')->nullable();
            $table->decimal('co2_absorption_kg_per_year', 8, 2);
            $table->decimal('co2_absorption_min', 8, 2)->nullable();
            $table->decimal('co2_absorption_max', 8, 2)->nullable();
            $table->text('description')->nullable();
            $table->enum('plant_type', ['tree', 'shrub', 'herb', 'grass', 'vine', 'palm', 'conifer', 'fern', 'succulent', 'bamboo'])->default('tree');
            $table->enum('size_category', ['small', 'medium', 'large', 'giant'])->default('medium');
            $table->decimal('height_min', 5, 2)->nullable();
            $table->decimal('height_max', 5, 2)->nullable();
            $table->integer('lifespan_years')->nullable();
            $table->integer('growth_rate_cm_year')->nullable();
            $table->json('climate_zones')->nullable();
            $table->string('soil_types')->nullable();
            $table->decimal('water_needs_mm_year', 8, 2)->nullable();
            $table->boolean('drought_resistant')->default(false);
            $table->boolean('frost_resistant')->default(false);
            $table->boolean('is_endemic')->default(false);
            $table->boolean('is_invasive')->default(false);
            $table->boolean('suitable_for_reforestation')->default(true);
            $table->boolean('suitable_for_urban')->default(false);
            $table->string('flowering_season')->nullable();
            $table->string('fruit_season')->nullable();
            $table->boolean('provides_food')->default(false);
            $table->boolean('provides_timber')->default(false);
            $table->boolean('medicinal_use')->default(false);
            $table->decimal('planting_cost_eur', 8, 2)->nullable();
            $table->decimal('maintenance_cost_eur_year', 8, 2)->nullable();
            $table->integer('survival_rate_percent')->nullable();
            $table->foreignId('image_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('native_region_id')->nullable()->constrained('regions')->nullOnDelete();
            $table->string('source')->default('manual');
            $table->string('source_url')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->string('verification_entity')->nullable();
            $table->timestamps();
            
            $table->index(['plant_type', 'co2_absorption_kg_per_year'], 'plant_species_type_co2_idx');
            $table->index(['suitable_for_reforestation', 'co2_absorption_kg_per_year'], 'plant_species_reforestation_co2_idx');
            $table->index(['is_verified', 'co2_absorption_kg_per_year'], 'plant_species_verified_co2_idx');
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