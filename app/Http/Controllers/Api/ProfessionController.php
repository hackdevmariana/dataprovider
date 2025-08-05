<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profession;
use Illuminate\Http\Request;
use App\Http\Resources\ProfessionResource;

class ProfessionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/professions",
     *     summary="Obtener listado de profesiones",
     *     tags={"Professions"},
     *     @OA\Response(
     *         response=200,
     *         description="Listado de profesiones",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Profession"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        $professions = Profession::all();
        return ProfessionResource::collection($professions);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/professions/{idOrSlug}",
     *     summary="Mostrar detalles de una profesión",
     *     tags={"Professions"},
     *     @OA\Parameter(
     *         name="idOrSlug",
     *         in="path",
     *         required=true,
     *         description="ID o slug de la profesión",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profesión encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/Profession")
     *     ),
     *     @OA\Response(response=404, description="Profesión no encontrada")
     * )
     */
    public function show($idOrSlug)
    {
        $profession = Profession::where('slug', $idOrSlug)
            ->orWhere('id', $idOrSlug)
            ->firstOrFail();

        return new ProfessionResource($profession);
    }
}
