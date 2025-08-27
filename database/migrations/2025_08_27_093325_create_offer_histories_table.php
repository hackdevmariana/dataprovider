<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offer_histories', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('offer_type');
            $table->json('offer_details');
            $table->timestamp('valid_from');
            $table->timestamp('valid_until')->nullable();
            $table->decimal('price', 8, 4);
            $table->string('currency')->default('EUR');
            $table->string('unit')->default('MWh');
            $table->json('terms_conditions')->nullable();
            $table->string('status')->default('active');
            $table->json('restrictions')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['company_name', 'status']);
            $table->index(['valid_from', 'valid_until']);
            $table->index(['price']);
            $table->index(['is_featured']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_histories');
    }
};
