<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProjectInvestmentResource;
use App\Models\ProjectInvestment;
use App\Models\ProjectProposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @group Project Investments
 *
 * APIs para la gestión de inversiones en proyectos colaborativos.
 * Permite a los usuarios invertir en proyectos energéticos de la cooperativa.
 */
/**
 * @OA\Tag(
 *     name="Inversiones en Proyectos",
 *     description="APIs para la gestión de Inversiones en Proyectos"
 * )
 */
class ProjectInvestmentController extends Controller
{
    /**
     * Display a listing of investments
     *
     * Obtiene una lista de inversiones con opciones de filtrado.
     *
     * @queryParam project_proposal_id int ID del proyecto. Example: 1
     * @queryParam investor_id int ID del inversor. Example: 1
     * @queryParam investment_type string Tipo de inversión (monetary, equipment, service, labor, mixed). Example: monetary
     * @queryParam status string Estado de la inversión (pending, confirmed, completed, cancelled, refunded). Example: confirmed
     * @queryParam min_amount number Cantidad mínima. Example: 1000
     * @queryParam max_amount number Cantidad máxima. Example: 10000
     * @queryParam is_public boolean Filtrar por inversiones públicas. Example: true
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\ProjectInvestmentResource
     * @apiResourceModel App\Models\ProjectInvestment
     */
    public function index(Request $request)
    {
        $query = ProjectInvestment::with(['projectProposal', 'investor'])
            ->when($request->project_proposal_id, fn($q, $projectId) => $q->where('project_proposal_id', $projectId))
            ->when($request->investor_id, fn($q, $investorId) => $q->where('investor_id', $investorId))
            ->when($request->investment_type, fn($q, $type) => $q->where('investment_type', $type))
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->min_amount, fn($q, $amount) => $q->where('amount', '>=', $amount))
            ->when($request->max_amount, fn($q, $amount) => $q->where('amount', '<=', $amount))
            ->when($request->has('is_public'), fn($q) => $q->where('is_public', $request->boolean('is_public')))
            ->orderBy('invested_at', 'desc');

        $perPage = min($request->get('per_page', 15), 100);
        $investments = $query->paginate($perPage);

        return ProjectInvestmentResource::collection($investments);
    }

    /**
     * Store a new investment
     *
     * Crea una nueva inversión en un proyecto.
     *
     * @bodyParam project_proposal_id int required ID del proyecto. Example: 1
     * @bodyParam amount number required Cantidad a invertir. Example: 5000
     * @bodyParam investment_type string required Tipo de inversión (monetary, equipment, service, labor, mixed). Example: monetary
     * @bodyParam expected_return_rate number Tasa de retorno esperada (%). Example: 8.5
     * @bodyParam expected_return_period_months int Período de retorno en meses. Example: 24
     * @bodyParam terms string Términos de la inversión. Example: Inversión con retorno fijo
     * @bodyParam conditions string Condiciones adicionales. Example: Pago mensual
     * @bodyParam notes string Notas del inversor. Example: Interesado en energía solar
     * @bodyParam is_public boolean Si la inversión es pública. Default: false. Example: false
     * @bodyParam is_anonymous boolean Si la inversión es anónima. Default: false. Example: false
     * @bodyParam accepts_partial_funding boolean Si acepta financiación parcial. Default: true. Example: true
     *
     * @apiResource App\Http\Resources\V1\ProjectInvestmentResource
     * @apiResourceModel App\Models\ProjectInvestment
     *
     * @response 201 {"data": {...}, "message": "Inversión creada exitosamente"}
     * @response 422 {"message": "Datos inválidos", "errors": {...}}
     * @authenticated
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_proposal_id' => 'required|exists:project_proposals,id',
            'amount' => 'required|numeric|min:0.01',
            'investment_type' => 'required|in:monetary,equipment,service,labor,mixed',
            'expected_return_rate' => 'nullable|numeric|min:0|max:100',
            'expected_return_period_months' => 'nullable|integer|min:1',
            'terms' => 'nullable|string|max:1000',
            'conditions' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'is_public' => 'boolean',
            'is_anonymous' => 'boolean',
            'accepts_partial_funding' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::guard('sanctum')->user();
        
        // Verificar que el proyecto acepte inversiones
        $project = ProjectProposal::findOrFail($request->project_proposal_id);
        
        if (!$project->accepts_investments) {
            return response()->json([
                'message' => 'Este proyecto no acepta inversiones'
            ], 422);
        }

        // Verificar que el usuario no sea el proponente del proyecto
        if ($project->proposer_id === $user->id) {
            return response()->json([
                'message' => 'No puedes invertir en tu propio proyecto'
            ], 422);
        }

        $investment = ProjectInvestment::create(array_merge($validator->validated(), [
            'investor_id' => $user->id,
            'status' => 'pending',
            'invested_at' => now(),
        ]));

        $investment->load(['projectProposal', 'investor']);

        return response()->json([
            'data' => new ProjectInvestmentResource($investment),
            'message' => 'Inversión creada exitosamente'
        ], 201);
    }

    /**
     * Display the specified investment
     *
     * Muestra una inversión específica.
     *
     * @urlParam projectInvestment int required ID de la inversión. Example: 1
     *
     * @apiResource App\Http\Resources\V1\ProjectInvestmentResource
     * @apiResourceModel App\Models\ProjectInvestment
     *
     * @response 404 {"message": "Inversión no encontrada"}
     */
    public function show(ProjectInvestment $projectInvestment)
    {
        $user = Auth::guard('sanctum')->user();

        // Solo mostrar inversiones públicas o propias
        if (!$projectInvestment->is_public && $projectInvestment->investor_id !== $user->id) {
            return response()->json(['message' => 'Inversión no encontrada'], 404);
        }

        $projectInvestment->load(['projectProposal', 'investor']);
        return new ProjectInvestmentResource($projectInvestment);
    }

    /**
     * Update the specified investment
     *
     * Actualiza una inversión existente (solo si está pendiente).
     *
     * @urlParam projectInvestment int required ID de la inversión. Example: 1
     * @bodyParam amount number Cantidad a invertir. Example: 7500
     * @bodyParam expected_return_rate number Tasa de retorno esperada. Example: 9.0
     * @bodyParam expected_return_period_months int Período de retorno. Example: 18
     * @bodyParam terms string Términos actualizados. Example: Nuevos términos
     * @bodyParam conditions string Condiciones actualizadas. Example: Nuevas condiciones
     * @bodyParam notes string Notas actualizadas. Example: Notas adicionales
     * @bodyParam is_public boolean Visibilidad pública. Example: true
     *
     * @apiResource App\Http\Resources\V1\ProjectInvestmentResource
     * @apiResourceModel App\Models\ProjectInvestment
     *
     * @response 403 {"message": "No autorizado"}
     * @response 422 {"message": "No se puede modificar una inversión confirmada"}
     * @authenticated
     */
    public function update(Request $request, ProjectInvestment $projectInvestment)
    {
        $user = Auth::guard('sanctum')->user();

        if ($projectInvestment->investor_id !== $user->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        if ($projectInvestment->status !== 'pending') {
            return response()->json([
                'message' => 'No se puede modificar una inversión confirmada'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'numeric|min:0.01',
            'expected_return_rate' => 'nullable|numeric|min:0|max:100',
            'expected_return_period_months' => 'nullable|integer|min:1',
            'terms' => 'nullable|string|max:1000',
            'conditions' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'is_public' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $projectInvestment->update($validator->validated());
        $projectInvestment->load(['projectProposal', 'investor']);

        return new ProjectInvestmentResource($projectInvestment);
    }

    /**
     * Remove the specified investment
     *
     * Cancela una inversión (solo si está pendiente).
     *
     * @urlParam projectInvestment int required ID de la inversión. Example: 1
     *
     * @response 200 {"message": "Inversión cancelada exitosamente"}
     * @response 403 {"message": "No autorizado"}
     * @response 422 {"message": "No se puede cancelar una inversión confirmada"}
     * @authenticated
     */
    public function destroy(ProjectInvestment $projectInvestment)
    {
        $user = Auth::guard('sanctum')->user();

        if ($projectInvestment->investor_id !== $user->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        if ($projectInvestment->status !== 'pending') {
            return response()->json([
                'message' => 'No se puede cancelar una inversión confirmada'
            ], 422);
        }

        $projectInvestment->update(['status' => 'cancelled']);

        return response()->json(['message' => 'Inversión cancelada exitosamente']);
    }

    /**
     * Get investments for a project
     *
     * Obtiene todas las inversiones de un proyecto específico.
     *
     * @urlParam project int required ID del proyecto. Example: 1
     * @queryParam status string Filtrar por estado. Example: confirmed
     * @queryParam investment_type string Filtrar por tipo. Example: monetary
     * @queryParam per_page int Cantidad por página. Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\ProjectInvestmentResource
     * @apiResourceModel App\Models\ProjectInvestment
     */
    public function projectInvestments(Request $request, ProjectProposal $project)
    {
        $query = $project->investments()
            ->with(['projectProposal', 'investor'])
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->investment_type, fn($q, $type) => $q->where('investment_type', $type))
            ->where('is_public', true) // Solo mostrar inversiones públicas
            ->orderBy('invested_at', 'desc');

        $perPage = min($request->get('per_page', 15), 100);
        $investments = $query->paginate($perPage);

        return ProjectInvestmentResource::collection($investments);
    }

    /**
     * Confirm an investment
     *
     * Confirma una inversión pendiente (solo para administradores del proyecto).
     *
     * @urlParam projectInvestment int required ID de la inversión. Example: 1
     *
     * @response 200 {"message": "Inversión confirmada", "data": {...}}
     * @response 403 {"message": "No autorizado"}
     * @response 422 {"message": "La inversión ya está confirmada"}
     * @authenticated
     */
    public function confirm(ProjectInvestment $projectInvestment)
    {
        $user = Auth::guard('sanctum')->user();

        // Solo el proponente del proyecto puede confirmar inversiones
        if ($projectInvestment->projectProposal->proposer_id !== $user->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        if ($projectInvestment->status !== 'pending') {
            return response()->json([
                'message' => 'La inversión ya está confirmada'
            ], 422);
        }

        $projectInvestment->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);

        $projectInvestment->load(['projectProposal', 'investor']);

        return response()->json([
            'message' => 'Inversión confirmada',
            'data' => new ProjectInvestmentResource($projectInvestment)
        ]);
    }
}