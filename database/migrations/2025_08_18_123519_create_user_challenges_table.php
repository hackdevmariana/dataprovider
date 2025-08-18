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
        Schema::create('user_challenges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('challenge_id')->constrained()->cascadeOnDelete();
            $table->enum('status', [
                'registered',       // Registrado
                'active',          // Participando activamente
                'completed',       // Completado
                'failed',          // Fallido
                'abandoned'        // Abandonado
            ])->default('registered');
            $table->datetime('joined_at');
            $table->datetime('completed_at')->nullable();
            $table->json('progress')->nullable(); // Progreso en el reto
            $table->decimal('current_value', 15, 4)->default(0); // Valor actual alcanzado
            $table->integer('ranking_position')->nullable(); // Posición en el ranking
            $table->integer('points_earned')->default(0);
            $table->decimal('reward_earned', 10, 2)->default(0); // Recompensa económica
            $table->json('achievements_unlocked')->nullable(); // Logros desbloqueados
            $table->text('notes')->nullable(); // Notas del usuario
            $table->boolean('is_team_leader')->default(false); // Para retos en equipo
            $table->foreignId('team_id')->nullable(); // Para retos en equipo
            $table->timestamps();
            
            $table->unique(['user_id', 'challenge_id']); // Un usuario por reto
            $table->index(['challenge_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['ranking_position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_challenges');
    }
};