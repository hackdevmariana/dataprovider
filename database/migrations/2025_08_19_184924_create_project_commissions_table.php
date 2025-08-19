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
        Schema::create('project_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_proposal_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Usuario que debe pagar
            $table->enum('type', ['success_fee', 'listing_fee', 'verification_fee', 'premium_fee']);
            $table->decimal('amount', 10, 2); // Cantidad de la comisión
            $table->decimal('rate', 5, 4); // Porcentaje aplicado
            $table->decimal('base_amount', 10, 2); // Cantidad base sobre la que se calcula
            $table->string('currency', 3)->default('EUR');
            $table->enum('status', ['pending', 'paid', 'waived', 'disputed', 'refunded']);
            $table->datetime('due_date');
            $table->datetime('paid_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();
            $table->text('description');
            $table->json('calculation_details'); // Detalles del cálculo
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['project_proposal_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['type', 'status']);
            $table->index(['due_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_commissions');
    }
};