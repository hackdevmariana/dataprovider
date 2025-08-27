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
            
            // Información básica del tema
            $table->string('name'); // "Instalaciones Solares", "Legislación Energética"
            $table->string('slug')->unique(); // "instalaciones-solares"
            $table->text('description');
            $table->text('rules')->nullable(); // Reglas específicas del tema
            $table->text('welcome_message')->nullable(); // Mensaje de bienvenida
            
            // Apariencia visual
            $table->string('icon')->nullable(); // Icono del tema (emoji o clase CSS)
            $table->string('color', 7)->default('#3B82F6'); // Color hexadecimal
            $table->string('banner_image')->nullable(); // URL de imagen de banner
            $table->string('avatar_image')->nullable(); // Avatar del tema
            
            // Creador y moderación
            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
            $table->json('moderator_ids')->nullable(); // IDs de moderadores adicionales
            $table->json('admin_ids')->nullable(); // IDs de administradores
            $table->json('banned_user_ids')->nullable(); // IDs de usuarios baneados
            
            // Configuración de acceso
            $table->enum('visibility', [
                'public',               // Visible para todos
                'private',              // Solo miembros pueden ver
                'restricted',           // Requiere aprobación para unirse
                'invite_only',          // Solo por invitación
                'archived'              // Archivado, solo lectura
            ])->default('public');
            
            $table->enum('join_policy', [
                'open',                 // Cualquiera puede unirse
                'approval_required',    // Requiere aprobación del moderador
                'invite_only',          // Solo por invitación
                'closed'                // No se permiten nuevos miembros
            ])->default('open');
            
            // Permisos de contenido
            $table->enum('post_permission', [
                'everyone',             // Cualquiera puede postear
                'members',              // Solo miembros
                'approved_members',     // Solo miembros aprobados
                'moderators',           // Solo moderadores
                'creator_only'          // Solo el creador
            ])->default('members');
            
            $table->enum('comment_permission', [
                'everyone',             // Cualquiera puede comentar
                'members',              // Solo miembros
                'verified',             // Solo usuarios verificados
                'moderators'            // Solo moderadores
            ])->default('members');
            
            // Categorización energética
            $table->enum('category', [
                'technology',           // Tecnología (paneles, inversores, baterías)
                'legislation',          // Legislación y normativas
                'financing',            // Financiación y ayudas
                'installation',         // Instalación y mantenimiento
                'cooperative',          // Cooperativas energéticas
                'market',              // Mercado energético
                'efficiency',          // Eficiencia energética
                'diy',                 // Hazlo tú mismo
                'news',                // Noticias del sector
                'beginners',           // Principiantes
                'professional',        // Profesionales
                'regional',            // Específico regional
                'research',            // Investigación y desarrollo
                'storage',             // Almacenamiento energético
                'grid',                // Red eléctrica
                'policy',              // Políticas energéticas
                'sustainability',      // Sostenibilidad
                'innovation',          // Innovación
                'general'              // General
            ])->default('general');
            
            $table->enum('difficulty_level', [
                'beginner',            // Principiante
                'intermediate',        // Intermedio
                'advanced',            // Avanzado
                'expert'               // Experto
            ])->default('beginner');
            
            // Configuración de funcionalidades
            $table->boolean('requires_approval')->default(false); // Posts necesitan aprobación
            $table->boolean('allow_polls')->default(true);
            $table->boolean('allow_images')->default(true);
            $table->boolean('allow_videos')->default(true);
            $table->boolean('allow_links')->default(true);
            $table->boolean('allow_files')->default(false);
            $table->boolean('allow_anonymous_posts')->default(false);
            $table->boolean('enable_wiki')->default(false); // Habilitar wiki colaborativa
            $table->boolean('enable_events')->default(false); // Habilitar eventos
            $table->boolean('enable_marketplace')->default(false); // Habilitar marketplace
            
            // Métricas de actividad
            $table->integer('members_count')->default(0);
            $table->integer('posts_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->integer('views_count')->default(0);
            $table->integer('active_users_count')->default(0); // Usuarios activos últimos 30 días
            $table->decimal('activity_score', 10, 2)->default(0); // Score de actividad calculado
            $table->decimal('quality_score', 8, 2)->default(100); // Score de calidad del contenido
            
            // Métricas de engagement
            $table->integer('likes_received')->default(0);
            $table->integer('shares_received')->default(0);
            $table->integer('bookmarks_received')->default(0);
            $table->decimal('avg_post_score', 8, 2)->default(0);
            $table->integer('featured_posts_count')->default(0);
            
            // Estados y configuración
            $table->boolean('is_featured')->default(false); // Destacado en la página principal
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false); // Tema verificado oficialmente
            $table->boolean('is_trending')->default(false); // Trending actualmente
            $table->boolean('is_nsfw')->default(false); // Contenido no apto para trabajo
            $table->boolean('auto_archive_inactive')->default(false); // Auto-archivar si inactivo
            
            // Configuración de notificaciones
            $table->boolean('notify_new_posts')->default(true);
            $table->boolean('notify_trending_posts')->default(true);
            $table->json('notification_settings')->nullable(); // Configuraciones avanzadas
            
            // Información temporal
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('last_post_at')->nullable();
            $table->integer('days_since_creation')->default(0);
            $table->integer('peak_members_count')->default(0);
            $table->timestamp('peak_activity_at')->nullable();
            
            // Configuración de algoritmo
            $table->decimal('trending_score', 10, 2)->default(0);
            $table->json('algorithm_weights')->nullable(); // Pesos personalizados para algoritmo
            $table->json('custom_fields')->nullable(); // Campos personalizados por categoría
            
            // SEO y descubrimiento
            $table->json('tags')->nullable(); // Etiquetas para búsqueda
            $table->json('related_topics')->nullable(); // IDs de temas relacionados
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            
            $table->timestamps();
            
            // Índices para optimización
            $table->index(['category', 'is_active', 'visibility']);
            $table->index(['visibility', 'is_active', 'activity_score']);
            $table->index(['is_featured', 'activity_score']);
            $table->index(['is_trending', 'trending_score']);
            $table->index(['creator_id', 'created_at']);
            $table->index(['last_activity_at', 'is_active']);
            $table->index(['members_count', 'is_active']);
            $table->index(['difficulty_level', 'category']);
            $table->fullText(['name', 'description', 'meta_description']);
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
