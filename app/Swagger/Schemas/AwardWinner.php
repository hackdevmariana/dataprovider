<?php 

/**
 * @OA\Schema(
 *     schema="AwardWinner",
 *     type="object",
 *     title="AwardWinner",
 *     required={"person_id", "award_id", "year"},
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="person_id", type="integer"),
 *     @OA\Property(property="award_id", type="integer"),
 *     @OA\Property(property="year", type="integer"),
 *     @OA\Property(property="classification", type="string"),
 *     @OA\Property(property="work_id", type="integer"),
 *     @OA\Property(property="municipality_id", type="integer")
 * )
 */
