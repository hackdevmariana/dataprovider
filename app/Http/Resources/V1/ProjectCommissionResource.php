<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectCommissionResource extends JsonResource
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
            'project_proposal' => [
                'id' => $this->projectProposal->id,
                'title' => $this->projectProposal->title,
                'slug' => $this->projectProposal->slug,
            ],
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'type' => $this->type,
            'type_label' => $this->getTypeLabel(),
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'status_color' => $this->getStatusColor(),
            'amount' => $this->amount,
            'formatted_amount' => $this->getFormattedAmount(),
            'rate' => $this->rate,
            'formatted_rate' => $this->getFormattedRate(),
            'base_amount' => $this->base_amount,
            'currency' => $this->currency,
            'due_date' => $this->due_date,
            'paid_at' => $this->paid_at,
            'payment_method' => $this->payment_method,
            'transaction_id' => $this->transaction_id,
            'description' => $this->description,
            'calculation_details' => $this->calculation_details,
            'notes' => $this->notes,
            'days_until_due' => $this->getDaysUntilDue(),
            'is_overdue' => $this->isOverdue(),
            'is_paid' => $this->isPaid(),
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