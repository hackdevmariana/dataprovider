<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource para fuentes de scraping.
 * 
 * Transforma los datos de ScrapingSource para la API,
 * incluyendo información de estado y métricas de actividad.
 */
class ScrapingSourceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'url' => $this->url,
            'type' => $this->type,
            'type_label' => $this->getTypeLabel(),
            'source_type_description' => $this->source_type_description,
            'frequency' => $this->frequency,
            'frequency_label' => $this->getFrequencyLabel(),
            'is_active' => $this->is_active,
            'status' => $this->is_active ? 'activa' : 'inactiva',
            'last_scraped_at' => $this->last_scraped_at?->toISOString(),
            'last_scraped_human' => $this->last_scraped_at?->diffForHumans(),
            'days_since_last_scrape' => $this->last_scraped_at ? 
                $this->last_scraped_at->diffInDays(now()) : null,
            'needs_scraping' => $this->needsScraping(),
            'is_sustainability_focused' => $this->isSustainabilityFocused(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }

    /**
     * Obtener etiqueta legible del tipo.
     */
    private function getTypeLabel(): string
    {
        return match($this->type) {
            'blog' => 'Blog',
            'newspaper' => 'Periódico',
            'wiki' => 'Wiki',
            'other' => 'Otro',
            default => 'Desconocido'
        };
    }

    /**
     * Obtener etiqueta legible de la frecuencia.
     */
    private function getFrequencyLabel(): string
    {
        return match($this->frequency) {
            'daily' => 'Diario',
            'weekly' => 'Semanal',
            'monthly' => 'Mensual',
            default => 'No definida'
        };
    }

    /**
     * Determinar si necesita scraping.
     */
    private function needsScraping(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if (!$this->last_scraped_at) {
            return true;
        }

        // Determinar intervalo según frecuencia
        $hoursThreshold = match($this->frequency) {
            'daily' => 24,
            'weekly' => 168, // 7 días
            'monthly' => 720, // 30 días
            default => 24
        };

        return $this->last_scraped_at->addHours($hoursThreshold) <= now();
    }

    /**
     * Determinar si está enfocada en sostenibilidad.
     */
    private function isSustainabilityFocused(): bool
    {
        $text = strtolower($this->name . ' ' . $this->source_type_description);
        
        $sustainabilityKeywords = [
            'sostenibilidad', 'medio ambiente', 'energía', 'renovable',
            'ecológico', 'verde', 'cambio climático', 'biodiversidad',
            'reciclaje', 'circular', 'carbono', 'emisiones'
        ];

        foreach ($sustainabilityKeywords as $keyword) {
            if (str_contains($text, $keyword)) {
                return true;
            }
        }

        return false;
    }
}
