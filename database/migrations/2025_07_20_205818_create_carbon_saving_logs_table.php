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
        Schema::create('carbon_saving_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('cooperative_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('kw_installed', 8, 2); // Potencia instalada en kW
            $table->decimal('production_kwh', 10, 2)->nullable(); // Producción en kWh
            $table->decimal('co2_saved_kg', 10, 2)->nullable(); // CO2 ahorrado en kg
            $table->date('date_range_start'); // Fecha de inicio del período
            $table->date('date_range_end')->nullable(); // Fecha de fin del período
            $table->string('estimation_source')->nullable(); // Fuente de la estimación
            $table->string('carbon_saving_method')->nullable(); // Método de ahorro de carbono
            $table->boolean('created_by_system')->default(false); // Si fue creado por el sistema
            $table->json('metadata')->nullable(); // Metadatos adicionales
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index(['user_id', 'date_range_start']);
            $table->index(['cooperative_id', 'date_range_start']);
            $table->index(['date_range_start', 'date_range_end']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carbon_saving_logs');
    }
};
