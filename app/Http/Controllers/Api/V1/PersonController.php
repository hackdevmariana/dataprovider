<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Person;
use Illuminate\Http\Request;
use App\Http\Resources\V1\PersonResource;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @group Persons
 *
 * APIs para la gestión de personas y perfiles individuales.
 * Permite consultar información de personas, artistas, expertos y otros perfiles.
 */
/**
 * @OA\Tag(
 *     name="Personas",
 *     description="APIs para la gestión de Personas"
 * )
 */
class PersonController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/persons",
     *     summary="Listar personas",
     *     description="Obtiene una lista de personas con sus relaciones cargadas",
     *     tags={"Personas"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de personas obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $persons = Person::with(['nationality', 'language', 'image', 'aliases'])->get();
        
        return response()->json([
            'data' => PersonResource::collection($persons)
        ]);
    }

    /**
     * Display the specified person
     *
     * Obtiene los detalles de una persona específica por ID o slug.
     *
     * @urlParam idOrSlug integer|string ID o slug de la persona. Example: 1
     *
     * @response 200 {
     *   "data": {
     *       "id": 1,
     *       "name": "Juan Pérez",
     *       "slug": "juan-perez",
     *       "nationality": {...},
     *       "language": {...},
     *       "image": {...},
     *       "aliases": [...]
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Persona no encontrada"
     * }
     *
     * @apiResourceModel App\Models\Person
     */
    public function show($idOrSlug): JsonResponse
    {
        $person = Person::with([
            'nationality',
            'language',
            'image',
            'aliases'
        ])->where('slug', $idOrSlug)
            ->orWhere('id', $idOrSlug)
            ->firstOrFail();

        return response()->json([
            'data' => new PersonResource($person)
        ]);
    }
}
