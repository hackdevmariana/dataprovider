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
        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // "Instalaciones Solares", "Legislación Energética"
            $table->string('slug')->unique(); // "instalaciones-solares"
            $table->text('description');
            $table->string('icon')->nullable(); // Icono del tema
            $table->string('color', 7)->default('#3B82F6'); // Color hexadecimal
            $table->string('banner_image')->nullable(); // Imagen de banner
            
            // Creador y moderación
            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
            $table->json('moderator_ids')->nullable(); // IDs de moderadores
            $table->json('rules')->nullable(); // Reglas específicas del tema
            
            // Configuración del tema
            $table->enum('visibility', ['public', 'private', 'restricted'])->default('public');
            $table->enum('post_permission', ['everyone', 'members', 'moderators'])->default('everyone');
            $table->enum('comment_permission', ['everyone', 'members', 'verified'])->default('everyone');
            
            // Categorización
            $table->enum('category', [
                'technology',      // Tecnología (paneles, inversores, baterías)
                'legislation',     // Legislación y normativas
                'financing',       // Financiación y ayudas
                'installation',    // Instalación y mantenimiento
                'cooperative',     // Cooperativas energéticas
                'market',         // Mercado energético
                'diy',            // Hazlo tú mismo
                'news',           // Noticias del sector
                'beginners',      // Principiantes
                'professional',   // Profesionales
                'regional',       // Específico regional
                'general'         // General
            ]);
            
            // Métricas
            $table->integer('members_count')->default(0);
            $table->integer('posts_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->decimal('activity_score', 8, 2)->default(0); // Score de actividad
            
            // Estados
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_approval')->default(false); // Posts necesitan aprobación
            $table->boolean('allow_polls')->default(true);
            $table->boolean('allow_images')->default(true);
            $table->boolean('allow_links')->default(true);
            
            $table->timestamps();
            
            $table->index(['category', 'is_active']);
            $table->index(['visibility', 'is_active']);
            $table->index(['activity_score', 'created_at']);
            $table->fullText(['name', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topics');
    }
};
