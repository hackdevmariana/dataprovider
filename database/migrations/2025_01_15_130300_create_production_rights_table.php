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
        Schema::create('production_rights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade'); // Vendedor
            $table->foreignId('buyer_id')->nullable()->constrained('users')->onDelete('set null'); // Comprador
            $table->foreignId('installation_id')->nullable()->constrained('energy_installations'); // Instalación asociada
            $table->foreignId('project_proposal_id')->nullable()->constrained()->onDelete('set null'); // Proyecto asociado
            
            // Información básica del derecho
            $table->string('title'); // Título del derecho
            $table->string('slug')->unique(); // URL amigable
            $table->text('description'); // Descripción detallada
            $table->string('right_identifier')->unique(); // Identificador único del derecho
            
            // Tipo de derecho de producción
            $table->enum('right_type', [
                'energy_production',     // Derecho a producción energética
                'excess_energy',         // Derecho a excedentes
                'carbon_credits',        // Créditos de carbono
                'renewable_certificates', // Certificados renovables
                'grid_injection',        // Derecho de inyección a red
                'virtual_battery',       // Batería virtual
                'demand_response',       // Respuesta a la demanda
                'capacity_rights',       // Derechos de capacidad
                'green_certificates',    // Certificados verdes
                'other'                  // Otro tipo
            ]);
            
            // Características del derecho
            $table->decimal('total_capacity_kw', 10, 2); // Capacidad total en kW
            $table->decimal('available_capacity_kw', 10, 2); // Capacidad disponible
            $table->decimal('reserved_capacity_kw', 10, 2)->default(0); // Capacidad reservada
            $table->decimal('sold_capacity_kw', 10, 2)->default(0); // Capacidad vendida
            
            $table->decimal('estimated_annual_production_kwh', 12, 2)->nullable(); // Producción anual estimada
            $table->decimal('guaranteed_annual_production_kwh', 12, 2)->nullable(); // Producción garantizada
            $table->decimal('actual_annual_production_kwh', 12, 2)->default(0); // Producción real
            
            // Período de validez del derecho
            $table->date('valid_from'); // Válido desde
            $table->date('valid_until'); // Válido hasta
            $table->integer('duration_years')->nullable(); // Duración en años
            $table->boolean('renewable_right')->default(false); // Derecho renovable
            $table->integer('renewal_period_years')->nullable(); // Período de renovación
            
            // Aspectos económicos
            $table->enum('pricing_model', [
                'fixed_price_kwh',       // Precio fijo por kWh
                'market_price',          // Precio de mercado
                'premium_over_market',   // Prima sobre mercado
                'auction_based',         // Basado en subasta
                'performance_based',     // Basado en rendimiento
                'subscription_model',    // Modelo de suscripción
                'revenue_sharing',       // Participación en ingresos
                'hybrid'                 // Modelo híbrido
            ]);
            
            $table->decimal('price_per_kwh', 8, 4)->nullable(); // Precio por kWh
            $table->decimal('market_premium_percentage', 5, 2)->nullable(); // Prima sobre mercado
            $table->decimal('minimum_guaranteed_price', 8, 4)->nullable(); // Precio mínimo garantizado
            $table->decimal('maximum_price_cap', 8, 4)->nullable(); // Precio máximo
            $table->json('price_escalation_terms')->nullable(); // Términos de escalación de precios
            
            // Términos del contrato
            $table->decimal('upfront_payment', 10, 2)->nullable(); // Pago inicial
            $table->decimal('periodic_payment', 8, 2)->nullable(); // Pago periódico
            $table->enum('payment_frequency', [
                'monthly', 'quarterly', 'biannual', 'annual', 'on_production'
            ])->nullable();
            
            $table->decimal('security_deposit', 8, 2)->nullable(); // Depósito de garantía
            $table->json('payment_terms')->nullable(); // Términos de pago
            $table->json('penalty_clauses')->nullable(); // Cláusulas de penalización
            
            // Garantías y seguros
            $table->boolean('production_guaranteed')->default(false); // Producción garantizada
            $table->decimal('production_guarantee_percentage', 5, 2)->nullable(); // % garantía producción
            $table->boolean('insurance_included')->default(false); // Seguro incluido
            $table->text('insurance_details')->nullable(); // Detalles del seguro
            $table->json('risk_allocation')->nullable(); // Asignación de riesgos
            
            // Derechos y obligaciones
            $table->json('buyer_rights')->nullable(); // Derechos del comprador
            $table->json('buyer_obligations')->nullable(); // Obligaciones del comprador
            $table->json('seller_rights')->nullable(); // Derechos del vendedor
            $table->json('seller_obligations')->nullable(); // Obligaciones del vendedor
            
            // Transferibilidad y restricciones
            $table->boolean('is_transferable')->default(true); // Es transferible
            $table->integer('max_transfers')->nullable(); // Máximo de transferencias
            $table->integer('current_transfers')->default(0); // Transferencias actuales
            $table->json('transfer_restrictions')->nullable(); // Restricciones de transferencia
            $table->decimal('transfer_fee_percentage', 5, 2)->nullable(); // Comisión por transferencia
            
            // Estado del derecho
            $table->enum('status', [
                'available',             // Disponible
                'reserved',              // Reservado
                'under_negotiation',     // En negociación
                'contracted',            // Contratado
                'active',                // Activo
                'suspended',             // Suspendido
                'expired',               // Expirado
                'cancelled',             // Cancelado
                'disputed'               // En disputa
            ])->default('available');
            
            $table->text('status_notes')->nullable(); // Notas sobre el estado
            $table->timestamp('contract_signed_at')->nullable(); // Fecha firma contrato
            $table->timestamp('activated_at')->nullable(); // Fecha activación
            
            // Seguimiento de rendimiento
            $table->decimal('current_month_production_kwh', 10, 2)->default(0); // Producción mes actual
            $table->decimal('ytd_production_kwh', 12, 2)->default(0); // Producción año hasta fecha
            $table->decimal('lifetime_production_kwh', 15, 2)->default(0); // Producción total histórica
            $table->decimal('performance_ratio', 5, 2)->default(100); // Ratio de rendimiento
            $table->json('monthly_production_history')->nullable(); // Historial mensual
            
            // Información regulatoria
            $table->string('regulatory_framework')->nullable(); // Marco regulatorio aplicable
            $table->json('applicable_regulations')->nullable(); // Regulaciones aplicables
            $table->boolean('grid_code_compliant')->default(true); // Cumple código de red
            $table->json('certifications')->nullable(); // Certificaciones
            
            // Documentación legal
            $table->json('legal_documents')->nullable(); // Documentos legales
            $table->string('contract_template_version')->nullable(); // Versión plantilla contrato
            $table->boolean('electronic_signature_valid')->default(false); // Firma electrónica válida
            $table->json('signature_details')->nullable(); // Detalles de firmas
            
            // Métricas de mercado
            $table->integer('views_count')->default(0);
            $table->integer('inquiries_count')->default(0);
            $table->integer('offers_received')->default(0);
            $table->decimal('highest_offer_price', 8, 4)->nullable(); // Mejor oferta recibida
            $table->decimal('average_market_price', 8, 4)->nullable(); // Precio promedio de mercado
            
            // Configuración
            $table->boolean('is_active')->default(true); // Derecho activo
            $table->boolean('is_featured')->default(false); // Derecho destacado
            $table->boolean('auto_accept_offers')->default(false); // Aceptar ofertas automáticamente
            $table->decimal('auto_accept_threshold', 8, 4)->nullable(); // Umbral aceptación automática
            $table->boolean('allow_partial_sales')->default(true); // Permitir ventas parciales
            $table->decimal('minimum_sale_capacity_kw', 8, 2)->nullable(); // Capacidad mínima venta
            
            $table->timestamps();
            
            $table->index(['seller_id', 'status']);
            $table->index(['right_type', 'is_active']);
            $table->index(['valid_from', 'valid_until']);
            $table->index(['pricing_model', 'price_per_kwh']);
            $table->index(['installation_id', 'status']);
            $table->fullText(['title', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_rights');
    }
};
