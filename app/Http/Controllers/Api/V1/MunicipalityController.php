<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Municipality;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @group Municipalities
 *
 * APIs para la gestión de municipios y localidades.
 * Permite consultar información de municipios, provincias y países.
 */
/**
 * @OA\Tag(
 *     name="Municipios",
 *     description="APIs para la gestión de Municipios"
 * )
 */
class MunicipalityController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/municipalities",
     *     summary="Listar municipios",
     *     description="Obtiene una lista paginada de municipios con sus relaciones",
     *     tags={"Municipios"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número de página",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Cantidad por página (máx 100)",
     *         required=false,
     *         @OA\Schema(type="integer", example=50)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de municipios obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100'
        ]);

        $perPage = min($request->get('per_page', 50), 100);
        $municipalities = Municipality::with(['province', 'autonomousCommunity', 'country'])->paginate($perPage);
        
        return response()->json([
            'data' => $municipalities->items(),
            'meta' => [
                'current_page' => $municipalities->currentPage(),
                'last_page' => $municipalities->lastPage(),
                'per_page' => $municipalities->perPage(),
                'total' => $municipalities->total(),
            ]
        ]);
    }

    /**
     * Get municipalities by province
     *
     * Obtiene los municipios de una provincia específica.
     *
     * @urlParam slug string Slug de la provincia. Example: barcelona
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Barcelona",
     *       "slug": "barcelona",
     *       "ine_code": "08019",
     *       "province": {...}
     *     }
     *   ]
     * }
     */
    public function byProvince($slug): JsonResponse
    {
        $municipalities = Municipality::whereHas('province', fn($q) => $q->where('slug', $slug))
            ->with(['province', 'autonomousCommunity', 'country'])
            ->get();

        return response()->json([
            'data' => $municipalities
        ]);
    }

    /**
     * Get municipalities by country
     *
     * Obtiene los municipios de un país específico.
     *
     * @urlParam slug string Slug del país. Example: espana
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 50
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Barcelona",
     *       "slug": "barcelona",
     *       "ine_code": "08019",
     *       "country": {...}
     *     }
     *   ],
     *   "meta": {...}
     * }
     */
    public function byCountry($slug, Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100'
        ]);

        $perPage = min($request->get('per_page', 50), 100);
        $municipalities = Municipality::whereHas('country', fn($q) => $q->where('slug', $slug))
            ->with(['province', 'autonomousCommunity', 'country'])
            ->paginate($perPage);

        return response()->json([
            'data' => $municipalities->items(),
            'meta' => [
                'current_page' => $municipalities->currentPage(),
                'last_page' => $municipalities->lastPage(),
                'per_page' => $municipalities->perPage(),
                'total' => $municipalities->total(),
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/municipalities/{idOrSlug}",
     *     summary="Obtener municipio específico",
     *     description="Obtiene los detalles de un municipio específico por ID o slug",
     *     tags={"Municipios"},
     *     @OA\Parameter(
     *         name="idOrSlug",
     *         in="path",
     *         description="ID o slug del municipio",
     *         required=true,
     *         @OA\Schema(type="string", example="barcelona")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Municipio obtenido exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Municipio no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Municipio no encontrado")
     *         )
     *     )
     * )
     */
    public function show($idOrSlug): JsonResponse
    {
        $municipality = Municipality::with(['province', 'autonomousCommunity', 'country'])
            ->where('id', $idOrSlug)
            ->orWhere('slug', $idOrSlug)
            ->first();

        if (!$municipality) {
            return response()->json(['message' => 'Municipio no encontrado'], 404);
        }

        return response()->json([
            'data' => $municipality
        ]);
    }

    /**
     * Search municipalities
     *
     * Busca municipios por nombre o código INE.
     *
     * @queryParam q string Término de búsqueda. Example: barcelona
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 50
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Barcelona",
     *       "slug": "barcelona",
     *       "ine_code": "08019"
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

        $perPage = min($request->get('per_page', 50), 100);
        $searchTerm = $request->q;

        $municipalities = Municipality::with(['province', 'autonomousCommunity', 'country'])
            ->where('name', 'LIKE', "%{$searchTerm}%")
            ->orWhere('ine_code', 'LIKE', "%{$searchTerm}%")
            ->orderBy('name')
            ->paginate($perPage);

        return response()->json([
            'data' => $municipalities->items(),
            'meta' => [
                'current_page' => $municipalities->currentPage(),
                'last_page' => $municipalities->lastPage(),
                'per_page' => $municipalities->perPage(),
                'total' => $municipalities->total(),
            ]
        ]);
    }
}
