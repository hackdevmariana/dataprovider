<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\AwardResource;
use App\Http\Requests\StoreAwardRequest;
use App\Services\AwardsService;
use Illuminate\Http\JsonResponse;

/**
 * @group Awards
 *
 * APIs para la gestión de premios y reconocimientos.
 * Permite crear, consultar y gestionar premios del sistema.
 */
class AwardController extends Controller
{
    /**
     * Display a listing of awards
     *
     * Obtiene una lista de premios usando el servicio de premios.
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Premio Nobel de la Paz",
     *       "slug": "premio-nobel-paz",
     *       "description": "Reconocimiento a la paz mundial",
     *       "awarded_by": "Comité Nobel",
     *       "first_year_awarded": 1901,
     *       "category": "paz"
     *     }
     *   ]
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\AwardResource
     * @apiResourceModel App\Models\Award
     */
    public function index(AwardsService $awardsService): JsonResponse
    {
        $awards = $awardsService->listAwards();
        
        return response()->json([
            'data' => AwardResource::collection($awards)
        ]);
    }

    /**
     * Display the specified award
     *
     * Obtiene los detalles de un premio específico por ID o slug.
     *
     * @urlParam idOrSlug integer|string ID o slug del premio. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Premio Nobel de la Paz",
     *     "slug": "premio-nobel-paz",
     *     "description": "Reconocimiento a la paz mundial",
     *     "awarded_by": "Comité Nobel",
     *     "first_year_awarded": 1901,
     *     "category": "paz"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Premio no encontrado"
     * }
     *
     * @apiResourceModel App\Models\Award
     */
    public function show($idOrSlug, AwardsService $awardsService): JsonResponse
    {
        $award = $awardsService->getAwardByIdOrSlug($idOrSlug);

        return response()->json([
            'data' => new AwardResource($award)
        ]);
    }

    /**
     * Store a newly created award
     *
     * Crea un nuevo premio en el sistema.
     *
     * @bodyParam name string required Nombre del premio. Example: Premio Nobel de la Paz
     * @bodyParam slug string required Slug único del premio. Example: premio-nobel-paz
     * @bodyParam description string Descripción del premio. Example: Reconocimiento a la paz mundial
     * @bodyParam awarded_by string Entidad que otorga el premio. Example: Comité Nobel
     * @bodyParam first_year_awarded integer Primer año en que se otorgó. Example: 1901
     * @bodyParam category string Categoría del premio. Example: paz
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "name": "Premio Nobel de la Paz",
     *     "slug": "premio-nobel-paz",
     *     "description": "Reconocimiento a la paz mundial",
     *     "awarded_by": "Comité Nobel",
     *     "first_year_awarded": 1901,
     *     "category": "paz"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\Award
     * @authenticated
     */
    public function store(StoreAwardRequest $request, AwardsService $awardsService): JsonResponse
    {
        $award = $awardsService->createAward($request->validated());

        return response()->json([
            'data' => new AwardResource($award)
        ], 201);
    }
}
