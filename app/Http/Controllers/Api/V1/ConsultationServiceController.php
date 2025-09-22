<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ConsultationServiceResource;
use App\Models\ConsultationService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * @tags Consultation Services
 * @group Sistema de Monetización - Consultorías
 */
/**
 * @OA\Tag(
 *     name="Servicios de Consultoría",
 *     description="APIs para la gestión de Servicios de Consultoría"
 * )
 */
class ConsultationServiceController extends Controller
{
    /**
     * Listar servicios de consultoría
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $query = ConsultationService::query()->with(['consultant:id,name,email', 'client:id,name,email']);

        // Filtrar por rol del usuario
        if ($request->get('role') === 'consultant') {
            $query->where('consultant_id', $user->id);
        } elseif ($request->get('role') === 'client') {
            $query->where('client_id', $user->id);
        } else {
            $query->where(function($q) use ($user) {
                $q->where('consultant_id', $user->id)->orWhere('client_id', $user->id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $consultations = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'data' => ConsultationServiceResource::collection($consultations),
            'meta' => [
                'current_page' => $consultations->currentPage(),
                'total' => $consultations->total(),
                'per_page' => $consultations->perPage(),
            ]
        ]);
    }

    /**
     * Crear nueva solicitud de consultoría
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $request->validate([
            'consultant_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'type' => ['required', Rule::in(['technical', 'legal', 'financial', 'installation', 'maintenance', 'custom'])],
            'format' => ['required', Rule::in(['online', 'onsite', 'hybrid', 'document_review', 'phone_call'])],
            'hourly_rate' => 'nullable|numeric|min:0',
            'fixed_price' => 'nullable|numeric|min:0',
            'estimated_hours' => 'nullable|integer|min:1',
            'deadline' => 'nullable|date|after:today',
        ]);

        if ($request->consultant_id == $user->id) {
            return response()->json(['message' => 'No puedes solicitar consultoría a ti mismo'], 400);
        }

        $consultant = User::findOrFail($request->consultant_id);
        if (!$consultant->canBeConsultant()) {
            return response()->json(['message' => 'El usuario seleccionado no puede ofrecer servicios de consultoría'], 400);
        }

        $totalAmount = null;
        if ($request->fixed_price) {
            $totalAmount = $request->fixed_price;
        } elseif ($request->hourly_rate && $request->estimated_hours) {
            $totalAmount = $request->hourly_rate * $request->estimated_hours;
        }

        $consultation = ConsultationService::create([
            'consultant_id' => $request->consultant_id,
            'client_id' => $user->id,
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'format' => $request->format,
            'status' => 'requested',
            'hourly_rate' => $request->hourly_rate,
            'fixed_price' => $request->fixed_price,
            'total_amount' => $totalAmount,
            'estimated_hours' => $request->estimated_hours,
            'requested_at' => now(),
            'deadline' => $request->deadline,
            'requirements' => $request->requirements ?? [],
        ]);

        $consultation->load(['consultant:id,name,email', 'client:id,name,email']);

        return response()->json([
            'data' => new ConsultationServiceResource($consultation),
            'message' => 'Solicitud de consultoría creada exitosamente'
        ], 201);
    }

    /**
     * Mostrar consultoría específica
     */
    public function show(ConsultationService $consultationService): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        if ($consultationService->consultant_id !== $user->id && $consultationService->client_id !== $user->id) {
            return response()->json(['message' => 'Sin permisos para ver esta consultoría'], 403);
        }

        $consultationService->load(['consultant:id,name,email', 'client:id,name,email', 'payments']);

        return response()->json([
            'data' => new ConsultationServiceResource($consultationService)
        ]);
    }

    /**
     * Aceptar solicitud de consultoría
     */
    public function accept(Request $request, ConsultationService $consultationService): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user || $consultationService->consultant_id !== $user->id) {
            return response()->json(['message' => 'Sin permisos'], 403);
        }

        if ($consultationService->status !== 'requested') {
            return response()->json(['message' => 'Solo se pueden aceptar solicitudes pendientes'], 400);
        }

        $request->validate([
            'total_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $terms = [];
        if ($request->filled('total_amount')) {
            $terms['total_amount'] = $request->total_amount;
        }
        if ($request->filled('notes')) {
            $terms['notes'] = $request->notes;
        }

        $consultationService->accept($terms);
        $consultationService->load(['consultant:id,name,email', 'client:id,name,email']);

        return response()->json([
            'data' => new ConsultationServiceResource($consultationService),
            'message' => 'Consultoría aceptada exitosamente'
        ]);
    }

    /**
     * Iniciar consultoría
     */
    public function start(ConsultationService $consultationService): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user || $consultationService->consultant_id !== $user->id) {
            return response()->json(['message' => 'Sin permisos'], 403);
        }

        if ($consultationService->status !== 'accepted') {
            return response()->json(['message' => 'Solo se pueden iniciar consultorías aceptadas'], 400);
        }

        $consultationService->start();

        return response()->json([
            'data' => new ConsultationServiceResource($consultationService),
            'message' => 'Consultoría iniciada'
        ]);
    }

    /**
     * Completar consultoría
     */
    public function complete(Request $request, ConsultationService $consultationService): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user || $consultationService->consultant_id !== $user->id) {
            return response()->json(['message' => 'Sin permisos'], 403);
        }

        if ($consultationService->status !== 'in_progress') {
            return response()->json(['message' => 'Solo se pueden completar consultorías en progreso'], 400);
        }

        $request->validate([
            'deliverables' => 'nullable|array',
            'actual_hours' => 'nullable|integer|min:1',
        ]);

        $deliverables = $request->deliverables ?? [];
        
        if ($request->filled('actual_hours')) {
            $consultationService->updateActualHours($request->actual_hours - ($consultationService->actual_hours ?? 0));
        }

        $consultationService->complete($deliverables);

        return response()->json([
            'data' => new ConsultationServiceResource($consultationService),
            'message' => 'Consultoría completada exitosamente'
        ]);
    }

    /**
     * Valorar consultoría
     */
    public function rate(Request $request, ConsultationService $consultationService): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        if ($consultationService->consultant_id !== $user->id && $consultationService->client_id !== $user->id) {
            return response()->json(['message' => 'Sin permisos para valorar esta consultoría'], 403);
        }

        if ($consultationService->status !== 'completed') {
            return response()->json(['message' => 'Solo se pueden valorar consultorías completadas'], 400);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        if ($consultationService->client_id === $user->id) {
            $consultationService->rateByClient($request->rating, $request->review);
            $message = 'Valoración del cliente registrada';
        } else {
            $consultationService->rateByConsultant($request->rating, $request->review);
            $message = 'Valoración del consultor registrada';
        }

        return response()->json([
            'data' => new ConsultationServiceResource($consultationService),
            'message' => $message
        ]);
    }
}