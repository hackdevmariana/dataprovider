<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subscription_plan' => [
                'id' => $this->subscriptionPlan->id,
                'name' => $this->subscriptionPlan->name,
                'slug' => $this->subscriptionPlan->slug,
                'type' => $this->subscriptionPlan->type,
                'price' => $this->subscriptionPlan->price,
                'billing_cycle' => $this->subscriptionPlan->billing_cycle,
                'features' => $this->subscriptionPlan->features,
                'limits' => $this->subscriptionPlan->limits,
                'commission_rate' => $this->subscriptionPlan->commission_rate,
            ],
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'status_color' => $this->getStatusColor(),
            'amount_paid' => $this->amount_paid,
            'currency' => $this->currency,
            'billing_cycle' => $this->billing_cycle,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'trial_ends_at' => $this->trial_ends_at,
            'cancelled_at' => $this->cancelled_at,
            'next_billing_at' => $this->next_billing_at,
            'next_billing_formatted' => $this->getNextBillingFormatted(),
            'payment_method' => $this->payment_method,
            'external_subscription_id' => $this->external_subscription_id,
            'auto_renew' => $this->auto_renew,
            'days_remaining' => $this->daysRemaining(),
            'trial_days_remaining' => $this->trialDaysRemaining(),
            'usage_stats' => $this->usage_stats,
            'cancellation_reason' => $this->cancellation_reason,
            'is_active' => $this->isActive(),
            'is_on_trial' => $this->isOnTrial(),
            'is_expired' => $this->isExpired(),
            'is_cancelled' => $this->isCancelled(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Información adicional cuando se incluyen pagos
            'payments' => $this->whenLoaded('payments', function () {
                return $this->payments->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'amount' => $payment->amount,
                        'status' => $payment->status,
                        'processed_at' => $payment->processed_at,
                        'payment_method' => $payment->payment_method,
                    ];
                });
            }),
        ];
    }
}