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
        Schema::create('carbon_saving_requests', function (Blueprint $table) {
            $table->id();
            $table->decimal('installation_power_kw', 10, 2); // Potencia de instalación en kW
            $table->decimal('production_kwh', 10, 2)->nullable(); // Producción en kWh (opcional)
            $table->foreignId('province_id')->nullable()->constrained('provinces')->onDelete('set null');
            $table->foreignId('municipality_id')->nullable()->constrained('municipalities')->onDelete('set null');
            $table->enum('period', ['annual', 'monthly', 'daily']); // Período de cálculo
            $table->date('start_date')->nullable(); // Fecha de inicio (opcional)
            $table->date('end_date')->nullable(); // Fecha de fin (opcional)
            $table->decimal('efficiency_ratio', 5, 4)->nullable(); // Ratio de eficiencia (opcional, 0.0000 a 1.0000)
            $table->decimal('loss_factor', 5, 4)->nullable(); // Factor de pérdidas (opcional, 0.0000 a 1.0000)
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index(['province_id', 'municipality_id']);
            $table->index(['period', 'start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carbon_saving_requests');
    }
};
