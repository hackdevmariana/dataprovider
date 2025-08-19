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
        Schema::create('hashtags', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique(); // #energiasolar, #autoconsumo
            $table->string('slug', 100)->unique(); // energiasolar, autoconsumo
            $table->text('description')->nullable(); // Descripción del hashtag
            $table->string('color', 7)->default('#3B82F6'); // Color para visualización
            $table->string('icon')->nullable(); // Icono asociado
            
            // Categorización automática
            $table->enum('category', [
                'technology',      // #panelessolares, #inversores
                'legislation',     // #rd244, #autoconsumo
                'financing',       // #subvenciones, #ayudas
                'installation',    // #mantenimiento, #instalacion
                'cooperative',     // #cooperativa, #comunidadenergetica
                'market',         // #precioelectricidad, #mercado
                'diy',            // #hazlotumismo, #bricolaje
                'sustainability', // #sostenibilidad, #medioambiente
                'location',       // #madrid, #andalucia
                'general'         // #energia, #renovables
            ])->default('general');
            
            // Métricas de uso
            $table->integer('usage_count')->default(0); // Veces que se ha usado
            $table->integer('posts_count')->default(0); // Posts que lo usan
            $table->integer('followers_count')->default(0); // Usuarios que lo siguen
            $table->decimal('trending_score', 8, 2)->default(0); // Score de tendencia
            
            // Estados y moderación
            $table->boolean('is_trending')->default(false);
            $table->boolean('is_verified')->default(false); // Hashtag oficial/verificado
            $table->boolean('is_blocked')->default(false); // Bloqueado por spam/abuse
            $table->foreignId('created_by')->nullable()->constrained('users');
            
            // Configuración de sugerencias
            $table->json('related_hashtags')->nullable(); // Hashtags relacionados
            $table->json('synonyms')->nullable(); // Sinónimos del hashtag
            $table->boolean('auto_suggest')->default(true); // Si aparece en sugerencias
            
            $table->timestamps();
            
            $table->index(['category', 'usage_count']);
            $table->index(['is_trending', 'trending_score']);
            $table->index(['is_verified', 'usage_count']);
            $table->fullText(['name', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hashtags');
    }
};

