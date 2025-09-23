<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PersonWork;
use App\Http\Resources\V1\PersonWorkResource;
use App\Http\Requests\StorePersonWorkRequest;
use App\Http\Requests\UpdatePersonWorkRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
/**
 * @group Person Works
 *
 * APIs para la gestión de trabajos de personas del sistema.
 * Permite crear, consultar y gestionar la relación entre personas y trabajos.
 */
class PersonWorkController extends Controller
{
    /**
     * Display a listing of person works
     *
     * Obtiene una lista paginada de todos los trabajos de personas.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     * @queryParam person_id integer Filtrar por persona. Example: 1
     * @queryParam work_id integer Filtrar por trabajo. Example: 1
     * @queryParam is_current boolean Filtrar por trabajo actual. Example: true
     * @queryParam status string Filtrar por estado (active, completed, terminated). Example: active
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "person_id": 1,
     *       "work_id": 1,
     *       "is_current": true,
     *       "status": "active",
     *       "started_at": "2020-01-01",
     *       "person": {...},
     *       "work": {...}
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\PersonWorkResource
     * @apiResourceModel App\Models\PersonWork
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'person_id' => 'sometimes|integer|exists:people,id',
            'work_id' => 'sometimes|integer|exists:works,id',
            'is_current' => 'sometimes|boolean',
            'status' => 'sometimes|string|in:active,completed,terminated'
        ]);

        $query = PersonWork::with(['person', 'work']);

        if ($request->has('person_id')) {
            $query->where('person_id', $request->person_id);
        }

        if ($request->has('work_id')) {
            $query->where('work_id', $request->work_id);
        }

        if ($request->has('is_current')) {
            $query->where('is_current', $request->boolean('is_current'));
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $personWorks = $query->orderBy('is_current', 'desc')
                            ->orderBy('started_at', 'desc')
                            ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => PersonWorkResource::collection($personWorks),
            'meta' => [
                'current_page' => $personWorks->currentPage(),
                'last_page' => $personWorks->lastPage(),
                'per_page' => $personWorks->perPage(),
                'total' => $personWorks->total(),
            ]
        ]);
    }

    /**
     * Store a newly created person work
     *
     * Crea un nuevo trabajo de persona en el sistema.
     *
     * @bodyParam person_id integer required ID de la persona. Example: 1
     * @bodyParam work_id integer required ID del trabajo. Example: 1
     * @bodyParam is_current boolean Si es el trabajo actual. Example: true
     * @bodyParam status string Estado del trabajo (active, completed, terminated). Example: active
     * @bodyParam started_at string Fecha de inicio (YYYY-MM-DD). Example: 2020-01-01
     * @bodyParam ended_at string Fecha de finalización (YYYY-MM-DD). Example: null
     * @bodyParam metadata object Metadatos adicionales (JSON). Example: {"position": "Senior Developer", "salary": 75000}
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *       "person_id": 1,
     *       "work_id": 1,
     *       "is_current": true,
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
     * @apiResourceModel App\Models\PersonWork
     * @authenticated
     */
    public function store(StorePersonWorkRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        if (!isset($data['started_at'])) {
            $data['started_at'] = now()->toDateString();
        }
        
        $personWork = PersonWork::create($data);

        return response()->json([
            'data' => new PersonWorkResource($personWork->load(['person', 'work']))
        ], 201);
    }

    /**
     * Display the specified person work
     *
     * Obtiene los detalles de un trabajo de persona específico.
     *
     * @urlParam personWork integer ID del trabajo de persona. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "person_id": 1,
     *       "work_id": 1,
     *       "is_current": true,
     *       "status": "active",
     *       "started_at": "2020-01-01",
     *       "ended_at": null,
     *       "metadata": {
     *         "position": "Senior Developer",
     *         "salary": 75000
     *       },
     *       "person": {...},
     *       "work": {...}
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Trabajo de persona no encontrado"
     * }
     *
     * @apiResourceModel App\Models\PersonWork
     */
    public function show(PersonWork $personWork): JsonResponse
    {
        return response()->json([
            'data' => new PersonWorkResource($personWork->load(['person', 'work']))
        ]);
    }

    /**
     * Update the specified person work
     *
     * Actualiza un trabajo de persona existente.
     *
     * @urlParam personWork integer ID del trabajo de persona. Example: 1
     * @bodyParam is_current boolean Si es el trabajo actual. Example: false
     * @bodyParam status string Estado del trabajo (active, completed, terminated). Example: completed
     * @bodyParam started_at string Fecha de inicio (YYYY-MM-DD). Example: 2020-01-01
     * @bodyParam ended_at string Fecha de finalización (YYYY-MM-DD). Example: 2024-01-01
     * @bodyParam metadata object Metadatos adicionales (JSON). Example: {"position": "Lead Developer", "salary": 85000, "achievements": ["Project A", "Team Lead"]}
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "is_current": false,
     *       "status": "completed",
     *       "ended_at": "2024-01-01",
     *       "metadata": {
     *         "position": "Lead Developer",
     *         "salary": 85000,
     *         "achievements": ["Project A", "Team Lead"]
     *       },
     *       "updated_at": "2024-01-02T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Trabajo de persona no encontrado"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\PersonWork
     * @authenticated
     */
    public function update(UpdatePersonWorkRequest $request, PersonWork $personWork): JsonResponse
    {
        $data = $request->validated();
        
        $personWork->update($data);

        return response()->json([
            'data' => new PersonWorkResource($personWork->load(['person', 'work']))
        ]);
    }

    /**
     * Remove the specified person work
     *
     * Elimina un trabajo de persona del sistema.
     *
     * @urlParam personWork integer ID del trabajo de persona. Example: 1
     *
     * @response 204 {
     *   "message": "Trabajo de persona eliminado exitosamente"
     * }
     *
     * @response 404 {
     *   "message": "Trabajo de persona no encontrado"
     * }
     *
     * @authenticated
     */
    public function destroy(PersonWork $personWork): JsonResponse
    {
        $personWork->delete();

        return response()->json([
            'message' => 'Trabajo de persona eliminado exitosamente'
        ], 204);
    }
}
