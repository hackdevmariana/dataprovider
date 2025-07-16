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
        Schema::create('electricity_prices', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->tinyInteger('hour')->nullable(); // 0–23 si es por hora
            $table->enum('type', ['pvpc', 'spot']);
            $table->decimal('price_eur_mwh', 10, 4);
            $table->decimal('price_min', 10, 4)->nullable();
            $table->decimal('price_max', 10, 4)->nullable();
            $table->decimal('price_avg', 10, 4)->nullable();
            $table->boolean('forecast_for_tomorrow')->default(false);
            $table->string('source')->nullable(); // OMIE, REE, etc.
            $table->foreignId('price_unit_id')->nullable()->constrained('price_units');
            $table->timestamps();

            $table->unique(['date', 'hour', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electricity_prices');
    }
};
