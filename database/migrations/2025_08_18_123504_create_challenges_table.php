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
        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('instructions')->nullable(); // Instrucciones detalladas
            $table->string('icon')->nullable();
            $table->string('banner_color', 7)->default('#FCD34D'); // Color del banner
            $table->enum('type', [
                'individual',       // Reto individual
                'community',        // Reto comunitario
                'cooperative'       // Reto entre cooperativas
            ])->default('individual');
            $table->enum('category', [
                'energy_saving',    // Ahorro energético
                'solar_production', // Producción solar
                'cooperation',      // Cooperativismo
                'sustainability',   // Sostenibilidad
                'education'         // Educativo
            ]);
            $table->enum('difficulty', [
                'easy',
                'medium',
                'hard',
                'expert'
            ])->default('easy');
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->json('goals')->nullable(); // Objetivos del reto (ej: {"energy_saved": 500, "unit": "kWh"})
            $table->json('rewards')->nullable(); // Recompensas (puntos, logros, etc.)
            $table->integer('max_participants')->nullable(); // Límite de participantes
            $table->integer('min_participants')->default(1); // Mínimo de participantes
            $table->decimal('entry_fee', 8, 2)->default(0); // Cuota de entrada
            $table->decimal('prize_pool', 10, 2)->default(0); // Premio acumulado
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false); // Reto destacado
            $table->boolean('auto_join')->default(false); // Auto-inscripción
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['type', 'is_active']);
            $table->index(['category', 'is_active']);
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenges');
    }
};