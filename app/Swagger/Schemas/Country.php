<?php

/**
 * @OA\Schema(
 *     schema="Country",
 *     title="País",
 *     description="Representación de un país",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="España"),
 *     @OA\Property(property="slug", type="string", example="espana"),
 *     @OA\Property(property="iso_alpha2", type="string", example="ES"),
 *     @OA\Property(property="iso_alpha3", type="string", example="ESP"),
 *     @OA\Property(property="iso_numeric", type="string", example="724"),
 *     @OA\Property(property="demonym", type="string", example="español"),
 *     @OA\Property(property="official_language", type="string", example="es"),
 *     @OA\Property(property="currency_code", type="string", example="EUR"),
 *     @OA\Property(property="phone_code", type="string", example="+34"),
 *     @OA\Property(property="latitude", type="number", format="float", example=40.4637),
 *     @OA\Property(property="longitude", type="number", format="float", example=-3.7492),
 *     @OA\Property(property="flag_url", type="string", format="url", example="https://flagcdn.com/es.svg"),
 *     @OA\Property(property="population", type="integer", example=47000000),
 *     @OA\Property(property="gdp_usd", type="number", format="float", example=1600000000000),
 *     @OA\Property(property="region_group", type="string", example="Europa"),
 *     @OA\Property(property="area_km2", type="number", format="float", example=505944),
 *     @OA\Property(property="altitude_m", type="integer", example=650),
 *     @OA\Property(
 *         property="timezone",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Europe/Madrid"),
 *         @OA\Property(property="offset", type="string", example="+01:00")
 *     ),
 *     @OA\Property(
 *         property="languages",
 *         type="array",
 *         @OA\Items(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="language", type="string", example="Español"),
 *             @OA\Property(property="iso_639_1", type="string", example="es"),
 *             @OA\Property(property="native_name", type="string", example="español")
 *         )
 *     )
 * )
 */
