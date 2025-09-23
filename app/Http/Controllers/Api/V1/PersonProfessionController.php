<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PersonProfession;
use App\Http\Resources\V1\PersonProfessionResource;
use App\Http\Requests\StorePersonProfessionRequest;
use App\Http\Requests\UpdatePersonProfessionRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
/**
 * @group Person Professions
 *
 * APIs para la gestión de profesiones de personas del sistema.
 * Permite crear, consultar y gestionar la relación entre personas y profesiones.
 */
class PersonProfessionController extends Controller
{
    /**
     * Display a listing of person professions
     *
     * Obtiene una lista paginada de todas las profesiones de personas.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     * @queryParam person_id integer Filtrar por persona. Example: 1
     * @queryParam profession_id integer Filtrar por profesión. Example: 1
     * @queryParam is_primary boolean Filtrar por profesión principal. Example: true
     * @queryParam status string Filtrar por estado (active, inactive, retired). Example: active
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "person_id": 1,
     *       "profession_id": 1,
     *       "is_primary": true,
     *       "status": "active",
     *       "started_at": "2020-01-01",
     *       "person": {...},
     *       "profession": {...}
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\PersonProfessionResource
     * @apiResourceModel App\Models\PersonProfession
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'person_id' => 'sometimes|integer|exists:people,id',
            'profession_id' => 'sometimes|integer|exists:professions,id',
            'is_primary' => 'sometimes|boolean',
            'status' => 'sometimes|string|in:active,inactive,retired'
        ]);

        $query = PersonProfession::with(['person', 'profession']);

        if ($request->has('person_id')) {
            $query->where('person_id', $request->person_id);
        }

        if ($request->has('profession_id')) {
            $query->where('profession_id', $request->profession_id);
        }

        if ($request->has('is_primary')) {
            $query->where('is_primary', $request->boolean('is_primary'));
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $personProfessions = $query->orderBy('is_primary', 'desc')
                                  ->orderBy('started_at', 'desc')
                                  ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => PersonProfessionResource::collection($personProfessions),
            'meta' => [
                'current_page' => $personProfessions->currentPage(),
                'last_page' => $personProfessions->lastPage(),
                'per_page' => $personProfessions->perPage(),
                'total' => $personProfessions->total(),
            ]
        ]);
    }

    /**
     * Store a newly created person profession
     *
     * Crea una nueva profesión de persona en el sistema.
     *
     * @bodyParam person_id integer required ID de la persona. Example: 1
     * @bodyParam profession_id integer required ID de la profesión. Example: 1
     * @bodyParam is_primary boolean Si es la profesión principal. Example: true
     * @bodyParam status string Estado de la profesión (active, inactive, retired). Example: active
     * @bodyParam started_at string Fecha de inicio (YYYY-MM-DD). Example: 2020-01-01
     * @bodyParam ended_at string Fecha de finalización (YYYY-MM-DD). Example: null
     * @bodyParam metadata object Metadatos adicionales (JSON). Example: {"specialization": "Web Development", "certifications": ["AWS", "Azure"]}
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *       "person_id": 1,
     *       "profession_id": 1,
     *       "is_primary": true,
     *       "status": "active",
     *       "started_at": "2020-01-01",
     *       "created_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\PersonProfession
     * @authenticated
     */
    public function store(StorePersonProfessionRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        if (!isset($data['started_at'])) {
            $data['started_at'] = now()->toDateString();
        }
        
        $personProfession = PersonProfession::create($data);

        return response()->json([
            'data' => new PersonProfessionResource($personProfession->load(['person', 'profession']))
        ], 201);
    }

    /**
     * Display the specified person profession
     *
     * Obtiene los detalles de una profesión de persona específica.
     *
     * @urlParam personProfession integer ID de la profesión de persona. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "person_id": 1,
     *       "profession_id": 1,
     *       "is_primary": true,
     *       "status": "active",
     *       "started_at": "2020-01-01",
     *       "ended_at": null,
     *       "metadata": {
     *         "specialization": "Web Development",
     *         "certifications": ["AWS", "Azure"]
     *       },
     *       "person": {...},
     *       "profession": {...}
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Profesión de persona no encontrada"
     * }
     *
     * @apiResourceModel App\Models\PersonProfession
     */
    public function show(PersonProfession $personProfession): JsonResponse
    {
        return response()->json([
            'data' => new PersonProfessionResource($personProfession->load(['person', 'profession']))
        ]);
    }

    /**
     * Update the specified person profession
     *
     * Actualiza una profesión de persona existente.
     *
     * @urlParam personProfession integer ID de la profesión de persona. Example: 1
     * @bodyParam is_primary boolean Si es la profesión principal. Example: false
     * @bodyParam status string Estado de la profesión (active, inactive, retired). Example: active
     * @bodyParam started_at string Fecha de inicio (YYYY-MM-DD). Example: 2020-01-01
     * @bodyParam ended_at string Fecha de finalización (YYYY-MM-DD). Example: 2024-01-01
     * @bodyParam metadata object Metadatos adicionales (JSON). Example: {"specialization": "Full Stack Development", "certifications": ["AWS", "Azure", "GCP"]}
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "is_primary": false,
     *       "status": "active",
     *       "ended_at": "2024-01-01",
     *       "metadata": {
     *         "specialization": "Full Stack Development",
     *         "certifications": ["AWS", "Azure", "GCP"]
     *       },
     *       "updated_at": "2024-01-02T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Profesión de persona no encontrada"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\PersonProfession
     * @authenticated
     */
    public function update(UpdatePersonProfessionRequest $request, PersonProfession $personProfession): JsonResponse
    {
        $data = $request->validated();
        
        $personProfession->update($data);

        return response()->json([
            'data' => new PersonProfessionResource($personProfession->load(['person', 'profession']))
        ]);
    }

    /**
     * Remove the specified person profession
     *
     * Elimina una profesión de persona del sistema.
     *
     * @urlParam personProfession integer ID de la profesión de persona. Example: 1
     *
     * @response 204 {
     *   "message": "Profesión de persona eliminada exitosamente"
     * }
     *
     * @response 404 {
     *   "message": "Profesión de persona no encontrada"
     * }
     *
     * @authenticated
     */
    public function destroy(PersonProfession $personProfession): JsonResponse
    {
        $personProfession->delete();

        return response()->json([
            'message' => 'Profesión de persona eliminada exitosamente'
        ], 204);
    }
}
