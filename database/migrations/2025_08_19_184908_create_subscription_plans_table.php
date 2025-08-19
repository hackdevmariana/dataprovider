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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Basic, Premium, Professional, Enterprise
            $table->string('slug')->unique();
            $table->text('description');
            $table->enum('type', ['individual', 'cooperative', 'business', 'enterprise']);
            $table->enum('billing_cycle', ['monthly', 'yearly', 'one_time']);
            $table->decimal('price', 10, 2); // Precio en euros
            $table->decimal('setup_fee', 10, 2)->default(0);
            $table->integer('trial_days')->default(0);
            $table->integer('max_projects')->nullable(); // null = ilimitado
            $table->integer('max_cooperatives')->nullable();
            $table->integer('max_investments')->nullable();
            $table->integer('max_consultations')->nullable();
            $table->json('features'); // Array de features incluidas
            $table->json('limits'); // Límites específicos
            $table->decimal('commission_rate', 5, 4)->default(0.05); // 5% por defecto
            $table->boolean('priority_support')->default(false);
            $table->boolean('verified_badge')->default(false);
            $table->boolean('analytics_access')->default(false);
            $table->boolean('api_access')->default(false);
            $table->boolean('white_label')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['type', 'is_active']);
            $table->index(['billing_cycle', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};