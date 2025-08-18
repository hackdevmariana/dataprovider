<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\UserGeneratedContent;
use App\Http\Resources\V1\UserGeneratedContentResource;
use App\Http\Requests\StoreUserGeneratedContentRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Controlador para gestión de contenido generado por usuarios.
 */
class UserGeneratedContentController extends Controller
{
    /**
     * Listar contenido de usuarios.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->get('per_page', 15), 100);
        
        $query = UserGeneratedContent::with(['user', 'related'])
                                    ->published()
                                    ->notSpam();

        if ($request->filled('content_type')) {
            $query->byType($request->content_type);
        }

        if ($request->filled('related_type')) {
            $query->where('related_type', $request->related_type);
        }

        if ($request->filled('related_id')) {
            $query->where('related_id', $request->related_id);
        }

        if ($request->boolean('featured')) {
            $query->featured();
        }

        if ($request->boolean('verified')) {
            $query->verified();
        }

        if ($request->filled('rating')) {
            $query->where('rating', '>=', (float) $request->rating);
        }

        $content = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'data' => $content->items(),
            'meta' => [
                'current_page' => $content->currentPage(),
                'total' => $content->total(),
            ]
        ]);
    }

    /**
     * Crear nuevo contenido.
     */
    public function store(StoreUserGeneratedContentRequest $request): JsonResponse
    {
        $data = $request->getProcessedData();
        
        $content = UserGeneratedContent::create($data);

        // Análisis automático
        $content->analyzeSentiment();
        $content->generateAutoTags();
        $content->shouldNeedModeration();

        return new UserGeneratedContentResource($content);
    }

    /**
     * Mostrar contenido específico.
     */
    public function show(UserGeneratedContent $content): JsonResponse
    {
        $content->load(['user', 'related', 'replies']);
        return response()->json($content);
    }

    /**
     * Comentarios.
     */
    public function comments(Request $request): JsonResponse
    {
        $limit = min((int) $request->get('limit', 20), 50);
        
        $comments = UserGeneratedContent::with(['user', 'related'])
                                       ->comments()
                                       ->published()
                                       ->notSpam()
                                       ->orderBy('created_at', 'desc')
                                       ->limit($limit)
                                       ->get();

        return response()->json(['data' => $comments]);
    }

    /**
     * Reseñas.
     */
    public function reviews(Request $request): JsonResponse
    {
        $limit = min((int) $request->get('limit', 15), 50);
        
        $reviews = UserGeneratedContent::with(['user', 'related'])
                                      ->reviews()
                                      ->published()
                                      ->notSpam()
                                      ->orderBy('rating', 'desc')
                                      ->limit($limit)
                                      ->get();

        return response()->json(['data' => $reviews]);
    }

    /**
     * Contenido destacado.
     */
    public function featured(Request $request): JsonResponse
    {
        $limit = min((int) $request->get('limit', 10), 30);
        
        $featured = UserGeneratedContent::with(['user', 'related'])
                                       ->featured()
                                       ->published()
                                       ->notSpam()
                                       ->orderBy('likes_count', 'desc')
                                       ->limit($limit)
                                       ->get();

        return response()->json(['data' => $featured]);
    }

    /**
     * Contenido popular.
     */
    public function popular(Request $request): JsonResponse
    {
        $limit = min((int) $request->get('limit', 20), 50);
        
        $popular = UserGeneratedContent::with(['user', 'related'])
                                      ->popular(10)
                                      ->published()
                                      ->notSpam()
                                      ->limit($limit)
                                      ->get();

        return response()->json(['data' => $popular]);
    }

    /**
     * Dar like a contenido.
     */
    public function like(UserGeneratedContent $content): JsonResponse
    {
        $content->incrementLikes();

        return response()->json([
            'content_id' => $content->id,
            'likes_count' => $content->likes_count,
            'message' => 'Like registrado',
        ]);
    }

    /**
     * Dar dislike a contenido.
     */
    public function dislike(UserGeneratedContent $content): JsonResponse
    {
        $content->incrementDislikes();

        return response()->json([
            'content_id' => $content->id,
            'dislikes_count' => $content->dislikes_count,
            'message' => 'Dislike registrado',
        ]);
    }

    /**
     * Estadísticas de contenido.
     */
    public function statistics(): JsonResponse
    {
        $totalContent = UserGeneratedContent::count();
        $published = UserGeneratedContent::published()->count();
        $comments = UserGeneratedContent::where('type', 'comment')->count();
        $reviews = UserGeneratedContent::where('type', 'suggestion')->count();
        $featured = UserGeneratedContent::featured()->count();

        return response()->json([
            'total_content' => $totalContent,
            'published' => $published,
            'comments' => $comments,
            'reviews' => $reviews,
            'featured' => $featured,
        ]);
    }
}
