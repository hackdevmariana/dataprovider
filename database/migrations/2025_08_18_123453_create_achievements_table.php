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
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('icon')->nullable(); // Nombre del icono o URL
            $table->string('badge_color', 7)->default('#22C55E'); // Color del badge en HEX
            $table->enum('category', [
                'energy_saving',     // Ahorro energético
                'solar_production',  // Producción solar
                'cooperation',       // Cooperativismo
                'sustainability',    // Sostenibilidad
                'engagement',        // Participación
                'milestone',         // Hitos importantes
                'streak',           // Rachas
                'community'         // Comunidad
            ]);
            $table->enum('type', [
                'single',           // Se obtiene una vez
                'progressive',      // Niveles progresivos
                'recurring'         // Se puede obtener repetidamente
            ])->default('single');
            $table->enum('difficulty', [
                'bronze',
                'silver', 
                'gold',
                'platinum',
                'legendary'
            ])->default('bronze');
            $table->json('conditions')->nullable(); // Condiciones para obtener el logro
            $table->integer('points')->default(10); // Puntos que otorga
            $table->integer('required_value')->nullable(); // Valor requerido (ej: 100 kWh)
            $table->string('required_unit')->nullable(); // Unidad (kWh, EUR, días, etc.)
            $table->boolean('is_active')->default(true);
            $table->boolean('is_hidden')->default(false); // Logros secretos
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['category', 'is_active']);
            $table->index(['difficulty', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};