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
        Schema::create('project_investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_proposal_id')->constrained()->onDelete('cascade');
            $table->foreignId('investor_id')->constrained('users')->onDelete('cascade');
            
            // Detalles de la inversión
            $table->decimal('investment_amount', 10, 2); // Cantidad invertida
            $table->decimal('investment_percentage', 5, 2)->nullable(); // % del proyecto
            $table->enum('investment_type', [
                'monetary',               // Inversión monetaria
                'in_kind',               // Inversión en especie
                'labor',                 // Aportación de trabajo
                'materials',             // Aportación de materiales
                'expertise',             // Aportación de conocimiento
                'equipment',             // Aportación de equipamiento
                'land_use',              // Cesión de terreno
                'mixed'                  // Combinación
            ])->default('monetary');
            
            $table->json('investment_details')->nullable(); // Detalles específicos
            $table->text('investment_description')->nullable(); // Descripción de la aportación
            
            // Términos y condiciones
            $table->decimal('expected_return_percentage', 5, 2)->nullable(); // Retorno esperado
            $table->integer('investment_term_years')->nullable(); // Plazo de la inversión
            $table->enum('return_frequency', [
                'monthly',               // Retorno mensual
                'quarterly',             // Retorno trimestral
                'biannual',              // Retorno semestral
                'annual',                // Retorno anual
                'at_completion',         // Al completar proyecto
                'custom'                 // Personalizado
            ])->nullable();
            
            $table->json('return_schedule')->nullable(); // Calendario de retornos
            $table->boolean('reinvest_returns')->default(false); // Reinvertir beneficios
            
            // Estado de la inversión
            $table->enum('status', [
                'pending',               // Pendiente de confirmación
                'confirmed',             // Confirmada
                'paid',                  // Pagada/Realizada
                'active',                // Activa (proyecto en marcha)
                'completed',             // Completada
                'cancelled',             // Cancelada
                'refunded',              // Reembolsada
                'disputed'               // En disputa
            ])->default('pending');
            
            // Información de pago
            $table->string('payment_method')->nullable(); // Método de pago
            $table->string('payment_reference')->nullable(); // Referencia de pago
            $table->timestamp('payment_date')->nullable(); // Fecha de pago
            $table->timestamp('payment_confirmed_at')->nullable(); // Confirmación de pago
            $table->foreignId('payment_confirmed_by')->nullable()->constrained('users');
            
            // Documentación legal
            $table->json('legal_documents')->nullable(); // Documentos legales
            $table->boolean('terms_accepted')->default(false); // Términos aceptados
            $table->timestamp('terms_accepted_at')->nullable(); // Fecha aceptación términos
            $table->string('digital_signature')->nullable(); // Firma digital
            $table->json('contract_details')->nullable(); // Detalles del contrato
            
            // Seguimiento de retornos
            $table->decimal('total_returns_received', 10, 2)->default(0); // Retornos recibidos
            $table->decimal('pending_returns', 10, 2)->default(0); // Retornos pendientes
            $table->timestamp('last_return_date')->nullable(); // Última fecha de retorno
            $table->timestamp('next_return_date')->nullable(); // Próxima fecha de retorno
            
            // Participación en el proyecto
            $table->boolean('has_voting_rights')->default(false); // Derechos de voto
            $table->decimal('voting_weight', 5, 2)->default(0); // Peso en votaciones
            $table->boolean('can_participate_decisions')->default(false); // Participar en decisiones
            $table->boolean('receives_project_updates')->default(true); // Recibir actualizaciones
            
            // Configuración de comunicaciones
            $table->json('notification_preferences')->nullable(); // Preferencias notificaciones
            $table->boolean('public_investor')->default(false); // Inversor público (visible)
            $table->string('investor_alias')->nullable(); // Alias público
            
            // Métricas y seguimiento
            $table->decimal('current_roi', 5, 2)->default(0); // ROI actual
            $table->decimal('projected_final_roi', 5, 2)->nullable(); // ROI proyectado final
            $table->integer('months_invested')->default(0); // Meses invertido
            $table->json('performance_metrics')->nullable(); // Métricas de rendimiento
            
            // Salida de la inversión
            $table->boolean('exit_requested')->default(false); // Solicitud de salida
            $table->timestamp('exit_requested_at')->nullable(); // Fecha solicitud salida
            $table->decimal('exit_value', 10, 2)->nullable(); // Valor de salida
            $table->text('exit_terms')->nullable(); // Términos de salida
            
            $table->timestamps();
            
            $table->unique(['project_proposal_id', 'investor_id'], 'unique_project_investor');
            $table->index(['investor_id', 'status']);
            $table->index(['project_proposal_id', 'investment_amount']);
            $table->index(['status', 'payment_date']);
            $table->index(['next_return_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_investments');
    }
};
