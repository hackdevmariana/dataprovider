<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Sistema de bookmarks/favoritos personalizado estilo Pocket.
 * 
 * Permite a los usuarios guardar cualquier tipo de contenido
 * con organización avanzada, notas y recordatorios.
 */
class UserBookmark extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bookmarkable_type',
        'bookmarkable_id',
        'folder',
        'tags',
        'personal_notes',
        'priority',
        'reminder_enabled',
        'reminder_date',
        'reminder_frequency',
        'access_count',
        'last_accessed_at',
        'is_public',
    ];

    protected $casts = [
        'tags' => 'array',
        'reminder_enabled' => 'boolean',
        'reminder_date' => 'datetime',
        'last_accessed_at' => 'datetime',
        'is_public' => 'boolean',
    ];

    /**
     * Usuario propietario del bookmark.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Contenido marcado como bookmark (polimórfico).
     */
    public function bookmarkable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Incrementar contador de accesos.
     */
    public function incrementAccess(): void
    {
        $this->increment('access_count');
        $this->update(['last_accessed_at' => now()]);
    }

    /**
     * Obtener bookmarks por carpeta.
     */
    public static function getByFolder(User $user, string $folder)
    {
        return static::where('user_id', $user->id)
                    ->where('folder', $folder)
                    ->with('bookmarkable')
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    /**
     * Obtener carpetas del usuario.
     */
    public static function getUserFolders(User $user): array
    {
        return static::where('user_id', $user->id)
                    ->whereNotNull('folder')
                    ->distinct()
                    ->pluck('folder')
                    ->toArray();
    }

    /**
     * Obtener bookmarks con recordatorios pendientes.
     */
    public static function getPendingReminders()
    {
        return static::where('reminder_enabled', true)
                    ->where('reminder_date', '<=', now())
                    ->with(['user', 'bookmarkable'])
                    ->get();
    }

    /**
     * Verificar si el contenido ya está marcado.
     */
    public static function isBookmarked(User $user, Model $content): bool
    {
        return static::where('user_id', $user->id)
                    ->where('bookmarkable_type', get_class($content))
                    ->where('bookmarkable_id', $content->id)
                    ->exists();
    }

    /**
     * Obtener bookmarks públicos del usuario.
     */
    public static function getPublicBookmarks(User $user)
    {
        return static::where('user_id', $user->id)
                    ->where('is_public', true)
                    ->with('bookmarkable')
                    ->orderBy('access_count', 'desc')
                    ->get();
    }
}

