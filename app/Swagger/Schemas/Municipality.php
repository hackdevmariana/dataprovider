<?php

/**
 * @OA\Schema(
 *     schema="Municipality",
 *     type="object",
 *     title="Municipio",
 *     required={"id", "name", "province_id", "country_id"},
 *     @OA\Property(property="id", type="integer", example=28079),
 *     @OA\Property(property="name", type="string", example="Madrid"),
 *     @OA\Property(property="slug", type="string", example="madrid"),
 *     @OA\Property(property="ine_code", type="string", example="28079"),
 *     @OA\Property(property="postal_code", type="string", example="28001"),
 *     @OA\Property(property="population", type="integer", example=3200000),
 *     @OA\Property(property="mayor_name", type="string", example="José Luis Martínez-Almeida"),
 *     @OA\Property(property="mayor_salary", type="number", format="float", example=103000.50),
 *     @OA\Property(property="latitude", type="number", format="float", example=40.4168),
 *     @OA\Property(property="longitude", type="number", format="float", example=-3.7038),
 *     @OA\Property(property="area_km2", type="number", format="float", example=604.3),
 *     @OA\Property(property="altitude_m", type="integer", example=667),
 *     @OA\Property(property="is_capital", type="boolean", example=true),
 *     @OA\Property(property="tourism_info", type="string", example="Capital de España, ciudad histórica y moderna."),
 *     @OA\Property(property="province_id", type="integer", example=1),
 *     @OA\Property(property="autonomous_community_id", type="integer", example=1),
 *     @OA\Property(property="country_id", type="integer", example=1),
 *     @OA\Property(property="timezone_id", type="integer", example=1),
 * )
 */
