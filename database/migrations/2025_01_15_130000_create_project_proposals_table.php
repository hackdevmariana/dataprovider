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
        Schema::create('project_proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposer_id')->constrained('users')->onDelete('cascade'); // Usuario que propone
            $table->foreignId('cooperative_id')->nullable()->constrained()->onDelete('set null'); // Cooperativa asociada
            
            // Información básica del proyecto
            $table->string('title'); // Título del proyecto
            $table->string('slug')->unique(); // URL amigable
            $table->text('description'); // Descripción detallada
            $table->text('summary')->nullable(); // Resumen ejecutivo
            $table->json('objectives')->nullable(); // Objetivos específicos
            $table->json('benefits')->nullable(); // Beneficios esperados
            
            // Tipo y categoría del proyecto
            $table->enum('project_type', [
                'individual_installation',  // Instalación individual
                'community_installation',   // Instalación comunitaria
                'shared_installation',      // Instalación compartida
                'energy_storage',           // Almacenamiento energético
                'smart_grid',              // Red inteligente
                'efficiency_improvement',   // Mejora de eficiencia
                'research_development',     // I+D
                'educational',             // Educativo
                'infrastructure',          // Infraestructura
                'other'                    // Otro
            ]);
            
            $table->enum('scale', [
                'residential',             // Residencial (< 10kW)
                'commercial',              // Comercial (10-100kW)
                'industrial',              // Industrial (100kW-1MW)
                'utility',                 // Gran escala (> 1MW)
                'community'                // Comunitario (variable)
            ]);
            
            // Ubicación y características técnicas
            $table->foreignId('municipality_id')->nullable()->constrained();
            $table->string('specific_location')->nullable(); // Dirección específica
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('estimated_power_kw', 10, 2)->nullable(); // Potencia estimada
            $table->decimal('estimated_annual_production_kwh', 12, 2)->nullable(); // Producción anual
            $table->json('technical_specifications')->nullable(); // Especificaciones técnicas
            
            // Aspectos económicos
            $table->decimal('total_investment_required', 12, 2); // Inversión total necesaria
            $table->decimal('investment_raised', 12, 2)->default(0); // Inversión conseguida
            $table->decimal('min_investment_per_participant', 8, 2)->nullable(); // Inversión mínima
            $table->decimal('max_investment_per_participant', 8, 2)->nullable(); // Inversión máxima
            $table->integer('max_participants')->nullable(); // Máximo de participantes
            $table->integer('current_participants')->default(0); // Participantes actuales
            
            // Rentabilidad y retorno
            $table->decimal('estimated_roi_percentage', 5, 2)->nullable(); // ROI estimado
            $table->integer('payback_period_years')->nullable(); // Período de amortización
            $table->decimal('estimated_annual_savings', 10, 2)->nullable(); // Ahorro anual estimado
            $table->json('financial_projections')->nullable(); // Proyecciones financieras
            
            // Cronograma del proyecto
            $table->date('funding_deadline'); // Fecha límite de financiación
            $table->date('project_start_date')->nullable(); // Fecha inicio del proyecto
            $table->date('expected_completion_date')->nullable(); // Fecha finalización esperada
            $table->integer('estimated_duration_months')->nullable(); // Duración estimada
            $table->json('project_milestones')->nullable(); // Hitos del proyecto
            
            // Documentación y validación
            $table->json('documents')->nullable(); // Documentos adjuntos
            $table->json('images')->nullable(); // Imágenes del proyecto
            $table->json('technical_reports')->nullable(); // Informes técnicos
            $table->boolean('has_permits')->default(false); // Tiene permisos
            $table->json('permits_status')->nullable(); // Estado de permisos
            $table->boolean('is_technically_validated')->default(false); // Validado técnicamente
            $table->foreignId('technical_validator_id')->nullable()->constrained('users');
            $table->timestamp('technical_validation_date')->nullable();
            
            // Estado y seguimiento
            $table->enum('status', [
                'draft',                   // Borrador
                'under_review',           // En revisión
                'approved',               // Aprobado
                'funding',                // En financiación
                'funded',                 // Financiado
                'in_progress',            // En ejecución
                'completed',              // Completado
                'cancelled',              // Cancelado
                'on_hold',                // En pausa
                'rejected'                // Rechazado
            ])->default('draft');
            
            $table->text('status_notes')->nullable(); // Notas sobre el estado
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            
            // Métricas de engagement
            $table->integer('views_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->integer('shares_count')->default(0);
            $table->integer('bookmarks_count')->default(0);
            $table->decimal('engagement_score', 8, 2)->default(0);
            
            // Configuración de visibilidad
            $table->boolean('is_public')->default(true); // Visible públicamente
            $table->boolean('is_featured')->default(false); // Proyecto destacado
            $table->boolean('allow_comments')->default(true); // Permitir comentarios
            $table->boolean('allow_investments')->default(true); // Permitir inversiones
            $table->boolean('notify_updates')->default(true); // Notificar actualizaciones
            
            $table->timestamps();
            
            $table->index(['status', 'is_public']);
            $table->index(['project_type', 'scale']);
            $table->index(['funding_deadline', 'status']);
            $table->index(['municipality_id', 'project_type']);
            $table->index(['engagement_score', 'is_featured']);
            $table->fullText(['title', 'description', 'summary']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_proposals');
    }
};
