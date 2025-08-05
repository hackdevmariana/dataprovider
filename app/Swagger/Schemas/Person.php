<?php

/**
 * @OA\Schema(
 *     schema="Person",
 *     title="Persona",
 *     description="Modelo de una persona",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Pablo Picasso"),
 *     @OA\Property(property="birth_name", type="string", example="Pablo Diego José Francisco de Paula Juan Nepomuceno María de los Remedios Cipriano de la Santísima Trinidad Ruiz y Picasso"),
 *     @OA\Property(property="slug", type="string", example="pablo-picasso"),
 *     @OA\Property(property="birth_date", type="string", format="date", example="1881-10-25"),
 *     @OA\Property(property="death_date", type="string", format="date", example="1973-04-08"),
 *     @OA\Property(property="birth_place", type="string", example="Málaga, España"),
 *     @OA\Property(property="death_place", type="string", example="Mougins, Francia"),
 *     @OA\Property(property="gender", type="string", example="male"),
 *     @OA\Property(property="official_website", type="string", format="url", example="https://picasso.com"),
 *     @OA\Property(property="wikidata_id", type="string", example="Q5593"),
 *     @OA\Property(property="wikipedia_url", type="string", format="url", example="https://es.wikipedia.org/wiki/Pablo_Picasso"),
 *     @OA\Property(property="notable_for", type="string", example="Pintor, escultor"),
 *     @OA\Property(property="occupation_summary", type="string", example="Artista polifacético del siglo XX."),
 *     @OA\Property(property="social_handles", type="object",
 *         @OA\Property(property="twitter", type="string", example="@picasso"),
 *         @OA\Property(property="instagram", type="string", example="@picasso_art")
 *     ),
 *     @OA\Property(property="is_influencer", type="boolean", example=false),
 *     @OA\Property(property="search_boost", type="integer", example=10),
 *     @OA\Property(property="short_bio", type="string", example="Pintor y escultor español."),
 *     @OA\Property(property="long_bio", type="string", example="Fue uno de los mayores exponentes del cubismo..."),
 *     @OA\Property(property="source_url", type="string", format="url", example="https://api.source.com/picasso"),
 *     @OA\Property(property="last_updated_from_source", type="string", format="date-time", example="2024-05-01T12:00:00Z"),
 *     @OA\Property(property="nationality", ref="#/components/schemas/Country"),
 *     @OA\Property(property="language", ref="#/components/schemas/Language"),
 *     @OA\Property(property="image", ref="#/components/schemas/Image"),

 * 
 * @OA\Property(
 *         property="aliases",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Alias")
 *     ),
 * )
 */
