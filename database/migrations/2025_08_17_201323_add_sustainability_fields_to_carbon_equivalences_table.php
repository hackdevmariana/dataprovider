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
        Schema::table('carbon_equivalences', function (Blueprint $table) {
            // Renombrar la columna existente kg_co2e a co2_kg_equivalent
            $table->renameColumn('kg_co2e', 'co2_kg_equivalent');
            
            // Añadir nuevas columnas necesarias
            $table->string('name')->after('id');
            $table->string('slug')->after('name')->unique();
            $table->string('category')->after('co2_kg_equivalent')->default('other');
            $table->string('unit')->after('category')->default('kg');
            $table->decimal('efficiency_ratio', 8, 4)->after('unit')->nullable();
            $table->decimal('loss_factor', 8, 4)->after('efficiency_ratio')->nullable();
            $table->string('calculation_method')->after('loss_factor')->nullable();
            $table->json('calculation_params')->after('calculation_method')->nullable();
            $table->string('source')->after('calculation_params')->default('manual');
            $table->string('source_url')->after('source')->nullable();
            $table->boolean('is_verified')->after('source_url')->default(false);
            $table->string('verification_entity')->after('is_verified')->nullable();
            $table->timestamp('last_updated')->after('verification_entity')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carbon_equivalences', function (Blueprint $table) {
            // Renombrar de vuelta
            $table->renameColumn('co2_kg_equivalent', 'kg_co2e');
            
            // Eliminar las nuevas columnas
            $table->dropColumn([
                'name',
                'slug',
                'category',
                'unit',
                'efficiency_ratio',
                'loss_factor',
                'calculation_method',
                'calculation_params',
                'source',
                'source_url',
                'is_verified',
                'verification_entity',
                'last_updated'
            ]);
        });
    }
};