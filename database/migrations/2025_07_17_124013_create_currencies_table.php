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
        Schema::create('currencies', function (Blueprint $table) {
            $table->string('iso_code')->primary(); // EUR, USD, BTC
            $table->string('symbol');              // €, ₿, $
            $table->string('name');                // Euro, Dollar
            $table->boolean('is_crypto')->default(false);
            $table->boolean('is_supported_by_app')->default(true);
            $table->boolean('exchangeable_in_calculator')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
