<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Http\Resources\V1\ArtistResource;
use App\Http\Requests\StoreArtistRequest;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @group Artists
 *
 * APIs para la gestión de artistas y perfiles artísticos.
 * Permite crear, consultar y gestionar artistas de diferentes disciplinas.
 */
/**
 * @OA\Tag(
 *     name="Artistas",
 *     description="APIs para la gestión de Artistas"
 * )
 */
class ArtistController extends Controller
{
    /**
     * Display a listing of artists
     *
     * Obtiene una lista de artistas con paginación.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Pablo Picasso",
     *       "slug": "pablo-picasso",
     *       "genre": "pintura",
     *       "stage_name": "Picasso",
     *       "photo": "https://example.com/photo.jpg"
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\ArtistResource
     * @apiResourceModel App\Models\Artist
     */
    public function index(): JsonResponse
    {
        $artists = Artist::paginate(20);
        
        return response()->json([
            'data' => ArtistResource::collection($artists),
            'meta' => [
                'current_page' => $artists->currentPage(),
                'last_page' => $artists->lastPage(),
                'per_page' => $artists->perPage(),
                'total' => $artists->total(),
            ]
        ]);
    }

    /**
     * Display the specified artist
     *
     * Obtiene los detalles de un artista específico por ID o slug.
     *
     * @urlParam idOrSlug integer|string ID o slug del artista. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "name": "Pablo Picasso",
     *       "slug": "pablo-picasso",
     *       "genre": "pintura",
     *       "stage_name": "Picasso",
     *       "bio": "Artista español del siglo XX",
     *       "photo": "https://example.com/photo.jpg"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Artista no encontrado"
     * }
     *
     * @apiResourceModel App\Models\Artist
     */
    public function show($idOrSlug): JsonResponse
    {
        $artist = Artist::where('id', $idOrSlug)
            ->orWhere('slug', $idOrSlug)
            ->firstOrFail();
            
        return response()->json([
            'data' => new ArtistResource($artist)
        ]);
    }

    /**
     * Store a newly created artist
     *
     * Crea un nuevo artista (público).
     *
     * @bodyParam name string required Nombre del artista. Example: Pablo Picasso
     * @bodyParam slug string required Slug único del artista. Example: pablo-picasso
     * @bodyParam description string Descripción breve del artista. Example: Artista español del siglo XX
     * @bodyParam birth_date date Fecha de nacimiento. Example: 1881-10-25
     * @bodyParam genre string Género o disciplina artística. Example: pintura
     * @bodyParam person_id integer ID de la persona asociada. Example: 1
     * @bodyParam stage_name string Nombre artístico o de escena. Example: Picasso
     * @bodyParam group_name string Nombre del grupo o banda. Example: Cubismo
     * @bodyParam active_years_start integer Año de inicio de actividad. Example: 1890
     * @bodyParam active_years_end integer Año de fin de actividad. Example: 1973
     * @bodyParam bio string Biografía completa del artista. Example: Pablo Picasso fue un pintor español...
     * @bodyParam photo string URL de la foto del artista. Example: https://example.com/photo.jpg
     * @bodyParam social_links array Enlaces a redes sociales. Example: ["https://instagram.com/picasso"]
     * @bodyParam language_id integer ID del idioma principal. Example: 1
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "name": "Pablo Picasso",
     *     "slug": "pablo-picasso",
     *     "genre": "pintura",
     *     "stage_name": "Picasso"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\Artist
     */
    public function store(StoreArtistRequest $request): JsonResponse
    {
        $artist = Artist::create($request->validated());
        
        return response()->json([
            'data' => new ArtistResource($artist)
        ], 201);
    }
}
