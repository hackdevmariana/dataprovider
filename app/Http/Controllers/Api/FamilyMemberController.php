<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FamilyMember;
use App\Http\Resources\FamilyMemberResource;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Family Members",
 *     description="Relaciones familiares entre personas"
 * )
 */
class FamilyMemberController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/family-members",
     *     summary="Listado de relaciones familiares",
     *     tags={"Family Members"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de relaciones familiares",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/FamilyMember")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $family = FamilyMember::with(['person', 'relative'])->get();
        return FamilyMemberResource::collection($family);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/family-members/{id}",
     *     summary="Mostrar relación familiar por ID",
     *     tags={"Family Members"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la relación familiar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Relación encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/FamilyMember")
     *     ),
     *     @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function show($id)
    {
        $family = FamilyMember::with(['person', 'relative'])->findOrFail($id);
        return new FamilyMemberResource($family);
    }
}
