<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProjectCommissionResource;
use App\Models\ProjectCommission;
use App\Models\ProjectProposal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Auth;

/**
 * @tags Project Commissions
 * @group Sistema de Monetización - Comisiones
 */
/**
 * @OA\Tag(
 *     name="Comisiones de Proyectos",
 *     description="APIs para la gestión de Comisiones de Proyectos"
 * )
 */
class ProjectCommissionController extends Controller
{
    /**
     * Listar comisiones del usuario autenticado
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $query = $user->commissions()->with(['projectProposal:id,title,slug']);

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->boolean('overdue_only')) {
            $query->where('status', 'pending')->where('due_date', '<', now());
        }

        $commissions = $query->orderBy('due_date', 'desc')->paginate(20);

        return response()->json([
            'data' => ProjectCommissionResource::collection($commissions),
            'meta' => [
                'current_page' => $commissions->currentPage(),
                'total' => $commissions->total(),
                'per_page' => $commissions->perPage(),
                'total_pending' => $user->pendingCommissions()->sum('amount'),
                'total_paid' => $user->commissions()->where('status', 'paid')->sum('amount'),
                'overdue_count' => $user->commissions()->where('status', 'pending')->where('due_date', '<', now())->count(),
            ]
        ]);
    }

    /**
     * Mostrar comisión específica
     */
    public function show(ProjectCommission $projectCommission): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        // Verificar permisos
        if ($projectCommission->user_id !== $user->id && !$user->hasRole('admin')) {
            return response()->json(['message' => 'Sin permisos para ver esta comisión'], 403);
        }

        $projectCommission->load(['projectProposal', 'payments']);

        return response()->json([
            'data' => new ProjectCommissionResource($projectCommission)
        ]);
    }

    /**
     * Crear comisión automáticamente para un proyecto exitoso
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $request->validate([
            'project_proposal_id' => 'required|exists:project_proposals,id',
            'type' => 'required|in:success_fee,listing_fee,verification_fee,premium_fee',
        ]);

        $project = ProjectProposal::findOrFail($request->project_proposal_id);

        // Verificar que el usuario es propietario del proyecto
        if ($project->user_id !== $user->id) {
            return response()->json(['message' => 'Sin permisos para crear comisión de este proyecto'], 403);
        }

        // Verificar que no existe ya una comisión del mismo tipo para este proyecto
        $existingCommission = ProjectCommission::where('project_proposal_id', $project->id)
                                               ->where('type', $request->type)
                                               ->first();

        if ($existingCommission) {
            return response()->json([
                'message' => 'Ya existe una comisión de este tipo para este proyecto',
                'existing_commission' => new ProjectCommissionResource($existingCommission)
            ], 409);
        }

        // Crear comisión según el tipo
        $commission = match ($request->type) {
            'success_fee' => ProjectCommission::createSuccessFee($project, $user),
            'listing_fee' => $this->createListingFee($project, $user),
            'verification_fee' => $this->createVerificationFee($project, $user),
            'premium_fee' => $this->createPremiumFee($project, $user),
        };

        $commission->load(['projectProposal']);

        return response()->json([
            'data' => new ProjectCommissionResource($commission),
            'message' => 'Comisión creada exitosamente'
        ], 201);
    }

    /**
     * Marcar comisión como pagada (solo admins)
     */
    public function markAsPaid(Request $request, ProjectCommission $projectCommission): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['message' => 'Sin permisos de administrador'], 403);
        }

        if ($projectCommission->status !== 'pending') {
            return response()->json(['message' => 'Solo se pueden marcar como pagadas las comisiones pendientes'], 400);
        }

        $request->validate([
            'payment_method' => 'nullable|string|max:255',
            'transaction_id' => 'nullable|string|max:255',
        ]);

        $projectCommission->markAsPaid(
            $request->payment_method,
            $request->transaction_id
        );

        return response()->json([
            'data' => new ProjectCommissionResource($projectCommission),
            'message' => 'Comisión marcada como pagada'
        ]);
    }

    /**
     * Exonerar comisión (solo admins)
     */
    public function waive(ProjectCommission $projectCommission): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['message' => 'Sin permisos de administrador'], 403);
        }

        if ($projectCommission->status !== 'pending') {
            return response()->json(['message' => 'Solo se pueden exonerar comisiones pendientes'], 400);
        }

        $projectCommission->update(['status' => 'waived']);

        return response()->json([
            'data' => new ProjectCommissionResource($projectCommission),
            'message' => 'Comisión exonerada'
        ]);
    }

    /**
     * Estadísticas de comisiones del usuario
     */
    public function stats(): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $commissions = $user->commissions();

        return response()->json([
            'data' => [
                'total_commissions' => $commissions->count(),
                'pending_amount' => $commissions->where('status', 'pending')->sum('amount'),
                'paid_amount' => $commissions->where('status', 'paid')->sum('amount'),
                'waived_amount' => $commissions->where('status', 'waived')->sum('amount'),
                'overdue_count' => $commissions->where('status', 'pending')->where('due_date', '<', now())->count(),
                'overdue_amount' => $commissions->where('status', 'pending')->where('due_date', '<', now())->sum('amount'),
                'average_commission_rate' => $commissions->avg('rate'),
                'by_type' => [
                    'success_fee' => $commissions->where('type', 'success_fee')->count(),
                    'listing_fee' => $commissions->where('type', 'listing_fee')->count(),
                    'verification_fee' => $commissions->where('type', 'verification_fee')->count(),
                    'premium_fee' => $commissions->where('type', 'premium_fee')->count(),
                ],
            ]
        ]);
    }

    // Métodos privados para crear diferentes tipos de comisiones

    private function createListingFee(ProjectProposal $project, $user): ProjectCommission
    {
        return ProjectCommission::create([
            'project_proposal_id' => $project->id,
            'user_id' => $user->id,
            'type' => 'listing_fee',
            'amount' => 49.99,
            'rate' => 0,
            'base_amount' => 49.99,
            'status' => 'pending',
            'due_date' => now()->addDays(7),
            'description' => "Tarifa de listado para proyecto: {$project->title}",
            'calculation_details' => [
                'type' => 'fixed_fee',
                'amount' => 49.99,
                'project_title' => $project->title,
            ],
        ]);
    }

    private function createVerificationFee(ProjectProposal $project, $user): ProjectCommission
    {
        return ProjectCommission::create([
            'project_proposal_id' => $project->id,
            'user_id' => $user->id,
            'type' => 'verification_fee',
            'amount' => 199.00,
            'rate' => 0,
            'base_amount' => 199.00,
            'status' => 'pending',
            'due_date' => now()->addDays(14),
            'description' => "Tarifa de verificación para proyecto: {$project->title}",
            'calculation_details' => [
                'type' => 'verification_basic',
                'amount' => 199.00,
                'project_title' => $project->title,
            ],
        ]);
    }

    private function createPremiumFee(ProjectProposal $project, $user): ProjectCommission
    {
        return ProjectCommission::create([
            'project_proposal_id' => $project->id,
            'user_id' => $user->id,
            'type' => 'premium_fee',
            'amount' => 99.99,
            'rate' => 0,
            'base_amount' => 99.99,
            'status' => 'pending',
            'due_date' => now()->addDays(30),
            'description' => "Tarifa premium para proyecto: {$project->title}",
            'calculation_details' => [
                'type' => 'premium_listing',
                'amount' => 99.99,
                'project_title' => $project->title,
            ],
        ]);
    }
}