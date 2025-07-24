<?php


/**
 * @OA\Schema(
 *     schema="Province",
 *     type="object",
 *     title="Provincia",
 *     description="Representación de una provincia de España",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Madrid"),
 *     @OA\Property(property="slug", type="string", example="madrid"),
 *     @OA\Property(property="ine_code", type="string", example="28"),
 *     @OA\Property(property="latitude", type="number", format="float", example=40.4168),
 *     @OA\Property(property="longitude", type="number", format="float", example=-3.7038),
 *     @OA\Property(property="area_km2", type="number", format="float", example=8028.0),
 *     @OA\Property(property="altitude_m", type="integer", example=667),
 *     @OA\Property(
 *         property="autonomous_community",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Comunidad de Madrid"),
 *         @OA\Property(property="slug", type="string", example="comunidad-de-madrid")
 *     ),
 *     @OA\Property(
 *         property="country",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="España"),
 *         @OA\Property(property="slug", type="string", example="espana")
 *     )
 * )
 */
