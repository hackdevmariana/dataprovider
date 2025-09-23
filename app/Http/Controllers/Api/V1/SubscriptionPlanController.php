<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
/**
 * @tags Subscription Plans
 * @group Sistema de Monetización
 * 
 * API endpoints para gestionar planes de suscripción de KiroLux.
 */
class SubscriptionPlanController extends Controller
{
    /**
     * Listar planes de suscripción disponibles
     */
    public function index(Request $request): JsonResponse
    {
        $query = SubscriptionPlan::query();

        // Filtros
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('billing_cycle')) {
            $query->byBillingCycle($request->billing_cycle);
        }

        if ($request->boolean('featured')) {
            $query->featured();
        }

        if ($request->boolean('active', true)) {
            $query->active();
        }

        // Ordenamiento
        $plans = $query->orderBy('sort_order')
                      ->orderBy('price')
                      ->get();

        return response()->json([
            'data' => $plans->map(function ($plan) {
                return $this->transformPlan($plan);
            }),
            'meta' => [
                'count' => $plans->count(),
                'featured_count' => $plans->where('is_featured', true)->count(),
                'free_plans' => $plans->where('price', 0)->count(),
                'paid_plans' => $plans->where('price', '>', 0)->count(),
            ]
        ]);
    }

    /**
     * Mostrar plan específico
     */
    public function show(string $plan): JsonResponse
    {
        $subscriptionPlan = SubscriptionPlan::where('slug', $plan)
                                          ->orWhere('id', $plan)
                                          ->active()
                                          ->firstOrFail();

        return response()->json([
            'data' => $this->transformPlan($subscriptionPlan, true)
        ]);
    }

    /**
     * Transformar plan para respuesta API
     */
    private function transformPlan(SubscriptionPlan $plan, bool $detailed = false): array
    {
        $data = [
            'id' => $plan->id,
            'name' => $plan->name,
            'slug' => $plan->slug,
            'description' => $plan->description,
            'type' => $plan->type,
            'type_label' => $plan->getTypeLabel(),
            'billing_cycle' => $plan->billing_cycle,
            'price' => $plan->price,
            'formatted_price' => $plan->getFormattedPrice(),
            'setup_fee' => $plan->setup_fee,
            'trial_days' => $plan->trial_days,
            'features' => $plan->features,
            'limits' => $plan->limits,
            'commission_rate' => $plan->commission_rate,
            'priority_support' => $plan->priority_support,
            'verified_badge' => $plan->verified_badge,
            'analytics_access' => $plan->analytics_access,
            'is_featured' => $plan->is_featured,
            'color' => $plan->getColor(),
        ];

        if ($detailed) {
            $data = array_merge($data, [
                'yearly_price' => $plan->getYearlyPrice(),
                'monthly_price' => $plan->getMonthlyPrice(),
                'has_trial' => $plan->hasTrial(),
                'api_access' => $plan->api_access,
                'white_label' => $plan->white_label,
                'stats' => $plan->getStats(),
                'created_at' => $plan->created_at,
                'updated_at' => $plan->updated_at,
            ]);
        }

        return $data;
    }

    /**
     * Comparar múltiples planes
     */
    public function compare(Request $request): JsonResponse
    {
        $planSlugs = explode(',', $request->get('plans', ''));
        
        if (empty($planSlugs)) {
            return response()->json([
                'message' => 'Debe proporcionar al menos un plan para comparar'
            ], 400);
        }

        $plans = SubscriptionPlan::whereIn('slug', $planSlugs)
                                ->active()
                                ->orderBy('price')
                                ->get();

        if ($plans->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron planes válidos'
            ], 404);
        }

        // Obtener todas las características únicas
        $allFeatures = $plans->flatMap(function ($plan) {
            return $plan->features ?? [];
        })->unique()->sort()->values();

        // Comparación detallada
        $comparison = $plans->map(function ($plan) use ($allFeatures) {
            $planFeatures = $plan->features ?? [];
            
            return [
                'plan' => [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'slug' => $plan->slug,
                    'price' => $plan->price,
                    'formatted_price' => $plan->getFormattedPrice(),
                    'billing_cycle' => $plan->billing_cycle,
                    'type' => $plan->type,
                    'commission_rate' => $plan->commission_rate,
                ],
                'features' => $allFeatures->mapWithKeys(function ($feature) use ($planFeatures) {
                    return [$feature => in_array($feature, $planFeatures)];
                }),
                'limits' => $plan->limits,
            ];
        });

        return response()->json([
            'data' => [
                'comparison' => $comparison,
                'price_range' => [
                    'min' => $plans->min('price'),
                    'max' => $plans->max('price'),
                ],
            ]
        ]);
    }
}