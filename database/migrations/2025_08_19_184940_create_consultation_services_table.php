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
        Schema::create('consultation_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['technical', 'legal', 'financial', 'installation', 'maintenance', 'custom']);
            $table->enum('format', ['online', 'onsite', 'hybrid', 'document_review', 'phone_call']);
            $table->enum('status', ['requested', 'accepted', 'in_progress', 'completed', 'cancelled', 'disputed']);
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->decimal('fixed_price', 10, 2)->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->string('currency', 3)->default('EUR');
            $table->integer('estimated_hours')->nullable();
            $table->integer('actual_hours')->nullable();
            $table->datetime('requested_at');
            $table->datetime('accepted_at')->nullable();
            $table->datetime('started_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->datetime('deadline')->nullable();
            $table->json('requirements'); // Requerimientos específicos
            $table->json('deliverables'); // Entregables acordados
            $table->json('milestones')->nullable(); // Hitos del proyecto
            $table->text('client_notes')->nullable();
            $table->text('consultant_notes')->nullable();
            $table->integer('client_rating')->nullable(); // 1-5
            $table->integer('consultant_rating')->nullable(); // 1-5
            $table->text('client_review')->nullable();
            $table->text('consultant_review')->nullable();
            $table->decimal('platform_commission', 5, 4)->default(0.15); // 15% comisión
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            
            $table->index(['consultant_id', 'status']);
            $table->index(['client_id', 'status']);
            $table->index(['type', 'status']);
            $table->index(['deadline', 'status']);
            $table->index(['completed_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultation_services');
    }
};