/**
 * @OA\Schema(
 *     schema="Timezone",
 *     title="Zona horaria",
 *     description="Información sobre una zona horaria",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Europe/Madrid"),
 *     @OA\Property(property="offset", type="string", example="+01:00"),
 *     @OA\Property(property="dst_offset", type="string", example="+02:00"),
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
