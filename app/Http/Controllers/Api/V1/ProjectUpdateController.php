<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ProjectUpdate;
use App\Models\ProjectProposal;
use App\Http\Resources\V1\ProjectUpdateResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @group Project Updates
 *
 * APIs para la gestión de actualizaciones de proyectos.
 * Permite a los usuarios y administradores de proyectos
 * publicar actualizaciones sobre el progreso, hitos y novedades.
 */
/**
 * @OA\Tag(
 *     name="Actualizaciones de Proyectos",
 *     description="APIs para la gestión de Actualizaciones de Proyectos"
 * )
 */
class ProjectUpdateController extends Controller
{
    /**
     * Display a listing of project updates
     *
     * Obtiene una lista de actualizaciones de proyectos con opciones de filtrado.
     *
     * @queryParam project_proposal_id int ID del proyecto para filtrar. Example: 1
     * @queryParam author_id int ID del autor de la actualización. Example: 2
     * @queryParam type string Tipo de actualización (progress, milestone, announcement, issue, financial). Example: progress
     * @queryParam is_public boolean Filtrar por actualizaciones públicas. Example: true
     * @queryParam has_images boolean Filtrar por actualizaciones con imágenes. Example: true
     * @queryParam date_from date Fecha de inicio (YYYY-MM-DD). Example: 2024-01-01
     * @queryParam date_to date Fecha de fin (YYYY-MM-DD). Example: 2024-12-31
     * @queryParam sort string Ordenamiento (recent, oldest, important, type). Example: recent
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\ProjectUpdateResource
     * @apiResourceModel App\Models\ProjectUpdate
     */
    public function index(Request $request): JsonResponse
    {
        $query = ProjectUpdate::with(['projectProposal', 'author']);

        if ($request->filled('project_proposal_id')) {
            $query->where('project_proposal_id', $request->project_proposal_id);
        }

        if ($request->filled('author_id')) {
            $query->where('author_id', $request->author_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('is_public')) {
            $query->where('is_public', $request->boolean('is_public'));
        }

        if ($request->filled('has_images')) {
            if ($request->boolean('has_images')) {
                $query->whereNotNull('images')->where('images', '!=', '[]');
            } else {
                $query->whereNull('images')->orWhere('images', '[]');
            }
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
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'important':
                $query->orderBy('is_important', 'desc')->orderBy('created_at', 'desc');
                break;
            case 'type':
                $query->orderBy('type')->orderBy('created_at', 'desc');
                break;
            default: // recent
                $query->orderBy('created_at', 'desc');
        }

        $perPage = min($request->get('per_page', 15), 100);
        $updates = $query->paginate($perPage);

        return response()->json([
            'data' => ProjectUpdateResource::collection($updates),
            'meta' => [
                'current_page' => $updates->currentPage(),
                'last_page' => $updates->lastPage(),
                'per_page' => $updates->perPage(),
                'total' => $updates->total(),
                'filters_applied' => $request->only([
                    'project_proposal_id', 'author_id', 'type', 'is_public',
                    'has_images', 'date_from', 'date_to', 'sort'
                ])
            ]
        ]);
    }

    /**
     * Store a newly created project update
     *
     * Crea una nueva actualización de proyecto. Solo usuarios autorizados
     * pueden crear actualizaciones.
     *
     * @bodyParam project_proposal_id int required ID del proyecto. Example: 1
     * @bodyParam title string required Título de la actualización (máx 200 caracteres). Example: Instalación completada al 75%
     * @bodyParam content string required Contenido de la actualización (máx 5000 caracteres). Example: Hemos completado la instalación de los paneles solares
     * @bodyParam type string Tipo de actualización (progress, milestone, announcement, issue, financial). Example: progress
     * @bodyParam progress_percentage int Porcentaje de progreso (0-100). Example: 75
     * @bodyParam current_production_kwh float Producción actual en kWh. Example: 150.5
     * @bodyParam financial_impact string Impacto financiero de la actualización. Example: Ahorro mensual de 200€
     * @bodyParam metrics json Métricas y datos técnicos. Example: {"efficiency": 0.85, "temperature": 45}
     * @bodyParam images array URLs de imágenes relacionadas. Example: ["url1.jpg", "url2.jpg"]
     * @bodyParam is_public boolean Si la actualización es pública. Example: true
     * @bodyParam is_important boolean Si es una actualización importante. Example: false
     * @bodyParam metadata json Metadatos adicionales. Example: {"weather_conditions": "soleado"}
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "project_proposal_id": 1,
     *     "author_id": 2,
     *     "title": "Instalación completada al 75%",
     *     "content": "Hemos completado la instalación de los paneles solares",
     *     "type": "progress",
     *     "progress_percentage": 75,
     *     "current_production_kwh": 150.5,
     *     "financial_impact": "Ahorro mensual de 200€",
     *     "is_public": true,
     *     "created_at": "2024-01-15T10:30:00.000000Z"
     *   },
     *   "message": "Actualización de proyecto creada exitosamente"
     * }
     *
     * @authenticated
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'project_proposal_id' => 'required|integer|exists:project_proposals,id',
            'title' => 'required|string|max:200',
            'content' => 'required|string|max:5000',
            'type' => 'string|in:progress,milestone,announcement,issue,financial',
            'progress_percentage' => 'nullable|integer|between:0,100',
            'current_production_kwh' => 'nullable|numeric|min:0',
            'financial_impact' => 'nullable|string|max:500',
            'metrics' => 'nullable|json',
            'images' => 'nullable|array',
            'images.*' => 'string|url',
            'is_public' => 'boolean',
            'is_important' => 'boolean',
            'metadata' => 'nullable|json'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar permisos del usuario
        $project = ProjectProposal::findOrFail($request->project_proposal_id);
        $currentUser = Auth::guard('sanctum')->id();

        if (!$project->canUserUpdate($currentUser)) {
            return response()->json([
                'message' => 'No tienes permisos para crear actualizaciones en este proyecto'
            ], 403);
        }

        $update = ProjectUpdate::create([
            'project_proposal_id' => $request->project_proposal_id,
            'author_id' => $currentUser,
            'title' => $request->title,
            'content' => $request->content,
            'type' => $request->type ?? 'progress',
            'progress_percentage' => $request->progress_percentage,
            'current_production_kwh' => $request->current_production_kwh,
            'financial_impact' => $request->financial_impact,
            'metrics' => $request->metrics,
            'images' => $request->images,
            'is_public' => $request->boolean('is_public', true),
            'is_important' => $request->boolean('is_important', false),
            'metadata' => $request->metadata
        ]);

        return response()->json([
            'data' => new ProjectUpdateResource($update),
            'message' => 'Actualización de proyecto creada exitosamente'
        ], 201);
    }

    /**
     * Display the specified project update
     *
     * Obtiene los detalles de una actualización específica.
     *
     * @urlParam id int required ID de la actualización. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "project_proposal_id": 1,
     *     "author_id": 2,
     *     "title": "Instalación completada al 75%",
     *     "content": "Hemos completado la instalación de los paneles solares",
     *     "type": "progress",
     *     "progress_percentage": 75,
     *     "current_production_kwh": 150.5,
     *     "financial_impact": "Ahorro mensual de 200€",
     *     "is_public": true,
     *     "created_at": "2024-01-15T10:30:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Actualización no encontrada"
     * }
     */
    public function show(string $id): JsonResponse
    {
        $update = ProjectUpdate::with(['projectProposal', 'author'])
            ->findOrFail($id);

        return response()->json([
            'data' => new ProjectUpdateResource($update)
        ]);
    }

    /**
     * Update the specified project update
     *
     * Actualiza una actualización existente. Solo el autor puede editarla.
     *
     * @urlParam id int required ID de la actualización. Example: 1
     * @bodyParam title string Título de la actualización. Example: Instalación completada al 80%
     * @bodyParam content string Contenido de la actualización. Example: Actualización del progreso
     * @bodyParam type string Tipo de actualización. Example: progress
     * @bodyParam progress_percentage int Porcentaje de progreso. Example: 80
     * @bodyParam current_production_kwh float Producción actual en kWh. Example: 160.0
     * @bodyParam financial_impact string Impacto financiero. Example: Ahorro mensual de 220€
     * @bodyParam metrics json Métricas y datos técnicos. Example: {"efficiency": 0.87}
     * @bodyParam images array URLs de imágenes. Example: ["url1.jpg", "url3.jpg"]
     * @bodyParam is_public boolean Si es pública. Example: true
     * @bodyParam is_important boolean Si es importante. Example: true
     * @bodyParam metadata json Metadatos adicionales. Example: {"weather": "nublado"}
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "title": "Instalación completada al 80%",
     *     "progress_percentage": 80,
     *     "updated_at": "2024-01-15T11:30:00.000000Z"
     *   },
     *   "message": "Actualización actualizada exitosamente"
     * }
     *
     * @authenticated
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $update = ProjectUpdate::findOrFail($id);
        $currentUser = Auth::guard('sanctum')->id();

        if ($update->author_id !== $currentUser) {
            return response()->json([
                'message' => 'No tienes permisos para editar esta actualización'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'string|max:200',
            'content' => 'string|max:5000',
            'type' => 'string|in:progress,milestone,announcement,issue,financial',
            'progress_percentage' => 'nullable|integer|between:0,100',
            'current_production_kwh' => 'nullable|numeric|min:0',
            'financial_impact' => 'nullable|string|max:500',
            'metrics' => 'nullable|json',
            'images' => 'nullable|array',
            'images.*' => 'string|url',
            'is_public' => 'boolean',
            'is_important' => 'boolean',
            'metadata' => 'nullable|json'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()
            ], 422);
        }

        $update->update($request->only([
            'title', 'content', 'type', 'progress_percentage',
            'current_production_kwh', 'financial_impact', 'metrics',
            'images', 'is_public', 'is_important', 'metadata'
        ]));

        return response()->json([
            'data' => new ProjectUpdateResource($update),
            'message' => 'Actualización actualizada exitosamente'
        ]);
    }

    /**
     * Remove the specified project update
     *
     * Elimina una actualización. Solo el autor puede eliminarla.
     *
     * @urlParam id int required ID de la actualización. Example: 1
     *
     * @response 200 {
     *   "message": "Actualización eliminada exitosamente"
     * }
     *
     * @response 403 {
     *   "message": "No tienes permisos para eliminar esta actualización"
     * }
     *
     * @authenticated
     */
    public function destroy(string $id): JsonResponse
    {
        $update = ProjectUpdate::findOrFail($id);
        $currentUser = Auth::guard('sanctum')->id();

        if ($update->author_id !== $currentUser) {
            return response()->json([
                'message' => 'No tienes permisos para eliminar esta actualización'
            ], 403);
        }

        $update->delete();

        return response()->json([
            'message' => 'Actualización eliminada exitosamente'
        ]);
    }

    /**
     * Get updates for a specific project
     *
     * Obtiene todas las actualizaciones de un proyecto específico.
     *
     * @urlParam project int required ID del proyecto. Example: 1
     * @queryParam type string Tipo de actualización. Example: progress
     * @queryParam is_public boolean Solo actualizaciones públicas. Example: true
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Instalación completada al 75%",
     *       "type": "progress",
     *       "progress_percentage": 75,
     *       "created_at": "2024-01-15T10:30:00.000000Z"
     *     }
     *   ],
     *   "meta": {
     *     "current_page": 1,
     *     "total": 1,
     *     "latest_progress": 75
     *   }
     * }
     */
    public function projectUpdates(Request $request, int $project): JsonResponse
    {
        $query = ProjectUpdate::where('project_proposal_id', $project)
            ->with(['author']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('is_public')) {
            $query->where('is_public', $request->boolean('is_public'));
        }

        $perPage = min($request->get('per_page', 15), 100);
        $updates = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Obtener el progreso más reciente
        $latestProgress = ProjectUpdate::where('project_proposal_id', $project)
            ->whereNotNull('progress_percentage')
            ->orderBy('created_at', 'desc')
            ->value('progress_percentage');

        return response()->json([
            'data' => ProjectUpdateResource::collection($updates),
            'meta' => [
                'current_page' => $updates->currentPage(),
                'last_page' => $updates->lastPage(),
                'per_page' => $updates->perPage(),
                'total' => $updates->total(),
                'latest_progress' => $latestProgress
            ]
        ]);
    }
}
