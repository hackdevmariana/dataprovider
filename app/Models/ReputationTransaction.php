<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReputationTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'transaction_type',
        'source_type',
        'source_id',
        'points_change',
        'points_before',
        'points_after',
        'reason',
        'description',
        'metadata',
        'awarded_by',
        'category',
        'is_visible',
        'is_reversible',
        'reversed_by_transaction_id',
        'processed_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_visible' => 'boolean',
        'is_reversible' => 'boolean',
        'processed_at' => 'datetime',
    ];

    /**
     * Get the user this transaction belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who awarded/penalized
     */
    public function awarder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'awarded_by');
    }

    /**
     * Get the reversing transaction
     */
    public function reversingTransaction(): BelongsTo
    {
        return $this->belongsTo(ReputationTransaction::class, 'reversed_by_transaction_id');
    }

    /**
     * Create a reputation transaction
     */
    public static function createTransaction(
        User $user,
        string $type,
        int $pointsChange,
        string $reason,
        array $data = []
    ): self {
        $currentReputation = $user->userReputation->reputation_score ?? 0;
        
        return self::create([
            'user_id' => $user->id,
            'transaction_type' => $type,
            'source_type' => $data['source_type'] ?? null,
            'source_id' => $data['source_id'] ?? null,
            'points_change' => $pointsChange,
            'points_before' => $currentReputation,
            'points_after' => $currentReputation + $pointsChange,
            'reason' => $reason,
            'description' => $data['description'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'awarded_by' => $data['awarded_by'] ?? null,
            'category' => $data['category'] ?? null,
            'is_visible' => $data['is_visible'] ?? true,
            'is_reversible' => $data['is_reversible'] ?? false,
            'processed_at' => now(),
        ]);
    }

    /**
     * Reverse this transaction
     */
    public function reverse(User $reverser, string $reason): ?self
    {
        if (!$this->is_reversible) {
            return null;
        }

        return self::createTransaction(
            $this->user,
            'reversed',
            -$this->points_change,
            $reason,
            [
                'awarded_by' => $reverser->id,
                'metadata' => [
                    'original_transaction_id' => $this->id,
                    'reversal_reason' => $reason,
                ],
            ]
        );
    }

    /**
     * Scope for visible transactions
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Scope by transaction type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('transaction_type', $type);
    }

    /**
     * Scope by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}