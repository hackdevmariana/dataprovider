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
        Schema::create('project_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_proposal_id')->constrained()->onDelete('cascade');
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade'); // Autor de la actualización
            
            // Información básica de la actualización
            $table->string('title'); // Título de la actualización
            $table->text('content'); // Contenido detallado
            $table->text('summary')->nullable(); // Resumen ejecutivo
            
            // Tipo de actualización
            $table->enum('update_type', [
                'general',               // Actualización general
                'milestone',             // Hito del proyecto
                'financial',             // Actualización financiera
                'technical',             // Actualización técnica
                'regulatory',            // Actualización regulatoria
                'delay',                 // Retraso en el proyecto
                'completion',            // Finalización de fase
                'issue',                 // Problema o incidencia
                'success',               // Éxito o logro
                'funding',               // Actualización de financiación
                'partnership',           // Nueva asociación
                'media',                 // Cobertura mediática
                'community'              // Actualización comunitaria
            ]);
            
            // Progreso del proyecto
            $table->decimal('progress_percentage', 5, 2)->nullable(); // % progreso actual
            $table->decimal('previous_progress_percentage', 5, 2)->nullable(); // % progreso anterior
            $table->json('completed_milestones')->nullable(); // Hitos completados
            $table->json('upcoming_milestones')->nullable(); // Próximos hitos
            $table->date('revised_completion_date')->nullable(); // Fecha revisada de finalización
            
            // Información financiera
            $table->decimal('budget_spent', 10, 2)->nullable(); // Presupuesto gastado
            $table->decimal('budget_remaining', 10, 2)->nullable(); // Presupuesto restante
            $table->decimal('additional_funding_needed', 10, 2)->nullable(); // Financiación adicional necesaria
            $table->json('cost_breakdown')->nullable(); // Desglose de costes
            $table->text('financial_notes')->nullable(); // Notas financieras
            
            // Métricas técnicas
            $table->decimal('actual_power_installed_kw', 10, 2)->nullable(); // Potencia real instalada
            $table->decimal('production_to_date_kwh', 12, 2)->nullable(); // Producción hasta la fecha
            $table->decimal('performance_vs_expected', 5, 2)->nullable(); // Rendimiento vs esperado
            $table->json('technical_metrics')->nullable(); // Métricas técnicas
            $table->text('technical_notes')->nullable(); // Notas técnicas
            
            // Documentación adjunta
            $table->json('images')->nullable(); // Imágenes de progreso
            $table->json('videos')->nullable(); // Videos del proyecto
            $table->json('documents')->nullable(); // Documentos relacionados
            $table->json('reports')->nullable(); // Informes técnicos/financieros
            
            // Impacto y beneficios
            $table->decimal('co2_savings_kg', 10, 2)->nullable(); // Ahorro CO2 en kg
            $table->decimal('energy_savings_kwh', 10, 2)->nullable(); // Ahorro energético
            $table->decimal('cost_savings_eur', 8, 2)->nullable(); // Ahorro económico
            $table->json('environmental_impact')->nullable(); // Impacto ambiental
            $table->json('social_impact')->nullable(); // Impacto social
            
            // Participación de inversores
            $table->boolean('notify_all_investors')->default(true); // Notificar a todos los inversores
            $table->json('investor_specific_info')->nullable(); // Info específica por inversor
            $table->boolean('requires_investor_action')->default(false); // Requiere acción de inversores
            $table->text('required_action_description')->nullable(); // Descripción acción requerida
            $table->timestamp('action_deadline')->nullable(); // Fecha límite para acción
            
            // Engagement y feedback
            $table->integer('views_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->integer('shares_count')->default(0);
            $table->boolean('allow_comments')->default(true); // Permitir comentarios
            $table->boolean('allow_questions')->default(true); // Permitir preguntas
            
            // Configuración de visibilidad
            $table->enum('visibility', [
                'public',                // Público
                'investors_only',        // Solo inversores
                'team_only',            // Solo equipo del proyecto
                'stakeholders',         // Stakeholders específicos
                'private'               // Privado
            ])->default('investors_only');
            
            $table->boolean('is_featured')->default(false); // Actualización destacada
            $table->boolean('is_urgent')->default(false); // Actualización urgente
            $table->boolean('send_email_notification')->default(false); // Enviar notificación email
            $table->boolean('send_push_notification')->default(false); // Enviar notificación push
            
            // Estado y moderación
            $table->enum('status', [
                'draft',                 // Borrador
                'published',            // Publicada
                'scheduled',            // Programada
                'archived',             // Archivada
                'deleted'               // Eliminada
            ])->default('published');
            
            $table->timestamp('published_at')->nullable(); // Fecha de publicación
            $table->timestamp('scheduled_for')->nullable(); // Programada para
            
            // Métricas de rendimiento
            $table->decimal('investor_satisfaction_score', 3, 1)->nullable(); // Puntuación satisfacción
            $table->integer('questions_received')->default(0); // Preguntas recibidas
            $table->integer('questions_answered')->default(0); // Preguntas respondidas
            $table->boolean('all_questions_answered')->default(true); // Todas las preguntas respondidas
            
            $table->timestamps();
            
            $table->index(['project_proposal_id', 'published_at']);
            $table->index(['author_id', 'update_type']);
            $table->index(['visibility', 'status']);
            $table->index(['is_featured', 'is_urgent']);
            $table->index(['scheduled_for', 'status']);
            $table->fullText(['title', 'content', 'summary']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_updates');
    }
};
