<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Profession;
use App\Http\Requests\StoreProfessionRequest;
use App\Http\Resources\V1\ProfessionResource;
use App\Services\ProfessionsService;

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
    public function index(ProfessionsService $service)
    {
        $professions = $service->list();
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
    public function show($idOrSlug, ProfessionsService $service)
    {
        $profession = $service->findByIdOrSlug($idOrSlug);

        return new ProfessionResource($profession);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/professions",
     *     summary="Crear una nueva profesión",
     *     tags={"Professions"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "slug", "category", "is_public_facing"},
     *             @OA\Property(property="name", type="string", example="Ingeniero de Software"),
     *             @OA\Property(property="slug", type="string", example="ingeniero-de-software"),
     *             @OA\Property(property="category", type="string", example="Tecnología"),
     *             @OA\Property(property="is_public_facing", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Profesión creada exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Profession")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function store(StoreProfessionRequest $request, ProfessionsService $service)
    {
        $profession = $service->create($request->validated());

        return new ProfessionResource($profession);
    }
}


