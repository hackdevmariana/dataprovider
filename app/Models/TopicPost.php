<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Post dentro de un tema de discusión.
 * 
 * Representa un hilo de discusión dentro de un tema específico,
 * con soporte para diferentes tipos de contenido y engagement.
 * 
 * @property int $id
 * @property int $topic_id Tema al que pertenece
 * @property int $author_id Usuario autor
 * @property string $title Título del post
 * @property string $content Contenido del post
 * @property string $type Tipo de post
 * @property array|null $images URLs de imágenes
 * @property array|null $attachments Documentos adjuntos
 * @property array|null $links Enlaces externos
 * @property array|null $poll_data Datos de encuesta
 * @property array|null $tags Tags específicos
 * @property string|null $difficulty_level Nivel de dificultad
 * @property float|null $estimated_cost Coste estimado
 * @property string|null $location Ubicación
 * @property string $status Estado del post
 * @property int|null $approved_by Usuario que aprobó
 * @property \Carbon\Carbon|null $approved_at Fecha de aprobación
 * @property string|null $rejection_reason Razón de rechazo
 * @property int $views_count Visualizaciones
 * @property int $likes_count Likes
 * @property int $comments_count Comentarios
 * @property int $shares_count Compartidos
 * @property int $bookmarks_count Bookmarks
 * @property float $engagement_score Score de engagement
 * @property bool $is_pinned Si está fijado
 * @property bool $is_locked Si está bloqueado
 * @property bool $is_featured Si está destacado
 * @property bool $allow_comments Si permite comentarios
 * @property bool $notify_on_comment Si notifica comentarios
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * 
 * @property-read \App\Models\Topic $topic Tema
 * @property-read \App\Models\User $author Usuario autor
 * @property-read \App\Models\User|null $approver Usuario que aprobó
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TopicComment[] $comments Comentarios
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ContentVote[] $votes Votos
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserBookmark[] $bookmarks Bookmarks
 */
class TopicPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'author_id',
        'title',
        'content',
        'type',
        'images',
        'attachments',
        'links',
        'poll_data',
        'tags',
        'difficulty_level',
        'estimated_cost',
        'location',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'views_count',
        'likes_count',
        'comments_count',
        'shares_count',
        'bookmarks_count',
        'engagement_score',
        'is_pinned',
        'is_locked',
        'is_featured',
        'allow_comments',
        'notify_on_comment',
    ];

    protected $casts = [
        'images' => 'array',
        'attachments' => 'array',
        'links' => 'array',
        'poll_data' => 'array',
        'tags' => 'array',
        'estimated_cost' => 'decimal:2',
        'engagement_score' => 'decimal:2',
        'approved_at' => 'datetime',
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'is_featured' => 'boolean',
        'allow_comments' => 'boolean',
        'notify_on_comment' => 'boolean',
    ];

    /**
     * Tema al que pertenece el post.
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Usuario autor del post.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Usuario que aprobó el post.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Comentarios del post.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(TopicComment::class);
    }

    /**
     * Comentarios de nivel superior (no respuestas).
     */
    public function topLevelComments(): HasMany
    {
        return $this->hasMany(TopicComment::class)->whereNull('parent_id');
    }

    /**
     * Votos del post.
     */
    public function votes(): MorphMany
    {
        return $this->morphMany(ContentVote::class, 'votable');
    }

    /**
     * Bookmarks del post.
     */
    public function bookmarks(): MorphMany
    {
        return $this->morphMany(UserBookmark::class, 'bookmarkable');
    }

    /**
     * Hashtags del post.
     */
    public function hashtags(): MorphMany
    {
        return $this->morphMany(ContentHashtag::class, 'hashtaggable');
    }

    /**
     * Incrementar contador de vistas.
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
        $this->updateEngagementScore();
    }

    /**
     * Incrementar contador de likes.
     */
    public function incrementLikes(): void
    {
        $this->increment('likes_count');
        $this->updateEngagementScore();
    }

    /**
     * Decrementar contador de likes.
     */
    public function decrementLikes(): void
    {
        $this->decrement('likes_count');
        $this->updateEngagementScore();
    }

    /**
     * Incrementar contador de comentarios.
     */
    public function incrementComments(): void
    {
        $this->increment('comments_count');
        $this->topic->incrementCommentsCount();
        $this->updateEngagementScore();
    }

    /**
     * Incrementar contador de compartidos.
     */
    public function incrementShares(): void
    {
        $this->increment('shares_count');
        $this->updateEngagementScore();
    }

    /**
     * Incrementar contador de bookmarks.
     */
    public function incrementBookmarks(): void
    {
        $this->increment('bookmarks_count');
        $this->updateEngagementScore();
    }

    /**
     * Calcular y actualizar score de engagement.
     */
    public function updateEngagementScore(): void
    {
        // Algoritmo de engagement: vistas + likes*5 + comentarios*10 + shares*15 + bookmarks*8
        $score = $this->views_count + 
                ($this->likes_count * 5) + 
                ($this->comments_count * 10) + 
                ($this->shares_count * 15) + 
                ($this->bookmarks_count * 8);
        
        $this->update(['engagement_score' => $score]);
    }

    /**
     * Verificar si el usuario puede editar el post.
     */
    public function canEdit(User $user): bool
    {
        // El autor puede editar su propio post
        if ($this->author_id === $user->id) {
            return true;
        }

        // Los moderadores del tema pueden editar
        return $this->topic->isModerator($user);
    }

    /**
     * Verificar si el usuario puede eliminar el post.
     */
    public function canDelete(User $user): bool
    {
        // El autor puede eliminar su propio post
        if ($this->author_id === $user->id) {
            return true;
        }

        // Los moderadores del tema pueden eliminar
        return $this->topic->isModerator($user);
    }

    /**
     * Marcar post como solución (para posts tipo question).
     */
    public function markAsSolution(): void
    {
        if ($this->type === 'question') {
            // Buscar comentario marcado como solución
            $solutionComment = $this->comments()->where('is_solution', true)->first();
            if ($solutionComment) {
                $this->update(['status' => 'solved']);
            }
        }
    }

    /**
     * Obtener votos positivos.
     */
    public function upvotes()
    {
        return $this->votes()->where('vote_type', 'upvote');
    }

    /**
     * Obtener votos negativos.
     */
    public function downvotes()
    {
        return $this->votes()->where('vote_type', 'downvote');
    }

    /**
     * Calcular ratio de votos positivos.
     */
    public function getUpvoteRatio(): float
    {
        $totalVotes = $this->votes()->count();
        if ($totalVotes === 0) return 0;
        
        $upvotes = $this->upvotes()->count();
        return ($upvotes / $totalVotes) * 100;
    }
}
