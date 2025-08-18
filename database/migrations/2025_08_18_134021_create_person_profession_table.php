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
        Schema::create('person_profession', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained()->cascadeOnDelete();
            $table->foreignId('profession_id')->constrained()->cascadeOnDelete();
            $table->year('start_year')->nullable(); // Año de inicio en la profesión
            $table->year('end_year')->nullable(); // Año de fin (si ya no ejerce)
            $table->boolean('is_primary')->default(false); // Si es la profesión principal
            $table->boolean('is_current')->default(true); // Si actualmente ejerce
            $table->text('notes')->nullable(); // Notas adicionales
            $table->timestamps();
            
            $table->unique(['person_id', 'profession_id']); // Una persona no puede tener la misma profesión duplicada
            $table->index(['person_id', 'is_primary']);
            $table->index(['profession_id', 'is_current']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person_profession');
    }
};