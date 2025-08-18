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
        Schema::create('person_work', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained()->cascadeOnDelete();
            $table->foreignId('work_id')->constrained()->cascadeOnDelete();
            $table->string('role', 60); // "actor", "director", "guionista", "presentador", "productor"
            $table->string('character_name', 120)->nullable(); // Para actores: "Don Quijote", "Tony Stark"
            $table->string('credited_as', 120)->nullable(); // Nombre con el que aparece acreditado
            $table->unsignedSmallInteger('billing_order')->nullable(); // 1 = protagonista, 2 = coprotagonista
            $table->decimal('contribution_pct', 5, 2)->nullable(); // Porcentaje de contribución (33.33% del guion)
            $table->boolean('is_primary')->default(false); // Rol principal cuando tiene varios
            $table->text('notes')->nullable(); // "Cameo sin acreditar", etc.
            $table->timestamps();
            
            $table->index(['person_id', 'work_id']);
            $table->index(['work_id', 'role']);
            $table->index(['billing_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person_work');
    }
};