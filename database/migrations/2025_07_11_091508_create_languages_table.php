<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('language');       // Ej: "Spanish"
            $table->string('slug')->unique(); // Ej: "spanish"
            $table->string('native_name')->nullable(); // Ej: "español"
            $table->string('iso_639_1', 2)->nullable(); // Ej: "es"
            $table->string('iso_639_2', 3)->nullable(); // Ej: "spa"
            $table->boolean('rtl')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('languages');
    }
};
