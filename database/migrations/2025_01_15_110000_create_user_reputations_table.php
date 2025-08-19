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
        Schema::create('user_reputations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Reputación global y por categorías
            $table->integer('total_reputation')->default(1); // Reputación total (como StackOverflow)
            $table->json('category_reputation')->nullable(); // Reputación por categoría energética
            $table->json('topic_reputation')->nullable(); // Reputación por tema específico
            
            // Métricas de contribución
            $table->integer('helpful_answers')->default(0); // Respuestas marcadas como útiles
            $table->integer('accepted_solutions')->default(0); // Soluciones aceptadas
            $table->integer('quality_posts')->default(0); // Posts de alta calidad
            $table->integer('verified_contributions')->default(0); // Contribuciones verificadas por expertos
            
            // Métricas de engagement positivo
            $table->integer('upvotes_received')->default(0);
            $table->integer('downvotes_received')->default(0);
            $table->decimal('upvote_ratio', 5, 2)->default(0); // % upvotes vs total votes
            
            // Métricas de liderazgo comunitario
            $table->integer('topics_created')->default(0);
            $table->integer('successful_projects')->default(0); // Proyectos completados exitosamente
            $table->integer('mentorship_points')->default(0); // Puntos por ayudar principiantes
            
            // Penalizaciones y moderación
            $table->integer('warnings_received')->default(0);
            $table->integer('content_removed')->default(0);
            $table->boolean('is_suspended')->default(false);
            $table->timestamp('suspended_until')->nullable();
            
            // Rankings y posiciones
            $table->integer('global_rank')->nullable(); // Posición global
            $table->json('category_ranks')->nullable(); // Rankings por categoría
            $table->integer('monthly_rank')->nullable(); // Ranking mensual
            
            // Verificación y credenciales
            $table->boolean('is_verified_professional')->default(false);
            $table->json('professional_credentials')->nullable(); // Títulos, certificaciones
            $table->json('expertise_areas')->nullable(); // Áreas de expertise verificadas
            
            $table->timestamps();
            
            $table->unique('user_id');
            $table->index(['total_reputation', 'global_rank']);
            $table->index('is_verified_professional');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_reputations');
    }
};

