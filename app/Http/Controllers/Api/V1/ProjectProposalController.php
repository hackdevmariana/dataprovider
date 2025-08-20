<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProjectProposalResource;
use App\Models\ProjectProposal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Project Proposals",
 *     description="Sistema de propuestas de proyectos colaborativos energéticos"
 * )
 * 
 * @group Project Proposals
 *
 * APIs para la gestión de propuestas de proyectos colaborativos energéticos.
 * Permite a los usuarios crear, gestionar y participar en proyectos
 * de instalaciones energéticas, almacenamiento y eficiencia.
 */
class ProjectProposalController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/project-proposals",
     *     summary="Listar propuestas de proyectos",
     *     tags={"Project Proposals"},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filtrar por estado",
     *         @OA\Schema(type="string", enum={"draft", "under_review", "approved", "funding", "funded", "in_progress", "completed", "cancelled", "on_hold", "rejected"})
     *     ),
     *     @OA\Parameter(
     *         name="project_type",
     *         in="query",
     *         description="Filtrar por tipo de proyecto",
     *         @OA\Schema(type="string", enum={"individual_installation", "community_installation", "shared_installation", "energy_storage", "smart_grid", "efficiency_improvement", "research_development", "educational", "infrastructure", "other"})
     *     ),
     *     @OA\Parameter(
     *         name="scale",
     *         in="query",
     *         description="Filtrar por escala",
     *         @OA\Schema(type="string", enum={"residential", "commercial", "industrial", "utility", "community"})
     *     ),
     *     @OA\Parameter(
     *         name="featured",
     *         in="query",
     *         description="Solo proyectos destacados",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Buscar en título y descripción",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Lista de propuestas de proyectos")
     * )
     * 
     * Display a listing of project proposals
     *
     * Obtiene una lista de propuestas de proyectos con opciones de filtrado.
     *
     * @queryParam status string Filtrar por estado del proyecto. Example: approved
     * @queryParam project_type string Filtrar por tipo de proyecto. Example: community_installation
     * @queryParam scale string Filtrar por escala del proyecto. Example: residential
     * @queryParam featured boolean Solo proyectos destacados. Example: true
     * @queryParam search string Buscar en título y descripción. Example: energía solar
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     *
     * @apiResourceCollection App\Http\Resources\V1\ProjectProposalResource
     * @apiResourceModel App\Models\ProjectProposal
     */
    public function index(Request $request)
    {
        $query = ProjectProposal::where('is_public', true);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('project_type')) {
            $query->where('project_type', $request->project_type);
        }

        if ($request->has('scale')) {
            $query->where('scale', $request->scale);
        }

        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        if ($request->has('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%")
                  ->orWhere('summary', 'like', "%{$term}%");
            });
        }

        $projects = $query->with(['proposer', 'cooperative', 'municipality'])
                         ->orderBy('engagement_score', 'desc')
                         ->paginate(20);

        return ProjectProposalResource::collection($projects);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/project-proposals",
     *     summary="Crear nueva propuesta de proyecto",
     *     tags={"Project Proposals"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "description", "project_type", "scale", "total_investment_required", "funding_deadline"},
     *             @OA\Property(property="title", type="string", example="Instalación Solar Comunitaria Barrio Verde"),
     *             @OA\Property(property="description", type="string", example="Proyecto de instalación solar compartida para 50 familias"),
     *             @OA\Property(property="summary", type="string", example="Instalación de 250kW para autoconsumo colectivo"),
     *             @OA\Property(property="project_type", type="string", enum={"community_installation", "shared_installation"}),
     *             @OA\Property(property="scale", type="string", enum={"residential", "commercial", "community"}),
     *             @OA\Property(property="total_investment_required", type="number", example=150000),
     *             @OA\Property(property="funding_deadline", type="string", format="date", example="2025-12-31"),
     *             @OA\Property(property="estimated_power_kw", type="number", example=250),
     *             @OA\Property(property="estimated_roi_percentage", type="number", example=8.5)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Propuesta creada"),
     *     @OA\Response(response=422, description="Error de validación")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'summary' => 'nullable|string|max:500',
            'project_type' => 'required|in:individual_installation,community_installation,shared_installation,energy_storage,smart_grid,efficiency_improvement,research_development,educational,infrastructure,other',
            'scale' => 'required|in:residential,commercial,industrial,utility,community',
            'municipality_id' => 'nullable|exists:municipalities,id',
            'specific_location' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'estimated_power_kw' => 'nullable|numeric|min:0',
            'estimated_annual_production_kwh' => 'nullable|numeric|min:0',
            'total_investment_required' => 'required|numeric|min:1',
            'min_investment_per_participant' => 'nullable|numeric|min:1',
            'max_investment_per_participant' => 'nullable|numeric|min:1',
            'max_participants' => 'nullable|integer|min:1',
            'estimated_roi_percentage' => 'nullable|numeric|min:0|max:100',
            'payback_period_years' => 'nullable|integer|min:1|max:50',
            'estimated_annual_savings' => 'nullable|numeric|min:0',
            'funding_deadline' => 'required|date|after:today',
            'project_start_date' => 'nullable|date|after:funding_deadline',
            'expected_completion_date' => 'nullable|date|after:project_start_date',
            'estimated_duration_months' => 'nullable|integer|min:1|max:120',
            'objectives' => 'nullable|array',
            'benefits' => 'nullable|array',
            'technical_specifications' => 'nullable|array',
            'financial_projections' => 'nullable|array',
            'project_milestones' => 'nullable|array',
        ]);

        $project = ProjectProposal::create([
            'proposer_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'summary' => $request->summary,
            'project_type' => $request->project_type,
            'scale' => $request->scale,
            'municipality_id' => $request->municipality_id,
            'specific_location' => $request->specific_location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'estimated_power_kw' => $request->estimated_power_kw,
            'estimated_annual_production_kwh' => $request->estimated_annual_production_kwh,
            'total_investment_required' => $request->total_investment_required,
            'min_investment_per_participant' => $request->min_investment_per_participant,
            'max_investment_per_participant' => $request->max_investment_per_participant,
            'max_participants' => $request->max_participants,
            'estimated_roi_percentage' => $request->estimated_roi_percentage,
            'payback_period_years' => $request->payback_period_years,
            'estimated_annual_savings' => $request->estimated_annual_savings,
            'funding_deadline' => $request->funding_deadline,
            'project_start_date' => $request->project_start_date,
            'expected_completion_date' => $request->expected_completion_date,
            'estimated_duration_months' => $request->estimated_duration_months,
            'objectives' => $request->objectives,
            'benefits' => $request->benefits,
            'technical_specifications' => $request->technical_specifications,
            'financial_projections' => $request->financial_projections,
            'project_milestones' => $request->project_milestones,
            'status' => 'draft',
        ]);

        return response()->json([
            'message' => 'Propuesta de proyecto creada exitosamente',
            'data' => new ProjectProposalResource($project),
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/project-proposals/{projectProposal}",
     *     summary="Obtener propuesta específica",
     *     tags={"Project Proposals"},
     *     @OA\Parameter(
     *         name="projectProposal",
     *         in="path",
     *         required=true,
     *         description="Slug del proyecto",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Detalles del proyecto"),
     *     @OA\Response(response=404, description="Proyecto no encontrado")
     * )
     */
    public function show(string $slug)
    {
        $project = ProjectProposal::where('slug', $slug)
                                 ->where('is_public', true)
                                 ->with([
                                     'proposer',
                                     'cooperative',
                                     'municipality',
                                     'investments.investor',
                                     'updates' => fn($q) => $q->latest()->limit(5)
                                 ])
                                 ->firstOrFail();

        $project->incrementViews();

        return new ProjectProposalResource($project);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/project-proposals/featured",
     *     summary="Obtener proyectos destacados",
     *     tags={"Project Proposals"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Número de proyectos a retornar",
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(response=200, description="Proyectos destacados")
     * )
     */
    public function featured(Request $request)
    {
        $limit = $request->integer('limit', 10);
        $projects = ProjectProposal::getFeatured($limit);
        
        return ProjectProposalResource::collection($projects);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/project-proposals/funding",
     *     summary="Obtener proyectos en financiación",
     *     tags={"Project Proposals"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Número de proyectos",
     *         @OA\Schema(type="integer", default=20)
     *     ),
     *     @OA\Response(response=200, description="Proyectos en financiación")
     * )
     */
    public function funding(Request $request)
    {
        $limit = $request->integer('limit', 20);
        $projects = ProjectProposal::getByStatus('funding', $limit);
        
        return ProjectProposalResource::collection($projects);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/project-proposals/{projectProposal}/invest",
     *     summary="Invertir en un proyecto",
     *     tags={"Project Proposals"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="projectProposal",
     *         in="path",
     *         required=true,
     *         description="Slug del proyecto",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"investment_amount"},
     *             @OA\Property(property="investment_amount", type="number", example=5000),
     *             @OA\Property(property="investment_type", type="string", enum={"monetary", "in_kind", "labor", "materials", "expertise"}, default="monetary"),
     *             @OA\Property(property="investment_description", type="string", example="Inversión para participar en el proyecto"),
     *             @OA\Property(property="expected_return_percentage", type="number", example=8.5),
     *             @OA\Property(property="investment_term_years", type="integer", example=10),
     *             @OA\Property(property="return_frequency", type="string", enum={"monthly", "quarterly", "biannual", "annual"})
     *         )
     *     ),
     *     @OA\Response(response=201, description="Inversión creada"),
     *     @OA\Response(response=400, description="Proyecto no acepta inversiones"),
     *     @OA\Response(response=422, description="Error de validación")
     * )
     */
    public function invest(Request $request, string $slug): JsonResponse
    {
        $project = ProjectProposal::where('slug', $slug)->firstOrFail();

        if (!$project->isOpenForInvestment()) {
            return response()->json([
                'message' => 'Este proyecto no está abierto a inversiones actualmente',
            ], 400);
        }

        $request->validate([
            'investment_amount' => 'required|numeric|min:1',
            'investment_type' => 'nullable|in:monetary,in_kind,labor,materials,expertise,equipment,land_use,mixed',
            'investment_description' => 'nullable|string|max:1000',
            'expected_return_percentage' => 'nullable|numeric|min:0|max:100',
            'investment_term_years' => 'nullable|integer|min:1|max:50',
            'return_frequency' => 'nullable|in:monthly,quarterly,biannual,annual,at_completion,custom',
        ]);

        // Validar límites de inversión
        $amount = $request->investment_amount;
        
        if ($project->min_investment_per_participant && $amount < $project->min_investment_per_participant) {
            return response()->json([
                'message' => "La inversión mínima es de €{$project->min_investment_per_participant}",
            ], 422);
        }

        if ($project->max_investment_per_participant && $amount > $project->max_investment_per_participant) {
            return response()->json([
                'message' => "La inversión máxima es de €{$project->max_investment_per_participant}",
            ], 422);
        }

        if ($amount > $project->getRemainingInvestment()) {
            return response()->json([
                'message' => "Solo quedan €{$project->getRemainingInvestment()} por financiar",
            ], 422);
        }

        // Verificar si el usuario ya ha invertido
        $existingInvestment = $project->investments()
                                    ->where('investor_id', auth()->id())
                                    ->first();

        if ($existingInvestment) {
            return response()->json([
                'message' => 'Ya tienes una inversión en este proyecto',
            ], 400);
        }

        $investment = $project->addInvestment(auth()->user(), $amount, [
            'type' => $request->investment_type ?? 'monetary',
            'description' => $request->investment_description,
            'return_percentage' => $request->expected_return_percentage,
            'term_years' => $request->investment_term_years,
            'return_frequency' => $request->return_frequency,
        ]);

        return response()->json([
            'message' => 'Inversión realizada exitosamente',
            'data' => [
                'investment_id' => $investment->id,
                'amount' => $investment->investment_amount,
                'project_funding_percentage' => $project->fresh()->getFundingPercentage(),
                'remaining_investment' => $project->fresh()->getRemainingInvestment(),
            ],
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/project-proposals/nearby",
     *     summary="Obtener proyectos cercanos",
     *     tags={"Project Proposals"},
     *     @OA\Parameter(
     *         name="latitude",
     *         in="query",
     *         required=true,
     *         description="Latitud",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="longitude",
     *         in="query",
     *         required=true,
     *         description="Longitud",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="radius",
     *         in="query",
     *         description="Radio en kilómetros",
     *         @OA\Schema(type="number", default=50)
     *     ),
     *     @OA\Response(response=200, description="Proyectos cercanos")
     * )
     */
    public function nearby(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:1|max:500',
        ]);

        $projects = ProjectProposal::getNearby(
            $request->latitude,
            $request->longitude,
            $request->radius ?? 50
        );

        return ProjectProposalResource::collection($projects);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/project-proposals/{projectProposal}/stats",
     *     summary="Obtener estadísticas del proyecto",
     *     tags={"Project Proposals"},
     *     @OA\Parameter(
     *         name="projectProposal",
     *         in="path",
     *         required=true,
     *         description="Slug del proyecto",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Estadísticas del proyecto")
     * )
     */
    public function stats(string $slug): JsonResponse
    {
        $project = ProjectProposal::where('slug', $slug)
                                 ->where('is_public', true)
                                 ->firstOrFail();

        $stats = [
            'funding' => [
                'total_required' => $project->total_investment_required,
                'amount_raised' => $project->investment_raised,
                'percentage_funded' => $project->getFundingPercentage(),
                'remaining_amount' => $project->getRemainingInvestment(),
                'investors_count' => $project->current_participants,
                'days_remaining' => now()->diffInDays($project->funding_deadline, false),
            ],
            'technical' => [
                'estimated_power_kw' => $project->estimated_power_kw,
                'estimated_annual_production_kwh' => $project->estimated_annual_production_kwh,
                'estimated_roi_percentage' => $project->estimated_roi_percentage,
                'payback_period_years' => $project->payback_period_years,
                'estimated_annual_savings' => $project->estimated_annual_savings,
            ],
            'engagement' => [
                'views_count' => $project->views_count,
                'likes_count' => $project->likes_count,
                'comments_count' => $project->comments_count,
                'shares_count' => $project->shares_count,
                'bookmarks_count' => $project->bookmarks_count,
                'engagement_score' => $project->engagement_score,
            ],
            'status' => [
                'current_status' => $project->status,
                'is_open_for_investment' => $project->isOpenForInvestment(),
                'is_fully_funded' => $project->isFullyFunded(),
                'is_technically_validated' => $project->is_technically_validated,
                'has_permits' => $project->has_permits,
            ],
        ];

        return response()->json($stats);
    }
}
