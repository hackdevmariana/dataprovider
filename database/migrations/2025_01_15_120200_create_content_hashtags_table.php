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
        Schema::create('content_hashtags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hashtag_id')->constrained()->onDelete('cascade');
            $table->morphs('hashtaggable'); // hashtaggable_type + hashtaggable_id
            $table->foreignId('added_by')->constrained('users')->onDelete('cascade'); // Quien añadió el hashtag
            
            // Métricas de engagement por hashtag en contenido específico
            $table->integer('clicks_count')->default(0); // Clicks en el hashtag desde este contenido
            $table->decimal('relevance_score', 5, 2)->default(100); // Relevancia del hashtag para este contenido (0-100)
            
            // Validación automática vs manual
            $table->boolean('is_auto_generated')->default(false); // Si fue añadido automáticamente por IA
            $table->decimal('confidence_score', 5, 2)->nullable(); // Confianza de la IA (0-100)
            
            $table->timestamps();
            
            $table->unique(['hashtag_id', 'hashtaggable_type', 'hashtaggable_id'], 'ch_unique');
            $table->index(['hashtaggable_type', 'hashtaggable_id'], 'ch_morphs');
            $table->index(['added_by', 'created_at'], 'ch_added');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_hashtags');
    }
};
