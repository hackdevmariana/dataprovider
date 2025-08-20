<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SocialComparisonResource extends JsonResource
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
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'comparison_type' => $this->comparison_type,
            'comparison_type_label' => $this->getComparisonTypeLabel(),
            'period' => $this->period,
            'period_label' => $this->getPeriodLabel(),
            'scope' => $this->scope,
            'scope_label' => $this->getScopeLabel(),
            'user_value' => $this->user_value,
            'unit' => $this->unit,
            'average_value' => $this->average_value,
            'median_value' => $this->median_value,
            'best_value' => $this->best_value,
            'user_rank' => $this->user_rank,
            'total_participants' => $this->total_participants,
            'percentile' => $this->percentile,
            'comparison_group' => $this->comparison_group,
            'comparison_group_label' => $this->getComparisonGroupLabel(),
            'group_id' => $this->group_id,
            'breakdown' => $this->breakdown ?? [],
            'comparison_date' => $this->comparison_date,
            'is_public' => $this->is_public,
            'performance_category' => $this->getPerformanceCategory(),
            'performance_description' => $this->getPerformanceDescription(),
            'improvement_suggestions' => $this->getImprovementSuggestions(),
            'formatted_values' => $this->getFormattedValues(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Get human-readable comparison type label
     */
    private function getComparisonTypeLabel(): string
    {
        return match ($this->comparison_type) {
            'energy_savings' => 'Ahorro de Energía',
            'carbon_reduction' => 'Reducción de Carbono',
            'community_participation' => 'Participación Comunitaria',
            'project_contributions' => 'Contribuciones a Proyectos',
            'knowledge_sharing' => 'Compartir Conocimiento',
            default => ucfirst(str_replace('_', ' ', $this->comparison_type)),
        };
    }

    /**
     * Get human-readable period label
     */
    private function getPeriodLabel(): string
    {
        return match ($this->period) {
            'daily' => 'Diario',
            'weekly' => 'Semanal',
            'monthly' => 'Mensual',
            'yearly' => 'Anual',
            'all_time' => 'Histórico',
            default => ucfirst($this->period),
        };
    }

    /**
     * Get human-readable scope label
     */
    private function getScopeLabel(): string
    {
        return match ($this->scope) {
            'personal' => 'Personal',
            'cooperative' => 'Cooperativa',
            'regional' => 'Regional',
            'national' => 'Nacional',
            'global' => 'Global',
            default => ucfirst($this->scope),
        };
    }

    /**
     * Get human-readable comparison group label
     */
    private function getComparisonGroupLabel(): string
    {
        return match ($this->comparison_group) {
            'municipality' => 'Municipio',
            'province' => 'Provincia',
            'autonomous_community' => 'Comunidad Autónoma',
            'country' => 'País',
            'cooperative' => 'Cooperativa',
            'age_group' => 'Grupo de Edad',
            'installation_type' => 'Tipo de Instalación',
            default => ucfirst(str_replace('_', ' ', $this->comparison_group)),
        ];
    }

    /**
     * Get performance category based on percentile
     */
    private function getPerformanceCategory(): string
    {
        if (!$this->percentile) {
            return 'unknown';
        }

        return match (true) {
            $this->percentile >= 90 => 'excellent',
            $this->percentile >= 75 => 'good',
            $this->percentile >= 50 => 'average',
            $this->percentile >= 25 => 'below_average',
            default => 'needs_improvement',
        };
    }

    /**
     * Get performance description
     */
    private function getPerformanceDescription(): string
    {
        $category = $this->getPerformanceCategory();
        $rank = $this->user_rank;
        $total = $this->total_participants;

        return match ($category) {
            'excellent' => "¡Excelente! Estás entre el 10% superior (puesto {$rank} de {$total})",
            'good' => "¡Muy bien! Estás por encima del promedio (puesto {$rank} de {$total})",
            'average' => "Rendimiento promedio (puesto {$rank} de {$total})",
            'below_average' => "Por debajo del promedio (puesto {$rank} de {$total})",
            'needs_improvement' => "Hay margen de mejora (puesto {$rank} de {$total})",
            default => "Rendimiento no evaluado",
        };
    }

    /**
     * Get improvement suggestions based on performance
     */
    private function getImprovementSuggestions(): array
    {
        $category = $this->getPerformanceCategory();
        $type = $this->comparison_type;

        $suggestions = match ($category) {
            'excellent' => [
                'Mantén tu excelente rendimiento',
                'Considera compartir tus mejores prácticas con la comunidad',
                'Explora nuevas formas de optimizar aún más',
            ],
            'good' => [
                '¡Vas por buen camino! Sigue así',
                'Busca pequeñas optimizaciones para llegar al top 10%',
                'Conecta con otros usuarios de alto rendimiento',
            ],
            'average' => [
                'Hay oportunidades de mejora',
                'Revisa las mejores prácticas de usuarios destacados',
                'Considera participar más activamente en la comunidad',
            ],
            'below_average' => [
                'Enfócate en las acciones básicas de mejora',
                'Busca ayuda de usuarios más experimentados',
                'Participa en programas de formación disponibles',
            ],
            'needs_improvement' => [
                'Comienza con cambios pequeños pero constantes',
                'Busca asesoramiento personalizado',
                'Únete a grupos de apoyo en tu área',
            ],
            default => ['Datos insuficientes para generar sugerencias'],
        };

        // Personalizar sugerencias según el tipo de comparación
        if ($type === 'energy_savings') {
            array_unshift($suggestions, 'Revisa tu consumo energético diario');
        } elseif ($type === 'community_participation') {
            array_unshift($suggestions, 'Participa más en discusiones y eventos');
        }

        return $suggestions;
    }

    /**
     * Get formatted values for display
     */
    private function getFormattedValues(): array
    {
        return [
            'user_value' => $this->formatValue($this->user_value),
            'average_value' => $this->formatValue($this->average_value),
            'median_value' => $this->formatValue($this->median_value),
            'best_value' => $this->formatValue($this->best_value),
            'percentile' => $this->percentile ? number_format($this->percentile, 1) . '%' : 'N/A',
            'rank_display' => "#{$this->user_rank} de {$this->total_participants}",
        ];
    }

    /**
     * Format numeric value with unit
     */
    private function formatValue($value): string
    {
        if ($value === null) {
            return 'N/A';
        }

        $formatted = number_format($value, 2);
        return "{$formatted} {$this->unit}";
    }
}