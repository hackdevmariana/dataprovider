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
        Schema::create('sponsored_content', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sponsor_id')->constrained('users')->onDelete('cascade'); // Empresa/usuario patrocinador
            $table->morphs('sponsorable'); // sponsorable_type + sponsorable_id (post, comment, topic, etc.)
            
            // Configuración de la campaña
            $table->string('campaign_name'); // Nombre interno de la campaña
            $table->text('campaign_description')->nullable();
            $table->enum('content_type', [
                'promoted_post',    // Post promocionado
                'banner_ad',        // Banner publicitario
                'sponsored_topic',  // Tema patrocinado
                'product_placement', // Placement de producto
                'native_content',   // Contenido nativo
                'event_promotion',  // Promoción de evento
                'job_posting',      // Oferta de trabajo
                'service_highlight' // Destacar servicio
            ]);
            
            // Targeting y audiencia
            $table->json('target_audience')->nullable(); // Criterios de audiencia
            $table->json('target_topics')->nullable(); // Temas específicos donde mostrar
            $table->json('target_locations')->nullable(); // Ubicaciones geográficas
            $table->json('target_demographics')->nullable(); // Demografía objetivo
            
            // Configuración de visualización
            $table->string('ad_label')->default('Patrocinado'); // Etiqueta visible
            $table->string('call_to_action')->nullable(); // "Más info", "Contactar", etc.
            $table->string('destination_url')->nullable(); // URL de destino
            $table->json('creative_assets')->nullable(); // Imágenes, videos, etc.
            
            // Presupuesto y bidding
            $table->enum('pricing_model', ['cpm', 'cpc', 'cpa', 'fixed']); // CPM, CPC, CPA, Precio fijo
            $table->decimal('bid_amount', 10, 4); // Cantidad de bid
            $table->decimal('daily_budget', 10, 2)->nullable();
            $table->decimal('total_budget', 10, 2)->nullable();
            $table->decimal('spent_amount', 10, 2)->default(0);
            
            // Programación
            $table->timestamp('start_date');
            $table->timestamp('end_date')->nullable();
            $table->json('schedule_config')->nullable(); // Horarios específicos, días de semana, etc.
            
            // Estados y moderación
            $table->enum('status', [
                'draft',           // Borrador
                'pending_review',  // Pendiente de revisión
                'approved',        // Aprobado
                'active',          // Activo
                'paused',          // Pausado
                'completed',       // Completado
                'rejected',        // Rechazado
                'expired'          // Expirado
            ])->default('draft');
            
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            
            // Métricas de rendimiento
            $table->integer('impressions')->default(0); // Impresiones
            $table->integer('clicks')->default(0); // Clicks
            $table->integer('conversions')->default(0); // Conversiones
            $table->decimal('ctr', 5, 2)->default(0); // Click-through rate
            $table->decimal('conversion_rate', 5, 2)->default(0); // Tasa de conversión
            $table->decimal('engagement_rate', 5, 2)->default(0); // Tasa de engagement
            
            // Configuración de transparencia
            $table->boolean('show_sponsor_info')->default(true); // Mostrar info del patrocinador
            $table->boolean('allow_user_feedback')->default(true); // Permitir feedback de usuarios
            $table->json('disclosure_text')->nullable(); // Texto de divulgación personalizado
            
            $table->timestamps();
            
            $table->index(['sponsor_id', 'status']);
            $table->index(['content_type', 'status', 'start_date']);
            $table->index(['status', 'start_date', 'end_date']);
            $table->index(['pricing_model', 'bid_amount']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sponsored_content');
    }
};

