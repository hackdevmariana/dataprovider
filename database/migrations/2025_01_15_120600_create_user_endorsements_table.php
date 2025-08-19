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
        Schema::create('user_endorsements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('endorser_id')->constrained('users')->onDelete('cascade'); // Quien endosa
            $table->foreignId('endorsed_id')->constrained('users')->onDelete('cascade'); // Quien es endorsado
            
            // Área de expertise específica
            $table->enum('skill_category', [
                'solar_installation',    // Instalación solar
                'electrical_work',       // Trabajo eléctrico
                'project_management',    // Gestión de proyectos
                'energy_consulting',     // Consultoría energética
                'legal_advice',          // Asesoría legal
                'financing',             // Financiación
                'maintenance',           // Mantenimiento
                'design_engineering',    // Ingeniería de diseño
                'sales',                 // Ventas
                'customer_service',      // Atención al cliente
                'training',              // Formación
                'research',              // Investigación
                'policy_analysis',       // Análisis de políticas
                'community_building',    // Construcción de comunidad
                'technical_writing',     // Redacción técnica
                'general_knowledge'      // Conocimiento general
            ]);
            
            $table->string('specific_skill')->nullable(); // Habilidad específica dentro de la categoría
            $table->text('endorsement_text')->nullable(); // Texto del endorsement
            $table->decimal('skill_rating', 3, 1)->nullable(); // Rating de 1.0 a 5.0
            
            // Contexto del endorsement
            $table->enum('relationship_context', [
                'colleague',             // Colega
                'client',               // Cliente
                'supplier',             // Proveedor
                'mentor',               // Mentor
                'mentee',               // Aprendiz
                'collaborator',         // Colaborador
                'competitor',           // Competidor
                'community_member',     // Miembro de comunidad
                'student',              // Estudiante
                'teacher',              // Profesor
                'unknown'               // Desconocido
            ])->default('community_member');
            
            $table->string('project_context')->nullable(); // Proyecto específico donde colaboraron
            $table->integer('collaboration_duration_months')->nullable(); // Duración de colaboración en meses
            
            // Validación y confianza
            $table->boolean('is_verified')->default(false); // Verificado por el sistema
            $table->decimal('trust_score', 5, 2)->default(100); // Score de confianza (0-100)
            $table->integer('helpful_votes')->default(0); // Votos de "útil" de otros usuarios
            $table->integer('total_votes')->default(0); // Total de votos recibidos
            
            // Visibilidad y configuración
            $table->boolean('is_public')->default(true); // Visible públicamente
            $table->boolean('show_on_profile')->default(true); // Mostrar en perfil del endorsado
            $table->boolean('notify_endorsed')->default(true); // Notificar al endorsado
            
            // Reciprocidad
            $table->boolean('is_mutual')->default(false); // Si es recíproco
            $table->foreignId('reciprocal_endorsement_id')->nullable()->constrained('user_endorsements');
            
            // Moderación
            $table->enum('status', ['active', 'pending', 'rejected', 'disputed'])->default('active');
            $table->foreignId('disputed_by')->nullable()->constrained('users'); // Si el endorsado disputa
            $table->text('dispute_reason')->nullable();
            $table->timestamp('disputed_at')->nullable();
            
            $table->timestamps();
            
            $table->unique(['endorser_id', 'endorsed_id', 'skill_category', 'specific_skill'], 'unique_endorsement');
            $table->index(['endorsed_id', 'skill_category', 'is_public']);
            $table->index(['endorser_id', 'created_at']);
            $table->index(['skill_category', 'skill_rating', 'is_verified']);
            $table->index(['status', 'is_public']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_endorsements');
    }
};

