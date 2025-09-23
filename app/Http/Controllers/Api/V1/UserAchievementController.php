<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\UserAchievement;
use App\Http\Resources\V1\UserAchievementResource;
use App\Http\Requests\StoreUserAchievementRequest;
use App\Http\Requests\UpdateUserAchievementRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
/**
 * @group User Achievements
 *
 * APIs para la gestión de logros de usuarios del sistema.
 * Permite crear, consultar y gestionar logros obtenidos por usuarios.
 */
class UserAchievementController extends Controller
{
    /**
     * Display a listing of user achievements
     *
     * Obtiene una lista paginada de logros del usuario autenticado.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     * @queryParam achievement_id integer Filtrar por logro específico. Example: 1
     * @queryParam status string Filtrar por estado (unlocked, locked, in_progress). Example: unlocked
     * @queryParam date_from string Filtrar desde fecha (YYYY-MM-DD). Example: 2024-01-01
     * @queryParam date_to string Filtrar hasta fecha (YYYY-MM-DD). Example: 2024-01-31
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "user_id": 1,
     *       "achievement_id": 1,
     *       "status": "unlocked",
     *       "unlocked_at": "2024-01-01T00:00:00.000000Z",
     *       "progress": 100,
     *       "achievement": {...}
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\UserAchievementResource
     * @apiResourceModel App\Models\UserAchievement
     * @authenticated
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'achievement_id' => 'sometimes|integer|exists:achievements,id',
            'status' => 'sometimes|string|in:unlocked,locked,in_progress',
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date|after_or_equal:date_from'
        ]);

        $query = UserAchievement::where('user_id', auth()->id())
                               ->with(['achievement']);

        if ($request->has('achievement_id')) {
            $query->where('achievement_id', $request->achievement_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->where('unlocked_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('unlocked_at', '<=', $request->date_to);
        }

        $userAchievements = $query->orderBy('unlocked_at', 'desc')
                                 ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => UserAchievementResource::collection($userAchievements),
            'meta' => [
                'current_page' => $userAchievements->currentPage(),
                'last_page' => $userAchievements->lastPage(),
                'per_page' => $userAchievements->perPage(),
                'total' => $userAchievements->total(),
            ]
        ]);
    }

    /**
     * Store a newly created user achievement
     *
     * Crea un nuevo logro de usuario en el sistema.
     *
     * @bodyParam achievement_id integer required ID del logro. Example: 1
     * @bodyParam status string required Estado del logro (unlocked, locked, in_progress). Example: unlocked
     * @bodyParam progress integer Progreso del logro (0-100). Example: 100
     * @bodyParam unlocked_at string Fecha de desbloqueo (ISO 8601). Example: 2024-01-01T00:00:00Z
     * @bodyParam metadata object Metadatos adicionales (JSON). Example: {"points": 100, "bonus": "gold"}
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "user_id": 1,
     *     "achievement_id": 1,
     *     "status": "unlocked",
     *     "progress": 100,
     *     "unlocked_at": "2024-01-01T00:00:00.000000Z",
     *     "created_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\UserAchievement
     * @authenticated
     */
    public function store(StoreUserAchievementRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        
        if ($data['status'] === 'unlocked' && !isset($data['unlocked_at'])) {
            $data['unlocked_at'] = now();
        }
        
        $userAchievement = UserAchievement::create($data);

        return response()->json([
            'data' => new UserAchievementResource($userAchievement->load('achievement'))
        ], 201);
    }

    /**
     * Display the specified user achievement
     *
     * Obtiene los detalles de un logro de usuario específico.
     *
     * @urlParam userAchievement integer ID del logro de usuario. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "user_id": 1,
     *     "achievement_id": 1,
     *     "status": "unlocked",
     *     "progress": 100,
     *     "unlocked_at": "2024-01-01T00:00:00.000000Z",
     *     "metadata": {
     *       "points": 100,
     *       "bonus": "gold"
     *     },
     *     "achievement": {...}
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Logro de usuario no encontrado"
     * }
     *
     * @apiResourceModel App\Models\UserAchievement
     * @authenticated
     */
    public function show(UserAchievement $userAchievement): JsonResponse
    {
        // Verificar que el logro pertenece al usuario autenticado
        if ($userAchievement->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Logro de usuario no encontrado'
            ], 404);
        }

        return response()->json([
            'data' => new UserAchievementResource($userAchievement->load('achievement'))
        ]);
    }

    /**
     * Update the specified user achievement
     *
     * Actualiza un logro de usuario existente.
     *
     * @urlParam userAchievement integer ID del logro de usuario. Example: 1
     * @bodyParam status string Estado del logro (unlocked, locked, in_progress). Example: unlocked
     * @bodyParam progress integer Progreso del logro (0-100). Example: 100
     * @bodyParam unlocked_at string Fecha de desbloqueo (ISO 8601). Example: 2024-01-01T00:00:00Z
     * @bodyParam metadata object Metadatos adicionales (JSON). Example: {"points": 150, "bonus": "platinum"}
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "status": "unlocked",
     *     "progress": 100,
     *     "unlocked_at": "2024-01-01T00:00:00.000000Z",
     *     "metadata": {
     *       "points": 150,
     *       "bonus": "platinum"
     *     },
     *     "updated_at": "2024-01-02T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Logro de usuario no encontrado"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\UserAchievement
     * @authenticated
     */
    public function update(UpdateUserAchievementRequest $request, UserAchievement $userAchievement): JsonResponse
    {
        // Verificar que el logro pertenece al usuario autenticado
        if ($userAchievement->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Logro de usuario no encontrado'
            ], 404);
        }

        $data = $request->validated();
        
        if ($data['status'] === 'unlocked' && !isset($data['unlocked_at'])) {
            $data['unlocked_at'] = now();
        }
        
        $userAchievement->update($data);

        return response()->json([
            'data' => new UserAchievementResource($userAchievement->load('achievement'))
        ]);
    }

    /**
     * Remove the specified user achievement
     *
     * Elimina un logro de usuario del sistema.
     *
     * @urlParam userAchievement integer ID del logro de usuario. Example: 1
     *
     * @response 204 {
     *   "message": "Logro de usuario eliminado exitosamente"
     * }
     *
     * @response 404 {
     *   "message": "Logro de usuario no encontrado"
     * }
     *
     * @authenticated
     */
    public function destroy(UserAchievement $userAchievement): JsonResponse
    {
        // Verificar que el logro pertenece al usuario autenticado
        if ($userAchievement->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Logro de usuario no encontrado'
            ], 404);
        }

        $userAchievement->delete();

        return response()->json([
            'message' => 'Logro de usuario eliminado exitosamente'
        ], 204);
    }
}
