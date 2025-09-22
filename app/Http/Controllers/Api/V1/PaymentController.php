<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\PaymentResource;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Auth;

/**
 * @tags Payments
 * @group Sistema de Monetización - Pagos
 */
/**
 * @OA\Tag(
 *     name="Pagos",
 *     description="APIs para la gestión de Pagos"
 * )
 */
class PaymentController extends Controller
{
    /**
     * Listar pagos del usuario autenticado
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $query = $user->payments()->with(['payable']);

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('processor')) {
            $query->where('processor', $request->processor);
        }

        // Filtros por fecha
        if ($request->boolean('today')) {
            $query->whereDate('created_at', today());
        } elseif ($request->boolean('this_week')) {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($request->boolean('this_month')) {
            $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'data' => PaymentResource::collection($payments),
            'meta' => [
                'current_page' => $payments->currentPage(),
                'total' => $payments->total(),
                'per_page' => $payments->perPage(),
                'summary' => $this->getPaymentsSummary($user),
            ]
        ]);
    }

    /**
     * Mostrar pago específico
     */
    public function show(Payment $payment): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        // Verificar permisos
        if ($payment->user_id !== $user->id && !$user->hasRole('admin')) {
            return response()->json(['message' => 'Sin permisos para ver este pago'], 403);
        }

        $payment->load(['payable', 'user:id,name,email']);

        return response()->json([
            'data' => new PaymentResource($payment)
        ]);
    }

    /**
     * Obtener estadísticas de pagos del usuario
     */
    public function stats(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $period = $request->get('period', 'all'); // today, week, month, year, all
        $filters = ['user_id' => $user->id];

        if ($period !== 'all') {
            $filters['period'] = $period;
        }

        $stats = Payment::getStats($filters);

        // Estadísticas adicionales específicas del usuario
        $userStats = [
            'total_spent' => $user->completedPayments()->sum('amount'),
            'total_fees_paid' => $user->completedPayments()->sum('fee'),
            'favorite_payment_method' => $user->payments()
                ->selectRaw('payment_method, COUNT(*) as count')
                ->groupBy('payment_method')
                ->orderByDesc('count')
                ->first()?->payment_method ?? 'N/A',
            'by_type' => [
                'subscription' => $user->payments()->where('type', 'subscription')->count(),
                'commission' => $user->payments()->where('type', 'commission')->count(),
                'verification' => $user->payments()->where('type', 'verification')->count(),
                'consultation' => $user->payments()->where('type', 'consultation')->count(),
            ],
            'monthly_spending' => $user->payments()
                ->where('status', 'completed')
                ->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
                ->whereYear('created_at', now()->year)
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray(),
        ];

        return response()->json([
            'data' => array_merge($stats, ['user_specific' => $userStats])
        ]);
    }

    /**
     * Crear intención de pago (para integración con procesadores)
     */
    public function createPaymentIntent(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $request->validate([
            'payable_type' => 'required|string',
            'payable_id' => 'required|integer',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'nullable|string|size:3',
            'payment_method' => 'required|string',
            'processor' => 'required|in:stripe,paypal,bank',
            'description' => 'required|string|max:500',
        ]);

        // Verificar que el elemento a pagar existe y pertenece al usuario
        $payableType = $request->payable_type;
        $payableId = $request->payable_id;

        if (!class_exists($payableType)) {
            return response()->json(['message' => 'Tipo de pago no válido'], 400);
        }

        $payable = $payableType::find($payableId);
        if (!$payable) {
            return response()->json(['message' => 'Elemento a pagar no encontrado'], 404);
        }

        // Verificar permisos según el tipo
        $hasPermission = match ($payableType) {
            'App\Models\UserSubscription' => $payable->user_id === $user->id,
            'App\Models\ProjectCommission' => $payable->user_id === $user->id,
            'App\Models\ProjectVerification' => $payable->requested_by === $user->id,
            'App\Models\ConsultationService' => $payable->client_id === $user->id,
            default => false,
        };

        if (!$hasPermission) {
            return response()->json(['message' => 'Sin permisos para pagar este elemento'], 403);
        }

        // Generar ID único para la intención de pago
        $paymentIntentId = 'pi_' . uniqid() . '_' . time();

        // Calcular fee del procesador (simulado)
        $fee = $this->calculateProcessorFee($request->amount, $request->processor);

        $payment = Payment::create([
            'user_id' => $user->id,
            'payable_type' => $payableType,
            'payable_id' => $payableId,
            'payment_intent_id' => $paymentIntentId,
            'status' => 'pending',
            'type' => $this->getPaymentType($payableType),
            'amount' => $request->amount,
            'fee' => $fee,
            'net_amount' => $request->amount - $fee,
            'currency' => $request->currency ?? 'EUR',
            'payment_method' => $request->payment_method,
            'processor' => $request->processor,
            'description' => $request->description,
        ]);

        return response()->json([
            'data' => new PaymentResource($payment),
            'payment_intent' => [
                'id' => $paymentIntentId,
                'amount' => $request->amount,
                'currency' => $request->currency ?? 'EUR',
                'fee' => $fee,
                'net_amount' => $request->amount - $fee,
            ],
            'message' => 'Intención de pago creada. Procede con el procesador seleccionado.'
        ], 201);
    }

    /**
     * Confirmar pago (webhook simulado)
     */
    public function confirmPayment(Request $request, Payment $payment): JsonResponse
    {
        $request->validate([
            'processor_response' => 'required|array',
            'success' => 'required|boolean',
            'failure_reason' => 'nullable|string',
        ]);

        if ($request->boolean('success')) {
            $payment->markAsCompleted($request->processor_response);
            $message = 'Pago confirmado exitosamente';
        } else {
            $payment->markAsFailed(
                $request->failure_reason ?? 'Error del procesador',
                $request->processor_response
            );
            $message = 'Pago fallido';
        }

        return response()->json([
            'data' => new PaymentResource($payment),
            'message' => $message
        ]);
    }

    /**
     * Solicitar reembolso
     */
    public function requestRefund(Request $request, Payment $payment): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        if ($payment->user_id !== $user->id) {
            return response()->json(['message' => 'Sin permisos para solicitar reembolso de este pago'], 403);
        }

        if ($payment->status !== 'completed') {
            return response()->json(['message' => 'Solo se pueden reembolsar pagos completados'], 400);
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        // En un sistema real, aquí se iniciaría el proceso de reembolso con el procesador
        // Por ahora, solo cambiamos el estado
        $payment->markAsRefunded([
            'refund_reason' => $request->reason,
            'refund_requested_at' => now(),
            'refund_requested_by' => $user->id,
        ]);

        return response()->json([
            'data' => new PaymentResource($payment),
            'message' => 'Solicitud de reembolso procesada'
        ]);
    }

    /**
     * Obtener resumen de pagos del usuario
     */
    private function getPaymentsSummary($user): array
    {
        $payments = $user->payments();

        return [
            'total_payments' => $payments->count(),
            'completed_payments' => $payments->completed()->count(),
            'pending_payments' => $payments->pending()->count(),
            'failed_payments' => $payments->failed()->count(),
            'total_amount_paid' => $payments->completed()->sum('amount'),
            'total_fees_paid' => $payments->completed()->sum('fee'),
            'success_rate' => $payments->count() > 0 
                ? round(($payments->completed()->count() / $payments->count()) * 100, 1)
                : 0,
        ];
    }

    /**
     * Calcular fee del procesador
     */
    private function calculateProcessorFee(float $amount, string $processor): float
    {
        return match ($processor) {
            'stripe' => $amount * 0.029 + 0.30, // 2.9% + 0.30€
            'paypal' => $amount * 0.034 + 0.35, // 3.4% + 0.35€
            'bank' => 1.50, // Tarifa fija para transferencias
            default => 0,
        };
    }

    /**
     * Obtener tipo de pago basado en el modelo
     */
    private function getPaymentType(string $payableType): string
    {
        return match ($payableType) {
            'App\Models\UserSubscription' => 'subscription',
            'App\Models\ProjectCommission' => 'commission',
            'App\Models\ProjectVerification' => 'verification',
            'App\Models\ConsultationService' => 'consultation',
            default => 'other',
        };
    }
}