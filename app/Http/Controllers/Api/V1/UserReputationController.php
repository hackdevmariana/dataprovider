<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserReputationResource;
use App\Models\UserReputation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group User Reputation
 *
 * APIs para la gestión del sistema de reputación de usuarios.
 * Similar al karma de Stack Overflow, permite a los usuarios ganar
 * reputación por contribuciones positivas a la comunidad.
 */
/**
 * @OA\Tag(
 *     name="Reputación de Usuario",
 *     description="APIs para la gestión de Reputación de Usuario"
 * )
 */
class UserReputationController extends Controller
{
    /**
     * Display a listing of user reputations
     *
     * Obtiene una lista de reputaciones de usuarios con opciones de filtrado.
     *
     * @queryParam user_id int ID del usuario para filtrar. Example: 1
     * @queryParam min_score int Puntuación mínima de reputación. Example: 100
     * @queryParam max_score int Puntuación máxima de reputación. Example: 1000
     * @queryParam level string Nivel de reputación (beginner, intermediate, advanced, expert, master). Example: expert
     * @queryParam is_verified boolean Filtrar por usuarios verificados. Example: true
     * @queryParam sort string Ordenamiento (score_desc, score_asc, recent, oldest). Example: score_desc
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\UserReputationResource
     * @apiResourceModel App\Models\UserReputation
     */
    public function index(Request $request)
    {
        $query = UserReputation::with(['user'])
            ->when($request->user_id, fn($q, $userId) => $q->where('user_id', $userId))
            ->when($request->min_score, fn($q, $score) => $q->where('score', '>=', $score))
            ->when($request->max_score, fn($q, $score) => $q->where('score', '<=', $score))
            ->when($request->level, fn($q, $level) => $q->where('level', $level))
            ->when($request->has('is_verified'), fn($q) => $q->whereHas('user', fn($u) => $u->where('is_verified', $request->boolean('is_verified'))));

        // Aplicar ordenamiento
        switch ($request->get('sort', 'score_desc')) {
            case 'score_asc':
                $query->orderBy('score', 'asc');
                break;
            case 'recent':
                $query->orderBy('updated_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('updated_at', 'asc');
                break;
            default: // score_desc
                $query->orderBy('score', 'desc');
        }

        $perPage = min($request->get('per_page', 15), 100);
        $reputations = $query->paginate($perPage);

        return UserReputationResource::collection($reputations);
    }

    /**
     * Store a new user reputation
     *
     * Crea una nueva reputación para un usuario.
     *
     * @bodyParam user_id int required ID del usuario. Example: 1
     * @bodyParam score int Puntuación inicial de reputación. Default: 0. Example: 0
     * @bodyParam level string Nivel inicial (beginner, intermediate, advanced, expert, master). Default: beginner. Example: beginner
     * @bodyParam badges_count int Cantidad inicial de insignias. Default: 0. Example: 0
     * @bodyParam contributions_count int Cantidad inicial de contribuciones. Default: 0. Example: 0
     * @bodyParam positive_feedback_count int Cantidad de feedback positivo. Default: 0. Example: 0
     * @bodyParam negative_feedback_count int Cantidad de feedback negativo. Default: 0. Example: 0
     * @bodyParam last_activity_at datetime Última actividad. Example: 2024-01-01 12:00:00
     *
     * @apiResource App\Http\Resources\V1\UserReputationResource
     * @apiResourceModel App\Models\UserReputation
     *
     * @response 201 {"data": {...}, "message": "Reputación creada exitosamente"}
     * @response 409 {"message": "El usuario ya tiene reputación"}
     * @response 422 {"message": "Datos inválidos", "errors": {...}}
     * @authenticated
     */
    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'score' => 'integer|min:0',
            'level' => 'in:beginner,intermediate,advanced,expert,master',
            'badges_count' => 'integer|min:0',
            'contributions_count' => 'integer|min:0',
            'positive_feedback_count' => 'integer|min:0',
            'negative_feedback_count' => 'integer|min:0',
            'last_activity_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar si ya existe reputación
        $existingReputation = UserReputation::where('user_id', $request->user_id)->first();
        if ($existingReputation) {
            return response()->json([
                'message' => 'El usuario ya tiene reputación',
                'data' => new UserReputationResource($existingReputation)
            ], 409);
        }

        $reputation = UserReputation::create(array_merge($validator->validated(), [
            'score' => $request->get('score', 0),
            'level' => $request->get('level', 'beginner'),
            'badges_count' => $request->get('badges_count', 0),
            'contributions_count' => $request->get('contributions_count', 0),
            'positive_feedback_count' => $request->get('positive_feedback_count', 0),
            'negative_feedback_count' => $request->get('negative_feedback_count', 0),
            'last_activity_at' => $request->get('last_activity_at', now()),
        ]));

        $reputation->load(['user']);

        return response()->json([
            'data' => new UserReputationResource($reputation),
            'message' => 'Reputación creada exitosamente'
        ], 201);
    }

    /**
     * Display the specified user reputation
     *
     * Muestra la reputación de un usuario específico.
     *
     * @urlParam userReputation int required ID de la reputación. Example: 1
     *
     * @apiResource App\Http\Resources\V1\UserReputationResource
     * @apiResourceModel App\Models\UserReputation
     *
     * @response 404 {"message": "Reputación no encontrada"}
     */
    public function show(UserReputation $userReputation)
    {
        $userReputation->load(['user']);
        return new UserReputationResource($userReputation);
    }

    /**
     * Update the specified user reputation
     *
     * Actualiza la reputación de un usuario.
     *
     * @urlParam userReputation int required ID de la reputación. Example: 1
     * @bodyParam score int Nueva puntuación. Example: 150
     * @bodyParam level string Nuevo nivel. Example: intermediate
     * @bodyParam badges_count int Cantidad de insignias. Example: 5
     * @bodyParam contributions_count int Cantidad de contribuciones. Example: 25
     * @bodyParam positive_feedback_count int Feedback positivo. Example: 20
     * @bodyParam negative_feedback_count int Feedback negativo. Example: 2
     * @bodyParam last_activity_at datetime Última actividad. Example: 2024-01-01 12:00:00
     *
     * @apiResource App\Http\Resources\V1\UserReputationResource
     * @apiResourceModel App\Models\UserReputation
     *
     * @response 403 {"message": "No autorizado"}
     * @response 422 {"message": "Datos inválidos", "errors": {...}}
     * @authenticated
     */
    public function update(Request $request, UserReputation $userReputation)
    {
        $user = Auth::guard('sanctum')->user();

        // Solo el usuario propietario o administradores pueden actualizar
        if ($userReputation->user_id !== $user->id && !$user->hasRole('admin')) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'score' => 'integer|min:0',
            'level' => 'in:beginner,intermediate,advanced,expert,master',
            'badges_count' => 'integer|min:0',
            'contributions_count' => 'integer|min:0',
            'positive_feedback_count' => 'integer|min:0',
            'negative_feedback_count' => 'integer|min:0',
            'last_activity_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $userReputation->update($validator->validated());
        $userReputation->load(['user']);

        return new UserReputationResource($userReputation);
    }

    /**
     * Remove the specified user reputation
     *
     * Elimina la reputación de un usuario.
     *
     * @urlParam userReputation int required ID de la reputación. Example: 1
     *
     * @response 200 {"message": "Reputación eliminada exitosamente"}
     * @response 403 {"message": "No autorizado"}
     * @authenticated
     */
    public function destroy(UserReputation $userReputation)
    {
        $user = Auth::guard('sanctum')->user();

        if ($userReputation->user_id !== $user->id && !$user->hasRole('admin')) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $userReputation->delete();

        return response()->json(['message' => 'Reputación eliminada exitosamente']);
    }

    /**
     * Get reputation for a specific user
     *
     * Obtiene la reputación de un usuario específico.
     *
     * @urlParam user int required ID del usuario. Example: 1
     *
     * @apiResource App\Http\Resources\V1\UserReputationResource
     * @apiResourceModel App\Models\UserReputation
     *
     * @response 404 {"message": "Usuario no encontrado o sin reputación"}
     */
    public function userReputation(User $user)
    {
        $reputation = $user->reputation;
        
        if (!$reputation) {
            return response()->json(['message' => 'Usuario no encontrado o sin reputación'], 404);
        }

        $reputation->load(['user']);
        return new UserReputationResource($reputation);
    }
}
