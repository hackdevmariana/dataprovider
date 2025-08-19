<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Elemento individual dentro de una lista de usuario.
 * 
 * Representa un elemento específico (post, usuario, proyecto, etc.)
 * dentro de una lista con metadata personalizada.
 */
class ListItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_list_id',
        'listable_type',
        'listable_id',
        'added_by',
        'position',
        'personal_note',
        'tags',
        'personal_rating',
        'added_mode',
        'status',
        'reviewed_by',
        'reviewed_at',
        'clicks_count',
        'likes_count',
        'last_accessed_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'personal_rating' => 'decimal:1',
        'reviewed_at' => 'datetime',
        'last_accessed_at' => 'datetime',
    ];

    /**
     * Lista a la que pertenece el elemento.
     */
    public function userList(): BelongsTo
    {
        return $this->belongsTo(UserList::class);
    }

    /**
     * Contenido del elemento (polimórfico).
     */
    public function listable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Usuario que añadió el elemento.
     */
    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    /**
     * Usuario que revisó el elemento.
     */
    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Incrementar clicks del elemento.
     */
    public function incrementClicks(): void
    {
        $this->increment('clicks_count');
        $this->update(['last_accessed_at' => now()]);
    }

    /**
     * Incrementar likes del elemento.
     */
    public function incrementLikes(): void
    {
        $this->increment('likes_count');
    }

    /**
     * Cambiar posición del elemento en la lista.
     */
    public function moveToPosition(int $newPosition): void
    {
        $oldPosition = $this->position;
        
        if ($newPosition === $oldPosition) {
            return;
        }

        // Ajustar posiciones de otros elementos
        if ($newPosition > $oldPosition) {
            // Mover hacia abajo
            static::where('user_list_id', $this->user_list_id)
                  ->whereBetween('position', [$oldPosition + 1, $newPosition])
                  ->decrement('position');
        } else {
            // Mover hacia arriba
            static::where('user_list_id', $this->user_list_id)
                  ->whereBetween('position', [$newPosition, $oldPosition - 1])
                  ->increment('position');
        }

        $this->update(['position' => $newPosition]);
    }

    /**
     * Aprobar elemento sugerido.
     */
    public function approve(User $reviewer): void
    {
        $this->update([
            'status' => 'active',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
        ]);
    }

    /**
     * Rechazar elemento sugerido.
     */
    public function reject(User $reviewer): void
    {
        $this->update([
            'status' => 'rejected',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
        ]);
    }

    /**
     * Archivar elemento.
     */
    public function archive(): void
    {
        $this->update(['status' => 'archived']);
    }

    /**
     * Obtener elementos activos de una lista ordenados.
     */
    public static function getActiveForList(UserList $list)
    {
        return static::where('user_list_id', $list->id)
                    ->where('status', 'active')
                    ->with('listable')
                    ->orderBy('position')
                    ->get();
    }

    /**
     * Obtener elementos pendientes de revisión.
     */
    public static function getPendingForList(UserList $list)
    {
        return static::where('user_list_id', $list->id)
                    ->where('status', 'pending')
                    ->with(['listable', 'addedBy'])
                    ->orderBy('created_at')
                    ->get();
    }

    /**
     * Boot del modelo.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (is_null($item->position)) {
                $maxPosition = static::where('user_list_id', $item->user_list_id)
                                   ->max('position');
                $item->position = $maxPosition + 1;
            }
        });

        static::deleted(function ($item) {
            // Reordenar posiciones después de eliminar
            static::where('user_list_id', $item->user_list_id)
                  ->where('position', '>', $item->position)
                  ->decrement('position');
        });
    }
}
