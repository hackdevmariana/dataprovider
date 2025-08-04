<?php 

/**
 * @OA\Schema(
 *     schema="AutonomousCommunity",
 *     title="Comunidad Autónoma",
 *     description="Representación de una comunidad autónoma",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Comunidad de Madrid"),
 *     @OA\Property(property="slug", type="string", example="comunidad-de-madrid"),
 *     @OA\Property(property="latitude", type="number", format="float", example=40.4168),
 *     @OA\Property(property="longitude", type="number", format="float", example=-3.7038),
 *     @OA\Property(property="country", type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="España"),
 *         @OA\Property(property="slug", type="string", example="espana")
 *     )
 * )
 */
