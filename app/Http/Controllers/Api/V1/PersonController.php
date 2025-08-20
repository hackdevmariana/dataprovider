<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Person;
use Illuminate\Http\Request;
use App\Http\Resources\V1\PersonResource;
use Illuminate\Http\JsonResponse;

/**
 * @group Persons
 *
 * APIs para la gestión de personas y perfiles individuales.
 * Permite consultar información de personas, artistas, expertos y otros perfiles.
 */
class PersonController extends Controller
{
    /**
     * Display a listing of persons
     *
     * Obtiene una lista de personas con sus relaciones cargadas.
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Juan Pérez",
     *       "slug": "juan-perez",
     *       "nationality": {...},
     *       "language": {...},
     *       "image": {...},
     *       "aliases": [...]
     *     }
     *   ]
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\PersonResource
     * @apiResourceModel App\Models\Person
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
