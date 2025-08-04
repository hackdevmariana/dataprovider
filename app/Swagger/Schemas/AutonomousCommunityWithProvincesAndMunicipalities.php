<?php 

/**
 * @OA\Schema(
 *     schema="AutonomousCommunityWithProvincesAndMunicipalities",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/AutonomousCommunity"),
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="provinces",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=2),
 *                     @OA\Property(property="name", type="string", example="Madrid"),
 *                     @OA\Property(property="slug", type="string", example="madrid"),
 *                     @OA\Property(
 *                         property="municipalities",
 *                         type="array",
 *                         @OA\Items(
 *                             type="object",
 *                             @OA\Property(property="id", type="integer", example=101),
 *                             @OA\Property(property="name", type="string", example="Alcalá de Henares"),
 *                             @OA\Property(property="slug", type="string", example="alcala-de-henares")
 *                         )
 *                     )
 *                 )
 *             )
 *         )
 *     }
 * )
 */
