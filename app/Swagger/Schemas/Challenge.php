<?php

/**
 * @OA\Schema(
 *     schema="Challenge",
 *     type="object",
 *     title="Challenge",
 *     description="Challenge model for gamification system",
 *     required={"id", "title", "slug", "type", "category", "difficulty", "is_active"},
 *     @OA\Property(property="id", type="integer", example=1, description="Challenge ID"),
 *     @OA\Property(property="title", type="string", example="Reto Ahorro Semanal", description="Challenge title"),
 *     @OA\Property(property="slug", type="string", example="weekly-savings-challenge", description="Challenge slug"),
 *     @OA\Property(property="description", type="string", example="Ahorra energía durante una semana", description="Challenge description"),
 *     @OA\Property(property="instructions", type="string", example="Reduce tu consumo en un 10% esta semana", description="Challenge instructions"),
 *     @OA\Property(property="type", type="string", enum={"individual", "community", "cooperative"}, example="individual", description="Challenge type"),
 *     @OA\Property(property="category", type="string", enum={"energy_saving", "solar_production", "sustainability", "community"}, example="energy_saving", description="Challenge category"),
 *     @OA\Property(property="difficulty", type="string", enum={"easy", "medium", "hard", "expert"}, example="medium", description="Challenge difficulty"),
 *     @OA\Property(property="start_date", type="string", format="date-time", example="2024-01-01T00:00:00Z", description="Challenge start date"),
 *     @OA\Property(property="end_date", type="string", format="date-time", example="2024-01-07T23:59:59Z", description="Challenge end date"),
 *     @OA\Property(property="goals", type="object", example={"energy_reduction": 10, "unit": "percent"}, description="Challenge goals as JSON"),
 *     @OA\Property(property="rewards", type="object", example={"points": 50, "badge": "energy_saver"}, description="Challenge rewards as JSON"),
 *     @OA\Property(property="max_participants", type="integer", example=100, description="Maximum participants"),
 *     @OA\Property(property="min_participants", type="integer", example=1, description="Minimum participants"),
 *     @OA\Property(property="entry_fee", type="number", format="float", example=0.00, description="Entry fee"),
 *     @OA\Property(property="prize_pool", type="number", format="float", example=0.00, description="Prize pool"),
 *     @OA\Property(property="icon", type="string", example="challenge-icon", description="Challenge icon"),
 *     @OA\Property(property="banner_color", type="string", example="#FCD34D", description="Banner color"),
 *     @OA\Property(property="is_active", type="boolean", example=true, description="Is challenge active"),
 *     @OA\Property(property="is_featured", type="boolean", example=false, description="Is challenge featured"),
 *     @OA\Property(property="auto_join", type="boolean", example=false, description="Auto join enabled"),
 *     @OA\Property(property="sort_order", type="integer", example=0, description="Sort order"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T00:00:00Z")
 * )
 */
class Challenge
{
    // This class is used only for Swagger documentation
}


