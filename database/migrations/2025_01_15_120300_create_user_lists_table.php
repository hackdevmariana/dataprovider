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
        Schema::create('user_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // "Instaladores de confianza", "Proyectos interesantes"
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color', 7)->default('#3B82F6');
            $table->string('cover_image')->nullable();
            
            // Tipo de lista y contenido permitido
            $table->enum('list_type', [
                'mixed',           // Contenido mixto
                'users',           // Solo usuarios
                'posts',           // Solo posts
                'projects',        // Solo proyectos
                'companies',       // Solo empresas/cooperativas
                'resources',       // Solo recursos/enlaces
                'events',          // Solo eventos
                'custom'           // Personalizado
            ])->default('mixed');
            
            $table->json('allowed_content_types')->nullable(); // Tipos de contenido específicos permitidos
            
            // Configuración de privacidad y colaboración
            $table->enum('visibility', ['private', 'public', 'followers', 'collaborative'])->default('private');
            $table->json('collaborator_ids')->nullable(); // IDs de usuarios que pueden editar
            $table->boolean('allow_suggestions')->default(false); // Si otros pueden sugerir elementos
            $table->boolean('allow_comments')->default(false); // Si permite comentarios en la lista
            
            // Configuración de curación
            $table->enum('curation_mode', [
                'manual',          // Solo añadido manualmente
                'auto_hashtag',    // Auto-añadir por hashtags
                'auto_keyword',    // Auto-añadir por palabras clave
                'auto_author',     // Auto-añadir por autores específicos
                'auto_topic'       // Auto-añadir por temas
            ])->default('manual');
            
            $table->json('auto_criteria')->nullable(); // Criterios para auto-curación
            
            // Métricas
            $table->integer('items_count')->default(0);
            $table->integer('followers_count')->default(0);
            $table->integer('views_count')->default(0);
            $table->integer('shares_count')->default(0);
            $table->decimal('engagement_score', 8, 2)->default(0);
            
            // Estados
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_template')->default(false); // Si puede usarse como plantilla
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            $table->unique(['user_id', 'slug']);
            $table->index(['user_id', 'list_type', 'visibility']);
            $table->index(['visibility', 'is_featured', 'engagement_score']);
            $table->index(['list_type', 'is_active']);
            $table->fullText(['name', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_lists');
    }
};

