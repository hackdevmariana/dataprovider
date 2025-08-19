<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConsultationServiceResource extends JsonResource
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
            'consultant' => [
                'id' => $this->consultant->id,
                'name' => $this->consultant->name,
                'email' => $this->consultant->email,
            ],
            'client' => [
                'id' => $this->client->id,
                'name' => $this->client->name,
                'email' => $this->client->email,
            ],
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'type_label' => $this->getTypeLabel(),
            'format' => $this->format,
            'format_label' => $this->getFormatLabel(),
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'status_color' => $this->getStatusColor(),
            'hourly_rate' => $this->hourly_rate,
            'fixed_price' => $this->fixed_price,
            'total_amount' => $this->total_amount,
            'formatted_price' => $this->getFormattedPrice(),
            'currency' => $this->currency,
            'estimated_hours' => $this->estimated_hours,
            'actual_hours' => $this->actual_hours,
            'platform_commission' => $this->platform_commission,
            'net_amount' => $this->calculateNetAmount(),
            'commission_amount' => $this->calculatePlatformCommission(),
            'requested_at' => $this->requested_at,
            'accepted_at' => $this->accepted_at,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
            'deadline' => $this->deadline,
            'days_until_deadline' => $this->getDaysUntilDeadline(),
            'is_overdue' => $this->isOverdue(),
            'is_completed' => $this->isCompleted(),
            'progress' => $this->getProgress(),
            'requirements' => $this->requirements,
            'deliverables' => $this->deliverables,
            'milestones' => $this->milestones,
            'client_notes' => $this->client_notes,
            'consultant_notes' => $this->consultant_notes,
            'client_rating' => $this->client_rating,
            'consultant_rating' => $this->consultant_rating,
            'average_rating' => $this->getAverageRating(),
            'client_review' => $this->client_review,
            'consultant_review' => $this->consultant_review,
            'is_featured' => $this->is_featured,
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