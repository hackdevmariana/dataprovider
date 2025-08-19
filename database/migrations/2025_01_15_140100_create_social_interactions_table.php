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
        Schema::create('social_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Usuario que interactúa
            $table->morphs('interactable'); // interactable_type + interactable_id
            
            // Tipo de interacción social
            $table->enum('interaction_type', [
                'like',                 // Me gusta básico
                'love',                 // Me encanta (más fuerte que like)
                'wow',                  // Me asombra (para logros impresionantes)
                'celebrate',            // Celebrar (para hitos y éxitos)
                'support',              // Apoyo (para proyectos y propuestas)
                'share',                // Compartir
                'bookmark',             // Guardar en favoritos
                'follow',               // Seguir (para contenido recurrente)
                'subscribe',            // Suscribirse a notificaciones
                'report',               // Reportar contenido
                'hide',                 // Ocultar de mi feed
                'block'                 // Bloquear tipo de contenido
            ]);
            
            // Contexto adicional de la interacción
            $table->text('interaction_note')->nullable(); // Nota opcional del usuario
            $table->json('interaction_data')->nullable(); // Datos adicionales específicos
            
            // Información de la interacción
            $table->string('source')->nullable(); // Desde dónde interactuó (feed, profile, notification)
            $table->string('device_type')->nullable(); // Tipo de dispositivo
            $table->decimal('latitude', 10, 8)->nullable(); // Ubicación de la interacción
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Configuración de privacidad
            $table->boolean('is_public')->default(true); // Si la interacción es pública
            $table->boolean('notify_author')->default(true); // Notificar al autor del contenido
            $table->boolean('show_in_activity')->default(true); // Mostrar en actividad del usuario
            
            // Métricas de engagement
            $table->integer('engagement_weight')->default(1); // Peso de la interacción para algoritmos
            $table->decimal('quality_score', 5, 2)->default(100); // Score de calidad de la interacción
            
            // Agrupación temporal (para evitar spam)
            $table->timestamp('interaction_expires_at')->nullable(); // Cuándo expira (para likes temporales)
            $table->boolean('is_temporary')->default(false); // Si es una interacción temporal
            
            // Estados
            $table->enum('status', [
                'active',               // Activa
                'withdrawn',            // Retirada por el usuario
                'expired',              // Expirada
                'flagged',              // Reportada
                'hidden'                // Oculta
            ])->default('active');
            
            $table->timestamps();
            
            // Constraint único para evitar interacciones duplicadas del mismo tipo
            $table->unique(['user_id', 'interactable_type', 'interactable_id', 'interaction_type'], 'unique_user_interaction');
            
            // Índices para optimización
            $table->index(['interactable_type', 'interactable_id', 'interaction_type'], 'si_morphs_type_idx');
            $table->index(['user_id', 'interaction_type', 'created_at'], 'si_user_type_created');
            $table->index(['interaction_type', 'status', 'created_at'], 'si_type_status_created');
            $table->index(['is_public', 'show_in_activity'], 'si_public_activity');
            $table->index(['interaction_expires_at', 'is_temporary'], 'si_expires_temp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_interactions');
    }
};
