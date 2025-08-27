<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CompanyCertification extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'certification_name',
        'certification_type',
        'issuing_organization',
        'certification_number',
        'issue_date',
        'expiry_date',
        'status',
        'scope',
        'standards_met',
        'audit_frequency',
        'last_audit_date',
        'next_audit_date',
        'certification_level',
        'is_renewable',
        'renewal_requirements',
        'notes',
        'document_urls',
        'contact_person',
        'contact_email',
        'contact_phone',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'last_audit_date' => 'date',
        'next_audit_date' => 'date',
        'scope' => 'array',
        'standards_met' => 'array',
        'renewal_requirements' => 'array',
        'document_urls' => 'array',
        'is_renewable' => 'boolean',
    ];

    // Atributos calculados
    public function getCertificationTypeLabelAttribute(): string
    {
        return match ($this->certification_type) {
            'quality' => 'Calidad',
            'environmental' => 'Ambiental',
            'safety' => 'Seguridad',
            'energy' => 'Energía',
            'information_security' => 'Seguridad de la Información',
            'food_safety' => 'Seguridad Alimentaria',
            'occupational_health' => 'Salud Ocupacional',
            'social_responsibility' => 'Responsabilidad Social',
            'sustainability' => 'Sostenibilidad',
            'compliance' => 'Cumplimiento',
            'technical' => 'Técnica',
            'management' => 'Gestión',
            default => 'Otro',
        };
    }

    public function getCertificationTypeColorAttribute(): string
    {
        return match ($this->certification_type) {
            'quality' => 'success',
            'environmental' => 'info',
            'safety' => 'warning',
            'energy' => 'warning',
            'information_security' => 'danger',
            'food_safety' => 'success',
            'occupational_health' => 'primary',
            'social_responsibility' => 'secondary',
            'sustainability' => 'success',
            'compliance' => 'dark',
            'technical' => 'info',
            'management' => 'primary',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Activa',
            'expired' => 'Expirada',
            'suspended' => 'Suspendida',
            'revoked' => 'Revocada',
            'pending' => 'Pendiente',
            'under_review' => 'En Revisión',
            'conditional' => 'Condicional',
            default => 'Desconocido',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active' => 'success',
            'expired' => 'danger',
            'suspended' => 'warning',
            'revoked' => 'dark',
            'pending' => 'info',
            'under_review' => 'secondary',
            'conditional' => 'warning',
            default => 'gray',
        };
    }

    public function getCertificationLevelLabelAttribute(): string
    {
        return match ($this->certification_level) {
            'basic' => 'Básico',
            'intermediate' => 'Intermedio',
            'advanced' => 'Avanzado',
            'expert' => 'Experto',
            'master' => 'Maestro',
            'gold' => 'Oro',
            'silver' => 'Plata',
            'bronze' => 'Bronce',
            'platinum' => 'Platino',
            default => 'Sin especificar',
        };
    }

    public function getCertificationLevelColorAttribute(): string
    {
        return match ($this->certification_level) {
            'basic' => 'secondary',
            'intermediate' => 'info',
            'advanced' => 'warning',
            'expert' => 'primary',
            'master' => 'success',
            'gold' => 'warning',
            'silver' => 'light',
            'bronze' => 'secondary',
            'platinum' => 'info',
            default => 'gray',
        };
    }

    public function getAuditFrequencyLabelAttribute(): string
    {
        return match ($this->audit_frequency) {
            'annual' => 'Anual',
            'biannual' => 'Semestral',
            'quarterly' => 'Trimestral',
            'monthly' => 'Mensual',
            'continuous' => 'Continuo',
            'on_demand' => 'Bajo Demanda',
            'random' => 'Aleatorio',
            default => 'Sin especificar',
        };
    }

    public function getFormattedIssueDateAttribute(): string
    {
        return $this->issue_date ? $this->issue_date->format('d/m/Y') : 'Sin fecha';
    }

    public function getFormattedExpiryDateAttribute(): string
    {
        return $this->expiry_date ? $this->expiry_date->format('d/m/Y') : 'Sin fecha';
    }

    public function getFormattedLastAuditDateAttribute(): string
    {
        return $this->last_audit_date ? $this->last_audit_date->format('d/m/Y') : 'Sin auditoría';
    }

    public function getFormattedNextAuditDateAttribute(): string
    {
        return $this->next_audit_date ? $this->next_audit_date->format('d/m/Y') : 'Sin programar';
    }

    public function getDaysUntilExpiryAttribute(): int
    {
        if (!$this->expiry_date) {
            return 0;
        }
        return Carbon::now()->diffInDays($this->expiry_date, false);
    }

    public function getDaysUntilNextAuditAttribute(): int
    {
        if (!$this->next_audit_date) {
            return 0;
        }
        return Carbon::now()->diffInDays($this->next_audit_date, false);
    }

    public function getIsExpiredAttribute(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }
        return Carbon::now()->gt($this->expiry_date);
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }
        return $this->days_until_expiry <= 90;
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getIsUnderReviewAttribute(): bool
    {
        return $this->status === 'under_review';
    }

    public function getIsSuspendedAttribute(): bool
    {
        return $this->status === 'suspended';
    }

    public function getIsRevokedAttribute(): bool
    {
        return $this->status === 'revoked';
    }

    public function getExpiryStatusLabelAttribute(): string
    {
        if ($this->is_expired) {
            return 'Expirada';
        } elseif ($this->is_expiring_soon) {
            return 'Expira Pronto';
        } else {
            return 'Vigente';
        }
    }

    public function getExpiryStatusColorAttribute(): string
    {
        if ($this->is_expired) {
            return 'danger';
        } elseif ($this->is_expiring_soon) {
            return 'warning';
        } else {
            return 'success';
        }
    }

    public function getScopeCountAttribute(): int
    {
        if (is_array($this->scope)) {
            return count($this->scope);
        }
        return 0;
    }

    public function getStandardsCountAttribute(): int
    {
        if (is_array($this->standards_met)) {
            return count($this->standards_met);
        }
        return 0;
    }

    public function getDocumentsCountAttribute(): int
    {
        if (is_array($this->document_urls)) {
            return count($this->document_urls);
        }
        return 0;
    }

    public function getRenewalRequirementsCountAttribute(): int
    {
        if (is_array($this->renewal_requirements)) {
            return count($this->renewal_requirements);
        }
        return 0;
    }

    public function getValidityPeriodAttribute(): int
    {
        if (!$this->issue_date || !$this->expiry_date) {
            return 0;
        }
        return $this->issue_date->diffInDays($this->expiry_date);
    }

    public function getValidityPeriodYearsAttribute(): float
    {
        if (!$this->issue_date || !$this->expiry_date) {
            return 0;
        }
        return $this->issue_date->floatDiffInYears($this->expiry_date);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('certification_type', $type);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByLevel($query, string $level)
    {
        return $query->where('certification_level', $level);
    }

    public function scopeByCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByOrganization($query, string $organization)
    {
        return $query->where('issuing_organization', 'like', '%' . $organization . '%');
    }

    public function scopeExpiringSoon($query, int $days = 90)
    {
        return $query->where('expiry_date', '<=', Carbon::now()->addDays($days))
                    ->where('status', 'active');
    }

    public function scopeExpiredByDate($query)
    {
        return $query->where('expiry_date', '<', Carbon::now())
                    ->where('status', 'active');
    }

    public function scopeByIssueDate($query, $date)
    {
        return $query->whereDate('issue_date', $date);
    }

    public function scopeByExpiryDate($query, $date)
    {
        return $query->whereDate('expiry_date', $date);
    }

    public function scopeRenewable($query)
    {
        return $query->where('is_renewable', true);
    }

    public function scopeByAuditFrequency($query, string $frequency)
    {
        return $query->where('audit_frequency', $frequency);
    }

    public function scopeNeedsAudit($query)
    {
        return $query->where('next_audit_date', '<=', Carbon::now())
                    ->where('status', 'active');
    }

    public function scopeOrderByExpiryDate($query, string $direction = 'asc')
    {
        return $query->orderBy('expiry_date', $direction);
    }

    public function scopeOrderByIssueDate($query, string $direction = 'desc')
    {
        return $query->orderBy('issue_date', $direction);
    }

    public function scopeOrderByLevel($query, string $direction = 'desc')
    {
        return $query->orderBy('certification_level', $direction);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('certification_name', 'like', '%' . $search . '%')
              ->orWhere('issuing_organization', 'like', '%' . $search . '%')
              ->orWhere('certification_number', 'like', '%' . $search . '%');
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

    public function isExpiringSoon(): bool
    {
        return $this->is_expiring_soon;
    }

    public function isPending(): bool
    {
        return $this->is_pending;
    }

    public function isUnderReview(): bool
    {
        return $this->is_under_review;
    }

    public function isSuspended(): bool
    {
        return $this->is_suspended;
    }

    public function isRevoked(): bool
    {
        return $this->is_revoked;
    }

    public function isRenewable(): bool
    {
        return $this->is_renewable;
    }

    public function hasScope(): bool
    {
        return $this->scope_count > 0;
    }

    public function hasStandards(): bool
    {
        return $this->standards_count > 0;
    }

    public function hasDocuments(): bool
    {
        return $this->documents_count > 0;
    }

    public function hasRenewalRequirements(): bool
    {
        return $this->renewal_requirements_count > 0;
    }

    public function hasContactInfo(): bool
    {
        return !empty($this->contact_person) || !empty($this->contact_email) || !empty($this->contact_phone);
    }

    public function needsRenewal(): bool
    {
        if (!$this->is_renewable || !$this->expiry_date) {
            return false;
        }
        return $this->days_until_expiry <= 180; // 6 meses antes
    }

    public function needsAudit(): bool
    {
        if (!$this->next_audit_date) {
            return false;
        }
        return $this->days_until_next_audit <= 0;
    }

    public function getScopeList(): array
    {
        if (is_array($this->scope)) {
            return $this->scope;
        }
        return [];
    }

    public function getStandardsList(): array
    {
        if (is_array($this->standards_met)) {
            return $this->standards_met;
        }
        return [];
    }

    public function getDocumentsList(): array
    {
        if (is_array($this->document_urls)) {
            return $this->document_urls;
        }
        return [];
    }

    public function getRenewalRequirementsList(): array
    {
        if (is_array($this->renewal_requirements)) {
            return $this->renewal_requirements;
        }
        return [];
    }

    public function getValidityPeriodLabelAttribute(): string
    {
        $years = $this->validity_period_years;
        if ($years === 0) {
            return 'Sin período válido';
        } elseif ($years < 1) {
            $months = round($years * 12);
            return $months . ' mes' . ($months > 1 ? 'es' : '');
        } elseif ($years < 2) {
            return '1 año';
        } else {
            return round($years) . ' años';
        }
    }

    public function getCertificationSummaryAttribute(): string
    {
        $type = $this->certification_type_label;
        $level = $this->certification_level_label;
        $status = $this->status_label;
        
        return "Certificación de {$type} - Nivel {$level} - {$status}";
    }
}
