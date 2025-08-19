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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->morphs('payable'); // Polimórfico: subscription, commission, verification, consultation
            $table->string('payment_intent_id')->unique(); // ID de Stripe/PayPal
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded']);
            $table->enum('type', ['subscription', 'commission', 'verification', 'consultation', 'refund']);
            $table->decimal('amount', 10, 2);
            $table->decimal('fee', 8, 2)->default(0); // Comisión del procesador de pagos
            $table->decimal('net_amount', 10, 2); // Cantidad neta después de fees
            $table->string('currency', 3)->default('EUR');
            $table->string('payment_method'); // card, bank_transfer, paypal, etc.
            $table->string('processor'); // stripe, paypal, bank
            $table->json('processor_response')->nullable(); // Respuesta del procesador
            $table->json('metadata')->nullable(); // Metadatos adicionales
            $table->text('description');
            $table->text('failure_reason')->nullable();
            $table->datetime('processed_at')->nullable();
            $table->datetime('failed_at')->nullable();
            $table->datetime('refunded_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['status', 'processed_at']);
            $table->index(['type', 'status']);
            $table->index(['processor', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};