<?php 

/**
 * @OA\Schema(
 *     schema="Award",
 *     type="object",
 *     title="Award",
 *     required={"id", "name", "slug"},
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="slug", type="string"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="awarded_by", type="string"),
 *     @OA\Property(property="first_year_awarded", type="integer"),
 *     @OA\Property(property="category", type="string"),
 *     @OA\Property(
 *         property="award_winners",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/AwardWinner")
 *     )
 * )
 */
