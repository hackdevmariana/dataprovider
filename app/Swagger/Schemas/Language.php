/**
 * @OA\Schema(
 *     schema="Language",
 *     title="Idioma",
 *     description="Representación de un idioma",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="language", type="string", example="Español"),
 *     @OA\Property(property="slug", type="string", example="espanol"),
 *     @OA\Property(property="native_name", type="string", example="español"),
 *     @OA\Property(property="iso_639_1", type="string", example="es"),
 *     @OA\Property(property="iso_639_2", type="string", example="spa"),
 *     @OA\Property(property="rtl", type="boolean", example=false),
 *     @OA\Property(
 *         property="countries",
 *         type="array",
 *         @OA\Items(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="España"),
 *             @OA\Property(property="slug", type="string", example="espana")
 *         )
 *     )
 * )
 */
