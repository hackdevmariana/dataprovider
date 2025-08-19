<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\HashtagResource;
use App\Models\Hashtag;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Hashtags",
 *     description="Sistema de hashtags inteligente con trending y categorización"
 * )
 */
class HashtagController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/hashtags",
     *     summary="Listar hashtags",
     *     tags={"Hashtags"},
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filtrar por categoría",
     *         @OA\Schema(type="string", enum={"technology", "legislation", "financing", "installation", "cooperative", "market", "diy", "sustainability", "location", "general"})
     *     ),
     *     @OA\Parameter(
     *         name="trending",
     *         in="query",
     *         description="Solo hashtags trending",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Buscar hashtags por nombre",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Lista de hashtags")
     * )
     */
    public function index(Request $request)
    {
        $query = Hashtag::where('is_blocked', false);

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->boolean('trending')) {
            $query->where('is_trending', true);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $hashtags = $query->orderBy('usage_count', 'desc')
                         ->paginate(20);

        return HashtagResource::collection($hashtags);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/hashtags/trending",
     *     summary="Obtener hashtags trending",
     *     tags={"Hashtags"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Número de hashtags a retornar",
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(response=200, description="Hashtags trending")
     * )
     */
    public function trending(Request $request)
    {
        $limit = $request->integer('limit', 10);
        $hashtags = Hashtag::getTrending($limit);
        
        return HashtagResource::collection($hashtags);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/hashtags/suggest",
     *     summary="Sugerir hashtags por término",
     *     tags={"Hashtags"},
     *     @OA\Parameter(
     *         name="term",
     *         in="query",
     *         required=true,
     *         description="Término de búsqueda",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Número de sugerencias",
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(response=200, description="Sugerencias de hashtags")
     * )
     */
    public function suggest(Request $request)
    {
        $request->validate([
            'term' => 'required|string|min:2',
            'limit' => 'integer|min:1|max:50',
        ]);

        $term = $request->term;
        $limit = $request->integer('limit', 10);
        
        $hashtags = Hashtag::search($term, $limit);
        
        return HashtagResource::collection($hashtags);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/hashtags/{hashtag}",
     *     summary="Obtener hashtag específico",
     *     tags={"Hashtags"},
     *     @OA\Parameter(
     *         name="hashtag",
     *         in="path",
     *         required=true,
     *         description="Slug del hashtag",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Detalles del hashtag"),
     *     @OA\Response(response=404, description="Hashtag no encontrado")
     * )
     */
    public function show(string $hashtag)
    {
        $hashtagModel = Hashtag::where('slug', $hashtag)
                              ->where('is_blocked', false)
                              ->firstOrFail();

        return new HashtagResource($hashtagModel);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/hashtags/{hashtag}/related",
     *     summary="Obtener hashtags relacionados",
     *     tags={"Hashtags"},
     *     @OA\Parameter(
     *         name="hashtag",
     *         in="path",
     *         required=true,
     *         description="Slug del hashtag",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Número de hashtags relacionados",
     *         @OA\Schema(type="integer", default=5)
     *     ),
     *     @OA\Response(response=200, description="Hashtags relacionados")
     * )
     */
    public function related(string $hashtag, Request $request)
    {
        $hashtagModel = Hashtag::where('slug', $hashtag)
                              ->where('is_blocked', false)
                              ->firstOrFail();

        $limit = $request->integer('limit', 5);
        $related = $hashtagModel->getRelated($limit);
        
        return HashtagResource::collection($related);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/hashtags",
     *     summary="Crear nuevo hashtag",
     *     tags={"Hashtags"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="energiasolar"),
     *             @OA\Property(property="description", type="string", example="Todo sobre energía solar"),
     *             @OA\Property(property="color", type="string", example="#3B82F6"),
     *             @OA\Property(property="icon", type="string", example="solar-panel")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Hashtag creado"),
     *     @OA\Response(response=422, description="Error de validación")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:hashtags,name',
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string|max:50',
        ]);

        $hashtag = Hashtag::findOrCreateByName(
            $request->name,
            $request->user()
        );

        if ($request->has('description')) {
            $hashtag->update(['description' => $request->description]);
        }

        if ($request->has('color')) {
            $hashtag->update(['color' => $request->color]);
        }

        if ($request->has('icon')) {
            $hashtag->update(['icon' => $request->icon]);
        }

        return response()->json([
            'message' => 'Hashtag creado exitosamente',
            'data' => new HashtagResource($hashtag),
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/hashtags/extract",
     *     summary="Extraer hashtags de un texto",
     *     tags={"Hashtags"},
     *     @OA\Parameter(
     *         name="text",
     *         in="query",
     *         required=true,
     *         description="Texto del cual extraer hashtags",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Hashtags extraídos")
     * )
     */
    public function extract(Request $request): JsonResponse
    {
        $request->validate([
            'text' => 'required|string',
        ]);

        $extractedHashtags = Hashtag::extractFromText($request->text);
        
        $hashtags = [];
        foreach ($extractedHashtags as $name) {
            $hashtag = Hashtag::where('name', $name)->first();
            if ($hashtag) {
                $hashtags[] = new HashtagResource($hashtag);
            } else {
                $hashtags[] = [
                    'name' => $name,
                    'slug' => \Str::slug($name),
                    'exists' => false,
                ];
            }
        }

        return response()->json([
            'extracted_hashtags' => $extractedHashtags,
            'hashtag_data' => $hashtags,
        ]);
    }
}
