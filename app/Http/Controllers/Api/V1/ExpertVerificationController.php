<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ExpertVerificationResource;
use App\Models\ExpertVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @group Expert Verifications
 * 
 * API endpoints for managing expert verification requests and processes.
 */
class ExpertVerificationController extends Controller
{
    /**
     * Display expert verifications
     */
    public function index(Request $request)
    {
        $query = ExpertVerification::with(['user', 'verifier'])
            ->when($request->user_id, fn($q, $userId) => $q->where('user_id', $userId))
            ->when($request->expertise_area, fn($q, $area) => $q->where('expertise_area', $area))
            ->when($request->verification_level, fn($q, $level) => $q->where('verification_level', $level))
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->verified_by, fn($q, $verifierId) => $q->where('verified_by', $verifierId))
            ->orderBy('submitted_at', 'desc');

        $perPage = min($request->get('per_page', 15), 100);
        $verifications = $query->paginate($perPage);

        return ExpertVerificationResource::collection($verifications);
    }

    /**
     * Submit expert verification request
     */
    public function store(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $validator = Validator::make($request->all(), [
            'expertise_area' => 'required|in:solar,wind,legal,financial,technical,installation,grid,storage,efficiency,sustainability',
            'verification_level' => 'required|in:basic,advanced,professional,expert',
            'years_experience' => 'required|integer|min:0|max:50',
            'expertise_description' => 'required|string|min:50|max:2000',
            'credentials' => 'nullable|array',
            'verification_documents' => 'nullable|array',
            'certifications' => 'nullable|array',
            'education' => 'nullable|array',
            'work_history' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar si ya existe una solicitud pendiente
        $existingRequest = ExpertVerification::where('user_id', $user->id)
            ->where('expertise_area', $request->expertise_area)
            ->whereIn('status', ['pending', 'under_review'])
            ->first();

        if ($existingRequest) {
            return response()->json([
                'message' => 'Ya tienes una solicitud pendiente para esta área de expertise'
            ], 409);
        }

        $verification = ExpertVerification::create(array_merge($validator->validated(), [
            'user_id' => $user->id,
            'status' => 'pending',
            'submitted_at' => now(),
            'verification_fee' => $this->calculateVerificationFee($request->verification_level),
        ]));

        $verification->load(['user']);

        return response()->json([
            'data' => new ExpertVerificationResource($verification),
            'message' => 'Solicitud de verificación enviada exitosamente'
        ], 201);
    }

    /**
     * Display the specified verification
     */
    public function show(ExpertVerification $expertVerification)
    {
        $user = Auth::guard('sanctum')->user();
        
        // Solo el usuario propietario, el verificador asignado o un admin pueden ver los detalles
        if (!$user || (
            $user->id !== $expertVerification->user_id && 
            $user->id !== $expertVerification->verified_by && 
            !$user->hasRole('admin')
        )) {
            return response()->json(['message' => 'No tienes permisos para ver esta verificación'], 403);
        }

        $expertVerification->load(['user', 'verifier']);
        
        return new ExpertVerificationResource($expertVerification);
    }

    /**
     * Start review process
     */
    public function startReview(ExpertVerification $expertVerification)
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['message' => 'No tienes permisos para revisar verificaciones'], 403);
        }

        if ($expertVerification->status !== 'pending') {
            return response()->json(['message' => 'Esta verificación no está pendiente'], 400);
        }

        $expertVerification->update([
            'status' => 'under_review',
            'verified_by' => $user->id,
            'reviewed_at' => now(),
        ]);

        $expertVerification->load(['user', 'verifier']);

        return response()->json([
            'data' => new ExpertVerificationResource($expertVerification),
            'message' => 'Revisión iniciada exitosamente'
        ]);
    }

    /**
     * Approve verification
     */
    public function approve(ExpertVerification $expertVerification, Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['message' => 'No tienes permisos para aprobar verificaciones'], 403);
        }

        if ($expertVerification->status !== 'under_review') {
            return response()->json(['message' => 'Esta verificación no está en revisión'], 400);
        }

        $validator = Validator::make($request->all(), [
            'verification_score' => 'required|integer|min:1|max:100',
            'verification_notes' => 'nullable|string|max:1000',
            'expires_at' => 'nullable|date|after:today',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $expertVerification->update(array_merge($validator->validated(), [
            'status' => 'approved',
            'verified_at' => now(),
            'is_public' => true,
        ]));

        $expertVerification->load(['user', 'verifier']);

        return response()->json([
            'data' => new ExpertVerificationResource($expertVerification),
            'message' => 'Verificación aprobada exitosamente'
        ]);
    }

    /**
     * Reject verification
     */
    public function reject(ExpertVerification $expertVerification, Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['message' => 'No tienes permisos para rechazar verificaciones'], 403);
        }

        if ($expertVerification->status !== 'under_review') {
            return response()->json(['message' => 'Esta verificación no está en revisión'], 400);
        }

        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string|max:1000',
            'verification_notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $expertVerification->update(array_merge($validator->validated(), [
            'status' => 'rejected',
        ]));

        $expertVerification->load(['user', 'verifier']);

        return response()->json([
            'data' => new ExpertVerificationResource($expertVerification),
            'message' => 'Verificación rechazada'
        ]);
    }

    /**
     * Get verification statistics
     */
    public function stats(Request $request)
    {
        $query = ExpertVerification::query();

        if ($request->period && $request->period !== 'all') {
            $date = match($request->period) {
                'week' => now()->subWeek(),
                'month' => now()->subMonth(),
                'year' => now()->subYear(),
                default => now()->subMonth()
            };
            $query->where('submitted_at', '>=', $date);
        }

        $verifications = $query->get();

        $stats = [
            'total_requests' => $verifications->count(),
            'by_status' => $verifications->groupBy('status')->map->count(),
            'by_expertise_area' => $verifications->groupBy('expertise_area')->map->count(),
            'by_verification_level' => $verifications->groupBy('verification_level')->map->count(),
            'average_processing_time' => $this->calculateAverageProcessingTime($verifications),
            'approval_rate' => $this->calculateApprovalRate($verifications),
        ];

        return response()->json(['data' => $stats]);
    }

    /**
     * Calculate verification fee based on level
     */
    private function calculateVerificationFee(string $level): float
    {
        return match($level) {
            'basic' => 25.00,
            'advanced' => 50.00,
            'professional' => 100.00,
            'expert' => 200.00,
            default => 25.00,
        };
    }

    /**
     * Calculate average processing time in days
     */
    private function calculateAverageProcessingTime($verifications): ?float
    {
        $completed = $verifications->whereIn('status', ['approved', 'rejected'])
            ->filter(fn($v) => $v->submitted_at && $v->reviewed_at);

        if ($completed->isEmpty()) {
            return null;
        }

        $totalDays = $completed->sum(function($verification) {
            return $verification->submitted_at->diffInDays($verification->reviewed_at);
        });

        return round($totalDays / $completed->count(), 1);
    }

    /**
     * Calculate approval rate percentage
     */
    private function calculateApprovalRate($verifications): float
    {
        $completed = $verifications->whereIn('status', ['approved', 'rejected']);
        
        if ($completed->isEmpty()) {
            return 0;
        }

        $approved = $completed->where('status', 'approved')->count();
        
        return round(($approved / $completed->count()) * 100, 1);
    }
}