<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Provinces",
 *     description="API endpoints para gestión de provincias y divisiones administrativas"
 * )
 * 
 * @group Provinces
 *
 * APIs para la gestión de provincias y divisiones administrativas.
 * Permite consultar información de provincias, comunidades autónomas y países.
 */
class ProvinceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/provinces",
     *     summary="List provinces",
     *     tags={"Provinces"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page (max 100)",
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of provinces",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Barcelona"),
     *                 @OA\Property(property="slug", type="string", example="barcelona"),
     *                 @OA\Property(property="code", type="string", example="08"),
     *                 @OA\Property(property="area_km2", type="number", example=7726),
     *                 @OA\Property(property="population", type="integer", example=5662000),
     *                 @OA\Property(property="autonomous_community", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Cataluña")
     *                 )
     *             )),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="total", type="integer", example=50),
     *                 @OA\Property(property="per_page", type="integer", example=20),
     *                 @OA\Property(property="current_page", type="integer", example=1)
     *             )
     *         )
     *     )
     * )
     * 
     * Display a listing of provinces
     *
     * Obtiene una lista paginada de provincias con sus relaciones.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Barcelona",
     *       "slug": "barcelona",
     *       "ine_code": "08",
     *       "latitude": 41.3851,
     *       "longitude": 2.1734,
     *       "area_km2": 7721.5,
     *       "altitude_m": 12,
     *       "autonomous_community": {
     *         "name": "Cataluña"
     *       },
     *       "country": {
     *         "name": "España"
     *       }
     *     }
     *   ],
     *   "meta": {...}
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100'
        ]);

        $perPage = min($request->get('per_page', 20), 100);
        $provinces = Province::with(['autonomousCommunity', 'country'])->paginate($perPage);
        
        return response()->json([
            'data' => $provinces->items(),
            'meta' => [
                'current_page' => $provinces->currentPage(),
                'last_page' => $provinces->lastPage(),
                'per_page' => $provinces->perPage(),
                'total' => $provinces->total(),
            ]
        ]);
    }

    /**
     * Display the specified province
     *
     * Obtiene los detalles de una provincia específica por ID o slug.
     *
     * @urlParam idOrSlug integer|string ID o slug de la provincia. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Barcelona",
     *     "slug": "barcelona",
     *     "ine_code": "08",
     *     "latitude": 41.3851,
     *     "longitude": 2.1734,
     *     "area_km2": 7721.5,
     *     "altitude_m": 12,
     *     "autonomous_community": {
     *       "name": "Cataluña"
     *     },
     *     "country": {
     *       "name": "España"
     *     }
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Provincia no encontrada"
     * }
     */
    public function show($idOrSlug): JsonResponse
    {
        $province = Province::with(['autonomousCommunity', 'country'])
            ->where('id', $idOrSlug)
            ->orWhere('slug', $idOrSlug)
            ->first();

        if (!$province) {
            return response()->json(['message' => 'Provincia no encontrada'], 404);
        }

        return response()->json([
            'data' => $province
        ]);
    }

    /**
     * Get provinces by autonomous community
     *
     * Obtiene las provincias de una comunidad autónoma específica.
     *
     * @queryParam autonomous_community_id int ID de la comunidad autónoma. Example: 1
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Barcelona",
     *       "slug": "barcelona",
     *       "ine_code": "08"
     *     }
     *   ],
     *   "meta": {...}
     * }
     */
    public function byAutonomousCommunity(Request $request): JsonResponse
    {
        $request->validate([
            'autonomous_community_id' => 'required|integer|exists:autonomous_communities,id',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100'
        ]);

        $perPage = min($request->get('per_page', 20), 100);
        $provinces = Province::where('autonomous_community_id', $request->autonomous_community_id)
            ->orderBy('name')
            ->paginate($perPage);

        return response()->json([
            'data' => $provinces->items(),
            'meta' => [
                'current_page' => $provinces->currentPage(),
                'last_page' => $provinces->lastPage(),
                'per_page' => $provinces->perPage(),
                'total' => $provinces->total(),
            ]
        ]);
    }

    /**
     * Search provinces
     *
     * Busca provincias por nombre o código INE.
     *
     * @queryParam q string Término de búsqueda. Example: barcelona
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Barcelona",
     *       "slug": "barcelona",
     *       "ine_code": "08"
     *     }
     *   ],
     *   "meta": {...}
     * }
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|max:255',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100'
        ]);

        $perPage = min($request->get('per_page', 20), 100);
        $searchTerm = $request->q;

        $provinces = Province::with(['autonomousCommunity', 'country'])
            ->where('name', 'LIKE', "%{$searchTerm}%")
            ->orWhere('ine_code', 'LIKE', "%{$searchTerm}%")
            ->orderBy('name')
            ->paginate($perPage);

        return response()->json([
            'data' => $provinces->items(),
            'meta' => [
                'current_page' => $provinces->currentPage(),
                'last_page' => $provinces->lastPage(),
                'per_page' => $provinces->perPage(),
                'total' => $provinces->total(),
            ]
        ]);
    }
}
