<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\AwardWinnerResource;
use App\Http\Requests\StoreAwardWinnerRequest;
use App\Services\AwardWinnersService;

/**
 * @OA\Tag(
 *     name="AwardWinners",
 *     description="Gestión de ganadores de premios"
 * )
 */
class AwardWinnerController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/award-winners",
     *     summary="Listado de ganadores de premios",
     *     tags={"AwardWinners"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de ganadores",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/AwardWinner")
     *         )
     *     )
     * )
     */
    public function index(AwardWinnersService $service)
    {
        $winners = $service->listWinners();
        return AwardWinnerResource::collection($winners);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/award-winners/{id}",
     *     summary="Mostrar ganador por ID",
     *     tags={"AwardWinners"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del ganador",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ganador encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/AwardWinner")
     *     ),
     *     @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function show($id, AwardWinnersService $service)
    {
        $winner = $service->getWinnerById((int) $id);
        return new AwardWinnerResource($winner);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/award-winners",
     *     summary="Crear un nuevo ganador de premio",
     *     tags={"AwardWinners"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"person_id", "award_id", "year"},
     *             @OA\Property(property="person_id", type="integer"),
     *             @OA\Property(property="award_id", type="integer"),
     *             @OA\Property(property="year", type="integer"),
     *             @OA\Property(property="classification", type="string"),
     *             @OA\Property(property="work_id", type="integer"),
     *             @OA\Property(property="municipality_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Ganador creado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/AwardWinner")
     *     ),
     *     @OA\Response(response=422, description="Datos inválidos")
     * )
     */
    public function store(StoreAwardWinnerRequest $request, AwardWinnersService $service)
    {
        $awardWinner = $service->createWinner($request->validated());

        return new AwardWinnerResource($awardWinner);
    }
}


