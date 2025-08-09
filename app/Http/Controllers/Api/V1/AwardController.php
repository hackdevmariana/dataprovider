<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Award;
use App\Http\Resources\V1\AwardResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * @OA\Tag(
 *     name="Awards",
 *     description="Gestión de premios"
 * )
 */
class AwardController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/awards",
     *     summary="Listado de premios",
     *     tags={"Awards"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de premios",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Award")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $awards = Award::with('awardWinners')->get();
        return AwardResource::collection($awards);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/awards/{idOrSlug}",
     *     summary="Mostrar premio por ID o slug",
     *     tags={"Awards"},
     *     @OA\Parameter(
     *         name="idOrSlug",
     *         in="path",
     *         required=true,
     *         description="ID o slug del premio",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Premio encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/Award")
     *     ),
     *     @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function show($idOrSlug)
    {
        $award = Award::with('awardWinners')
            ->where('id', $idOrSlug)
            ->orWhere('slug', $idOrSlug)
            ->firstOrFail();

        return new AwardResource($award);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/awards",
     *     summary="Crear un nuevo premio",
     *     tags={"Awards"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "slug"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="slug", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="awarded_by", type="string"),
     *             @OA\Property(property="first_year_awarded", type="integer"),
     *             @OA\Property(property="category", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Premio creado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Award")
     *     ),
     *     @OA\Response(response=422, description="Datos inválidos")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:awards,slug',
            'description' => 'nullable|string',
            'awarded_by' => 'nullable|string|max:255',
            'first_year_awarded' => 'nullable|integer|min:1800|max:' . date('Y'),
            'category' => 'nullable|string|max:255',
        ]);

        $award = Award::create($validated);

        return new AwardResource($award);
    }
}


