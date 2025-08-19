<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'payable_type' => $this->payable_type,
            'payable_id' => $this->payable_id,
            'payable_info' => $this->getPayableInfo(),
            'payment_intent_id' => $this->payment_intent_id,
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'status_color' => $this->getStatusColor(),
            'type' => $this->type,
            'type_label' => $this->getTypeLabel(),
            'amount' => $this->amount,
            'formatted_amount' => $this->getFormattedAmount(),
            'fee' => $this->fee,
            'formatted_fee' => $this->getFormattedFee(),
            'net_amount' => $this->net_amount,
            'formatted_net_amount' => $this->getFormattedNetAmount(),
            'currency' => $this->currency,
            'payment_method' => $this->payment_method,
            'processor' => $this->processor,
            'processor_response' => $this->processor_response,
            'metadata' => $this->metadata,
            'description' => $this->description,
            'failure_reason' => $this->failure_reason,
            'processed_at' => $this->processed_at,
            'failed_at' => $this->failed_at,
            'refunded_at' => $this->refunded_at,
            'is_completed' => $this->isCompleted(),
            'is_failed' => $this->isFailed(),
            'is_refunded' => $this->isRefunded(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Obtener información del elemento pagado
     */
    private function getPayableInfo(): array
    {
        if (!$this->payable) {
            return [];
        }

        return match ($this->payable_type) {
            'App\Models\UserSubscription' => [
                'type' => 'subscription',
                'plan_name' => $this->payable->subscriptionPlan?->name,
                'billing_cycle' => $this->payable->billing_cycle,
            ],
            'App\Models\ProjectCommission' => [
                'type' => 'commission',
                'project_title' => $this->payable->projectProposal?->title,
                'commission_type' => $this->payable->type,
            ],
            'App\Models\ProjectVerification' => [
                'type' => 'verification',
                'project_title' => $this->payable->projectProposal?->title,
                'verification_type' => $this->payable->type,
            ],
            'App\Models\ConsultationService' => [
                'type' => 'consultation',
                'consultation_title' => $this->payable->title,
                'consultant_name' => $this->payable->consultant?->name,
            ],
            default => [],
        };
    }
}