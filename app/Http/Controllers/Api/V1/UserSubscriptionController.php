<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserSubscriptionResource;
use App\Models\UserSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * @tags User Subscriptions
 * @group Sistema de Monetización - Suscripciones
 */
/**
 * @OA\Tag(
 *     name="Suscripciones de Usuario",
 *     description="APIs para la gestión de Suscripciones de Usuario"
 * )
 */
class UserSubscriptionController extends Controller
{
    /**
     * Listar suscripciones del usuario autenticado
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $query = $user->subscriptions()->with(['subscriptionPlan']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if (!$request->boolean('include_expired', true)) {
            $query->whereNotIn('status', ['expired']);
        }

        $subscriptions = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => UserSubscriptionResource::collection($subscriptions),
            'meta' => [
                'total' => $subscriptions->count(),
                'active_subscriptions' => $subscriptions->where('status', 'active')->count(),
                'total_spent' => $subscriptions->sum('amount_paid'),
                'current_plan' => $user->activeSubscription?->subscriptionPlan?->name ?? 'Sin suscripción activa',
            ]
        ]);
    }

    /**
     * Crear nueva suscripción
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        if ($user->hasActiveSubscription()) {
            $currentSubscription = $user->activeSubscription;
            return response()->json([
                'message' => 'Ya tienes una suscripción activa',
                'current_subscription' => [
                    'id' => $currentSubscription->id,
                    'plan' => $currentSubscription->subscriptionPlan->name,
                    'status' => $currentSubscription->status,
                ]
            ], 409);
        }

        $request->validate([
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'billing_cycle' => ['nullable', Rule::in(['monthly', 'yearly'])],
            'payment_method' => 'nullable|string|max:255',
        ]);

        $plan = SubscriptionPlan::findOrFail($request->subscription_plan_id);
        $billingCycle = $request->billing_cycle ?? $plan->billing_cycle;
        $startsAt = now();
        $trialEndsAt = $plan->hasTrial() ? $startsAt->copy()->addDays($plan->trial_days) : null;
        $status = $plan->hasTrial() ? 'trial' : 'active';
        
        $endsAt = match ($billingCycle) {
            'monthly' => $startsAt->copy()->addMonth(),
            'yearly' => $startsAt->copy()->addYear(),
            'one_time' => null,
            default => $startsAt->copy()->addMonth(),
        };

        $subscription = UserSubscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'status' => $status,
            'amount_paid' => $plan->price,
            'currency' => 'EUR',
            'billing_cycle' => $billingCycle,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'trial_ends_at' => $trialEndsAt,
            'next_billing_at' => $billingCycle !== 'one_time' ? $endsAt : null,
            'payment_method' => $request->payment_method,
            'auto_renew' => $billingCycle !== 'one_time',
        ]);

        $subscription->load(['subscriptionPlan']);

        $message = $plan->hasTrial() 
            ? "Suscripción creada con período de prueba de {$plan->trial_days} días"
            : 'Suscripción creada exitosamente';

        return response()->json([
            'data' => new UserSubscriptionResource($subscription),
            'message' => $message
        ], 201);
    }

    /**
     * Mostrar suscripción específica
     */
    public function show(UserSubscription $userSubscription): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        if ($userSubscription->user_id !== $user->id) {
            return response()->json(['message' => 'Sin permisos para ver esta suscripción'], 403);
        }

        $userSubscription->load(['subscriptionPlan', 'payments']);

        return response()->json([
            'data' => new UserSubscriptionResource($userSubscription)
        ]);
    }

    /**
     * Cancelar suscripción
     */
    public function destroy(Request $request, UserSubscription $userSubscription): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user || $userSubscription->user_id !== $user->id) {
            return response()->json(['message' => 'Sin permisos'], 403);
        }

        if ($userSubscription->status !== 'active') {
            return response()->json(['message' => 'Solo se pueden cancelar suscripciones activas'], 400);
        }

        $request->validate([
            'reason' => 'nullable|string|max:500',
            'cancel_immediately' => 'boolean',
        ]);

        $cancelImmediately = $request->boolean('cancel_immediately', false);
        
        if ($cancelImmediately) {
            $userSubscription->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'ends_at' => now(),
                'cancellation_reason' => $request->reason,
                'auto_renew' => false,
            ]);
            $message = 'Suscripción cancelada inmediatamente';
        } else {
            $userSubscription->cancel($request->reason);
            $endDate = $userSubscription->ends_at ? $userSubscription->ends_at->format('d/m/Y') : 'el final del período';
            $message = "Suscripción cancelada. Seguirás teniendo acceso hasta {$endDate}";
        }

        return response()->json([
            'data' => new UserSubscriptionResource($userSubscription),
            'message' => $message
        ]);
    }

    /**
     * Reactivar suscripción cancelada
     */
    public function reactivate(UserSubscription $userSubscription): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user || $userSubscription->user_id !== $user->id) {
            return response()->json(['message' => 'Sin permisos'], 403);
        }

        if (!$userSubscription->isCancelled()) {
            return response()->json(['message' => 'Solo se pueden reactivar suscripciones canceladas'], 400);
        }

        $success = $userSubscription->reactivate();

        if (!$success) {
            return response()->json(['message' => 'No se pudo reactivar la suscripción'], 400);
        }

        return response()->json([
            'data' => new UserSubscriptionResource($userSubscription),
            'message' => 'Suscripción reactivada exitosamente'
        ]);
    }
}