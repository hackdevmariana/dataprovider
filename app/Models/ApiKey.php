<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token',
        'scope',
        'rate_limit',
        'expires_at',
        'is_revoked',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_revoked' => 'boolean',
    ];

    // Relaciones
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Atributos calculados
    public function getScopeLabelAttribute(): string
    {
        return match ($this->scope) {
            'read-only' => 'Solo Lectura',
            'write' => 'Escritura',
            'full-access' => 'Acceso Completo',
            default => 'Desconocido',
        };
    }

    public function getScopeColorAttribute(): string
    {
        return match ($this->scope) {
            'read-only' => 'info',
            'write' => 'warning',
            'full-access' => 'success',
            default => 'secondary',
        };
    }

    public function getScopeIconAttribute(): string
    {
        return match ($this->scope) {
            'read-only' => 'heroicon-o-eye',
            'write' => 'heroicon-o-pencil',
            'full-access' => 'heroicon-o-key',
            default => 'heroicon-o-question-mark-circle',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->attributes['is_revoked']) {
            return 'Revocada';
        }
        
        if ($this->expires_at && $this->expires_at->isPast()) {
            return 'Expirada';
        }
        
        return 'Activa';
    }

    public function getStatusColorAttribute(): string
    {
        if ($this->attributes['is_revoked']) {
            return 'danger';
        }
        
        if ($this->expires_at && $this->expires_at->isPast()) {
            return 'warning';
        }
        
        return 'success';
    }

    public function getStatusIconAttribute(): string
    {
        if ($this->attributes['is_revoked']) {
            return 'heroicon-o-x-circle';
        }
        
        if ($this->expires_at && $this->expires_at->isPast()) {
            return 'heroicon-o-clock';
        }
        
        return 'heroicon-o-check-circle';
    }

    public function getIsActiveAttribute(): bool
    {
        return !$this->attributes['is_revoked'] && (!$this->expires_at || $this->expires_at->isFuture());
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getIsRevokedAttribute(): bool
    {
        return (bool) $this->attributes['is_revoked'];
    }

    public function getIsReadOnlyAttribute(): bool
    {
        return $this->scope === 'read-only';
    }

    public function getIsWriteAttribute(): bool
    {
        return $this->scope === 'write';
    }

    public function getIsFullAccessAttribute(): bool
    {
        return $this->scope === 'full-access';
    }

    public function getFormattedExpiresAtAttribute(): string
    {
        if (!$this->expires_at) {
            return 'Sin expiración';
        }
        
        return $this->expires_at->format('d/m/Y H:i');
    }

    public function getDaysUntilExpirationAttribute(): ?int
    {
        if (!$this->expires_at) {
            return null;
        }
        
        return $this->expires_at->diffInDays(now());
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        if (!$this->expires_at) {
            return false;
        }
        
        return $this->expires_at->diffInDays(now()) <= 7;
    }

    public function getTokenPreviewAttribute(): string
    {
        if (strlen($this->token) <= 8) {
            return $this->token;
        }
        
        return substr($this->token, 0, 4) . '...' . substr($this->token, -4);
    }

    public function getRateLimitLabelAttribute(): string
    {
        if ($this->rate_limit >= 10000) {
            return 'Muy Alto (10K+)';
        }
        
        if ($this->rate_limit >= 5000) {
            return 'Alto (5K+)';
        }
        
        if ($this->rate_limit >= 1000) {
            return 'Medio (1K+)';
        }
        
        if ($this->rate_limit >= 100) {
            return 'Bajo (100+)';
        }
        
        return 'Muy Bajo (<100)';
    }

    public function getRateLimitColorAttribute(): string
    {
        if ($this->rate_limit >= 10000) {
            return 'success';
        }
        
        if ($this->rate_limit >= 5000) {
            return 'info';
        }
        
        if ($this->rate_limit >= 1000) {
            return 'warning';
        }
        
        if ($this->rate_limit >= 100) {
            return 'danger';
        }
        
        return 'secondary';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_revoked', false)
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    public function scopeRevoked($query)
    {
        return $query->where('is_revoked', true);
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    public function scopeExpiringSoon($query, int $days = 7)
    {
        return $query->where('expires_at', '<=', now()->addDays($days))
                    ->where('expires_at', '>', now());
    }

    public function scopeByScope($query, string $scope)
    {
        return $query->where('scope', $scope);
    }

    public function scopeReadOnly($query)
    {
        return $query->where('scope', 'read-only');
    }

    public function scopeWrite($query)
    {
        return $query->where('scope', 'write');
    }

    public function scopeFullAccess($query)
    {
        return $query->where('scope', 'full-access');
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeHighRateLimit($query, int $minLimit = 5000)
    {
        return $query->where('rate_limit', '>=', $minLimit);
    }

    public function scopeLowRateLimit($query, int $maxLimit = 1000)
    {
        return $query->where('rate_limit', '<=', $maxLimit);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('token', 'like', '%' . $search . '%')
              ->orWhereHas('user', function ($userQuery) use ($search) {
                  $userQuery->where('name', 'like', '%' . $search . '%')
                           ->orWhere('email', 'like', '%' . $search . '%');
              });
        });
    }

    // Métodos
    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isExpired(): bool
    {
        return $this->is_expired;
    }

    public function isRevoked(): bool
    {
        return $this->is_revoked;
    }

    public function isReadOnly(): bool
    {
        return $this->is_read_only;
    }

    public function isWrite(): bool
    {
        return $this->is_write;
    }

    public function isFullAccess(): bool
    {
        return $this->is_full_access;
    }

    public function isExpiringSoon(): bool
    {
        return $this->is_expiring_soon;
    }

    public function revoke(): void
    {
        $this->update(['is_revoked' => true]);
    }

    public function activate(): void
    {
        $this->update(['is_revoked' => false]);
    }

    public function extendExpiration(int $days = 30): void
    {
        if ($this->expires_at) {
            $this->update(['expires_at' => $this->expires_at->addDays($days)]);
        } else {
            $this->update(['expires_at' => now()->addDays($days)]);
        }
    }

    public function setExpiration(int $days = 30): void
    {
        $this->update(['expires_at' => now()->addDays($days)]);
    }

    public function removeExpiration(): void
    {
        $this->update(['expires_at' => null]);
    }

    public function updateRateLimit(int $rateLimit): void
    {
        $this->update(['rate_limit' => $rateLimit]);
    }

    public function updateScope(string $scope): void
    {
        $this->update(['scope' => $scope]);
    }

    // Método estático para generar token
    public static function generateToken(int $length = 64): string
    {
        return Str::random($length);
    }

    // Método estático para crear API key
    public static function createForUser(User $user, string $scope = 'read-only', int $rateLimit = 1000, ?int $expirationDays = null): self
    {
        $expiresAt = $expirationDays ? now()->addDays($expirationDays) : null;
        
        return self::create([
            'user_id' => $user->id,
            'token' => self::generateToken(),
            'scope' => $scope,
            'rate_limit' => $rateLimit,
            'expires_at' => $expiresAt,
            'is_revoked' => false,
        ]);
    }

    // Método para obtener descripción completa
    public function getDescriptionAttribute(): string
    {
        $user = $this->user->name ?? 'Usuario desconocido';
        $scope = $this->scope_label;
        $status = $this->status_label;
        $rateLimit = $this->rate_limit;
        
        return "API Key de {$user} - {$scope} - {$status} - Límite: {$rateLimit}/h";
    }
}