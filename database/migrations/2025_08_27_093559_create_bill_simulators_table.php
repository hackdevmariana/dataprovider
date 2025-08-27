<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bill_simulators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('energy_type')->default('electricity');
            $table->string('zone')->default('peninsula');
            $table->decimal('monthly_consumption', 8, 2);
            $table->string('consumption_unit')->default('kWh');
            $table->string('contract_type')->default('fixed');
            $table->decimal('power_contracted', 6, 2)->nullable();
            $table->json('tariff_details')->nullable();
            $table->decimal('estimated_monthly_bill', 8, 2);
            $table->decimal('estimated_annual_bill', 8, 2);
            $table->json('breakdown')->nullable();
            $table->timestamp('simulation_date');
            $table->json('assumptions')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'energy_type']);
            $table->index(['monthly_consumption']);
            $table->index(['estimated_monthly_bill']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bill_simulators');
    }
};
