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
        Schema::create('electricity_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('energy_company_id')->constrained();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->decimal('price_fixed_eur_month', 8, 2)->nullable();
            $table->decimal('price_variable_eur_kwh', 8, 4)->nullable();
            $table->foreignId('price_unit_id')->nullable()->constrained('price_units');
            $table->enum('offer_type', ['fixed', 'variable', 'hybrid']);
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->string('conditions_url')->nullable();
            $table->unsignedTinyInteger('contract_length_months')->nullable();
            $table->boolean('requires_smart_meter')->default(false);
            $table->boolean('renewable_origin_certified')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electricity_offers');
    }
};
