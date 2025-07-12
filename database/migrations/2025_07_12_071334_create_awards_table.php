<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('awards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('awarded_by')->nullable();
            $table->year('first_year_awarded')->nullable();
            $table->string('category')->nullable(); // ej: literario, deportivo, etc.
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('awards');
    }
};
