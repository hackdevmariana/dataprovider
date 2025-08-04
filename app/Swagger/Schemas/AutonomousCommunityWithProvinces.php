<?php

/**
 * @OA\Schema(
 *     schema="AutonomousCommunityWithProvinces",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/AutonomousCommunity"),
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="provinces",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/Province")
 *             )
 *         )
 *     }
 * )
 */
