<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'icon' => $this->icon,
            'color' => $this->color,
            'cover_image' => $this->cover_image,
            'list_type' => $this->list_type,
            'allowed_content_types' => $this->allowed_content_types,
            'visibility' => $this->visibility,
            'collaborator_ids' => $this->collaborator_ids,
            'allow_suggestions' => $this->allow_suggestions,
            'allow_comments' => $this->allow_comments,
            'curation_mode' => $this->curation_mode,
            'auto_criteria' => $this->auto_criteria,
            'items_count' => $this->items_count,
            'followers_count' => $this->followers_count,
            'views_count' => $this->views_count,
            'shares_count' => $this->shares_count,
            'engagement_score' => $this->engagement_score,
            'is_featured' => $this->is_featured,
            'is_template' => $this->is_template,
            'is_active' => $this->is_active,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'avatar' => $this->user->avatar ?? null,
                ];
            }),
            'items' => $this->whenLoaded('items', function () {
                return $this->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'position' => $item->position,
                        'personal_note' => $item->personal_note,
                        'tags' => $item->tags,
                        'personal_rating' => $item->personal_rating,
                        'added_mode' => $item->added_mode,
                        'status' => $item->status,
                        'clicks_count' => $item->clicks_count,
                        'likes_count' => $item->likes_count,
                        'listable_type' => $item->listable_type,
                        'listable_id' => $item->listable_id,
                        'listable' => $this->when($item->relationLoaded('listable'), function () use ($item) {
                            // Aquí se podría implementar un ResourceResolver
                            // para convertir diferentes tipos de contenido a sus recursos apropiados
                            return [
                                'id' => $item->listable->id,
                                'type' => class_basename($item->listable),
                                'title' => $item->listable->name ?? $item->listable->title ?? 'Sin título',
                                'url' => $item->listable->url ?? null,
                            ];
                        }),
                        'added_by' => [
                            'id' => $item->added_by,
                            'name' => $item->addedBy->name ?? null,
                        ],
                        'created_at' => $item->created_at?->toISOString(),
                    ];
                });
            }),
            'can_edit' => $this->when(auth()->check(), function () {
                return $this->canEdit(auth()->user());
            }),
            'can_view' => $this->when(auth()->check(), function () {
                return $this->canView(auth()->user());
            }),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
