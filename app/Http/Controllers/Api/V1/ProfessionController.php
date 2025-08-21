<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Profession;
use App\Http\Requests\StoreProfessionRequest;
use App\Http\Resources\V1\ProfessionResource;
use App\Services\ProfessionsService;
use Illuminate\Http\JsonResponse;

/**
 * @group Professions
 *
 * APIs para la gestión de profesiones y oficios.
 * Permite crear, consultar y gestionar profesiones del sistema.
 */
class ProfessionController extends Controller
{
    /**
     * Display a listing of professions
     *
     * Obtiene una lista de todas las profesiones disponibles.
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Ingeniero de Software",
     *       "slug": "ingeniero-de-software",
     *       "category": "Tecnología",
     *       "is_public_facing": true
     *     }
     *   ]
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\ProfessionResource
     * @apiResourceModel App\Models\Profession
     */
    public function index(ProfessionsService $service): JsonResponse
    {
        $professions = $service->list();
        
        return response()->json([
            'data' => ProfessionResource::collection($professions)
        ]);
    }

    /**
     * Display the specified profession
     *
     * Obtiene los detalles de una profesión específica por ID o slug.
     *
     * @urlParam idOrSlug integer|string ID o slug de la profesión. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "name": "Ingeniero de Software",
     *       "slug": "ingeniero-de-software",
     *       "category": "Tecnología",
     *       "is_public_facing": true
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Profesión no encontrada"
     * }
     *
     * @apiResourceModel App\Models\Profession
     */
    public function show($idOrSlug, ProfessionsService $service): JsonResponse
    {
        $profession = $service->findByIdOrSlug($idOrSlug);

        return response()->json([
            'data' => new ProfessionResource($profession)
        ]);
    }

    /**
     * Store a newly created profession
     *
     * Crea una nueva profesión en el sistema.
     *
     * @bodyParam name string required Nombre de la profesión. Example: Ingeniero de Software
     * @bodyParam slug string required Slug único de la profesión. Example: ingeniero-de-software
     * @bodyParam category string required Categoría de la profesión. Example: Tecnología
     * @bodyParam is_public_facing boolean required Si la profesión es visible públicamente. Example: true
     * @bodyParam description string Descripción de la profesión. Example: Desarrollador de aplicaciones informáticas
     * @bodyParam requirements string Requisitos para ejercer la profesión. Example: Grado en Ingeniería Informática
     * @bodyParam salary_range string Rango salarial típico. Example: 30,000-60,000€
     * @bodyParam is_active boolean Si la profesión está activa. Example: true
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *       "name": "Ingeniero de Software",
     *       "slug": "ingeniero-de-software",
     *       "category": "Tecnología",
     *       "is_public_facing": true
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\Profession
     * @authenticated
     */
    public function store(StoreProfessionRequest $request, ProfessionsService $service): JsonResponse
    {
        $profession = $service->create($request->validated());

        return response()->json([
            'data' => new ProfessionResource($profession)
        ], 201);
    }
}
