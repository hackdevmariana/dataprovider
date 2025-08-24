<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'rules',
        'welcome_message',
        'icon',
        'color',
        'banner_image',
        'avatar_image',
        'creator_id',
        'moderator_ids',
        'admin_ids',
        'banned_user_ids',
        'visibility',
        'join_policy',
        'post_permission',
        'comment_permission',
        'category',
        'difficulty_level',
        'requires_approval',
        'allow_polls',
        'allow_images',
        'allow_videos',
        'allow_links',
        'allow_files',
        'allow_anonymous_posts',
        'enable_wiki',
        'enable_events',
        'enable_marketplace',
        'members_count',
        'posts_count',
        'comments_count',
        'views_count',
        'active_users_count',
        'activity_score',
        'quality_score',
        'likes_received',
        'shares_received',
        'bookmarks_received',
        'avg_post_score',
        'featured_posts_count',
        'is_featured',
        'is_active',
        'is_verified',
        'is_trending',
        'is_nsfw',
        'auto_archive_inactive',
        'notify_new_posts',
        'notify_trending_posts',
        'notification_settings',
        'last_activity_at',
        'last_post_at',
        'days_since_creation',
        'peak_members_count',
        'peak_activity_at',
        'trending_score',
        'algorithm_weights',
        'custom_fields',
        'tags',
        'related_topics',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'moderator_ids' => 'array',
        'admin_ids' => 'array',
        'banned_user_ids' => 'array',
        'notification_settings' => 'array',
        'algorithm_weights' => 'array',
        'custom_fields' => 'array',
        'tags' => 'array',
        'related_topics' => 'array',
        'last_activity_at' => 'datetime',
        'last_post_at' => 'datetime',
        'peak_activity_at' => 'datetime',
        'activity_score' => 'decimal:2',
        'quality_score' => 'decimal:2',
        'avg_post_score' => 'decimal:2',
        'trending_score' => 'decimal:2',
        'requires_approval' => 'boolean',
        'allow_polls' => 'boolean',
        'allow_images' => 'boolean',
        'allow_videos' => 'boolean',
        'allow_links' => 'boolean',
        'allow_files' => 'boolean',
        'allow_anonymous_posts' => 'boolean',
        'enable_wiki' => 'boolean',
        'enable_events' => 'boolean',
        'enable_marketplace' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'is_trending' => 'boolean',
        'is_nsfw' => 'boolean',
        'auto_archive_inactive' => 'boolean',
        'notify_new_posts' => 'boolean',
        'notify_trending_posts' => 'boolean',
    ];

    // Relaciones

    /**
     * Usuario creador del tema
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Membresías del tema
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(TopicMembership::class);
    }

    /**
     * Miembros del tema
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'topic_memberships')
                    ->withPivot([
                        'role',
                        'status',
                        'reputation_score',
                        'posts_count',
                        'comments_count',
                        'joined_at',
                        'last_activity_at'
                    ])
                    ->withTimestamps();
    }

    /**
     * Miembros activos
     */
    public function activeMembers(): BelongsToMany
    {
        return $this->members()->wherePivot('status', 'active');
    }

    /**
     * Moderadores del tema
     */
    public function moderators(): BelongsToMany
    {
        return $this->members()->whereIn('role', ['moderator', 'admin']);
    }

    /**
     * Posts del tema
     */
    public function posts(): HasMany
    {
        return $this->hasMany(TopicPost::class);
    }

    /**
     * Posts publicados
     */
    public function publishedPosts(): HasMany
    {
        return $this->posts()->where('status', 'published');
    }

    /**
     * Posts destacados
     */
    public function featuredPosts(): HasMany
    {
        return $this->posts()->where('is_featured', true)->where('status', 'published');
    }

    /**
     * Posts fijados
     */
    public function pinnedPosts(): HasMany
    {
        return $this->posts()->where('is_pinned', true)->where('status', 'published');
    }

    /**
     * Comentarios en el tema (a través de posts)
     */
    public function comments(): HasManyThrough
    {
        return $this->hasManyThrough(TopicComment::class, TopicPost::class);
    }

    // Scopes para consultas

    /**
     * Temas activos
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Temas públicos
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('visibility', 'public');
    }

    /**
     * Temas destacados
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Temas trending
     */
    public function scopeTrending(Builder $query): Builder
    {
        return $query->where('is_trending', true)
                    ->orderByDesc('trending_score')
                    ->orderByDesc('activity_score');
    }

    /**
     * Temas por categoría
     */
    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Temas por nivel de dificultad
     */
    public function scopeByDifficulty(Builder $query, string $level): Builder
    {
        return $query->where('difficulty_level', $level);
    }

    /**
     * Temas con alta actividad
     */
    public function scopeHighActivity(Builder $query, float $minScore = 50.0): Builder
    {
        return $query->where('activity_score', '>=', $minScore);
    }

    /**
     * Temas populares (por número de miembros)
     */
    public function scopePopular(Builder $query, int $minMembers = 10): Builder
    {
        return $query->where('members_count', '>=', $minMembers)
                    ->orderByDesc('members_count');
    }

    /**
     * Temas recientes
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Temas con actividad reciente
     */
    public function scopeRecentActivity(Builder $query, int $hours = 24): Builder
    {
        return $query->where('last_activity_at', '>=', now()->subHours($hours));
    }

    /**
     * Búsqueda de temas
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($query) use ($term) {
            $query->where('name', 'LIKE', "%{$term}%")
                  ->orWhere('description', 'LIKE', "%{$term}%")
                  ->orWhereJsonContains('tags', $term);
        });
    }

    /**
     * Temas accesibles para un usuario
     */
    public function scopeAccessibleFor(Builder $query, ?User $user = null): Builder
    {
        return $query->where(function ($query) use ($user) {
            $query->where('visibility', 'public');
            
            if ($user) {
                $query->orWhere(function ($query) use ($user) {
                    // Temas privados donde el usuario es miembro
                    $query->where('visibility', 'private')
                          ->whereHas('members', function ($q) use ($user) {
                              $q->where('user_id', $user->id)
                                ->where('status', 'active');
                          });
                })
                ->orWhere('creator_id', $user->id); // Propios temas
            }
        });
    }

    // Métodos auxiliares

    /**
     * Generar slug único
     */
    public static function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $count = 0;
        $originalSlug = $slug;
        
        while (static::where('slug', $slug)->exists()) {
            $count++;
            $slug = $originalSlug . '-' . $count;
        }
        
        return $slug;
    }

    /**
     * Verificar si un usuario puede ver este tema
     */
    public function canBeViewedBy(?User $user = null): bool
    {
        if (!$this->is_active) {
            return false;
        }

        return match ($this->visibility) {
            'public' => true,
            'private', 'restricted', 'invite_only' => $user && (
                $this->creator_id === $user->id ||
                $this->isMember($user) ||
                $this->isModerator($user)
            ),
            'archived' => $user && (
                $this->creator_id === $user->id ||
                $this->isModerator($user)
            ),
            default => false,
        };
    }

    /**
     * Verificar si un usuario puede postear en este tema
     */
    public function canPostBy(?User $user = null): bool
    {
        if (!$user || !$this->canBeViewedBy($user)) {
            return false;
        }

        return match ($this->post_permission) {
            'everyone' => true,
            'members' => $this->isMember($user),
            'approved_members' => $this->isApprovedMember($user),
            'moderators' => $this->isModerator($user),
            'creator_only' => $this->creator_id === $user->id,
            default => false,
        };
    }

    /**
     * Verificar si un usuario puede comentar en este tema
     */
    public function canCommentBy(?User $user = null): bool
    {
        if (!$user || !$this->canBeViewedBy($user)) {
            return false;
        }

        return match ($this->comment_permission) {
            'everyone' => true,
            'members' => $this->isMember($user),
            'verified' => $user->hasVerifiedEmail(),
            'moderators' => $this->isModerator($user),
            default => false,
        };
    }

    /**
     * Verificar si un usuario es miembro
     */
    public function isMember(User $user): bool
    {
        return $this->members()
                    ->wherePivot('user_id', $user->id)
                    ->wherePivot('status', 'active')
                    ->exists();
    }

    /**
     * Verificar si un usuario es miembro aprobado
     */
    public function isApprovedMember(User $user): bool
    {
        $membership = $this->memberships()
                          ->where('user_id', $user->id)
                          ->where('status', 'active')
                          ->first();

        return $membership && in_array($membership->role, ['contributor', 'moderator', 'admin']);
    }

    /**
     * Verificar si un usuario es moderador
     */
    public function isModerator(User $user): bool
    {
        if ($this->creator_id === $user->id) {
            return true;
        }

        return $this->members()
                    ->wherePivot('user_id', $user->id)
                    ->whereIn('role', ['moderator', 'admin'])
                    ->exists();
    }

    /**
     * Añadir miembro al tema
     */
    public function addMember(User $user, string $role = 'member'): TopicMembership
    {
        return TopicMembership::firstOrCreate([
            'topic_id' => $this->id,
            'user_id' => $user->id,
        ], [
            'role' => $role,
            'status' => $this->join_policy === 'approval_required' ? 'pending' : 'active',
            'joined_at' => now(),
        ]);
    }

    /**
     * Remover miembro del tema
     */
    public function removeMember(User $user): bool
    {
        return $this->memberships()
                    ->where('user_id', $user->id)
                    ->delete() > 0;
    }

    /**
     * Actualizar contadores de actividad
     */
    public function updateActivityCounters(): void
    {
        $this->update([
            'members_count' => $this->activeMembers()->count(),
            'posts_count' => $this->publishedPosts()->count(),
            'comments_count' => $this->comments()->where('status', 'published')->count(),
            'last_activity_at' => $this->posts()->latest('created_at')->first()?->created_at ?? now(),
            'last_post_at' => $this->posts()->latest('created_at')->first()?->created_at,
        ]);
    }

    /**
     * Calcular score de actividad
     */
    public function calculateActivityScore(): float
    {
        $score = 0;

        // Factor de posts recientes (últimos 7 días)
        $recentPosts = $this->posts()
                           ->where('created_at', '>=', now()->subDays(7))
                           ->count();
        $score += $recentPosts * 10;

        // Factor de comentarios recientes
        $recentComments = $this->comments()
                              ->where('topic_comments.created_at', '>=', now()->subDays(7))
                              ->count();
        $score += $recentComments * 2;

        // Factor de miembros activos
        $activeMembers = $this->memberships()
                             ->where('last_activity_at', '>=', now()->subDays(30))
                             ->count();
        $score += $activeMembers * 5;

        // Factor de engagement
        $score += $this->likes_received * 0.5;
        $score += $this->shares_received * 2;
        $score += $this->bookmarks_received * 1;

        // Factor de calidad
        $score *= ($this->quality_score / 100);

        return round($score, 2);
    }

    /**
     * Actualizar score de actividad
     */
    public function updateActivityScore(): void
    {
        $this->update(['activity_score' => $this->calculateActivityScore()]);
    }

    /**
     * Obtener estadísticas del tema
     */
    public function getStats(): array
    {
        return [
            'members_count' => $this->members_count,
            'posts_count' => $this->posts_count,
            'comments_count' => $this->comments_count,
            'activity_score' => $this->activity_score,
            'avg_post_score' => $this->avg_post_score,
            'posts_today' => $this->posts()->whereDate('created_at', today())->count(),
            'posts_this_week' => $this->posts()->whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'active_members_30d' => $this->memberships()
                ->where('last_activity_at', '>=', now()->subDays(30))
                ->count(),
            'top_contributors' => $this->memberships()
                ->orderByDesc('reputation_score')
                ->limit(5)
                ->with('user:id,name,email')
                ->get(),
        ];
    }

    /**
     * Obtener etiqueta legible de la categoría
     */
    public function getCategoryLabel(): string
    {
        return match ($this->category) {
            'technology' => 'Tecnología',
            'legislation' => 'Legislación',
            'financing' => 'Financiación',
            'installation' => 'Instalación',
            'cooperative' => 'Cooperativas',
            'market' => 'Mercado',
            'efficiency' => 'Eficiencia',
            'diy' => 'Hazlo Tú Mismo',
            'news' => 'Noticias',
            'beginners' => 'Principiantes',
            'professional' => 'Profesionales',
            'regional' => 'Regional',
            'research' => 'Investigación',
            'storage' => 'Almacenamiento',
            'grid' => 'Red Eléctrica',
            'policy' => 'Políticas',
            'sustainability' => 'Sostenibilidad',
            'innovation' => 'Innovación',
            default => 'General',
        };
    }

    /**
     * Obtener icono por defecto según la categoría
     */
    public function getDefaultIcon(): string
    {
        return match ($this->category) {
            'technology' => '⚙️',
            'legislation' => '📜',
            'financing' => '💰',
            'installation' => '🔧',
            'cooperative' => '🤝',
            'market' => '📈',
            'efficiency' => '💡',
            'diy' => '🛠️',
            'news' => '📰',
            'beginners' => '🌱',
            'professional' => '👔',
            'regional' => '🌍',
            'research' => '🔬',
            'storage' => '🔋',
            'grid' => '⚡',
            'policy' => '🏛️',
            'sustainability' => '♻️',
            'innovation' => '🚀',
            default => '💬',
        };
    }

    // Eventos del modelo

    protected static function booted()
    {
        // Generar slug automáticamente
        static::creating(function (Topic $topic) {
            if (empty($topic->slug)) {
                $topic->slug = static::generateUniqueSlug($topic->name);
            }
            
            if (empty($topic->icon)) {
                $topic->icon = $topic->getDefaultIcon();
            }
        });

        // Actualizar contadores al crear posts/comentarios
        static::updated(function (Topic $topic) {
            if ($topic->isDirty('posts_count') || $topic->isDirty('comments_count')) {
                $topic->updateActivityScore();
            }
        });
    }
}