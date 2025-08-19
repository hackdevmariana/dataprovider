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
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_plan_id')->constrained()->onDelete('cascade');
            $table->string('status'); // active, cancelled, expired, trial, suspended
            $table->decimal('amount_paid', 10, 2);
            $table->string('currency', 3)->default('EUR');
            $table->enum('billing_cycle', ['monthly', 'yearly', 'one_time']);
            $table->datetime('starts_at');
            $table->datetime('ends_at')->nullable();
            $table->datetime('trial_ends_at')->nullable();
            $table->datetime('cancelled_at')->nullable();
            $table->datetime('next_billing_at')->nullable();
            $table->string('payment_method')->nullable(); // stripe, paypal, bank_transfer
            $table->string('external_subscription_id')->nullable(); // ID en Stripe/PayPal
            $table->json('usage_stats')->nullable(); // Estadísticas de uso
            $table->json('metadata')->nullable(); // Datos adicionales
            $table->text('cancellation_reason')->nullable();
            $table->boolean('auto_renew')->default(true);
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['status', 'ends_at']);
            $table->index(['next_billing_at']);
            $table->unique(['user_id', 'subscription_plan_id', 'status'], 'user_plan_status_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_subscriptions');
    }
};