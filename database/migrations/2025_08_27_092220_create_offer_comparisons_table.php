<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offer_comparisons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('energy_type')->default('electricity');
            $table->string('consumption_profile');
            $table->json('offers_compared');
            $table->string('best_offer_id');
            $table->decimal('savings_amount', 8, 2)->nullable();
            $table->decimal('savings_percentage', 5, 2)->nullable();
            $table->json('comparison_criteria')->nullable();
            $table->timestamp('comparison_date');
            $table->boolean('is_shared')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'energy_type']);
            $table->index(['comparison_date']);
            $table->index(['savings_percentage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_comparisons');
    }
};
