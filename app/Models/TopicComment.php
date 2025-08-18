<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Comentario en un post de tema.
 * 
 * Representa un comentario dentro de un post, con soporte para
 * comentarios anidados y marcado de soluciones.
 * 
 * @property int $id
 * @property int $topic_post_id Post al que pertenece
 * @property int $author_id Usuario autor
 * @property int|null $parent_id Comentario padre
 * @property string $content Contenido del comentario
 * @property array|null $images URLs de imágenes
 * @property array|null $attachments Documentos adjuntos
 * @property array|null $links Enlaces externos
 * @property string $status Estado del comentario
 * @property string|null $edit_reason Razón de edición
 * @property \Carbon\Carbon|null $edited_at Fecha de edición
 * @property int $likes_count Likes
 * @property int $replies_count Respuestas
 * @property bool $is_solution Si es solución
 * @property bool $is_pinned Si está fijado
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * 
 * @property-read \App\Models\TopicPost $post Post
 * @property-read \App\Models\User $author Usuario autor
 * @property-read \App\Models\TopicComment|null $parent Comentario padre
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TopicComment[] $replies Respuestas
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ContentVote[] $votes Votos
 */
class TopicComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_post_id',
        'author_id',
        'parent_id',
        'content',
        'images',
        'attachments',
        'links',
        'status',
        'edit_reason',
        'edited_at',
        'likes_count',
        'replies_count',
        'is_solution',
        'is_pinned',
    ];

    protected $casts = [
        'images' => 'array',
        'attachments' => 'array',
        'links' => 'array',
        'edited_at' => 'datetime',
        'is_solution' => 'boolean',
        'is_pinned' => 'boolean',
    ];

    /**
     * Post al que pertenece el comentario.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(TopicPost::class, 'topic_post_id');
    }

    /**
     * Usuario autor del comentario.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Comentario padre (para comentarios anidados).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(TopicComment::class, 'parent_id');
    }

    /**
     * Respuestas al comentario.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(TopicComment::class, 'parent_id');
    }

    /**
     * Votos del comentario.
     */
    public function votes(): MorphMany
    {
        return $this->morphMany(ContentVote::class, 'votable');
    }

    /**
     * Incrementar contador de likes.
     */
    public function incrementLikes(): void
    {
        $this->increment('likes_count');
    }

    /**
     * Decrementar contador de likes.
     */
    public function decrementLikes(): void
    {
        $this->decrement('likes_count');
    }

    /**
     * Incrementar contador de respuestas.
     */
    public function incrementReplies(): void
    {
        $this->increment('replies_count');
    }

    /**
     * Marcar como solución.
     */
    public function markAsSolution(): void
    {
        // Solo el autor del post o moderadores pueden marcar soluciones
        $this->update(['is_solution' => true]);
        
        // Desmarcar otras soluciones en el mismo post
        $this->post->comments()
             ->where('id', '!=', $this->id)
             ->where('is_solution', true)
             ->update(['is_solution' => false]);

        // Actualizar estado del post si es pregunta
        $this->post->markAsSolution();
    }

    /**
     * Desmarcar como solución.
     */
    public function unmarkAsSolution(): void
    {
        $this->update(['is_solution' => false]);
    }

    /**
     * Verificar si el usuario puede editar el comentario.
     */
    public function canEdit(User $user): bool
    {
        // El autor puede editar su propio comentario
        if ($this->author_id === $user->id) {
            return true;
        }

        // Los moderadores del tema pueden editar
        return $this->post->topic->isModerator($user);
    }

    /**
     * Verificar si el usuario puede eliminar el comentario.
     */
    public function canDelete(User $user): bool
    {
        // El autor puede eliminar su propio comentario
        if ($this->author_id === $user->id) {
            return true;
        }

        // Los moderadores del tema pueden eliminar
        return $this->post->topic->isModerator($user);
    }

    /**
     * Verificar si el usuario puede marcar como solución.
     */
    public function canMarkAsSolution(User $user): bool
    {
        // Solo para posts tipo pregunta o ayuda
        if (!in_array($this->post->type, ['question', 'help'])) {
            return false;
        }

        // El autor del post puede marcar soluciones
        if ($this->post->author_id === $user->id) {
            return true;
        }

        // Los moderadores del tema pueden marcar soluciones
        return $this->post->topic->isModerator($user);
    }

    /**
     * Obtener nivel de anidamiento del comentario.
     */
    public function getNestingLevel(): int
    {
        $level = 0;
        $parent = $this->parent;
        
        while ($parent) {
            $level++;
            $parent = $parent->parent;
        }
        
        return $level;
    }

    /**
     * Obtener hilo completo de comentarios.
     */
    public function getThread()
    {
        $thread = collect([$this]);
        $parent = $this->parent;
        
        while ($parent) {
            $thread->prepend($parent);
            $parent = $parent->parent;
        }
        
        return $thread;
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
     * Calcular score del comentario.
     */
    public function getScore(): int
    {
        return $this->upvotes()->count() - $this->downvotes()->count();
    }
}
