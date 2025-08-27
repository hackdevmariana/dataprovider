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
        Schema::create('energy_installations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['solar', 'wind', 'hydro', 'biomass', 'other']);
            $table->float('capacity_kw');
            $table->string('location');
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('commissioned_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('energy_installations');
    }
};
