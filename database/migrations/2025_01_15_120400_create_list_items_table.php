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
        Schema::create('list_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_list_id')->constrained()->onDelete('cascade');
            $table->morphs('listable'); // listable_type + listable_id
            $table->foreignId('added_by')->constrained('users')->onDelete('cascade');
            
            // Organización dentro de la lista
            $table->integer('position')->default(0); // Orden en la lista
            $table->text('personal_note')->nullable(); // Nota personal sobre el elemento
            $table->json('tags')->nullable(); // Tags personalizados para este elemento
            $table->decimal('personal_rating', 3, 1)->nullable(); // Rating personal (1.0-5.0)
            
            // Modo de adición
            $table->enum('added_mode', [
                'manual',          // Añadido manualmente
                'auto_hashtag',    // Auto-añadido por hashtag
                'auto_keyword',    // Auto-añadido por palabra clave
                'auto_author',     // Auto-añadido por autor
                'suggested',       // Sugerido por otro usuario
                'imported'         // Importado de otra lista
            ])->default('manual');
            
            // Validación y moderación
            $table->enum('status', ['active', 'pending', 'rejected', 'archived'])->default('active');
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            
            // Métricas de engagement del elemento en la lista
            $table->integer('clicks_count')->default(0); // Clicks desde la lista
            $table->integer('likes_count')->default(0); // Likes específicos en la lista
            $table->timestamp('last_accessed_at')->nullable();
            
            $table->timestamps();
            
            $table->unique(['user_list_id', 'listable_type', 'listable_id']);
            $table->index(['user_list_id', 'position']);
            $table->index(['added_by', 'created_at']);
            $table->index(['status', 'added_mode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('list_items');
    }
};

