<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Tema de discusión especializado (estilo subreddit energético).
 * 
 * Representa un tema específico donde los usuarios pueden crear posts,
 * comentar, y participar en discusiones organizadas por categorías energéticas.
 * 
 * @property int $id
 * @property string $name Nombre del tema
 * @property string $slug Slug único para URLs
 * @property string $description Descripción del tema
 * @property string|null $icon Icono del tema
 * @property string $color Color hexadecimal
 * @property string|null $banner_image Imagen de banner
 * @property int $creator_id Usuario creador
 * @property array|null $moderator_ids IDs de moderadores
 * @property array|null $rules Reglas específicas
 * @property string $visibility Visibilidad del tema
 * @property string $post_permission Permisos para posts
 * @property string $comment_permission Permisos para comentarios
 * @property string $category Categoría energética
 * @property int $members_count Número de miembros
 * @property int $posts_count Número de posts
 * @property int $comments_count Número de comentarios
 * @property float $activity_score Score de actividad
 * @property bool $is_featured Si está destacado
 * @property bool $is_active Si está activo
 * @property bool $requires_approval Si posts necesitan aprobación
 * @property bool $allow_polls Si permite encuestas
 * @property bool $allow_images Si permite imágenes
 * @property bool $allow_links Si permite enlaces
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * 
 * @property-read \App\Models\User $creator Usuario creador
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TopicPost[] $posts Posts del tema
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TopicMembership[] $memberships Membresías
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $members Miembros del tema
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $followers Seguidores del tema
 */
class Topic extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'banner_image',
        'creator_id',
        'moderator_ids',
        'rules',
        'visibility',
        'post_permission',
        'comment_permission',
        'category',
        'members_count',
        'posts_count',
        'comments_count',
        'activity_score',
        'is_featured',
        'is_active',
        'requires_approval',
        'allow_polls',
        'allow_images',
        'allow_links',
    ];

    protected $casts = [
        'moderator_ids' => 'array',
        'rules' => 'array',
        'activity_score' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'requires_approval' => 'boolean',
        'allow_polls' => 'boolean',
        'allow_images' => 'boolean',
        'allow_links' => 'boolean',
    ];

    /**
     * Usuario que creó el tema.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Posts del tema.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(TopicPost::class);
    }

    /**
     * Membresías del tema.
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(TopicMembership::class);
    }

    /**
     * Miembros del tema.
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'topic_memberships')
                    ->withPivot([
                        'role',
                        'status',
                        'notifications_enabled',
                        'email_notifications',
                        'notification_preferences',
                        'posts_count',
                        'comments_count',
                        'reputation_score',
                        'last_activity_at'
                    ])
                    ->withTimestamps();
    }

    /**
     * Usuarios que siguen el tema.
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'topic_following')
                    ->withPivot('notification_level')
                    ->withTimestamps();
    }

    /**
     * Obtener moderadores del tema.
     */
    public function moderators()
    {
        return $this->members()->wherePivot('role', 'moderator');
    }

    /**
     * Obtener administradores del tema.
     */
    public function admins()
    {
        return $this->members()->wherePivot('role', 'admin');
    }

    /**
     * Posts destacados del tema.
     */
    public function featuredPosts()
    {
        return $this->posts()->where('is_featured', true);
    }

    /**
     * Posts fijados del tema.
     */
    public function pinnedPosts()
    {
        return $this->posts()->where('is_pinned', true);
    }

    /**
     * Verificar si un usuario es moderador del tema.
     */
    public function isModerator(User $user): bool
    {
        return $this->members()
                    ->where('user_id', $user->id)
                    ->wherePivotIn('role', ['moderator', 'admin'])
                    ->exists();
    }

    /**
     * Verificar si un usuario es miembro del tema.
     */
    public function isMember(User $user): bool
    {
        return $this->members()
                    ->where('user_id', $user->id)
                    ->exists();
    }

    /**
     * Obtener posts recientes del tema.
     */
    public function recentPosts()
    {
        return $this->posts()
                    ->where('status', 'published')
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Obtener posts populares del tema.
     */
    public function popularPosts()
    {
        return $this->posts()
                    ->where('status', 'published')
                    ->orderBy('engagement_score', 'desc');
    }

    /**
     * Incrementar contador de posts.
     */
    public function incrementPostsCount(): void
    {
        $this->increment('posts_count');
    }

    /**
     * Incrementar contador de comentarios.
     */
    public function incrementCommentsCount(): void
    {
        $this->increment('comments_count');
    }

    /**
     * Incrementar contador de miembros.
     */
    public function incrementMembersCount(): void
    {
        $this->increment('members_count');
    }

    /**
     * Decrementar contador de miembros.
     */
    public function decrementMembersCount(): void
    {
        $this->decrement('members_count');
    }

    /**
     * Calcular y actualizar score de actividad.
     */
    public function updateActivityScore(): void
    {
        // Algoritmo simple: posts recientes + comentarios + miembros activos
        $recentPosts = $this->posts()
                           ->where('created_at', '>=', now()->subDays(7))
                           ->count();
        
        $recentComments = TopicComment::whereIn('topic_post_id', $this->posts->pluck('id'))
                                     ->where('created_at', '>=', now()->subDays(7))
                                     ->count();
        
        $activeMembers = $this->memberships()
                             ->where('last_activity_at', '>=', now()->subDays(30))
                             ->count();

        $score = ($recentPosts * 10) + ($recentComments * 2) + ($activeMembers * 5);
        
        $this->update(['activity_score' => $score]);
    }
}
