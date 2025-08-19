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
        Schema::create('user_bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->morphs('bookmarkable'); // bookmarkable_type + bookmarkable_id
            
            // Organización en carpetas/colecciones
            $table->string('folder')->nullable(); // "Instalaciones", "Legislación", "Proyectos"
            $table->json('tags')->nullable(); // Etiquetas personalizadas
            $table->text('personal_notes')->nullable(); // Notas privadas del usuario
            $table->integer('priority')->default(0); // 0=normal, 1=importante, 2=urgente
            
            // Configuración de recordatorios
            $table->boolean('reminder_enabled')->default(false);
            $table->timestamp('reminder_date')->nullable();
            $table->enum('reminder_frequency', ['once', 'weekly', 'monthly'])->nullable();
            
            // Métricas
            $table->integer('access_count')->default(0); // Cuántas veces lo ha visitado
            $table->timestamp('last_accessed_at')->nullable();
            $table->boolean('is_public')->default(false); // Si es visible en perfil público
            
            $table->timestamps();
            
            $table->unique(['user_id', 'bookmarkable_type', 'bookmarkable_id']);
            $table->index(['user_id', 'folder']);
            $table->index(['user_id', 'priority', 'created_at']);
            $table->index(['reminder_enabled', 'reminder_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_bookmarks');
    }
};

