<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpertVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'expertise_area',
        'verification_level',
        'status',
        'credentials',
        'verification_documents',
        'expertise_description',
        'years_experience',
        'certifications',
        'education',
        'work_history',
        'verification_fee',
        'verified_by',
        'submitted_at',
        'reviewed_at',
        'verified_at',
        'expires_at',
        'verification_notes',
        'rejection_reason',
        'verification_score',
        'is_public',
    ];

    protected $casts = [
        'credentials' => 'array',
        'verification_documents' => 'array',
        'certifications' => 'array',
        'education' => 'array',
        'work_history' => 'array',
        'is_public' => 'boolean',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'verified_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user being verified
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the verifier
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Check if verification is approved and valid
     */
    public function isValid(): bool
    {
        return $this->status === 'approved' && 
               (!$this->expires_at || $this->expires_at->isFuture());
    }

    /**
     * Approve verification
     */
    public function approve(User $verifier, array $data = []): void
    {
        $this->update([
            'status' => 'approved',
            'verified_by' => $verifier->id,
            'verified_at' => now(),
            'reviewed_at' => now(),
            'verification_notes' => $data['notes'] ?? null,
            'verification_score' => $data['score'] ?? null,
            'expires_at' => now()->addYear(), // Valid for 1 year
        ]);
    }

    /**
     * Reject verification
     */
    public function reject(User $verifier, string $reason, string $notes = null): void
    {
        $this->update([
            'status' => 'rejected',
            'verified_by' => $verifier->id,
            'reviewed_at' => now(),
            'rejection_reason' => $reason,
            'verification_notes' => $notes,
        ]);
    }

    /**
     * Scope for approved verifications
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for valid verifications
     */
    public function scopeValid($query)
    {
        return $query->where('status', 'approved')
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }
}