<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_forecasts', function (Blueprint $table) {
            $table->id();
            $table->string('energy_type')->default('electricity');
            $table->string('zone')->default('peninsula');
            $table->timestamp('forecast_time');
            $table->timestamp('target_time');
            $table->decimal('predicted_price', 8, 4);
            $table->decimal('confidence_level', 3, 2);
            $table->string('forecast_model');
            $table->json('factors')->nullable();
            $table->decimal('min_price', 8, 4)->nullable();
            $table->decimal('max_price', 8, 4)->nullable();
            $table->string('accuracy_score')->nullable();
            $table->timestamps();

            $table->index(['energy_type', 'zone', 'target_time']);
            $table->index(['forecast_time']);
            $table->index(['confidence_level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_forecasts');
    }
};
