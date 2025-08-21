<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\UserChallenge;
use App\Http\Resources\V1\UserChallengeResource;
use App\Http\Requests\StoreUserChallengeRequest;
use App\Http\Requests\UpdateUserChallengeRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @group User Challenges
 *
 * APIs para la gestión de desafíos de usuarios del sistema.
 * Permite crear, consultar y gestionar desafíos participados por usuarios.
 */
class UserChallengeController extends Controller
{
    /**
     * Display a listing of user challenges
     *
     * Obtiene una lista paginada de desafíos del usuario autenticado.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     * @queryParam challenge_id integer Filtrar por desafío específico. Example: 1
     * @queryParam status string Filtrar por estado (active, completed, failed). Example: active
     * @queryParam date_from string Filtrar desde fecha (YYYY-MM-DD). Example: 2024-01-01
     * @queryParam date_to string Filtrar hasta fecha (YYYY-MM-DD). Example: 2024-01-31
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "user_id": 1,
     *       "challenge_id": 1,
     *       "status": "active",
     *       "started_at": "2024-01-01T00:00:00.000000Z",
     *       "progress": 75,
     *       "challenge": {...}
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\UserChallengeResource
     * @apiResourceModel App\Models\UserChallenge
     * @authenticated
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'challenge_id' => 'sometimes|integer|exists:challenges,id',
            'status' => 'sometimes|string|in:active,completed,failed',
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date|after_or_equal:date_from'
        ]);

        $query = UserChallenge::where('user_id', auth()->id())
                             ->with(['challenge']);

        if ($request->has('challenge_id')) {
            $query->where('challenge_id', $request->challenge_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->where('started_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('started_at', '<=', $request->date_to);
        }

        $userChallenges = $query->orderBy('started_at', 'desc')
                               ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => UserChallengeResource::collection($userChallenges),
            'meta' => [
                'current_page' => $userChallenges->currentPage(),
                'last_page' => $userChallenges->lastPage(),
                'per_page' => $userChallenges->perPage(),
                'total' => $userChallenges->total(),
            ]
        ]);
    }

    /**
     * Store a newly created user challenge
     *
     * Crea un nuevo desafío de usuario en el sistema.
     *
     * @bodyParam challenge_id integer required ID del desafío. Example: 1
     * @bodyParam status string required Estado del desafío (active, completed, failed). Example: active
     * @bodyParam progress integer Progreso del desafío (0-100). Example: 0
     * @bodyParam started_at string Fecha de inicio (ISO 8601). Example: 2024-01-01T00:00:00Z
     * @bodyParam completed_at string Fecha de finalización (ISO 8601). Example: 2024-01-31T23:59:59Z
     * @bodyParam metadata object Metadatos adicionales (JSON). Example: {"attempts": 1, "bonus": "early_bird"}
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "user_id": 1,
     *     "challenge_id": 1,
     *     "status": "active",
     *     "progress": 0,
     *     "started_at": "2024-01-01T00:00:00.000000Z",
     *     "created_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\UserChallenge
     * @authenticated
     */
    public function store(StoreUserChallengeRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        
        if (!isset($data['started_at'])) {
            $data['started_at'] = now();
        }
        
        $userChallenge = UserChallenge::create($data);

        return response()->json([
            'data' => new UserChallengeResource($userChallenge->load('challenge'))
        ], 201);
    }

    /**
     * Display the specified user challenge
     *
     * Obtiene los detalles de un desafío de usuario específico.
     *
     * @urlParam userChallenge integer ID del desafío de usuario. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "user_id": 1,
     *     "challenge_id": 1,
     *     "status": "active",
     *     "progress": 75,
     *     "started_at": "2024-01-01T00:00:00.000000Z",
     *     "completed_at": null,
     *     "metadata": {
     *       "attempts": 2,
     *       "bonus": "early_bird"
     *     },
     *     "challenge": {...}
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Desafío de usuario no encontrado"
     * }
     *
     * @apiResourceModel App\Models\UserChallenge
     * @authenticated
     */
    public function show(UserChallenge $userChallenge): JsonResponse
    {
        // Verificar que el desafío pertenece al usuario autenticado
        if ($userChallenge->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Desafío de usuario no encontrado'
            ], 404);
        }

        return response()->json([
            'data' => new UserChallengeResource($userChallenge->load('challenge'))
        ]);
    }

    /**
     * Update the specified user challenge
     *
     * Actualiza un desafío de usuario existente.
     *
     * @urlParam userChallenge integer ID del desafío de usuario. Example: 1
     * @bodyParam status string Estado del desafío (active, completed, failed). Example: completed
     * @bodyParam progress integer Progreso del desafío (0-100). Example: 100
     * @bodyParam started_at string Fecha de inicio (ISO 8601). Example: 2024-01-01T00:00:00Z
     * @bodyParam completed_at string Fecha de finalización (ISO 8601). Example: 2024-01-31T23:59:59Z
     * @bodyParam metadata object Metadatos adicionales (JSON). Example: {"attempts": 3, "bonus": "speed_runner"}
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "status": "completed",
     *     "progress": 100,
     *     "completed_at": "2024-01-31T23:59:59.000000Z",
     *     "metadata": {
     *       "attempts": 3,
     *       "bonus": "speed_runner"
     *     },
     *     "updated_at": "2024-01-31T23:59:59.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Desafío de usuario no encontrado"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\UserChallenge
     * @authenticated
     */
    public function update(UpdateUserChallengeRequest $request, UserChallenge $userChallenge): JsonResponse
    {
        // Verificar que el desafío pertenece al usuario autenticado
        if ($userChallenge->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Desafío de usuario no encontrado'
            ], 404);
        }

        $data = $request->validated();
        
        if ($data['status'] === 'completed' && !isset($data['completed_at'])) {
            $data['completed_at'] = now();
        }
        
        $userChallenge->update($data);

        return response()->json([
            'data' => new UserChallengeResource($userChallenge->load('challenge'))
        ]);
    }

    /**
     * Remove the specified user challenge
     *
     * Elimina un desafío de usuario del sistema.
     *
     * @urlParam userChallenge integer ID del desafío de usuario. Example: 1
     *
     * @response 204 {
     *   "message": "Desafío de usuario eliminado exitosamente"
     * }
     *
     * @response 404 {
     *   "message": "Desafío de usuario no encontrado"
     * }
     *
     * @authenticated
     */
    public function destroy(UserChallenge $userChallenge): JsonResponse
    {
        // Verificar que el desafío pertenece al usuario autenticado
        if ($userChallenge->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Desafío de usuario no encontrado'
            ], 404);
        }

        $userChallenge->delete();

        return response()->json([
            'message' => 'Desafío de usuario eliminado exitosamente'
        ], 204);
    }
}
