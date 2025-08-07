<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AwardWinner;
use App\Http\Resources\AwardWinnerResource;
use Illuminate\Http\Request;

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
    public function index()
    {
        $winners = AwardWinner::with(['person', 'award', 'work', 'municipality'])->get();
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
    public function show($id)
    {
        $winner = AwardWinner::with(['person', 'award', 'work', 'municipality'])->findOrFail($id);
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'person_id' => 'required|exists:people,id',
            'award_id' => 'required|exists:awards,id',
            'year' => 'required|integer|min:1800|max:' . date('Y'),
            'classification' => 'nullable|string|max:255',
            'work_id' => 'nullable|exists:works,id',
            'municipality_id' => 'nullable|exists:municipalities,id',
        ]);

        $awardWinner = AwardWinner::create($validated);

        return new AwardWinnerResource($awardWinner);
    }
}
