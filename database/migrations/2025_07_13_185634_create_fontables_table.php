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
        Schema::create('fontables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('font_id')->constrained()->cascadeOnDelete();
            $table->morphs('fontable');
            $table->string('usage')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fontables');
    }
};
