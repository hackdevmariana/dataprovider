<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('real_time_prices', function (Blueprint $table) {
            $table->id();
            $table->string('energy_type')->default('electricity');
            $table->string('zone')->default('peninsula');
            $table->timestamp('timestamp');
            $table->decimal('price', 8, 4);
            $table->string('currency')->default('EUR');
            $table->string('unit')->default('MWh');
            $table->string('source');
            $table->string('data_quality')->default('high');
            $table->json('additional_data')->nullable();
            $table->timestamps();

            $table->index(['energy_type', 'zone', 'timestamp']);
            $table->index(['timestamp']);
            $table->index(['price']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('real_time_prices');
    }
};
