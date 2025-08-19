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
        Schema::create('project_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_proposal_id')->constrained()->onDelete('cascade');
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('type', ['basic', 'advanced', 'professional', 'enterprise']);
            $table->enum('status', ['requested', 'in_review', 'approved', 'rejected', 'expired']);
            $table->decimal('fee', 10, 2); // Tarifa de verificación (199€, 499€, etc.)
            $table->string('currency', 3)->default('EUR');
            $table->json('verification_criteria'); // Criterios a verificar
            $table->json('documents_required'); // Documentos necesarios
            $table->json('documents_provided')->nullable(); // Documentos aportados
            $table->json('verification_results')->nullable(); // Resultados de la verificación
            $table->text('verification_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->integer('score')->nullable(); // Puntuación de verificación (1-100)
            $table->datetime('requested_at');
            $table->datetime('reviewed_at')->nullable();
            $table->datetime('verified_at')->nullable();
            $table->datetime('expires_at')->nullable();
            $table->boolean('is_public')->default(true); // Mostrar badge público
            $table->string('certificate_number')->nullable()->unique();
            $table->timestamps();
            
            $table->index(['project_proposal_id', 'status']);
            $table->index(['requested_by', 'status']);
            $table->index(['verified_by', 'status']);
            $table->index(['type', 'status']);
            $table->index(['expires_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_verifications');
    }
};