<?php

/**
 * @OA\Schema(
 *     schema="Achievement",
 *     type="object",
 *     title="Achievement",
 *     description="Achievement model for gamification system",
 *     required={"id", "name", "slug", "type", "difficulty", "points", "is_secret", "is_active"},
 *     @OA\Property(property="id", type="integer", example=1, description="Achievement ID"),
 *     @OA\Property(property="name", type="string", example="Primer kWh Ahorrado", description="Achievement name"),
 *     @OA\Property(property="slug", type="string", example="first-kwh-saved", description="Achievement slug"),
 *     @OA\Property(property="description", type="string", example="Felicidades por ahorrar tu primer kWh", description="Achievement description"),
 *     @OA\Property(property="type", type="string", enum={"single", "progressive", "recurring"}, example="single", description="Achievement type"),
 *     @OA\Property(property="difficulty", type="string", enum={"bronze", "silver", "gold", "legendary"}, example="bronze", description="Achievement difficulty"),
 *     @OA\Property(property="points", type="integer", example=10, description="Points awarded"),
 *     @OA\Property(property="icon", type="string", example="energy-icon", description="Achievement icon"),
 *     @OA\Property(property="banner_color", type="string", example="#22C55E", description="Banner color"),
 *     @OA\Property(property="conditions", type="object", example={"energy_saved": 1, "unit": "kwh"}, description="Achievement conditions as JSON"),
 *     @OA\Property(property="is_secret", type="boolean", example=false, description="Is secret achievement"),
 *     @OA\Property(property="is_active", type="boolean", example=true, description="Is achievement active"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T00:00:00Z")
 * )
 */
class Achievement
{
    // This class is used only for Swagger documentation
}


