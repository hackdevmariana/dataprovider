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
        Schema::create('reputation_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Detalles de la transacción
            $table->enum('action_type', [
                // Acciones positivas
                'answer_accepted',      // +15 - Respuesta aceptada como solución
                'answer_upvoted',       // +10 - Respuesta votada positivamente  
                'question_upvoted',     // +5 - Pregunta votada positivamente
                'helpful_comment',      // +2 - Comentario marcado como útil
                'tutorial_featured',    // +50 - Tutorial destacado por moderadores
                'project_completed',    // +100 - Proyecto colaborativo completado
                'expert_verification',  // +500 - Verificación como experto profesional
                'community_award',      // +200 - Premio de la comunidad
                'first_answer',         // +1 - Primera respuesta en un tema
                'consistency_bonus',    // +10 - Bonus por actividad consistente
                
                // Acciones negativas
                'answer_downvoted',     // -2 - Respuesta votada negativamente
                'question_downvoted',   // -2 - Pregunta votada negativamente
                'spam_detected',        // -100 - Contenido marcado como spam
                'rule_violation',       // -50 - Violación de reglas comunitarias
                'answer_deleted',       // -15 - Respuesta eliminada por moderadores
                'reputation_reversal',  // Variable - Reversión por votos fraudulentos
                
                // Acciones neutras/administrativas
                'daily_login',          // +1 - Login diario (máximo 30/mes)
                'profile_completed',    // +10 - Completar perfil profesional
                'bounty_awarded',       // Variable - Recompensa por respuesta excepcional
                'seasonal_bonus'        // Variable - Bonificaciones estacionales
            ]);
            
            $table->integer('reputation_change'); // Cambio en reputación (+ o -)
            $table->string('category')->nullable(); // Categoría energética específica
            $table->foreignId('topic_id')->nullable()->constrained(); // Tema específico si aplica
            
            // Contexto de la acción
            $table->morphs('related'); // related_type + related_id (post, comment, project, etc.)
            $table->foreignId('triggered_by')->nullable()->constrained('users'); // Usuario que triggereó la acción
            $table->text('description')->nullable(); // Descripción de la acción
            $table->json('metadata')->nullable(); // Datos adicionales
            
            // Validación y reversión
            $table->boolean('is_validated')->default(true); // Si la transacción es válida
            $table->boolean('is_reversed')->default(false); // Si fue revertida
            $table->foreignId('reversed_by')->nullable()->constrained('users');
            $table->timestamp('reversed_at')->nullable();
            $table->text('reversal_reason')->nullable();
            
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['action_type', 'created_at']);
            $table->index(['category', 'created_at']);
            $table->index(['is_validated', 'is_reversed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reputation_transactions');
    }
};

