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
        Schema::create('carbon_saving_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('carbon_equivalence_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount_kg', 8, 2);
            $table->string('activity_type')->nullable(); // e.g., 'tree_planting', 'energy_saving'
            $table->json('metadata')->nullable(); // stores activity details
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carbon_saving_logs');
    }
};
