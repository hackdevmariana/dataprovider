<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserReviewResource;
use App\Models\UserReview;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

/**
 * @group User Reviews
 *
 * APIs para la gestión de reseñas de usuarios en la plataforma.
 * Permite a los usuarios valorar y reseñar a otros usuarios
 * basándose en su experiencia, conocimientos y contribuciones.
 */
class UserReviewController extends Controller
{
    /**
     * Display a listing of user reviews
     *
     * Obtiene una lista de reseñas de usuarios con opciones de filtrado.
     *
     * @queryParam reviewer_id int ID del usuario que hace la reseña. Example: 1
     * @queryParam reviewed_user_id int ID del usuario reseñado. Example: 2
     * @queryParam rating int Filtro por calificación (1-5). Example: 5
     * @queryParam category string Categoría de la reseña (expertise, collaboration, reliability, communication). Example: expertise
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
        $request->validate([
            'reviewer_id' => 'sometimes|integer|exists:users,id',
            'reviewed_user_id' => 'sometimes|integer|exists:users,id',
            'rating' => 'sometimes|integer|between:1,5',
            'category' => 'sometimes|string|in:expertise,collaboration,reliability,communication',
            'is_verified' => 'sometimes|boolean',
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date',
            'sort' => 'sometimes|string|in:recent,rating_desc,rating_asc,helpful_desc',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100'
        ]);

        $query = UserReview::with(['reviewer', 'reviewedUser']);

        // Filtros
        if ($request->filled('reviewer_id')) {
            $query->where('reviewer_id', $request->reviewer_id);
        }

        if ($request->filled('reviewed_user_id')) {
            $query->where('reviewed_user_id', $request->reviewed_user_id);
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('is_verified')) {
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
                $query->orderBy('helpful_count', 'desc');
                break;
            default:
                $query->latest();
        }

        $perPage = min($request->get('per_page', 15), 100);
        $reviews = $query->paginate($perPage);

        return UserReviewResource::collection($reviews)->response();
    }

    /**
     * Store a newly created user review
     *
     * Crea una nueva reseña de usuario. Solo se puede reseñar a un usuario
     * una vez por categoría.
     *
     * @bodyParam reviewed_user_id int required ID del usuario a reseñar. Example: 2
     * @bodyParam rating int required Calificación de 1 a 5. Example: 5
     * @bodyParam title string required Título de la reseña. Example: Excelente colaborador
     * @bodyParam content text required Contenido detallado de la reseña. Example: Este usuario demostró un conocimiento excepcional en energías renovables y fue muy colaborativo en el proyecto.
     * @bodyParam category string required Categoría de la reseña. Example: expertise
     * @bodyParam is_anonymous boolean Si la reseña es anónima. Example: false
     * @bodyParam tags array Lista de etiquetas relacionadas. Example: ["energía solar", "colaboración"]
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "reviewer_id": 1,
     *     "reviewed_user_id": 2,
     *     "rating": 5,
     *     "title": "Excelente colaborador",
     *     "content": "Este usuario demostró un conocimiento excepcional...",
     *     "category": "expertise",
     *     "is_verified": false,
     *     "helpful_count": 0,
     *     "created_at": "2024-01-15T10:00:00.000000Z"
     *   },
     *   "message": "Reseña creada exitosamente"
     * }
     *
     * @response 422 {
     *   "message": "Ya has reseñado a este usuario en esta categoría",
     *   "errors": {
     *     "category": ["Ya existe una reseña para este usuario en esta categoría"]
     *   }
     * }
     *
     * @authenticated
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'reviewed_user_id' => 'required|integer|exists:users,id|different:reviewer_id',
            'rating' => 'required|integer|between:1,5',
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10|max:2000',
            'category' => 'required|string|in:expertise,collaboration,reliability,communication',
            'is_anonymous' => 'sometimes|boolean',
            'tags' => 'sometimes|array',
            'tags.*' => 'string|max:50'
        ]);

        $reviewerId = Auth::guard('sanctum')->user()->id;

        // Verificar que no haya reseña previa en la misma categoría
        $existingReview = UserReview::where('reviewer_id', $reviewerId)
            ->where('reviewed_user_id', $request->reviewed_user_id)
            ->where('category', $request->category)
            ->first();

        if ($existingReview) {
            throw ValidationException::withMessages([
                'category' => ['Ya existe una reseña para este usuario en esta categoría']
            ]);
        }

        $review = UserReview::create([
            'reviewer_id' => $reviewerId,
            'reviewed_user_id' => $request->reviewed_user_id,
            'rating' => $request->rating,
            'title' => $request->title,
            'content' => $request->content,
            'category' => $request->category,
            'is_anonymous' => $request->boolean('is_anonymous', false),
            'tags' => $request->tags ?? [],
            'is_verified' => false,
            'helpful_count' => 0
        ]);

        return (new UserReviewResource($review))
            ->response()
            ->setStatusCode(201);
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
     *     "reviewer_id": 1,
     *     "reviewed_user_id": 2,
     *     "rating": 5,
     *     "title": "Excelente colaborador",
     *     "content": "Este usuario demostró un conocimiento excepcional...",
     *     "category": "expertise",
     *     "is_verified": true,
     *     "helpful_count": 3,
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
        $review = UserReview::with(['reviewer', 'reviewedUser'])
            ->findOrFail($id);

        return (new UserReviewResource($review))->response();
    }

    /**
     * Update the specified user review
     *
     * Actualiza una reseña existente. Solo el autor puede modificarla
     * y solo si no ha sido verificada.
     *
     * @urlParam id int required ID de la reseña. Example: 1
     * @bodyParam rating int Calificación de 1 a 5. Example: 4
     * @bodyParam title string Título de la reseña. Example: Buen colaborador
     * @bodyParam content text Contenido detallado de la reseña. Example: Usuario con buen conocimiento en el área.
     * @bodyParam is_anonymous boolean Si la reseña es anónima. Example: false
     * @bodyParam tags array Lista de etiquetas relacionadas. Example: ["energía", "colaboración"]
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "rating": 4,
     *     "title": "Buen colaborador",
     *     "content": "Usuario con buen conocimiento en el área.",
     *     "updated_at": "2024-01-15T11:00:00.000000Z"
     *   },
     *   "message": "Reseña actualizada exitosamente"
     * }
     *
     * @response 403 {
     *   "message": "No tienes permiso para modificar esta reseña"
     * }
     *
     * @response 422 {
     *   "message": "No se puede modificar una reseña verificada"
     * }
     *
     * @authenticated
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $review = UserReview::findOrFail($id);

        // Verificar permisos
        if ($review->reviewer_id !== Auth::guard('sanctum')->user()->id) {
            return response()->json([
                'message' => 'No tienes permiso para modificar esta reseña'
            ], 403);
        }

        if ($review->is_verified) {
            return response()->json([
                'message' => 'No se puede modificar una reseña verificada'
            ], 422);
        }

        $request->validate([
            'rating' => 'sometimes|integer|between:1,5',
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string|min:10|max:2000',
            'is_anonymous' => 'sometimes|boolean',
            'tags' => 'sometimes|array',
            'tags.*' => 'string|max:50'
        ]);

        $review->update($request->only([
            'rating', 'title', 'content', 'is_anonymous', 'tags'
        ]));

        return (new UserReviewResource($review))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Remove the specified user review
     *
     * Elimina una reseña. Solo el autor puede eliminarla
     * y solo si no ha sido verificada.
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
     * @response 422 {
     *   "message": "No se puede eliminar una reseña verificada"
     * }
     *
     * @authenticated
     */
    public function destroy(string $id): JsonResponse
    {
        $review = UserReview::findOrFail($id);

        // Verificar permisos
        if ($review->reviewer_id !== Auth::guard('sanctum')->user()->id) {
            return response()->json([
                'message' => 'No tienes permiso para eliminar esta reseña'
            ], 403);
        }

        if ($review->is_verified) {
            return response()->json([
                'message' => 'No se puede eliminar una reseña verificada'
            ], 422);
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
     * @urlParam user int required ID del usuario. Example: 2
     * @queryParam category string Filtrar por categoría. Example: expertise
     * @queryParam rating int Filtrar por calificación mínima. Example: 4
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "rating": 5,
     *       "title": "Excelente colaborador",
     *       "content": "Muy buen trabajo...",
     *       "category": "expertise"
     *     }
     *   ]
     * }
     */
    public function userReviews(int $user): JsonResponse
    {
        $user = User::findOrFail($user);

        $query = UserReview::where('reviewed_user_id', $user->id)
            ->with('reviewer')
            ->where('is_verified', true);

        if (request()->filled('category')) {
            $query->where('category', request('category'));
        }

        if (request()->filled('rating')) {
            $query->where('rating', '>=', request('rating'));
        }

        $perPage = min(request('per_page', 15), 100);
        $reviews = $query->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return UserReviewResource::collection($reviews)->response();
    }

    /**
     * Mark review as helpful
     *
     * Marca una reseña como útil.
     *
     * @urlParam id int required ID de la reseña. Example: 1
     *
     * @response 200 {
     *   "message": "Reseña marcada como útil",
     *   "helpful_count": 4
     * }
     *
     * @authenticated
     */
    public function markHelpful(string $id): JsonResponse
    {
        $review = UserReview::findOrFail($id);
        $review->increment('helpful_count');

        return response()->json([
            'message' => 'Reseña marcada como útil',
            'helpful_count' => $review->fresh()->helpful_count
        ]);
    }
}
