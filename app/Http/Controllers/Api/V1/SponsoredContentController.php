<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\SponsoredContentResource;
use App\Models\SponsoredContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @group Sponsored Content
 *
 * APIs para la gestión de contenido patrocinado en la plataforma.
 * Permite a empresas y cooperativas promocionar contenido relevante
 * para la comunidad energética.
 */
class SponsoredContentController extends Controller
{
    /**
     * Display a listing of sponsored content
     *
     * Obtiene una lista de contenido patrocinado con opciones de filtrado.
     *
     * @queryParam sponsor_id int ID del patrocinador. Example: 1
     * @queryParam content_type string Tipo de contenido (post, banner, video, article, event). Example: post
     * @queryParam status string Estado (active, paused, ended, pending). Example: active
     * @queryParam target_audience string Audiencia objetivo (all, members, experts, beginners). Example: all
     * @queryParam category string Categoría del contenido. Example: energy
     * @queryParam is_featured boolean Filtrar contenido destacado. Example: true
     * @queryParam date_from date Fecha de inicio (YYYY-MM-DD). Example: 2024-01-01
     * @queryParam date_to date Fecha de fin (YYYY-MM-DD). Example: 2024-12-31
     * @queryParam sort string Ordenamiento (recent, popular, budget_desc, clicks_desc). Example: recent
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\SponsoredContentResource
     * @apiResourceModel App\Models\SponsoredContent
     */
    public function index(Request $request)
    {
        $query = SponsoredContent::with(['sponsor', 'content'])
            ->when($request->sponsor_id, fn($q, $sponsorId) => $q->where('sponsor_id', $sponsorId))
            ->when($request->content_type, fn($q, $type) => $q->where('content_type', $type))
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->target_audience, fn($q, $audience) => $q->where('target_audience', $audience))
            ->when($request->category, fn($q, $category) => $q->where('category', $category))
            ->when($request->has('is_featured'), fn($q) => $q->where('is_featured', $request->boolean('is_featured')))
            ->when($request->date_from, fn($q, $date) => $q->whereDate('start_date', '>=', $date))
            ->when($request->date_to, fn($q, $date) => $q->whereDate('end_date', '<=', $date))
            ->where('status', '!=', 'deleted');

        // Aplicar ordenamiento
        switch ($request->get('sort', 'recent')) {
            case 'popular':
                $query->orderBy('clicks_count', 'desc')->orderBy('impressions_count', 'desc');
                break;
            case 'budget_desc':
                $query->orderBy('daily_budget', 'desc');
                break;
            case 'clicks_desc':
                $query->orderBy('clicks_count', 'desc');
                break;
            default: // recent
                $query->orderBy('created_at', 'desc');
        }

        $perPage = min($request->get('per_page', 15), 100);
        $sponsoredContent = $query->paginate($perPage);

        return SponsoredContentResource::collection($sponsoredContent);
    }

    /**
     * Store a new sponsored content
     *
     * Crea un nuevo contenido patrocinado.
     *
     * @bodyParam sponsor_id int required ID del patrocinador. Example: 1
     * @bodyParam content_type string required Tipo de contenido (post, banner, video, article, event). Example: post
     * @bodyParam content_id int required ID del contenido a patrocinar. Example: 1
     * @bodyParam title string required Título del contenido patrocinado. Example: Guía Solar 2024
     * @bodyParam description string Descripción del contenido. Example: La mejor guía sobre energía solar
     * @bodyParam target_audience string Audiencia objetivo (all, members, experts, beginners). Default: all. Example: all
     * @bodyParam category string Categoría del contenido. Example: energy
     * @bodyParam start_date date required Fecha de inicio de la campaña. Example: 2024-01-01
     * @bodyParam end_date date required Fecha de fin de la campaña. Example: 2024-12-31
     * @bodyParam daily_budget number Presupuesto diario en euros. Example: 50.00
     * @bodyParam total_budget number Presupuesto total de la campaña. Example: 1000.00
     * @bodyParam max_clicks int Máximo de clicks permitidos. Example: 1000
     * @bodyParam max_impressions int Máximo de impresiones permitidas. Example: 10000
     * @bodyParam is_featured boolean Si es contenido destacado. Default: false. Example: false
     * @bodyParam targeting_criteria array Criterios de segmentación. Example: {"location": "Spain", "interests": ["solar"]}
     * @bodyParam creative_assets array Assets creativos (imágenes, videos). Example: ["banner1.jpg", "video.mp4"]
     *
     * @apiResource App\Http\Resources\V1\SponsoredContentResource
     * @apiResourceModel App\Models\SponsoredContent
     *
     * @response 201 {"data": {...}, "message": "Contenido patrocinado creado exitosamente"}
     * @response 422 {"message": "Datos inválidos", "errors": {...}}
     * @authenticated
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sponsor_id' => 'required|exists:users,id',
            'content_type' => 'required|in:post,banner,video,article,event',
            'content_id' => 'required|integer',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'target_audience' => 'in:all,members,experts,beginners',
            'category' => 'nullable|string|max:100',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:start_date',
            'daily_budget' => 'nullable|numeric|min:0.01',
            'total_budget' => 'nullable|numeric|min:0.01',
            'max_clicks' => 'nullable|integer|min:1',
            'max_impressions' => 'nullable|integer|min:1',
            'is_featured' => 'boolean',
            'targeting_criteria' => 'nullable|array',
            'creative_assets' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::guard('sanctum')->user();
        
        // Solo el patrocinador o administradores pueden crear contenido patrocinado
        if ($request->sponsor_id !== $user->id && !$user->hasRole('admin')) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $sponsoredContent = SponsoredContent::create(array_merge($validator->validated(), [
            'status' => 'pending',
            'clicks_count' => 0,
            'impressions_count' => 0,
            'spent_budget' => 0.00,
            'ctr' => 0.00,
            'cpc' => 0.00,
        ]));

        $sponsoredContent->load(['sponsor', 'content']);

        return response()->json([
            'data' => new SponsoredContentResource($sponsoredContent),
            'message' => 'Contenido patrocinado creado exitosamente'
        ], 201);
    }

    /**
     * Display the specified sponsored content
     *
     * Muestra un contenido patrocinado específico.
     *
     * @urlParam sponsoredContent int required ID del contenido patrocinado. Example: 1
     *
     * @apiResource App\Http\Resources\V1\SponsoredContentResource
     * @apiResourceModel App\Models\SponsoredContent
     *
     * @response 404 {"message": "Contenido patrocinado no encontrado"}
     */
    public function show(SponsoredContent $sponsoredContent)
    {
        if ($sponsoredContent->status === 'deleted') {
            return response()->json(['message' => 'Contenido patrocinado no encontrado'], 404);
        }

        $sponsoredContent->load(['sponsor', 'content']);
        
        // Incrementar contador de impresiones
        $sponsoredContent->increment('impressions_count');

        return new SponsoredContentResource($sponsoredContent);
    }

    /**
     * Update the specified sponsored content
     *
     * Actualiza un contenido patrocinado existente.
     *
     * @urlParam sponsoredContent int required ID del contenido patrocinado. Example: 1
     * @bodyParam title string Nuevo título. Example: Título actualizado
     * @bodyParam description string Nueva descripción. Example: Descripción actualizada
     * @bodyParam target_audience string Nueva audiencia objetivo. Example: experts
     * @bodyParam category string Nueva categoría. Example: solar
     * @bodyParam start_date date Nueva fecha de inicio. Example: 2024-02-01
     * @bodyParam end_date date Nueva fecha de fin. Example: 2024-11-30
     * @bodyParam daily_budget number Nuevo presupuesto diario. Example: 75.00
     * @bodyParam total_budget number Nuevo presupuesto total. Example: 1500.00
     * @bodyParam max_clicks int Nuevo máximo de clicks. Example: 1500
     * @bodyParam max_impressions int Nuevo máximo de impresiones. Example: 15000
     * @bodyParam is_featured boolean Si es destacado. Example: true
     * @bodyParam targeting_criteria array Nuevos criterios. Example: {"location": "Europe"}
     * @bodyParam creative_assets array Nuevos assets. Example: ["banner2.jpg"]
     *
     * @apiResource App\Http\Resources\V1\SponsoredContentResource
     * @apiResourceModel App\Models\SponsoredContent
     *
     * @response 403 {"message": "No autorizado"}
     * @response 422 {"message": "Datos inválidos", "errors": {...}}
     * @authenticated
     */
    public function update(Request $request, SponsoredContent $sponsoredContent)
    {
        $user = Auth::guard('sanctum')->user();

        // Solo el patrocinador o administradores pueden editar
        if ($sponsoredContent->sponsor_id !== $user->id && !$user->hasRole('admin')) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'description' => 'nullable|string|max:1000',
            'target_audience' => 'in:all,members,experts,beginners',
            'category' => 'nullable|string|max:100',
            'start_date' => 'date|after:today',
            'end_date' => 'date|after:start_date',
            'daily_budget' => 'nullable|numeric|min:0.01',
            'total_budget' => 'nullable|numeric|min:0.01',
            'max_clicks' => 'nullable|integer|min:1',
            'max_impressions' => 'nullable|integer|min:1',
            'is_featured' => 'boolean',
            'targeting_criteria' => 'nullable|array',
            'creative_assets' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $sponsoredContent->update($validator->validated());
        $sponsoredContent->load(['sponsor', 'content']);

        return new SponsoredContentResource($sponsoredContent);
    }

    /**
     * Remove the specified sponsored content
     *
     * Elimina un contenido patrocinado (marca como eliminado).
     *
     * @urlParam sponsoredContent int required ID del contenido patrocinado. Example: 1
     *
     * @response 200 {"message": "Contenido patrocinado eliminado exitosamente"}
     * @response 403 {"message": "No autorizado"}
     * @authenticated
     */
    public function destroy(SponsoredContent $sponsoredContent)
    {
        $user = Auth::guard('sanctum')->user();

        if ($sponsoredContent->sponsor_id !== $user->id && !$user->hasRole('admin')) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $sponsoredContent->update(['status' => 'deleted']);

        return response()->json(['message' => 'Contenido patrocinado eliminado exitosamente']);
    }

    /**
     * Get active sponsored content
     *
     * Obtiene todo el contenido patrocinado activo para mostrar a usuarios.
     *
     * @queryParam target_audience string Filtrar por audiencia. Example: all
     * @queryParam category string Filtrar por categoría. Example: energy
     * @queryParam limit int Cantidad máxima de resultados. Example: 10
     *
     * @apiResourceCollection App\Http\Resources\V1\SponsoredContentResource
     * @apiResourceModel App\Models\SponsoredContent
     */
    public function active(Request $request)
    {
        $query = SponsoredContent::with(['sponsor', 'content'])
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->when($request->target_audience, fn($q, $audience) => $q->where('target_audience', $audience))
            ->when($request->category, fn($q, $category) => $q->where('category', $category))
            ->orderBy('is_featured', 'desc')
            ->orderBy('clicks_count', 'desc')
            ->limit($request->get('limit', 10));

        $sponsoredContent = $query->get();

        return SponsoredContentResource::collection($sponsoredContent);
    }

    /**
     * Track click on sponsored content
     *
     * Registra un click en contenido patrocinado.
     *
     * @urlParam sponsoredContent int required ID del contenido patrocinado. Example: 1
     *
     * @response 200 {"message": "Click registrado exitosamente"}
     * @response 404 {"message": "Contenido patrocinado no encontrado"}
     */
    public function trackClick(SponsoredContent $sponsoredContent)
    {
        if ($sponsoredContent->status !== 'active') {
            return response()->json(['message' => 'Contenido patrocinado no encontrado'], 404);
        }

        // Verificar límites de clicks
        if ($sponsoredContent->max_clicks && $sponsoredContent->clicks_count >= $sponsoredContent->max_clicks) {
            $sponsoredContent->update(['status' => 'paused']);
            return response()->json(['message' => 'Límite de clicks alcanzado']);
        }

        // Incrementar contador de clicks
        $sponsoredContent->increment('clicks_count');
        
        // Calcular CTR
        if ($sponsoredContent->impressions_count > 0) {
            $ctr = ($sponsoredContent->clicks_count / $sponsoredContent->impressions_count) * 100;
            $sponsoredContent->update(['ctr' => round($ctr, 2)]);
        }

        return response()->json(['message' => 'Click registrado exitosamente']);
    }

    /**
     * Pause sponsored content campaign
     *
     * Pausa una campaña de contenido patrocinado.
     *
     * @urlParam sponsoredContent int required ID del contenido patrocinado. Example: 1
     *
     * @response 200 {"message": "Campaña pausada exitosamente"}
     * @response 403 {"message": "No autorizado"}
     * @authenticated
     */
    public function pause(SponsoredContent $sponsoredContent)
    {
        $user = Auth::guard('sanctum')->user();

        if ($sponsoredContent->sponsor_id !== $user->id && !$user->hasRole('admin')) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $sponsoredContent->update(['status' => 'paused']);

        return response()->json(['message' => 'Campaña pausada exitosamente']);
    }

    /**
     * Resume sponsored content campaign
     *
     * Reanuda una campaña pausada de contenido patrocinado.
     *
     * @urlParam sponsoredContent int required ID del contenido patrocinado. Example: 1
     *
     * @response 200 {"message": "Campaña reanudada exitosamente"}
     * @response 403 {"message": "No autorizado"}
     * @authenticated
     */
    public function resume(SponsoredContent $sponsoredContent)
    {
        $user = Auth::guard('sanctum')->user();

        if ($sponsoredContent->sponsor_id !== $user->id && !$user->hasRole('admin')) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        if ($sponsoredContent->status !== 'paused') {
            return response()->json(['message' => 'La campaña no está pausada'], 422);
        }

        $sponsoredContent->update(['status' => 'active']);

        return response()->json(['message' => 'Campaña reanudada exitosamente']);
    }
}
