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
        Schema::create('user_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade'); // Quien hace la review
            $table->morphs('reviewable'); // reviewable_type + reviewable_id (user, cooperative, service, etc.)
            
            // Rating principal y detallado
            $table->decimal('overall_rating', 3, 1); // Rating general (1.0-5.0)
            $table->json('detailed_ratings')->nullable(); // Ratings específicos por aspecto
            
            // Contenido de la review
            $table->string('title'); // Título de la review
            $table->text('content'); // Contenido detallado
            $table->json('pros')->nullable(); // Aspectos positivos
            $table->json('cons')->nullable(); // Aspectos negativos
            $table->json('images')->nullable(); // Imágenes de la review
            $table->json('attachments')->nullable(); // Documentos adjuntos
            
            // Contexto del servicio/experiencia
            $table->enum('service_type', [
                'installation',          // Servicio de instalación
                'maintenance',           // Mantenimiento
                'consulting',            // Consultoría
                'design',                // Diseño
                'financing',             // Financiación
                'legal_advice',          // Asesoría legal
                'training',              // Formación
                'product_sale',          // Venta de producto
                'project_management',    // Gestión de proyecto
                'community_service',     // Servicio comunitario
                'platform_experience',   // Experiencia en plataforma
                'other'                  // Otro
            ]);
            
            $table->date('service_date')->nullable(); // Fecha del servicio
            $table->decimal('service_cost', 10, 2)->nullable(); // Coste del servicio
            $table->string('service_location')->nullable(); // Ubicación del servicio
            $table->integer('service_duration_days')->nullable(); // Duración en días
            
            // Verificación y autenticidad
            $table->boolean('is_verified_purchase')->default(false); // Compra/servicio verificado
            $table->string('verification_code')->nullable(); // Código de verificación
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');
            
            // Recomendación
            $table->boolean('would_recommend')->nullable(); // Si recomendaría
            $table->enum('recommendation_level', [
                'highly_recommend',      // Altamente recomendado
                'recommend',            // Recomendado
                'neutral',              // Neutral
                'not_recommend',        // No recomendado
                'strongly_not_recommend' // Fuertemente no recomendado
            ])->nullable();
            
            // Utilidad y engagement
            $table->integer('helpful_votes')->default(0); // Votos de "útil"
            $table->integer('not_helpful_votes')->default(0); // Votos de "no útil"
            $table->integer('total_votes')->default(0); // Total de votos
            $table->decimal('helpfulness_ratio', 5, 2)->default(0); // % de votos útiles
            $table->integer('views_count')->default(0); // Visualizaciones
            
            // Respuesta del proveedor
            $table->text('provider_response')->nullable(); // Respuesta del proveedor
            $table->timestamp('provider_responded_at')->nullable();
            $table->foreignId('provider_responder_id')->nullable()->constrained('users');
            
            // Moderación y estado
            $table->enum('status', [
                'published',            // Publicada
                'pending_review',       // Pendiente de revisión
                'flagged',              // Marcada para revisión
                'hidden',               // Oculta
                'rejected',             // Rechazada
                'disputed'              // En disputa
            ])->default('published');
            
            $table->integer('flags_count')->default(0); // Número de reportes
            $table->json('flag_reasons')->nullable(); // Razones de los reportes
            $table->foreignId('moderated_by')->nullable()->constrained('users');
            $table->timestamp('moderated_at')->nullable();
            $table->text('moderation_notes')->nullable();
            
            // Configuración de privacidad
            $table->boolean('is_anonymous')->default(false); // Review anónima
            $table->boolean('show_service_cost')->default(false); // Mostrar coste del servicio
            $table->boolean('allow_contact')->default(true); // Permitir contacto por la review
            
            $table->timestamps();
            
            $table->index(['reviewable_type', 'reviewable_id', 'status']);
            $table->index(['reviewer_id', 'created_at']);
            $table->index(['overall_rating', 'status']);
            $table->index(['service_type', 'is_verified_purchase']);
            $table->index(['helpful_votes', 'total_votes']);
            $table->index(['status', 'flags_count']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_reviews');
    }
};

