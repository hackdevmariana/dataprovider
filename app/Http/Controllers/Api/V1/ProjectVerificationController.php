<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ProjectVerification;
use App\Models\ProjectProposal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * @tags Project Verification
 * @group Sistema de Monetización
 * 
 * API endpoints para gestionar verificaciones de proyectos.
 */
/**
 * @OA\Tag(
 *     name="Verificaciones de Proyectos",
 *     description="APIs para la gestión de Verificaciones de Proyectos"
 * )
 */
class ProjectVerificationController extends Controller
{
    /**
     * Listar verificaciones
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $query = ProjectVerification::query();

        // Filtrar por usuario si no es admin
        if (!$user->hasRole('admin')) {
            $query->where(function ($q) use ($user) {
                $q->where('requested_by', $user->id)
                  ->orWhere('verified_by', $user->id);
            });
        }

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('project_id')) {
            $query->where('project_proposal_id', $request->project_id);
        }

        $verifications = $query->with(['projectProposal:id,title,slug', 'requester:id,name,email', 'verifier:id,name,email'])
                              ->orderBy('created_at', 'desc')
                              ->paginate(20);

        return response()->json([
            'data' => $verifications->map(function ($verification) {
                return $this->transformVerification($verification);
            }),
            'meta' => [
                'current_page' => $verifications->currentPage(),
                'total' => $verifications->total(),
                'per_page' => $verifications->perPage(),
            ]
        ]);
    }

    /**
     * Solicitar verificación de proyecto
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $request->validate([
            'project_proposal_id' => 'required|exists:project_proposals,id',
            'type' => ['required', Rule::in(['basic', 'advanced', 'professional', 'enterprise'])],
            'is_public' => 'boolean',
        ]);

        // Verificar que el usuario es propietario del proyecto
        $project = ProjectProposal::findOrFail($request->project_proposal_id);
        
        if ($project->user_id !== $user->id) {
            return response()->json([
                'message' => 'No tienes permisos para solicitar verificación de este proyecto'
            ], 403);
        }

        // Verificar que no hay una verificación activa
        $existingVerification = ProjectVerification::where('project_proposal_id', $project->id)
                                                  ->whereIn('status', ['requested', 'in_review', 'approved'])
                                                  ->first();

        if ($existingVerification) {
            return response()->json([
                'message' => 'Este proyecto ya tiene una verificación activa',
                'existing_verification' => $this->transformVerification($existingVerification)
            ], 409);
        }

        $verification = ProjectVerification::create([
            'project_proposal_id' => $project->id,
            'requested_by' => $user->id,
            'type' => $request->type,
            'status' => 'requested',
            'is_public' => $request->boolean('is_public', true),
        ]);

        $verification->load(['projectProposal:id,title,slug', 'requester:id,name,email']);

        return response()->json([
            'data' => $this->transformVerification($verification),
            'message' => 'Solicitud de verificación creada exitosamente'
        ], 201);
    }

    /**
     * Mostrar verificación específica
     */
    public function show(ProjectVerification $projectVerification): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        // Verificar permisos
        if (!$user->hasRole('admin') && 
            $projectVerification->requested_by !== $user->id && 
            $projectVerification->verified_by !== $user->id) {
            return response()->json(['message' => 'Sin permisos para ver esta verificación'], 403);
        }

        $projectVerification->load([
            'projectProposal:id,title,slug,description',
            'requester:id,name,email',
            'verifier:id,name,email'
        ]);

        return response()->json([
            'data' => $this->transformVerification($projectVerification, true)
        ]);
    }

    /**
     * Iniciar proceso de verificación (solo verificadores)
     */
    public function startReview(ProjectVerification $projectVerification): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        // Verificar que el usuario puede verificar proyectos
        if (!$user->canBeConsultant() && !$user->hasRole(['admin', 'verifier'])) {
            return response()->json([
                'message' => 'No tienes permisos para verificar proyectos'
            ], 403);
        }

        if ($projectVerification->status !== 'requested') {
            return response()->json([
                'message' => 'Esta verificación no está disponible para revisión'
            ], 400);
        }

        $projectVerification->startReview($user);
        $projectVerification->load(['projectProposal:id,title,slug', 'requester:id,name,email', 'verifier:id,name,email']);

        return response()->json([
            'data' => $this->transformVerification($projectVerification),
            'message' => 'Proceso de verificación iniciado'
        ]);
    }

    /**
     * Aprobar verificación
     */
    public function approve(Request $request, ProjectVerification $projectVerification): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        if ($projectVerification->verified_by !== $user->id && !$user->hasRole('admin')) {
            return response()->json(['message' => 'Sin permisos para aprobar esta verificación'], 403);
        }

        $request->validate([
            'verification_results' => 'required|array',
            'verification_notes' => 'nullable|string|max:2000',
            'score' => 'nullable|integer|min:1|max:100',
        ]);

        $projectVerification->approve(
            $request->verification_results,
            $request->verification_notes,
            $request->score
        );

        $projectVerification->load(['projectProposal:id,title,slug', 'requester:id,name,email', 'verifier:id,name,email']);

        return response()->json([
            'data' => $this->transformVerification($projectVerification),
            'message' => 'Verificación aprobada exitosamente'
        ]);
    }

    /**
     * Rechazar verificación
     */
    public function reject(Request $request, ProjectVerification $projectVerification): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        if ($projectVerification->verified_by !== $user->id && !$user->hasRole('admin')) {
            return response()->json(['message' => 'Sin permisos para rechazar esta verificación'], 403);
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
            'verification_notes' => 'nullable|string|max:2000',
        ]);

        $projectVerification->reject(
            $request->rejection_reason,
            $request->verification_notes
        );

        $projectVerification->load(['projectProposal:id,title,slug', 'requester:id,name,email', 'verifier:id,name,email']);

        return response()->json([
            'data' => $this->transformVerification($projectVerification),
            'message' => 'Verificación rechazada'
        ]);
    }

    /**
     * Transformar verificación para respuesta API
     */
    private function transformVerification(ProjectVerification $verification, bool $detailed = false): array
    {
        $data = [
            'id' => $verification->id,
            'project' => [
                'id' => $verification->projectProposal->id,
                'title' => $verification->projectProposal->title,
                'slug' => $verification->projectProposal->slug,
            ],
            'type' => $verification->type,
            'type_label' => $verification->getTypeLabel(),
            'status' => $verification->status,
            'status_label' => $verification->getStatusLabel(),
            'status_color' => $verification->getStatusColor(),
            'fee' => $verification->fee,
            'currency' => $verification->currency,
            'requester' => [
                'id' => $verification->requester->id,
                'name' => $verification->requester->name,
                'email' => $verification->requester->email,
            ],
            'verifier' => $verification->verifier ? [
                'id' => $verification->verifier->id,
                'name' => $verification->verifier->name,
                'email' => $verification->verifier->email,
            ] : null,
            'requested_at' => $verification->requested_at,
            'verified_at' => $verification->verified_at,
            'expires_at' => $verification->expires_at,
            'is_public' => $verification->is_public,
        ];

        if ($detailed) {
            $data = array_merge($data, [
                'verification_criteria' => $verification->verification_criteria,
                'documents_required' => $verification->documents_required,
                'documents_provided' => $verification->documents_provided,
                'verification_results' => $verification->verification_results,
                'verification_notes' => $verification->verification_notes,
                'rejection_reason' => $verification->rejection_reason,
                'score' => $verification->score,
                'certificate_number' => $verification->certificate_number,
                'badge' => $verification->getBadge(),
                'days_until_expiration' => $verification->getDaysUntilExpiration(),
            ]);
        }

        return $data;
    }
}