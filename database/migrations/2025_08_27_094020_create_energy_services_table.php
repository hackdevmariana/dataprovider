<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('energy_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('energy_companies')->onDelete('cascade');
            $table->string('service_name');
            $table->text('description')->nullable();
            $table->string('service_type');
            $table->string('energy_source')->nullable();
            $table->json('features')->nullable();
            $table->json('requirements')->nullable();
            $table->decimal('base_price', 8, 2)->nullable();
            $table->string('pricing_model')->nullable();
            $table->json('pricing_details')->nullable();
            $table->string('contract_duration')->nullable();
            $table->json('terms_conditions')->nullable();
            $table->boolean('is_available')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('popularity_score')->default(0);
            $table->timestamps();

            $table->index(['company_id', 'service_type']);
            $table->index(['is_available', 'is_featured']);
            $table->index(['popularity_score']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('energy_services');
    }
};
