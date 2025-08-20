<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserEndorsementResource;
use App\Models\UserEndorsement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @group User Endorsements
 *
 * APIs para la gestión de endorsements (recomendaciones) de usuarios.
 * Similar al sistema de LinkedIn, permite a los usuarios recomendar
 * las habilidades y conocimientos de otros usuarios.
 */
class UserEndorsementController extends Controller
{
    /**
     * Display a listing of user endorsements
     *
     * Obtiene una lista de endorsements con opciones de filtrado.
     *
     * @queryParam endorsed_user_id int ID del usuario recomendado. Example: 1
     * @queryParam endorser_id int ID del usuario que hace la recomendación. Example: 1
     * @queryParam skill_category string Categoría de habilidad. Example: energy
     * @queryParam skill_name string Nombre específico de la habilidad. Example: solar-installation
     * @queryParam endorsement_type string Tipo de endorsement (skill, knowledge, experience, project). Example: skill
     * @queryParam is_verified boolean Filtrar por endorsements verificados. Example: true
     * @queryParam min_rating number Puntuación mínima (1-5). Example: 4
     * @queryParam sort string Ordenamiento (recent, rating_desc, rating_asc, skill_name). Example: recent
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\UserEndorsementResource
     * @apiResourceModel App\Models\UserEndorsement
     */
    public function index(Request $request)
    {
        $query = UserEndorsement::with(['endorsedUser', 'endorser', 'skill'])
            ->when($request->endorsed_user_id, fn($q, $userId) => $q->where('endorsed_user_id', $userId))
            ->when($request->endorser_id, fn($q, $endorserId) => $q->where('endorser_id', $endorserId))
            ->when($request->skill_category, fn($q, $category) => $q->where('skill_category', $category))
            ->when($request->skill_name, fn($q, $skill) => $q->where('skill_name', $skill))
            ->when($request->endorsement_type, fn($q, $type) => $q->where('endorsement_type', $type))
            ->when($request->has('is_verified'), fn($q) => $q->where('is_verified', $request->boolean('is_verified')))
            ->when($request->min_rating, fn($q, $rating) => $q->where('rating', '>=', $rating));

        // Aplicar ordenamiento
        switch ($request->get('sort', 'recent')) {
            case 'rating_desc':
                $query->orderBy('rating', 'desc');
                break;
            case 'rating_asc':
                $query->orderBy('rating', 'asc');
                break;
            case 'skill_name':
                $query->orderBy('skill_name', 'asc');
                break;
            default: // recent
                $query->orderBy('created_at', 'desc');
        }

        $perPage = min($request->get('per_page', 15), 100);
        $endorsements = $query->paginate($perPage);

        return UserEndorsementResource::collection($endorsements);
    }

    /**
     * Store a new user endorsement
     *
     * Crea un nuevo endorsement para un usuario.
     *
     * @bodyParam endorsed_user_id int required ID del usuario a recomendar. Example: 1
     * @bodyParam skill_category string required Categoría de habilidad. Example: energy
     * @bodyParam skill_name string required Nombre de la habilidad. Example: solar-installation
     * @bodyParam endorsement_type string required Tipo de endorsement (skill, knowledge, experience, project). Example: skill
     * @bodyParam rating number required Puntuación (1-5). Example: 5
     * @bodyParam comment string Comentario sobre la habilidad. Example: Excelente conocimiento en instalaciones solares
     * @bodyParam project_context string Contexto del proyecto (si aplica). Example: Instalación residencial 5kW
     * @bodyParam duration_months int Duración de la experiencia en meses. Example: 24
     * @bodyParam is_anonymous boolean Si el endorsement es anónimo. Default: false. Example: false
     * @bodyParam is_verified boolean Si es verificado por la plataforma. Default: false. Example: false
     * @bodyParam metadata array Metadatos adicionales. Example: {"location": "Madrid", "certification": "solar"}
     *
     * @apiResource App\Http\Resources\V1\UserEndorsementResource
     * @apiResourceModel App\Models\UserEndorsement
     *
     * @response 201 {"data": {...}, "message": "Endorsement creado exitosamente"}
     * @response 409 {"message": "Ya has recomendado esta habilidad para este usuario"}
     * @response 422 {"message": "Datos inválidos", "errors": {...}}
     * @authenticated
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'endorsed_user_id' => 'required|exists:users,id',
            'skill_category' => 'required|string|max:100',
            'skill_name' => 'required|string|max:100',
            'endorsement_type' => 'required|in:skill,knowledge,experience,project',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'project_context' => 'nullable|string|max:500',
            'duration_months' => 'nullable|integer|min:1',
            'is_anonymous' => 'boolean',
            'is_verified' => 'boolean',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::guard('sanctum')->user();
        
        // No se puede hacer endorsement a uno mismo
        if ($request->endorsed_user_id === $user->id) {
            return response()->json([
                'message' => 'No puedes recomendarte a ti mismo'
            ], 422);
        }

        // Verificar que no exista ya un endorsement para la misma habilidad
        $existing = UserEndorsement::where('endorser_id', $user->id)
            ->where('endorsed_user_id', $request->endorsed_user_id)
            ->where('skill_name', $request->skill_name)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Ya has recomendado esta habilidad para este usuario',
                'data' => new UserEndorsementResource($existing)
            ], 409);
        }

        $endorsement = UserEndorsement::create(array_merge($validator->validated(), [
            'endorser_id' => $user->id,
            'is_anonymous' => $request->get('is_anonymous', false),
            'is_verified' => $request->get('is_verified', false),
        ]));

        $endorsement->load(['endorsedUser', 'endorser', 'skill']);

        return response()->json([
            'data' => new UserEndorsementResource($endorsement),
            'message' => 'Endorsement creado exitosamente'
        ], 201);
    }

    /**
     * Display the specified user endorsement
     *
     * Muestra un endorsement específico.
     *
     * @urlParam userEndorsement int required ID del endorsement. Example: 1
     *
     * @apiResource App\Http\Resources\V1\UserEndorsementResource
     * @apiResourceModel App\Models\UserEndorsement
     *
     * @response 404 {"message": "Endorsement no encontrado"}
     */
    public function show(UserEndorsement $userEndorsement)
    {
        $userEndorsement->load(['endorsedUser', 'endorser', 'skill']);
        return new UserEndorsementResource($userEndorsement);
    }

    /**
     * Update the specified user endorsement
     *
     * Actualiza un endorsement existente.
     *
     * @urlParam userEndorsement int required ID del endorsement. Example: 1
     * @bodyParam rating number Nueva puntuación (1-5). Example: 4
     * @bodyParam comment string Nuevo comentario. Example: Comentario actualizado
     * @bodyParam project_context string Nuevo contexto. Example: Nuevo contexto
     * @bodyParam duration_months int Nueva duración. Example: 36
     * @bodyParam is_anonymous boolean Si es anónimo. Example: false
     * @bodyParam metadata array Nuevos metadatos. Example: {"updated": true}
     *
     * @apiResource App\Http\Resources\V1\UserEndorsementResource
     * @apiResourceModel App\Models\UserEndorsement
     *
     * @response 403 {"message": "No autorizado"}
     * @response 422 {"message": "Datos inválidos", "errors": {...}}
     * @authenticated
     */
    public function update(Request $request, UserEndorsement $userEndorsement)
    {
        $user = Auth::guard('sanctum')->user();

        // Solo el endorser puede editar
        if ($userEndorsement->endorser_id !== $user->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'project_context' => 'nullable|string|max:500',
            'duration_months' => 'nullable|integer|min:1',
            'is_anonymous' => 'boolean',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $userEndorsement->update($validator->validated());
        $userEndorsement->load(['endorsedUser', 'endorser', 'skill']);

        return new UserEndorsementResource($userEndorsement);
    }

    /**
     * Remove the specified user endorsement
     *
     * Elimina un endorsement.
     *
     * @urlParam userEndorsement int required ID del endorsement. Example: 1
     *
     * @response 200 {"message": "Endorsement eliminado exitosamente"}
     * @response 403 {"message": "No autorizado"}
     * @authenticated
     */
    public function destroy(UserEndorsement $userEndorsement)
    {
        $user = Auth::guard('sanctum')->user();

        if ($userEndorsement->endorser_id !== $user->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $userEndorsement->delete();

        return response()->json(['message' => 'Endorsement eliminado exitosamente']);
    }

    /**
     * Get endorsements for a specific user
     *
     * Obtiene todos los endorsements de un usuario específico.
     *
     * @urlParam user int required ID del usuario. Example: 1
     * @queryParam skill_category string Filtrar por categoría. Example: energy
     * @queryParam endorsement_type string Filtrar por tipo. Example: skill
     * @queryParam sort string Ordenamiento. Example: rating_desc
     * @queryParam per_page int Cantidad por página. Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\UserEndorsementResource
     * @apiResourceModel App\Models\UserEndorsement
     */
    public function userEndorsements(Request $request, User $user)
    {
        $query = $user->endorsements()
            ->with(['endorsedUser', 'endorser', 'skill'])
            ->when($request->skill_category, fn($q, $category) => $q->where('skill_category', $category))
            ->when($request->endorsement_type, fn($q, $type) => $q->where('endorsement_type', $type));

        // Aplicar ordenamiento
        switch ($request->get('sort', 'rating_desc')) {
            case 'recent':
                $query->orderBy('created_at', 'desc');
                break;
            case 'rating_asc':
                $query->orderBy('rating', 'asc');
                break;
            case 'skill_name':
                $query->orderBy('skill_name', 'asc');
                break;
            default: // rating_desc
                $query->orderBy('rating', 'desc');
        }

        $perPage = min($request->get('per_page', 15), 100);
        $endorsements = $query->paginate($perPage);

        return UserEndorsementResource::collection($endorsements);
    }

    /**
     * Get skill summary for a user
     *
     * Obtiene un resumen de habilidades de un usuario basado en endorsements.
     *
     * @urlParam user int required ID del usuario. Example: 1
     * @queryParam skill_category string Filtrar por categoría. Example: energy
     *
     * @response 200 {"data": {"skills": [{"name": "solar-installation", "avg_rating": 4.5, "count": 10}]}}
     */
    public function skillSummary(Request $request, User $user)
    {
        $query = $user->endorsements()
            ->when($request->skill_category, fn($q, $category) => $q->where('skill_category', $category))
            ->selectRaw('
                skill_name,
                skill_category,
                COUNT(*) as endorsement_count,
                AVG(rating) as average_rating,
                MAX(rating) as max_rating,
                MIN(rating) as min_rating
            ')
            ->groupBy('skill_name', 'skill_category')
            ->orderBy('average_rating', 'desc')
            ->orderBy('endorsement_count', 'desc');

        $skills = $query->get();

        return response()->json([
            'data' => [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'total_endorsements' => $user->endorsements()->count(),
                'skills' => $skills
            ]
        ]);
    }

    /**
     * Verify an endorsement
     *
     * Marca un endorsement como verificado (solo para administradores).
     *
     * @urlParam userEndorsement int required ID del endorsement. Example: 1
     *
     * @response 200 {"message": "Endorsement verificado exitosamente"}
     * @response 403 {"message": "No autorizado"}
     * @authenticated
     */
    public function verify(UserEndorsement $userEndorsement)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user->hasRole('admin')) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $userEndorsement->update(['is_verified' => true]);

        return response()->json(['message' => 'Endorsement verificado exitosamente']);
    }
}
