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
        Schema::table('energy_companies', function (Blueprint $table) {
            // Modificar el enum para incluir 'cooperativa'
            $table->enum('company_type', ['comercializadora', 'distribuidora', 'mixta', 'cooperativa'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('energy_companies', function (Blueprint $table) {
            // Volver al enum original
            $table->enum('company_type', ['comercializadora', 'distribuidora', 'mixta'])->change();
        });
    }
};