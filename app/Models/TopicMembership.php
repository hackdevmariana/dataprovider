<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Membresía de usuario en un tema.
 * 
 * Representa la relación entre un usuario y un tema,
 * incluyendo rol, configuraciones y métricas de participación.
 * 
 * @property int $id
 * @property int $topic_id Tema
 * @property int $user_id Usuario
 * @property string $role Rol del usuario
 * @property string $status Estado del usuario
 * @property bool $notifications_enabled Si tiene notificaciones habilitadas
 * @property bool $email_notifications Si recibe emails
 * @property array|null $notification_preferences Preferencias de notificación
 * @property int $posts_count Posts del usuario en este tema
 * @property int $comments_count Comentarios del usuario en este tema
 * @property int $reputation_score Reputación del usuario en este tema
 * @property \Carbon\Carbon|null $last_activity_at Última actividad
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * 
 * @property-read \App\Models\Topic $topic Tema
 * @property-read \App\Models\User $user Usuario
 */
class TopicMembership extends Pivot
{
    protected $table = 'topic_memberships';

    protected $fillable = [
        'topic_id',
        'user_id',
        'role',
        'status',
        'notifications_enabled',
        'email_notifications',
        'notification_preferences',
        'posts_count',
        'comments_count',
        'reputation_score',
        'last_activity_at',
    ];

    protected $casts = [
        'notifications_enabled' => 'boolean',
        'email_notifications' => 'boolean',
        'notification_preferences' => 'array',
        'last_activity_at' => 'datetime',
    ];

    /**
     * Tema de la membresía.
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Usuario de la membresía.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Incrementar contador de posts.
     */
    public function incrementPosts(): void
    {
        $this->increment('posts_count');
        $this->updateLastActivity();
    }

    /**
     * Incrementar contador de comentarios.
     */
    public function incrementComments(): void
    {
        $this->increment('comments_count');
        $this->updateLastActivity();
    }

    /**
     * Actualizar última actividad.
     */
    public function updateLastActivity(): void
    {
        $this->update(['last_activity_at' => now()]);
    }

    /**
     * Promover usuario a moderador.
     */
    public function promoteToModerator(): void
    {
        $this->update(['role' => 'moderator']);
    }

    /**
     * Promover usuario a administrador.
     */
    public function promoteToAdmin(): void
    {
        $this->update(['role' => 'admin']);
    }

    /**
     * Degradar usuario a miembro normal.
     */
    public function demoteToMember(): void
    {
        $this->update(['role' => 'member']);
    }

    /**
     * Banear usuario del tema.
     */
    public function ban(): void
    {
        $this->update(['status' => 'banned']);
    }

    /**
     * Silenciar usuario en el tema.
     */
    public function mute(): void
    {
        $this->update(['status' => 'muted']);
    }

    /**
     * Reactivar usuario en el tema.
     */
    public function activate(): void
    {
        $this->update(['status' => 'active']);
    }

    /**
     * Verificar si el usuario es moderador.
     */
    public function isModerator(): bool
    {
        return in_array($this->role, ['moderator', 'admin']);
    }

    /**
     * Verificar si el usuario es administrador.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Verificar si el usuario está activo.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Verificar si el usuario está baneado.
     */
    public function isBanned(): bool
    {
        return $this->status === 'banned';
    }

    /**
     * Verificar si el usuario está silenciado.
     */
    public function isMuted(): bool
    {
        return $this->status === 'muted';
    }

    /**
     * Calcular nivel de participación.
     */
    public function getParticipationLevel(): string
    {
        $totalActivity = $this->posts_count + $this->comments_count;
        
        if ($totalActivity >= 100) return 'very_active';
        if ($totalActivity >= 50) return 'active';
        if ($totalActivity >= 10) return 'moderate';
        if ($totalActivity >= 1) return 'low';
        
        return 'lurker';
    }
}
