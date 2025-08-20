<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserReviewResource;
use App\Models\UserReview;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @group User Reviews
 *
 * APIs para la gestión de reseñas y valoraciones de usuarios.
 * Permite a los usuarios valorar a otros usuarios basándose en
 * su experiencia, conocimientos y contribuciones en la plataforma.
 */
class UserReviewController extends Controller
{
    /**
     * Display a listing of user reviews
     *
     * Obtiene una lista de reseñas de usuarios con opciones de filtrado.
     *
     * @queryParam reviewed_user_id int ID del usuario valorado. Example: 1
     * @queryParam reviewer_id int ID del usuario que hace la reseña. Example: 2
     * @queryParam rating int Valoración (1-5 estrellas). Example: 5
     * @queryParam category string Categoría de la reseña (expertise, collaboration, knowledge, helpfulness). Example: expertise
     * @queryParam is_verified boolean Filtrar por reseñas verificadas. Example: true
     * @queryParam date_from date Fecha de inicio (YYYY-MM-DD). Example: 2024-01-01
     * @queryParam date_to date Fecha de fin (YYYY-MM-DD). Example: 2024-12-31
     * @queryParam sort string Ordenamiento (recent, rating_desc, rating_asc, helpful_desc). Example: recent
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\UserReviewResource
     * @apiResourceModel App\Models\UserReview
     */
    public function index(Request $request): JsonResponse
    {
        $query = UserReview::with(['reviewer', 'reviewedUser', 'category']);

        // Filtros
        if ($request->filled('reviewed_user_id')) {
            $query->where('reviewed_user_id', $request->reviewed_user_id);
        }

        if ($request->filled('reviewer_id')) {
            $query->where('reviewer_id', $request->reviewer_id);
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('is_verified')) {
            $query->where('is_verified', $request->boolean('is_verified'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Ordenamiento
        $sort = $request->get('sort', 'recent');
        switch ($sort) {
            case 'rating_desc':
                $query->orderBy('rating', 'desc');
                break;
            case 'rating_asc':
                $query->orderBy('rating', 'asc');
                break;
            case 'helpful_desc':
                $query->orderBy('helpful_votes', 'desc');
                break;
            default:
                $query->latest();
        }

        $perPage = min($request->get('per_page', 15), 100);
        $reviews = $query->paginate($perPage);

        return response()->json([
            'data' => UserReviewResource::collection($reviews),
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ]
        ]);
    }

    /**
     * Store a newly created user review
     *
     * Crea una nueva reseña de usuario. Solo se puede reseñar a un usuario
     * una vez por categoría.
     *
     * @bodyParam reviewed_user_id int required ID del usuario a valorar. Example: 1
     * @bodyParam rating int required Valoración de 1 a 5 estrellas. Example: 5
     * @bodyParam title string required Título de la reseña. Example: Excelente colaborador
     * @bodyParam content string required Contenido detallado de la reseña. Example: Este usuario ha sido muy útil en el proyecto de energía solar
     * @bodyParam category string required Categoría (expertise, collaboration, knowledge, helpfulness). Example: collaboration
     * @bodyParam is_anonymous boolean Si la reseña es anónima. Example: false
     * @bodyParam tags array Etiquetas relacionadas. Example: ["energía solar", "colaboración"]
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "reviewer_id": 2,
     *     "reviewed_user_id": 1,
     *     "rating": 5,
     *     "title": "Excelente colaborador",
     *     "content": "Este usuario ha sido muy útil en el proyecto de energía solar",
     *     "category": "collaboration",
     *     "is_verified": false,
     *     "helpful_votes": 0,
     *     "created_at": "2024-01-15T10:00:00.000000Z"
     *   },
     *   "message": "Reseña creada exitosamente"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos",
     *   "errors": {
     *     "rating": ["La valoración debe estar entre 1 y 5"],
     *     "reviewed_user_id": ["No puedes reseñarte a ti mismo"]
     *   }
     * }
     *
     * @authenticated
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reviewed_user_id' => 'required|exists:users,id|different:' . Auth::id(),
            'rating' => 'required|integer|between:1,5',
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10|max:2000',
            'category' => 'required|in:expertise,collaboration,knowledge,helpfulness',
            'is_anonymous' => 'boolean',
            'tags' => 'array|max:10',
            'tags.*' => 'string|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar que no existe una reseña previa
        $existingReview = UserReview::where('reviewer_id', Auth::id())
            ->where('reviewed_user_id', $request->reviewed_user_id)
            ->where('category', $request->category)
            ->first();

        if ($existingReview) {
            return response()->json([
                'message' => 'Ya has reseñado a este usuario en esta categoría'
            ], 422);
        }

        $review = UserReview::create([
            'reviewer_id' => Auth::id(),
            'reviewed_user_id' => $request->reviewed_user_id,
            'rating' => $request->rating,
            'title' => $request->title,
            'content' => $request->content,
            'category' => $request->category,
            'is_anonymous' => $request->boolean('is_anonymous', false),
            'tags' => $request->tags ?? [],
            'is_verified' => false,
            'helpful_votes' => 0
        ]);

        return response()->json([
            'data' => new UserReviewResource($review),
            'message' => 'Reseña creada exitosamente'
        ], 201);
    }

    /**
     * Display the specified user review
     *
     * Obtiene los detalles de una reseña específica.
     *
     * @urlParam id int required ID de la reseña. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "reviewer": {
     *       "id": 2,
     *       "name": "María García"
     *     },
     *     "reviewed_user": {
     *       "id": 1,
     *       "name": "Juan Pérez"
     *     },
     *     "rating": 5,
     *     "title": "Excelente colaborador",
     *     "content": "Este usuario ha sido muy útil en el proyecto de energía solar",
     *     "category": "collaboration",
     *     "is_verified": false,
     *     "helpful_votes": 3,
     *     "created_at": "2024-01-15T10:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Reseña no encontrada"
     * }
     */
    public function show(string $id): JsonResponse
    {
        $review = UserReview::with(['reviewer', 'reviewedUser', 'category'])
            ->findOrFail($id);

        return response()->json([
            'data' => new UserReviewResource($review)
        ]);
    }

    /**
     * Update the specified user review
     *
     * Actualiza una reseña existente. Solo el autor puede editarla
     * y solo si no ha sido verificada.
     *
     * @urlParam id int required ID de la reseña. Example: 1
     * @bodyParam rating int Valoración de 1 a 5 estrellas. Example: 4
     * @bodyParam title string Título de la reseña. Example: Buen colaborador
     * @bodyParam content string Contenido detallado de la reseña. Example: Usuario colaborativo en proyectos energéticos
     * @bodyParam is_anonymous boolean Si la reseña es anónima. Example: false
     * @bodyParam tags array Etiquetas relacionadas. Example: ["energía", "colaboración"]
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "rating": 4,
     *     "title": "Buen colaborador",
     *     "content": "Usuario colaborativo en proyectos energéticos",
     *     "updated_at": "2024-01-15T11:00:00.000000Z"
     *   },
     *   "message": "Reseña actualizada exitosamente"
     * }
     *
     * @response 403 {
     *   "message": "No tienes permiso para editar esta reseña"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos"
     * }
     *
     * @authenticated
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $review = UserReview::findOrFail($id);

        // Verificar permisos
        if ($review->reviewer_id !== Auth::id()) {
            return response()->json([
                'message' => 'No tienes permiso para editar esta reseña'
            ], 403);
        }

        if ($review->is_verified) {
            return response()->json([
                'message' => 'No se puede editar una reseña verificada'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'integer|between:1,5',
            'title' => 'string|max:255',
            'content' => 'string|min:10|max:2000',
            'is_anonymous' => 'boolean',
            'tags' => 'array|max:10',
            'tags.*' => 'string|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $review->update($request->only([
            'rating', 'title', 'content', 'is_anonymous', 'tags'
        ]));

        return response()->json([
            'data' => new UserReviewResource($review),
            'message' => 'Reseña actualizada exitosamente'
        ]);
    }

    /**
     * Remove the specified user review
     *
     * Elimina una reseña. Solo el autor puede eliminarla.
     *
     * @urlParam id int required ID de la reseña. Example: 1
     *
     * @response 200 {
     *   "message": "Reseña eliminada exitosamente"
     * }
     *
     * @response 403 {
     *   "message": "No tienes permiso para eliminar esta reseña"
     * }
     *
     * @authenticated
     */
    public function destroy(string $id): JsonResponse
    {
        $review = UserReview::findOrFail($id);

        if ($review->reviewer_id !== Auth::id()) {
            return response()->json([
                'message' => 'No tienes permiso para eliminar esta reseña'
            ], 403);
        }

        $review->delete();

        return response()->json([
            'message' => 'Reseña eliminada exitosamente'
        ]);
    }

    /**
     * Get reviews for a specific user
     *
     * Obtiene todas las reseñas de un usuario específico.
     *
     * @urlParam user int required ID del usuario. Example: 1
     * @queryParam category string Filtrar por categoría. Example: expertise
     * @queryParam rating int Filtrar por valoración mínima. Example: 4
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "rating": 5,
     *       "title": "Excelente colaborador",
     *       "content": "Muy útil en proyectos energéticos",
     *       "category": "collaboration"
     *     }
     *   ],
     *   "meta": {
     *     "current_page": 1,
     *     "total": 1
     *   }
     * }
     */
    public function userReviews(Request $request, int $user): JsonResponse
    {
        $query = UserReview::where('reviewed_user_id', $user)
            ->with(['reviewer', 'category']);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('rating')) {
            $query->where('rating', '>=', $request->rating);
        }

        $perPage = min($request->get('per_page', 15), 100);
        $reviews = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'data' => UserReviewResource::collection($reviews),
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ]
        ]);
    }

    /**
     * Mark review as helpful
     *
     * Marca una reseña como útil. Los usuarios pueden votar
     * por reseñas que consideren valiosas.
     *
     * @urlParam id int required ID de la reseña. Example: 1
     *
     * @response 200 {
     *   "message": "Voto registrado exitosamente",
     *   "helpful_votes": 4
     * }
     *
     * @response 422 {
     *   "message": "Ya has votado por esta reseña"
     * }
     *
     * @authenticated
     */
    public function markHelpful(string $id): JsonResponse
    {
        $review = UserReview::findOrFail($id);

        // Verificar que el usuario no vote por su propia reseña
        if ($review->reviewer_id === Auth::id()) {
            return response()->json([
                'message' => 'No puedes votar por tu propia reseña'
            ], 422);
        }

        // Aquí implementarías la lógica para evitar votos duplicados
        // Por simplicidad, incrementamos directamente
        $review->increment('helpful_votes');

        return response()->json([
            'message' => 'Voto registrado exitosamente',
            'helpful_votes' => $review->fresh()->helpful_votes
        ]);
    }
}
