<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('energy_companies')->onDelete('cascade');
            $table->string('certification_name');
            $table->string('issuing_body');
            $table->string('certification_type');
            $table->date('issued_date');
            $table->date('expiry_date')->nullable();
            $table->string('certificate_number')->nullable();
            $table->text('description')->nullable();
            $table->json('scope')->nullable();
            $table->string('status')->default('active');
            $table->json('requirements_met')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index(['certification_type', 'is_verified']);
            $table->index(['expiry_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_certifications');
    }
};
