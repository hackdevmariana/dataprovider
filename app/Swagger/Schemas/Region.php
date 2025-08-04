<?php 

/**
 * @OA\Schema(
 *     schema="Region",
 *     type="object",
 *     title="Región",
 *     description="Representación de una región",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Sierra Norte"),
 *     @OA\Property(property="slug", type="string", example="sierra-norte"),
 *     @OA\Property(property="latitude", type="number", format="float", example=40.9123),
 *     @OA\Property(property="longitude", type="number", format="float", example=-3.7045),
 *     @OA\Property(property="province", type="object",
 *         @OA\Property(property="id", type="integer", example=2),
 *         @OA\Property(property="name", type="string", example="Madrid"),
 *         @OA\Property(property="slug", type="string", example="madrid")
 *     ),
 *     @OA\Property(property="autonomous_community", type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Comunidad de Madrid"),
 *         @OA\Property(property="slug", type="string", example="comunidad-de-madrid")
 *     ),
 *     @OA\Property(property="country", type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="España"),
 *         @OA\Property(property="slug", type="string", example="espana")
 *     ),
 *     @OA\Property(property="timezone", type="object",
 *         @OA\Property(property="name", type="string", example="Europe/Madrid"),
 *         @OA\Property(property="utc_offset", type="string", example="+01:00")
 *     )
 * )
 */
