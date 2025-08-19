<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Sistema de listas personalizadas estilo Twitter/Discord.
 * 
 * Permite a los usuarios crear listas curadas de contenido
 * con funcionalidades de colaboración y auto-curación.
 */
class UserList extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'cover_image',
        'list_type',
        'allowed_content_types',
        'visibility',
        'collaborator_ids',
        'allow_suggestions',
        'allow_comments',
        'curation_mode',
        'auto_criteria',
        'items_count',
        'followers_count',
        'views_count',
        'shares_count',
        'engagement_score',
        'is_featured',
        'is_template',
        'is_active',
    ];

    protected $casts = [
        'allowed_content_types' => 'array',
        'collaborator_ids' => 'array',
        'auto_criteria' => 'array',
        'allow_suggestions' => 'boolean',
        'allow_comments' => 'boolean',
        'engagement_score' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_template' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Usuario propietario de la lista.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Elementos de la lista.
     */
    public function items(): HasMany
    {
        return $this->hasMany(ListItem::class);
    }

    /**
     * Elementos activos de la lista.
     */
    public function activeItems(): HasMany
    {
        return $this->items()->where('status', 'active');
    }

    /**
     * Boot del modelo.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($list) {
            if (empty($list->slug)) {
                $list->slug = static::generateUniqueSlug($list->name, $list->user_id);
            }
        });
    }

    /**
     * Generar slug único para el usuario.
     */
    public static function generateUniqueSlug(string $name, int $userId): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (static::where('user_id', $userId)->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Verificar si el usuario puede editar la lista.
     */
    public function canEdit(User $user): bool
    {
        if ($this->user_id === $user->id) {
            return true;
        }

        if ($this->visibility === 'collaborative' && 
            in_array($user->id, $this->collaborator_ids ?? [])) {
            return true;
        }

        return false;
    }

    /**
     * Verificar si el usuario puede ver la lista.
     */
    public function canView(User $user): bool
    {
        if ($this->visibility === 'public') {
            return true;
        }

        if ($this->user_id === $user->id) {
            return true;
        }

        if ($this->visibility === 'followers') {
            // Aquí iría la lógica de seguimiento
            return $this->user->isFollowedBy($user);
        }

        if ($this->visibility === 'collaborative' && 
            in_array($user->id, $this->collaborator_ids ?? [])) {
            return true;
        }

        return false;
    }

    /**
     * Añadir elemento a la lista.
     */
    public function addItem(Model $item, User $addedBy, array $options = []): ListItem
    {
        $listItem = $this->items()->create([
            'listable_type' => get_class($item),
            'listable_id' => $item->id,
            'added_by' => $addedBy->id,
            'position' => $this->items()->max('position') + 1,
            'personal_note' => $options['note'] ?? null,
            'tags' => $options['tags'] ?? null,
            'personal_rating' => $options['rating'] ?? null,
            'added_mode' => $options['mode'] ?? 'manual',
        ]);

        $this->increment('items_count');
        $this->updateEngagementScore();

        return $listItem;
    }

    /**
     * Remover elemento de la lista.
     */
    public function removeItem(Model $item): bool
    {
        $removed = $this->items()
                       ->where('listable_type', get_class($item))
                       ->where('listable_id', $item->id)
                       ->delete();

        if ($removed) {
            $this->decrement('items_count');
            $this->updateEngagementScore();
        }

        return $removed > 0;
    }

    /**
     * Actualizar score de engagement.
     */
    public function updateEngagementScore(): void
    {
        $score = ($this->views_count * 0.1) + 
                ($this->followers_count * 2) + 
                ($this->shares_count * 5) + 
                ($this->items_count * 1);

        $this->update(['engagement_score' => $score]);
    }

    /**
     * Incrementar vistas.
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
        $this->updateEngagementScore();
    }

    /**
     * Obtener listas públicas destacadas.
     */
    public static function getFeatured(int $limit = 10)
    {
        return static::where('visibility', 'public')
                    ->where('is_featured', true)
                    ->where('is_active', true)
                    ->orderBy('engagement_score', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Buscar listas por término.
     */
    public static function search(string $term, int $limit = 20)
    {
        return static::where('visibility', 'public')
                    ->where('is_active', true)
                    ->where(function ($query) use ($term) {
                        $query->where('name', 'like', "%{$term}%")
                              ->orWhere('description', 'like', "%{$term}%");
                    })
                    ->orderBy('engagement_score', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Obtener listas por tipo.
     */
    public static function getByType(string $type, int $limit = 20)
    {
        return static::where('list_type', $type)
                    ->where('visibility', 'public')
                    ->where('is_active', true)
                    ->orderBy('engagement_score', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Crear lista desde plantilla.
     */
    public static function createFromTemplate(UserList $template, User $user, string $name): static
    {
        $newList = static::create([
            'user_id' => $user->id,
            'name' => $name,
            'description' => $template->description,
            'list_type' => $template->list_type,
            'allowed_content_types' => $template->allowed_content_types,
            'curation_mode' => $template->curation_mode,
            'auto_criteria' => $template->auto_criteria,
            'icon' => $template->icon,
            'color' => $template->color,
        ]);

        // Copiar elementos de la plantilla
        foreach ($template->activeItems as $item) {
            $newList->addItem($item->listable, $user, [
                'mode' => 'imported',
                'note' => $item->personal_note,
            ]);
        }

        return $newList;
    }
}
