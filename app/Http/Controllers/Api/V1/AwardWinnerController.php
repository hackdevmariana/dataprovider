<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\AwardWinnerResource;
use App\Http\Requests\StoreAwardWinnerRequest;
use App\Services\AwardWinnersService;
use Illuminate\Http\JsonResponse;

/**
 * @group Award Winners
 *
 * APIs para la gestión de ganadores de premios.
 * Permite crear, consultar y gestionar ganadores de premios del sistema.
 */
class AwardWinnerController extends Controller
{
    /**
     * Display a listing of award winners
     *
     * Obtiene una lista de todos los ganadores de premios.
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "person_id": 1,
     *       "award_id": 1,
     *       "year": 2023,
     *       "classification": "Winner",
     *       "work_id": 1,
     *       "municipality_id": 1
     *     }
     *   ]
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\AwardWinnerResource
     * @apiResourceModel App\Models\AwardWinner
     */
    public function index(AwardWinnersService $service): JsonResponse
    {
        $winners = $service->listWinners();
        
        return response()->json([
            'data' => AwardWinnerResource::collection($winners)
        ]);
    }

    /**
     * Display the specified award winner
     *
     * Obtiene los detalles de un ganador específico por ID.
     *
     * @urlParam id integer ID del ganador. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "person_id": 1,
     *       "award_id": 1,
     *       "year": 2023,
     *       "classification": "Winner",
     *       "work_id": 1,
     *       "municipality_id": 1
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Ganador no encontrado"
     * }
     *
     * @apiResourceModel App\Models\AwardWinner
     */
    public function show($id, AwardWinnersService $service): JsonResponse
    {
        $winner = $service->getWinnerById((int) $id);
        
        return response()->json([
            'data' => new AwardWinnerResource($winner)
        ]);
    }

    /**
     * Store a newly created award winner
     *

     * Crea un nuevo ganador de premio en el sistema.
     *
     * @bodyParam person_id integer required ID de la persona. Example: 1
     * @bodyParam award_id integer required ID del premio. Example: 1
     * @bodyParam year integer required Año del premio. Example: 2023
     * @bodyParam classification string Clasificación del premio. Example: Winner
     * @bodyParam work_id integer ID de la obra. Example: 1
     * @bodyParam municipality_id integer ID del municipio. Example: 1
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *       "person_id": 1,
     *       "award_id": 1,
     *       "year": 2023,
     *       "classification": "Winner"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\AwardWinner
     * @authenticated
     */
    public function store(StoreAwardWinnerRequest $request, AwardWinnersService $service): JsonResponse
    {
        $awardWinner = $service->createWinner($request->validated());

        return response()->json([
            'data' => new AwardWinnerResource($awardWinner)
        ], 201);
    }
}
