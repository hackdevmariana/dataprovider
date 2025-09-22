<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CarbonSavingRequestResource;
use App\Models\CarbonSavingRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * @group Carbon Saving Requests
 *
 * APIs para la gestión de solicitudes de ahorro de carbono.
 * Permite a los usuarios solicitar y gestionar proyectos
 * de reducción de emisiones de CO2.
 */
/**
 * @OA\Tag(
 *     name="Solicitudes de Ahorro de Carbono",
 *     description="APIs para la gestión de Solicitudes de Ahorro de Carbono"
 * )
 */
class CarbonSavingRequestController extends Controller
{
    /**
     * Display a listing of carbon saving requests
     *
     * Obtiene una lista de solicitudes de ahorro de carbono con opciones de filtrado.
     *
     * @queryParam user_id int ID del usuario solicitante. Example: 1
     * @queryParam project_type string Tipo de proyecto (energy_efficiency, renewable_energy, transport, waste_reduction, reforestation). Example: energy_efficiency
     * @queryParam status string Estado de la solicitud (draft, submitted, under_review, approved, rejected, in_progress, completed). Example: submitted
     * @queryParam min_co2_reduction int Reducción mínima de CO2 en kg. Example: 1000
     * @queryParam max_co2_reduction int Reducción máxima de CO2 en kg. Example: 10000
     * @queryParam min_budget_eur int Presupuesto mínimo en euros. Example: 1000
     * @queryParam max_budget_eur int Presupuesto máximo en euros. Example: 50000
     * @queryParam location string Ubicación del proyecto. Example: Madrid
     * @queryParam is_featured boolean Solo proyectos destacados. Example: true
     * @queryParam sort string Ordenamiento (recent, oldest, co2_reduction_desc, budget_desc, status). Example: recent
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\CarbonSavingRequestResource
     * @apiResourceModel App\Models\CarbonSavingRequest
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'sometimes|integer|exists:users,id',
            'project_type' => 'sometimes|string|in:energy_efficiency,renewable_energy,transport,waste_reduction,reforestation',
            'status' => 'sometimes|string|in:draft,submitted,under_review,approved,rejected,in_progress,completed',
            'min_co2_reduction' => 'sometimes|numeric|min:0',
            'max_co2_reduction' => 'sometimes|numeric|min:0',
            'min_budget_eur' => 'sometimes|numeric|min:0',
            'max_budget_eur' => 'sometimes|numeric|min:0',
            'location' => 'sometimes|string|max:255',
            'is_featured' => 'sometimes|boolean',
            'sort' => 'sometimes|string|in:recent,oldest,co2_reduction_desc,budget_desc,status',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100'
        ]);

        $query = CarbonSavingRequest::with(['user', 'reviewer']);

        // Filtros
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('project_type')) {
            $query->where('project_type', $request->project_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('min_co2_reduction')) {
            $query->where('estimated_co2_reduction_kg', '>=', $request->min_co2_reduction);
        }

        if ($request->filled('max_co2_reduction')) {
            $query->where('estimated_co2_reduction_kg', '<=', $request->max_co2_reduction);
        }

        if ($request->filled('min_budget_eur')) {
            $query->where('budget_eur', '>=', $request->min_budget_eur);
        }

        if ($request->filled('max_budget_eur')) {
            $query->where('budget_eur', '<=', $request->max_budget_eur);
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->filled('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        // Ordenamiento
        $sort = $request->get('sort', 'recent');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'co2_reduction_desc':
                $query->orderBy('estimated_co2_reduction_kg', 'desc');
                break;
            case 'budget_desc':
                $query->orderBy('budget_eur', 'desc');
                break;
            case 'status':
                $query->orderBy('status')->orderBy('created_at', 'desc');
                break;
            default: // recent
                $query->orderBy('created_at', 'desc');
        }

        $perPage = min($request->get('per_page', 15), 100);
        $requests = $query->paginate($perPage);

        return CarbonSavingRequestResource::collection($requests)->response();
    }

    /**
     * Store a newly created carbon saving request
     *
     * Crea una nueva solicitud de ahorro de carbono.
     *
     * @bodyParam project_title string required Título del proyecto. Example: Instalación de paneles solares residenciales
     * @bodyParam project_type string required Tipo de proyecto. Example: renewable_energy
     * @bodyParam description text required Descripción detallada del proyecto. Example: Instalación de sistema fotovoltaico de 5kW para vivienda unifamiliar
     * @bodyParam estimated_co2_reduction_kg number required Reducción estimada de CO2 en kg. Example: 2500
     * @bodyParam budget_eur number required Presupuesto estimado en euros. Example: 8000
     * @bodyParam location string required Ubicación del proyecto. Example: Madrid, España
     * @bodyParam implementation_timeline string Cronograma de implementación. Example: 3 meses
     * @bodyParam expected_benefits text Beneficios esperados. Example: Reducción del 80% en factura eléctrica
     * @bodyParam technical_details text Detalles técnicos. Example: Sistema on-grid con batería de respaldo
     * @bodyParam sustainability_criteria json Criterios de sostenibilidad. Example: ["energía renovable", "eficiencia energética"]
     * @bodyParam attachments array Archivos adjuntos. Example: ["proyecto.pdf", "presupuesto.xlsx"]
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "user_id": 1,
     *     "project_title": "Instalación de paneles solares residenciales",
     *     "project_type": "renewable_energy",
     *     "estimated_co2_reduction_kg": 2500,
     *     "budget_eur": 8000,
     *     "status": "draft",
     *     "created_at": "2024-01-15T10:00:00.000000Z"
     *   },
     *   "message": "Solicitud de ahorro de carbono creada exitosamente"
     * }
     *
     * @response 422 {
     *   "message": "Error de validación",
     *   "errors": {
     *     "project_title": ["El título del proyecto es requerido"]
     *   }
     * }
     *
     * @authenticated
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'project_title' => 'required|string|max:255',
            'project_type' => 'required|string|in:energy_efficiency,renewable_energy,transport,waste_reduction,reforestation',
            'description' => 'required|string|min:50|max:5000',
            'estimated_co2_reduction_kg' => 'required|numeric|min:1|max:1000000',
            'budget_eur' => 'required|numeric|min:1|max:10000000',
            'location' => 'required|string|max:255',
            'implementation_timeline' => 'sometimes|string|max:255',
            'expected_benefits' => 'sometimes|string|max:1000',
            'technical_details' => 'sometimes|string|max:2000',
            'sustainability_criteria' => 'sometimes|array',
            'sustainability_criteria.*' => 'string|max:100',
            'attachments' => 'sometimes|array',
            'attachments.*' => 'string|max:255'
        ]);

        $userId = Auth::guard('sanctum')->user()->id;

        $request = CarbonSavingRequest::create([
            'user_id' => $userId,
            'project_title' => $request->project_title,
            'project_type' => $request->project_type,
            'description' => $request->description,
            'estimated_co2_reduction_kg' => $request->estimated_co2_reduction_kg,
            'budget_eur' => $request->budget_eur,
            'location' => $request->location,
            'implementation_timeline' => $request->implementation_timeline,
            'expected_benefits' => $request->expected_benefits,
            'technical_details' => $request->technical_details,
            'sustainability_criteria' => $request->sustainability_criteria ?? [],
            'attachments' => $request->attachments ?? [],
            'status' => 'draft',
            'is_featured' => false,
            'priority_score' => $this->calculatePriorityScore($request->estimated_co2_reduction_kg, $request->budget_eur)
        ]);

        return (new CarbonSavingRequestResource($request))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified carbon saving request
     *
     * Obtiene los detalles de una solicitud de ahorro de carbono específica.
     *
     * @urlParam carbonSavingRequest int required ID de la solicitud. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "user_id": 1,
     *     "project_title": "Instalación de paneles solares residenciales",
     *     "project_type": "renewable_energy",
     *     "estimated_co2_reduction_kg": 2500,
     *     "budget_eur": 8000,
     *     "status": "submitted",
     *     "created_at": "2024-01-15T10:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Solicitud de ahorro de carbono no encontrada"
     * }
     */
    public function show(CarbonSavingRequest $carbonSavingRequest): JsonResponse
    {
        $carbonSavingRequest->load(['user', 'reviewer']);
        return (new CarbonSavingRequestResource($carbonSavingRequest))->response();
    }

    /**
     * Update the specified carbon saving request
     *
     * Actualiza una solicitud de ahorro de carbono existente. Solo el solicitante puede modificarla
     * y solo si está en borrador o rechazada.
     *
     * @urlParam carbonSavingRequest int required ID de la solicitud. Example: 1
     * @bodyParam project_title string Título del proyecto. Example: Instalación solar residencial actualizada
     * @bodyParam description text Descripción detallada del proyecto. Example: Proyecto actualizado con mejoras técnicas
     * @bodyParam estimated_co2_reduction_kg number Reducción estimada de CO2 en kg. Example: 3000
     * @bodyParam budget_eur number Presupuesto estimado en euros. Example: 9000
     * @bodyParam expected_benefits text Beneficios esperados. Example: Beneficios actualizados del proyecto
     * @bodyParam technical_details text Detalles técnicos. Example: Detalles técnicos mejorados
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "project_title": "Instalación solar residencial actualizada",
     *     "estimated_co2_reduction_kg": 3000,
     *     "budget_eur": 9000,
     *     "updated_at": "2024-01-15T11:00:00.000000Z"
     *   },
     *   "message": "Solicitud de ahorro de carbono actualizada exitosamente"
     * }
     *
     * @response 403 {
     *   "message": "No tienes permiso para modificar esta solicitud"
     * }
     *
     * @response 422 {
     *   "message": "No se puede modificar una solicitud aprobada"
     * }
     *
     * @authenticated
     */
    public function update(Request $request, CarbonSavingRequest $carbonSavingRequest): JsonResponse
    {
        // Verificar permisos
        if ($carbonSavingRequest->user_id !== Auth::guard('sanctum')->user()->id) {
            return response()->json([
                'message' => 'No tienes permiso para modificar esta solicitud'
            ], 403);
        }

        if (!in_array($carbonSavingRequest->status, ['draft', 'rejected'])) {
            return response()->json([
                'message' => 'No se puede modificar una solicitud aprobada o en revisión'
            ], 422);
        }

        $request->validate([
            'project_title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|min:50|max:5000',
            'estimated_co2_reduction_kg' => 'sometimes|numeric|min:1|max:1000000',
            'budget_eur' => 'sometimes|numeric|min:1|max:10000000',
            'expected_benefits' => 'sometimes|string|max:1000',
            'technical_details' => 'sometimes|string|max:2000'
        ]);

        // Recalcular puntuación de prioridad si se actualiza CO2 o presupuesto
        $priorityScore = $carbonSavingRequest->priority_score;
        if ($request->filled('estimated_co2_reduction_kg') || $request->filled('budget_eur')) {
            $co2Reduction = $request->get('estimated_co2_reduction_kg', $carbonSavingRequest->estimated_co2_reduction_kg);
            $budget = $request->get('budget_eur', $carbonSavingRequest->budget_eur);
            $priorityScore = $this->calculatePriorityScore($co2Reduction, $budget);
        }

        $carbonSavingRequest->update(array_merge($request->only([
            'project_title', 'description', 'estimated_co2_reduction_kg', 
            'budget_eur', 'expected_benefits', 'technical_details'
        ]), ['priority_score' => $priorityScore]));

        return (new CarbonSavingRequestResource($carbonSavingRequest))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Remove the specified carbon saving request
     *
     * Elimina una solicitud de ahorro de carbono. Solo el solicitante puede eliminarla
     * y solo si está en borrador o rechazada.
     *
     * @urlParam carbonSavingRequest int required ID de la solicitud. Example: 1
     *
     * @response 200 {
     *   "message": "Solicitud de ahorro de carbono eliminada exitosamente"
     * }
     *
     * @response 403 {
     *   "message": "No tienes permiso para eliminar esta solicitud"
     * }
     *
     * @response 422 {
     *   "message": "No se puede eliminar una solicitud aprobada"
     * }
     *
     * @authenticated
     */
    public function destroy(CarbonSavingRequest $carbonSavingRequest): JsonResponse
    {
        // Verificar permisos
        if ($carbonSavingRequest->user_id !== Auth::guard('sanctum')->user()->id) {
            return response()->json([
                'message' => 'No tienes permiso para eliminar esta solicitud'
            ], 403);
        }

        if (!in_array($carbonSavingRequest->status, ['draft', 'rejected'])) {
            return response()->json([
                'message' => 'No se puede eliminar una solicitud aprobada o en revisión'
            ], 422);
        }

        $carbonSavingRequest->delete();

        return response()->json([
            'message' => 'Solicitud de ahorro de carbono eliminada exitosamente'
        ]);
    }

    /**
     * Submit carbon saving request
     *
     * Envía una solicitud de ahorro de carbono para revisión.
     *
     * @urlParam carbonSavingRequest int required ID de la solicitud. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "status": "submitted",
     *     "submitted_at": "2024-01-15T12:00:00.000000Z"
     *   },
     *   "message": "Solicitud enviada exitosamente para revisión"
     * }
     *
     * @authenticated
     */
    public function submit(CarbonSavingRequest $carbonSavingRequest): JsonResponse
    {
        // Verificar permisos
        if ($carbonSavingRequest->user_id !== Auth::guard('sanctum')->user()->id) {
            return response()->json([
                'message' => 'No tienes permiso para enviar esta solicitud'
            ], 403);
        }

        if ($carbonSavingRequest->status !== 'draft') {
            return response()->json([
                'message' => 'Solo se pueden enviar solicitudes en borrador'
            ], 422);
        }

        $carbonSavingRequest->update([
            'status' => 'submitted',
            'submitted_at' => now()
        ]);

        return (new CarbonSavingRequestResource($carbonSavingRequest))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Get user's requests summary
     *
     * Obtiene un resumen de las solicitudes de ahorro de carbono del usuario.
     *
     * @queryParam period string Período de tiempo (month, quarter, year, all). Example: year
     *
     * @response 200 {
     *   "data": {
     *     "total_requests": 8,
     *     "total_co2_reduction": 15000,
     *     "total_budget": 45000,
     *     "by_status": {
     *       "draft": 2,
     *       "submitted": 3,
     *       "approved": 2,
     *       "completed": 1
     *     },
     *     "by_type": {
     *       "renewable_energy": 5,
     *       "energy_efficiency": 3
     *     }
     *   }
     * }
     *
     * @authenticated
     */
    public function summary(Request $request): JsonResponse
    {
        $request->validate([
            'period' => 'sometimes|string|in:month,quarter,year,all'
        ]);

        $userId = Auth::guard('sanctum')->user()->id;
        $query = CarbonSavingRequest::where('user_id', $userId);

        // Filtrar por período
        $period = $request->get('period', 'year');
        switch ($period) {
            case 'month':
                $query->whereDate('created_at', '>=', now()->startOfMonth());
                break;
            case 'quarter':
                $query->whereDate('created_at', '>=', now()->startOfQuarter());
                break;
            case 'year':
                $query->whereDate('created_at', '>=', now()->startOfYear());
                break;
            // 'all' no aplica filtro de fecha
        }

        $requests = $query->get();

        $totalRequests = $requests->count();
        $totalCo2Reduction = $requests->sum('estimated_co2_reduction_kg');
        $totalBudget = $requests->sum('budget_eur');

        $byStatus = $requests->groupBy('status')
            ->map(function ($group) {
                return $group->count();
            });

        $byType = $requests->groupBy('project_type')
            ->map(function ($group) {
                return $group->count();
            });

        return response()->json([
            'data' => [
                'total_requests' => $totalRequests,
                'total_co2_reduction' => round($totalCo2Reduction, 2),
                'total_budget' => round($totalBudget, 2),
                'by_status' => $byStatus,
                'by_type' => $byType,
                'period' => $period,
                'average_co2_reduction' => $totalRequests > 0 ? round($totalCo2Reduction / $totalRequests, 2) : 0,
                'average_budget' => $totalRequests > 0 ? round($totalBudget / $totalRequests, 2) : 0
            ]
        ]);
    }

    /**
     * Calculate priority score based on CO2 reduction and budget
     *
     * Calcula la puntuación de prioridad basada en la reducción de CO2 y el presupuesto.
     */
    private function calculatePriorityScore(float $co2Reduction, float $budget): float
    {
        // Fórmula: (CO2 reduction / budget) * 1000 para normalizar
        // Mayor puntuación = mejor relación reducción CO2 / presupuesto
        return round(($co2Reduction / $budget) * 1000, 2);
    }
}
