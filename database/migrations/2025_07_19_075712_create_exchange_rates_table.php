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
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->string('from_currency');
            $table->string('to_currency');
            $table->decimal('rate', 20, 8);
            $table->date('date');
            $table->string('source');
            $table->enum('market_type', ['fiat', 'crypto', 'metal']);
            $table->unsignedTinyInteger('precision')->nullable();
            $table->string('unit')->nullable();
            $table->decimal('volume_usd', 20, 2)->nullable();
            $table->decimal('market_cap', 20, 2)->nullable();
            $table->timestamp('retrieved_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_promoted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
