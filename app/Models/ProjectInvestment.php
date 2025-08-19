<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Inversión en un proyecto colaborativo.
 * 
 * Representa la participación de un usuario en la financiación
 * de un proyecto energético con términos específicos.
 */
class ProjectInvestment extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_proposal_id',
        'investor_id',
        'investment_amount',
        'investment_percentage',
        'investment_type',
        'investment_details',
        'investment_description',
        'expected_return_percentage',
        'investment_term_years',
        'return_frequency',
        'return_schedule',
        'reinvest_returns',
        'status',
        'payment_method',
        'payment_reference',
        'payment_date',
        'payment_confirmed_at',
        'payment_confirmed_by',
        'legal_documents',
        'terms_accepted',
        'terms_accepted_at',
        'digital_signature',
        'contract_details',
        'total_returns_received',
        'pending_returns',
        'last_return_date',
        'next_return_date',
        'has_voting_rights',
        'voting_weight',
        'can_participate_decisions',
        'receives_project_updates',
        'notification_preferences',
        'public_investor',
        'investor_alias',
        'current_roi',
        'projected_final_roi',
        'months_invested',
        'performance_metrics',
        'exit_requested',
        'exit_requested_at',
        'exit_value',
        'exit_terms',
    ];

    protected $casts = [
        'investment_amount' => 'decimal:2',
        'investment_percentage' => 'decimal:2',
        'investment_details' => 'array',
        'expected_return_percentage' => 'decimal:2',
        'return_schedule' => 'array',
        'reinvest_returns' => 'boolean',
        'payment_date' => 'datetime',
        'payment_confirmed_at' => 'datetime',
        'legal_documents' => 'array',
        'terms_accepted' => 'boolean',
        'terms_accepted_at' => 'datetime',
        'contract_details' => 'array',
        'total_returns_received' => 'decimal:2',
        'pending_returns' => 'decimal:2',
        'last_return_date' => 'datetime',
        'next_return_date' => 'datetime',
        'has_voting_rights' => 'boolean',
        'voting_weight' => 'decimal:2',
        'can_participate_decisions' => 'boolean',
        'receives_project_updates' => 'boolean',
        'notification_preferences' => 'array',
        'public_investor' => 'boolean',
        'current_roi' => 'decimal:2',
        'projected_final_roi' => 'decimal:2',
        'performance_metrics' => 'array',
        'exit_requested' => 'boolean',
        'exit_requested_at' => 'datetime',
        'exit_value' => 'decimal:2',
    ];

    /**
     * Proyecto en el que se invierte.
     */
    public function projectProposal(): BelongsTo
    {
        return $this->belongsTo(ProjectProposal::class);
    }

    /**
     * Usuario inversor.
     */
    public function investor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'investor_id');
    }

    /**
     * Usuario que confirmó el pago.
     */
    public function paymentConfirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payment_confirmed_by');
    }

    /**
     * Confirmar pago de la inversión.
     */
    public function confirmPayment(User $confirmer, array $paymentData = []): void
    {
        $this->update([
            'status' => 'paid',
            'payment_confirmed_at' => now(),
            'payment_confirmed_by' => $confirmer->id,
            'payment_method' => $paymentData['method'] ?? $this->payment_method,
            'payment_reference' => $paymentData['reference'] ?? $this->payment_reference,
        ]);
    }

    /**
     * Activar inversión (cuando el proyecto inicia).
     */
    public function activate(): void
    {
        $this->update([
            'status' => 'active',
            'months_invested' => 0,
        ]);
    }

    /**
     * Registrar retorno de inversión.
     */
    public function recordReturn(float $amount, string $period = null): void
    {
        $this->increment('total_returns_received', $amount);
        $this->decrement('pending_returns', $amount);
        
        $this->update([
            'last_return_date' => now(),
            'next_return_date' => $this->calculateNextReturnDate(),
        ]);

        $this->updateROI();
    }

    /**
     * Calcular próxima fecha de retorno.
     */
    protected function calculateNextReturnDate(): ?\Carbon\Carbon
    {
        if (!$this->return_frequency) {
            return null;
        }

        $lastDate = $this->last_return_date ?? $this->payment_confirmed_at;
        
        return match($this->return_frequency) {
            'monthly' => $lastDate->addMonth(),
            'quarterly' => $lastDate->addMonths(3),
            'biannual' => $lastDate->addMonths(6),
            'annual' => $lastDate->addYear(),
            default => null,
        };
    }

    /**
     * Actualizar ROI actual.
     */
    public function updateROI(): void
    {
        if ($this->investment_amount <= 0) {
            return;
        }

        $currentROI = (($this->total_returns_received / $this->investment_amount) - 1) * 100;
        $this->update(['current_roi' => round($currentROI, 2)]);
    }

    /**
     * Solicitar salida de la inversión.
     */
    public function requestExit(float $exitValue = null, string $terms = null): void
    {
        $this->update([
            'exit_requested' => true,
            'exit_requested_at' => now(),
            'exit_value' => $exitValue,
            'exit_terms' => $terms,
        ]);
    }

    /**
     * Completar inversión.
     */
    public function complete(): void
    {
        $this->update(['status' => 'completed']);
        $this->updateROI();
    }

    /**
     * Obtener inversiones activas de un usuario.
     */
    public static function getActiveForUser(User $user)
    {
        return static::where('investor_id', $user->id)
                    ->where('status', 'active')
                    ->with(['projectProposal'])
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    /**
     * Obtener inversiones pendientes de retorno.
     */
    public static function getPendingReturns()
    {
        return static::where('status', 'active')
                    ->where('next_return_date', '<=', now())
                    ->where('pending_returns', '>', 0)
                    ->with(['investor', 'projectProposal'])
                    ->get();
    }

    /**
     * Calcular ROI promedio de un proyecto.
     */
    public static function getAverageROIForProject(ProjectProposal $project): float
    {
        return static::where('project_proposal_id', $project->id)
                    ->where('status', 'active')
                    ->avg('current_roi') ?? 0;
    }
}
