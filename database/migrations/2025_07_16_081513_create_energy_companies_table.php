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
        Schema::create('energy_companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('website')->nullable();
            $table->string('phone_customer')->nullable();
            $table->string('phone_commercial')->nullable();
            $table->string('email_customer')->nullable();
            $table->string('email_commercial')->nullable();
            $table->text('highlighted_offer')->nullable();
            $table->string('cnmc_id')->nullable();
            $table->string('logo_url')->nullable();
            $table->foreignId('image_id')->nullable()->constrained('images');
            $table->enum('company_type', ['comercializadora', 'distribuidora', 'mixta']);
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->enum('coverage_scope', ['local', 'regional', 'nacional'])->default('nacional');
            $table->foreignId('municipality_id')->nullable()->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('energy_companies');
    }
};
