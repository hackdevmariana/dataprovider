<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('point_of_interests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('address')->nullable();
            $table->enum('type', ['hotel', 'bar', 'monument', 'museum', 'park', 'other'])->nullable();
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->foreignId('municipality_id')->constrained()->cascadeOnDelete();
            $table->string('source')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_cultural_center')->default(false);
            $table->boolean('is_energy_installation')->default(false);
            $table->boolean('is_cooperative_office')->default(false);
            $table->json('opening_hours')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('point_of_interests');
    }
};

