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
        Schema::create('price_units', function (Blueprint $table) {
            $table->id();
            $table->string('name');        // Ej: "Euro por megavatio hora"
            $table->string('short_name');  // Ej: "€/MWh"
            $table->string('unit_code')->nullable(); // código estándar
            $table->decimal('conversion_factor_to_kwh', 12, 6)->nullable(); // conversión opcional
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_units');
    }
};
