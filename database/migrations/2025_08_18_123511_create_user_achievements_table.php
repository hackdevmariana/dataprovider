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
        Schema::create('user_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('achievement_id')->constrained()->cascadeOnDelete();
            $table->integer('progress')->default(0); // Progreso hacia el logro
            $table->integer('level')->default(1); // Nivel para logros progresivos
            $table->boolean('is_completed')->default(false);
            $table->datetime('completed_at')->nullable();
            $table->json('metadata')->nullable(); // Datos adicionales del logro
            $table->decimal('value_achieved', 15, 4)->nullable(); // Valor alcanzado
            $table->integer('points_earned')->default(0); // Puntos ganados
            $table->boolean('is_notified')->default(false); // Si se notificó al usuario
            $table->timestamps();
            
            $table->unique(['user_id', 'achievement_id', 'level']); // Un logro por nivel por usuario
            $table->index(['user_id', 'is_completed']);
            $table->index(['achievement_id', 'is_completed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_achievements');
    }
};