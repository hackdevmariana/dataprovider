<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Http\Resources\V1\ArtistResource;
use App\Http\Requests\StoreArtistRequest;
use Illuminate\Http\JsonResponse;

/**
 * @group Artists
 *
 * APIs para la gestión de artistas y músicos del sistema.
 * Permite consultar, crear y gestionar perfiles de artistas.
 */
class ArtistController extends Controller
{
    /**
     * Display a listing of artists
     *
     * Obtiene una lista paginada de todos los artistas.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Bob Dylan",
     *       "slug": "bob-dylan",
     *       "description": "Cantautor y poeta estadounidense",
     *       "genre": "folk",
     *       "stage_name": "Bob Dylan",
     *       "active_years_start": 1961
     *     }
     *   ],
     *   "meta": {
     *     "current_page": 1,
     *     "last_page": 5,
     *     "per_page": 20,
     *     "total": 100
     *   }
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
     * @urlParam idOrSlug mixed ID o slug del artista. Example: bob-dylan
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Bob Dylan",
     *     "slug": "bob-dylan",
     *     "description": "Cantautor y poeta estadounidense",
     *     "birth_date": "1941-05-24",
     *     "genre": "folk",
     *     "stage_name": "Bob Dylan",
     *     "group_name": null,
     *     "active_years_start": 1961,
     *     "active_years_end": null,
     *     "bio": "Robert Allen Zimmerman, conocido artísticamente como Bob Dylan...",
     *     "photo": "https://example.com/bob-dylan.jpg",
     *     "social_links": ["https://twitter.com/bobdylan"],
     *     "language": {
     *       "id": 1,
     *       "name": "English"
     *     }
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
     * Crea un nuevo artista con los datos validados.
     *
     * @bodyParam name string required Nombre del artista. Example: Bob Dylan
     * @bodyParam slug string required Slug único del artista. Example: bob-dylan
     * @bodyParam description string Descripción breve del artista. Example: Cantautor y poeta estadounidense
     * @bodyParam birth_date date Fecha de nacimiento. Example: 1941-05-24
     * @bodyParam genre string Género musical. Example: folk
     * @bodyParam person_id int ID de la persona asociada. Example: 1
     * @bodyParam stage_name string Nombre artístico. Example: Bob Dylan
     * @bodyParam group_name string Nombre del grupo musical. Example: The Band
     * @bodyParam active_years_start int Año de inicio de actividad. Example: 1961
     * @bodyParam active_years_end int Año de fin de actividad. Example: null
     * @bodyParam bio string Biografía completa. Example: Robert Allen Zimmerman, conocido artísticamente como Bob Dylan...
     * @bodyParam photo string URL de la foto del artista. Example: https://example.com/bob-dylan.jpg
     * @bodyParam social_links array Enlaces a redes sociales. Example: ["https://twitter.com/bobdylan"]
     * @bodyParam language_id int ID del idioma principal. Example: 1
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "name": "Bob Dylan",
     *     "slug": "bob-dylan",
     *     "description": "Cantautor y poeta estadounidense",
     *     "genre": "folk"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {
     *     "name": ["El campo nombre es obligatorio."],
     *     "slug": ["El campo slug es obligatorio."]
     *   }
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
