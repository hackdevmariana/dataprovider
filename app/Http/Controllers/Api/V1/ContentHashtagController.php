<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ContentHashtagResource;
use App\Models\ContentHashtag;
use App\Models\Hashtag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @group Content Hashtags
 *
 * APIs para la gestión de relaciones entre contenido y hashtags.
 * Permite etiquetar posts, comentarios, proyectos y otros contenidos
 * con hashtags para mejor organización y búsqueda.
 */
class ContentHashtagController extends Controller
{
    /**
     * Display a listing of content-hashtag relationships
     *
     * Obtiene una lista de relaciones contenido-hashtag con opciones de filtrado.
     *
     * @queryParam hashtag_id int ID del hashtag para filtrar. Example: 1
     * @queryParam hashtaggable_type string Tipo de contenido (TopicPost, TopicComment, ProjectProposal, etc.). Example: TopicPost
     * @queryParam hashtaggable_id int ID del contenido. Example: 1
     * @queryParam added_by int ID del usuario que agregó el hashtag. Example: 1
     * @queryParam is_verified boolean Filtrar por hashtags verificados. Example: true
     * @queryParam sort string Ordenamiento (recent, oldest, hashtag_name, content_type). Example: recent
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\ContentHashtagResource
     * @apiResourceModel App\Models\ContentHashtag
     */
    public function index(Request $request)
    {
        $query = ContentHashtag::with(['hashtag', 'hashtaggable', 'addedBy'])
            ->when($request->hashtag_id, fn($q, $hashtagId) => $q->where('hashtag_id', $hashtagId))
            ->when($request->hashtaggable_type, fn($q, $type) => $q->where('hashtaggable_type', $type))
            ->when($request->hashtaggable_id, fn($q, $id) => $q->where('hashtaggable_id', $id))
            ->when($request->added_by, fn($q, $userId) => $q->where('added_by', $userId))
            ->when($request->has('is_verified'), fn($q) => $q->whereHas('hashtag', fn($h) => $h->where('is_verified', $request->boolean('is_verified'))));

        // Aplicar ordenamiento
        switch ($request->get('sort', 'recent')) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'hashtag_name':
                $query->orderByHas('hashtag.name', 'asc');
                break;
            case 'content_type':
                $query->orderBy('hashtaggable_type', 'asc');
                break;
            default: // recent
                $query->orderBy('created_at', 'desc');
        }

        $perPage = min($request->get('per_page', 15), 100);
        $contentHashtags = $query->paginate($perPage);

        return ContentHashtagResource::collection($contentHashtags);
    }

    /**
     * Store a new content-hashtag relationship
     *
     * Crea una nueva relación entre contenido y hashtag.
     *
     * @bodyParam hashtag_id int required ID del hashtag. Example: 1
     * @bodyParam hashtaggable_type string required Tipo de contenido. Example: App\Models\TopicPost
     * @bodyParam hashtaggable_id int required ID del contenido. Example: 1
     * @bodyParam relevance_score number Puntuación de relevancia (0-100). Example: 85
     * @bodyParam is_auto_tagged boolean Si fue etiquetado automáticamente. Default: false. Example: false
     * @bodyParam confidence_score number Puntuación de confianza para etiquetado automático (0-100). Example: 90
     * @bodyParam metadata array Metadatos adicionales. Example: {"context": "energy", "algorithm": "ai"}
     *
     * @apiResource App\Http\Resources\V1\ContentHashtagResource
     * @apiResourceModel App\Models\ContentHashtag
     *
     * @response 201 {"data": {...}, "message": "Hashtag agregado exitosamente"}
     * @response 409 {"message": "El hashtag ya está asociado a este contenido"}
     * @response 422 {"message": "Datos inválidos", "errors": {...}}
     * @authenticated
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hashtag_id' => 'required|exists:hashtags,id',
            'hashtaggable_type' => 'required|string|max:255',
            'hashtaggable_id' => 'required|integer',
            'relevance_score' => 'nullable|numeric|min:0|max:100',
            'is_auto_tagged' => 'boolean',
            'confidence_score' => 'nullable|numeric|min:0|max:100',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::guard('sanctum')->user();

        // Verificar que la relación no exista
        $existing = ContentHashtag::where('hashtag_id', $request->hashtag_id)
            ->where('hashtaggable_type', $request->hashtaggable_type)
            ->where('hashtaggable_id', $request->hashtaggable_id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'El hashtag ya está asociado a este contenido',
                'data' => new ContentHashtagResource($existing)
            ], 409);
        }

        $contentHashtag = ContentHashtag::create(array_merge($validator->validated(), [
            'added_by' => $user->id,
            'relevance_score' => $request->get('relevance_score', 50),
            'is_auto_tagged' => $request->get('is_auto_tagged', false),
            'confidence_score' => $request->get('confidence_score', 100),
        ]));

        $contentHashtag->load(['hashtag', 'hashtaggable', 'addedBy']);

        return response()->json([
            'data' => new ContentHashtagResource($contentHashtag),
            'message' => 'Hashtag agregado exitosamente'
        ], 201);
    }

    /**
     * Display the specified content-hashtag relationship
     *
     * Muestra una relación específica.
     *
     * @urlParam contentHashtag int required ID de la relación. Example: 1
     *
     * @apiResource App\Http\Resources\V1\ContentHashtagResource
     * @apiResourceModel App\Models\ContentHashtag
     *
     * @response 404 {"message": "Relación no encontrada"}
     */
    public function show(ContentHashtag $contentHashtag)
    {
        $contentHashtag->load(['hashtag', 'hashtaggable', 'addedBy']);
        return new ContentHashtagResource($contentHashtag);
    }

    /**
     * Update the specified content-hashtag relationship
     *
     * Actualiza una relación existente.
     *
     * @urlParam contentHashtag int required ID de la relación. Example: 1
     * @bodyParam relevance_score number Nueva puntuación de relevancia. Example: 95
     * @bodyParam is_auto_tagged boolean Si es auto-etiquetado. Example: false
     * @bodyParam confidence_score number Nueva puntuación de confianza. Example: 85
     * @bodyParam metadata array Metadatos actualizados. Example: {"updated": true}
     *
     * @apiResource App\Http\Resources\V1\ContentHashtagResource
     * @apiResourceModel App\Models\ContentHashtag
     *
     * @response 403 {"message": "No autorizado"}
     * @response 422 {"message": "Datos inválidos", "errors": {...}}
     * @authenticated
     */
    public function update(Request $request, ContentHashtag $contentHashtag)
    {
        $user = Auth::guard('sanctum')->user();

        // Solo el usuario que agregó el hashtag o administradores pueden editarlo
        if ($contentHashtag->added_by !== $user->id && !$user->hasRole('admin')) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validator = Validator::make($request->all(), [
            'relevance_score' => 'numeric|min:0|max:100',
            'is_auto_tagged' => 'boolean',
            'confidence_score' => 'nullable|numeric|min:0|max:100',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $contentHashtag->update($validator->validated());
        $contentHashtag->load(['hashtag', 'hashtaggable', 'addedBy']);

        return new ContentHashtagResource($contentHashtag);
    }

    /**
     * Remove the specified content-hashtag relationship
     *
     * Elimina una relación entre contenido y hashtag.
     *
     * @urlParam contentHashtag int required ID de la relación. Example: 1
     *
     * @response 200 {"message": "Hashtag removido exitosamente"}
     * @response 403 {"message": "No autorizado"}
     * @authenticated
     */
    public function destroy(ContentHashtag $contentHashtag)
    {
        $user = Auth::guard('sanctum')->user();

        if ($contentHashtag->added_by !== $user->id && !$user->hasRole('admin')) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $contentHashtag->delete();

        return response()->json(['message' => 'Hashtag removido exitosamente']);
    }

    /**
     * Get content for a specific hashtag
     *
     * Obtiene todo el contenido etiquetado con un hashtag específico.
     *
     * @urlParam hashtag int required ID del hashtag. Example: 1
     * @queryParam hashtaggable_type string Filtrar por tipo de contenido. Example: TopicPost
     * @queryParam sort string Ordenamiento (recent, relevance, popularity). Example: recent
     * @queryParam per_page int Cantidad por página. Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\ContentHashtagResource
     * @apiResourceModel App\Models\ContentHashtag
     */
    public function hashtagContent(Request $request, Hashtag $hashtag)
    {
        $query = $hashtag->content()
            ->with(['hashtag', 'hashtaggable', 'addedBy'])
            ->when($request->hashtaggable_type, fn($q, $type) => $q->where('hashtaggable_type', $type));

        // Aplicar ordenamiento
        switch ($request->get('sort', 'recent')) {
            case 'relevance':
                $query->orderBy('relevance_score', 'desc');
                break;
            case 'popularity':
                $query->orderBy('hashtaggable.views_count', 'desc');
                break;
            default: // recent
                $query->orderBy('created_at', 'desc');
        }

        $perPage = min($request->get('per_page', 15), 100);
        $contentHashtags = $query->paginate($perPage);

        return ContentHashtagResource::collection($contentHashtags);
    }

    /**
     * Auto-tag content with hashtags
     *
     * Etiqueta automáticamente contenido con hashtags relevantes usando IA.
     *
     * @bodyParam content string required Contenido a etiquetar. Example: Guía completa sobre paneles solares
     * @bodyParam content_type string required Tipo de contenido. Example: TopicPost
     * @bodyParam content_id int required ID del contenido. Example: 1
     * @bodyParam max_hashtags int Cantidad máxima de hashtags. Example: 5
     * @bodyParam min_confidence number Confianza mínima para etiquetado (0-100). Example: 70
     *
     * @response 200 {"message": "Contenido etiquetado automáticamente", "added_hashtags": 3}
     * @response 422 {"message": "Datos inválidos", "errors": {...}}
     * @authenticated
     */
    public function autoTag(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|min:10',
            'content_type' => 'required|string|max:255',
            'content_id' => 'required|integer',
            'max_hashtags' => 'integer|min:1|max:10',
            'min_confidence' => 'numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::guard('sanctum')->user();
        $maxHashtags = $request->get('max_hashtags', 5);
        $minConfidence = $request->get('min_confidence', 70);

        // TODO: Implementar lógica de IA para sugerir hashtags
        // Por ahora, usar hashtags populares relacionados
        $suggestedHashtags = Hashtag::where('is_verified', true)
            ->where('posts_count', '>', 10)
            ->inRandomOrder()
            ->limit($maxHashtags)
            ->get();

        $addedCount = 0;
        foreach ($suggestedHashtags as $hashtag) {
            // Verificar que no exista ya la relación
            $existing = ContentHashtag::where('hashtag_id', $hashtag->id)
                ->where('hashtaggable_type', $request->content_type)
                ->where('hashtaggable_id', $request->content_id)
                ->first();

            if (!$existing) {
                ContentHashtag::create([
                    'hashtag_id' => $hashtag->id,
                    'hashtaggable_type' => $request->content_type,
                    'hashtaggable_id' => $request->content_id,
                    'added_by' => $user->id,
                    'relevance_score' => rand(60, 90),
                    'is_auto_tagged' => true,
                    'confidence_score' => rand($minConfidence, 95),
                ]);
                $addedCount++;
            }
        }

        return response()->json([
            'message' => 'Contenido etiquetado automáticamente',
            'added_hashtags' => $addedCount
        ]);
    }
}
