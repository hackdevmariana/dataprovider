<?php

/**
 * @OA\Schema(
 *     schema="PointOfInterest",
 *     type="object",
 *     title="Point Of Interest",
 *     required={"name", "slug", "latitude", "longitude", "municipality_id"},
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="slug", type="string"),
 *     @OA\Property(property="address", type="string"),
 *     @OA\Property(property="type", type="string"),
 *     @OA\Property(property="latitude", type="number", format="float"),
 *     @OA\Property(property="longitude", type="number", format="float"),
 *     @OA\Property(property="municipality_id", type="integer"),
 *     @OA\Property(property="source", type="string"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="is_cultural_center", type="boolean"),
 *     @OA\Property(property="is_energy_installation", type="boolean"),
 *     @OA\Property(property="is_cooperative_office", type="boolean"),
 *     @OA\Property(property="opening_hours", type="array", @OA\Items(type="string")),
 * )
 */
