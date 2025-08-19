<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPrivilege extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'privilege_type',
        'scope',
        'scope_id',
        'level',
        'is_active',
        'permissions',
        'limits',
        'reputation_required',
        'granted_at',
        'expires_at',
        'granted_by',
        'reason',
    ];

    protected $casts = [
        'permissions' => 'array',
        'limits' => 'array',
        'is_active' => 'boolean',
        'granted_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected $dates = [
        'granted_at',
        'expires_at',
    ];

    /**
     * Get the user that owns the privilege
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who granted this privilege
     */
    public function grantor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by');
    }

    /**
     * Check if privilege is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if privilege is currently valid
     */
    public function isValid(): bool
    {
        return $this->is_active && !$this->isExpired();
    }

    /**
     * Get privilege type label
     */
    public function getPrivilegeTypeLabel(): string
    {
        return match ($this->privilege_type) {
            'posting' => 'Publicación',
            'voting' => 'Votación',
            'moderation' => 'Moderación',
            'verification' => 'Verificación',
            'administration' => 'Administración',
            'content_creation' => 'Creación de Contenido',
            'expert_answers' => 'Respuestas de Experto',
            'project_approval' => 'Aprobación de Proyectos',
            default => ucwords(str_replace('_', ' ', $this->privilege_type)),
        };
    }

    /**
     * Get scope label
     */
    public function getScopeLabel(): string
    {
        return match ($this->scope) {
            'global' => 'Global',
            'topic' => 'Tema',
            'cooperative' => 'Cooperativa',
            'project' => 'Proyecto',
            'region' => 'Región',
            default => ucfirst($this->scope),
        };
    }

    /**
     * Check if user has specific permission
     */
    public function hasPermission(string $permission): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        $permissions = $this->permissions ?? [];
        return in_array($permission, $permissions) || in_array('*', $permissions);
    }

    /**
     * Check if user is within usage limits
     */
    public function isWithinLimits(string $limitType, int $currentUsage): bool
    {
        if (!$this->limits) {
            return true;
        }

        $limit = $this->limits[$limitType] ?? null;
        return $limit === null || $currentUsage < $limit;
    }

    /**
     * Scope for active privileges
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for valid (active and not expired) privileges
     */
    public function scopeValid($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    /**
     * Scope by privilege type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('privilege_type', $type);
    }

    /**
     * Scope by scope
     */
    public function scopeByScope($query, string $scope, $scopeId = null)
    {
        $query = $query->where('scope', $scope);
        
        if ($scopeId !== null) {
            $query = $query->where('scope_id', $scopeId);
        }
        
        return $query;
    }

    /**
     * Scope by minimum level
     */
    public function scopeMinLevel($query, int $level)
    {
        return $query->where('level', '>=', $level);
    }
}