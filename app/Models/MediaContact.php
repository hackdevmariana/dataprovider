<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Contactos de medios de comunicación para gestión de relaciones públicas.
 * 
 * Sistema especializado para gestionar contactos de prensa, editores,
 * corresponsales y otros profesionales de medios de comunicación
 * con funcionalidades avanzadas para campañas de comunicación.
 * 
 * @property int $id
 * @property int $media_outlet_id ID del medio de comunicación
 * @property string $type Tipo de contacto
 * @property string $contact_name Nombre del contacto
 * @property string|null $job_title Cargo/posición
 * @property string|null $department Departamento
 * @property string|null $phone Teléfono principal
 * @property string|null $mobile_phone Teléfono móvil
 * @property string|null $email Email principal
 * @property string|null $secondary_email Email secundario
 * @property array|null $specializations Especializaciones temáticas
 * @property array|null $coverage_areas Áreas de cobertura
 * @property string|null $preferred_contact_method Método contacto preferido
 * @property array|null $availability_schedule Horario disponibilidad
 * @property string|null $language_preference Idioma preferido
 * @property bool $accepts_press_releases Si acepta comunicados
 * @property bool $accepts_interviews Si acepta entrevistas
 * @property bool $accepts_events_invitations Si acepta invitaciones eventos
 * @property bool $is_freelancer Si es freelance
 * @property bool $is_active Contacto activo
 * @property bool $is_verified Contacto verificado
 * @property int $priority_level Nivel de prioridad (1-5)
 * @property float|null $response_rate Tasa de respuesta
 * @property int $contacts_count Número de contactos realizados
 * @property int $successful_contacts Contactos exitosos
 * @property array|null $social_media_profiles Perfiles redes sociales
 * @property string|null $bio Biografía breve
 * @property array|null $recent_articles Artículos recientes
 * @property string|null $notes Notas internas
 * @property array|null $interaction_history Historial interacciones
 * @property \Carbon\Carbon|null $last_contacted_at Último contacto
 * @property \Carbon\Carbon|null $last_response_at Última respuesta
 * @property \Carbon\Carbon|null $verified_at Fecha verificación
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * 
 * @property-read \App\Models\MediaOutlet $mediaOutlet
 */
class MediaContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'media_outlet_id',
        'type',
        'contact_name',
        'job_title',
        'department',
        'phone',
        'mobile_phone',
        'email',
        'secondary_email',
        'specializations',
        'coverage_areas',
        'preferred_contact_method',
        'availability_schedule',
        'language_preference',
        'accepts_press_releases',
        'accepts_interviews',
        'accepts_events_invitations',
        'is_freelancer',
        'is_active',
        'is_verified',
        'priority_level',
        'response_rate',
        'contacts_count',
        'successful_contacts',
        'social_media_profiles',
        'bio',
        'recent_articles',
        'notes',
        'interaction_history',
        'last_contacted_at',
        'last_response_at',
        'verified_at',
    ];

    protected $casts = [
        'specializations' => 'array',
        'coverage_areas' => 'array',
        'availability_schedule' => 'array',
        'accepts_press_releases' => 'boolean',
        'accepts_interviews' => 'boolean',
        'accepts_events_invitations' => 'boolean',
        'is_freelancer' => 'boolean',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'priority_level' => 'integer',
        'response_rate' => 'float',
        'contacts_count' => 'integer',
        'successful_contacts' => 'integer',
        'social_media_profiles' => 'array',
        'recent_articles' => 'array',
        'interaction_history' => 'array',
        'last_contacted_at' => 'datetime',
        'last_response_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Medio de comunicación al que pertenece.
     */
    public function mediaOutlet(): BelongsTo
    {
        return $this->belongsTo(MediaOutlet::class);
    }

    /**
     * Scope para contactos activos.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para contactos verificados.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope para contactos por tipo.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope para editores y redactores.
     */
    public function scopeEditorial($query)
    {
        return $query->whereIn('type', ['editor', 'redactor', 'jefe_redaccion', 'director']);
    }

    /**
     * Scope para contactos de prensa.
     */
    public function scopePressContacts($query)
    {
        return $query->whereIn('type', ['prensa', 'comunicacion', 'relaciones_publicas']);
    }

    /**
     * Scope para corresponsales.
     */
    public function scopeCorrespondents($query)
    {
        return $query->where('type', 'corresponsal');
    }

    /**
     * Scope para contactos especializados en sostenibilidad.
     */
    public function scopeSustainabilityFocused($query)
    {
        return $query->where(function($q) {
            $q->whereJsonContains('specializations', 'sostenibilidad')
              ->orWhereJsonContains('specializations', 'medio_ambiente')
              ->orWhereJsonContains('specializations', 'energia')
              ->orWhereJsonContains('specializations', 'cambio_climatico');
        });
    }

    /**
     * Scope para contactos por nivel de prioridad.
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority_level', $priority);
    }

    /**
     * Scope para contactos de alta prioridad.
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority_level', '>=', 4);
    }

    /**
     * Scope para contactos con alta tasa de respuesta.
     */
    public function scopeResponsive($query, $minRate = 0.7)
    {
        return $query->where('response_rate', '>=', $minRate);
    }

    /**
     * Scope para freelancers.
     */
    public function scopeFreelancers($query)
    {
        return $query->where('is_freelancer', true);
    }

    /**
     * Registrar nueva interacción.
     */
    public function recordInteraction($type, $description, $successful = false)
    {
        $interaction = [
            'type' => $type,
            'description' => $description,
            'successful' => $successful,
            'timestamp' => now()->toISOString(),
        ];

        $history = $this->interaction_history ?? [];
        array_unshift($history, $interaction);

        // Mantener solo las últimas 50 interacciones
        $history = array_slice($history, 0, 50);

        $this->update([
            'interaction_history' => $history,
            'last_contacted_at' => now(),
            'contacts_count' => $this->contacts_count + 1,
            'successful_contacts' => $successful ? $this->successful_contacts + 1 : $this->successful_contacts,
        ]);

        $this->calculateResponseRate();
    }

    /**
     * Registrar respuesta del contacto.
     */
    public function recordResponse()
    {
        $this->update([
            'last_response_at' => now(),
        ]);

        $this->calculateResponseRate();
    }

    /**
     * Calcular tasa de respuesta.
     */
    public function calculateResponseRate()
    {
        if ($this->contacts_count == 0) {
            $this->response_rate = 0;
            return 0;
        }

        $this->response_rate = round($this->successful_contacts / $this->contacts_count, 2);
        $this->save();

        return $this->response_rate;
    }

    /**
     * Verificar disponibilidad actual.
     */
    public function isAvailableNow()
    {
        if (!$this->availability_schedule) {
            return true; // Si no hay horario definido, asumimos disponible
        }

        $now = now();
        $currentDay = strtolower($now->format('l')); // monday, tuesday, etc.
        $currentTime = $now->format('H:i');

        $schedule = $this->availability_schedule;

        if (!isset($schedule[$currentDay])) {
            return false;
        }

        $daySchedule = $schedule[$currentDay];
        
        if ($daySchedule === 'off' || !$daySchedule) {
            return false;
        }

        if (isset($daySchedule['start']) && isset($daySchedule['end'])) {
            return $currentTime >= $daySchedule['start'] && $currentTime <= $daySchedule['end'];
        }

        return true;
    }

    /**
     * Obtener método de contacto recomendado.
     */
    public function getRecommendedContactMethod()
    {
        if ($this->preferred_contact_method) {
            return $this->preferred_contact_method;
        }

        // Lógica automática basada en el tipo de contacto
        if (in_array($this->type, ['prensa', 'comunicacion'])) {
            return 'email';
        }

        if ($this->type === 'corresponsal') {
            return 'mobile_phone';
        }

        return 'email'; // Por defecto
    }

    /**
     * Verificar si acepta un tipo específico de contenido.
     */
    public function acceptsContent($contentType)
    {
        switch ($contentType) {
            case 'press_release':
                return $this->accepts_press_releases;
            case 'interview':
                return $this->accepts_interviews;
            case 'event_invitation':
                return $this->accepts_events_invitations;
            default:
                return false;
        }
    }

    /**
     * Obtener información de contacto completa.
     */
    public function getContactInfoAttribute()
    {
        return [
            'name' => $this->contact_name,
            'job_title' => $this->job_title,
            'department' => $this->department,
            'phone' => $this->phone,
            'mobile' => $this->mobile_phone,
            'email' => $this->email,
            'secondary_email' => $this->secondary_email,
            'preferred_method' => $this->getRecommendedContactMethod(),
            'available_now' => $this->isAvailableNow(),
        ];
    }

    /**
     * Obtener métricas de interacción.
     */
    public function getInteractionMetricsAttribute()
    {
        return [
            'total_contacts' => $this->contacts_count,
            'successful_contacts' => $this->successful_contacts,
            'response_rate' => $this->response_rate,
            'last_contacted' => $this->last_contacted_at?->diffForHumans(),
            'last_response' => $this->last_response_at?->diffForHumans(),
            'avg_response_time' => $this->calculateAverageResponseTime(),
        ];
    }

    /**
     * Obtener perfil profesional.
     */
    public function getProfessionalProfileAttribute()
    {
        return [
            'specializations' => $this->specializations,
            'coverage_areas' => $this->coverage_areas,
            'is_freelancer' => $this->is_freelancer,
            'priority_level' => $this->priority_level,
            'accepts_press_releases' => $this->accepts_press_releases,
            'accepts_interviews' => $this->accepts_interviews,
            'accepts_events' => $this->accepts_events_invitations,
            'bio' => $this->bio,
        ];
    }

    /**
     * Calcular tiempo promedio de respuesta.
     */
    private function calculateAverageResponseTime()
    {
        if (!$this->interaction_history || empty($this->interaction_history)) {
            return null;
        }

        $responseTimes = [];
        $interactions = collect($this->interaction_history);

        foreach ($interactions as $index => $interaction) {
            if ($interaction['successful'] && isset($interactions[$index + 1])) {
                $contactTime = \Carbon\Carbon::parse($interaction['timestamp']);
                $responseTime = \Carbon\Carbon::parse($interactions[$index + 1]['timestamp']);
                
                if ($responseTime->gt($contactTime)) {
                    $responseTimes[] = $responseTime->diffInHours($contactTime);
                }
            }
        }

        if (empty($responseTimes)) {
            return null;
        }

        return round(array_sum($responseTimes) / count($responseTimes), 1) . ' horas';
    }

    /**
     * Obtener tipo de contacto en español.
     */
    public function getTypeNameAttribute()
    {
        $types = [
            'editor' => 'Editor',
            'redactor' => 'Redactor',
            'jefe_redaccion' => 'Jefe de Redacción',
            'director' => 'Director',
            'corresponsal' => 'Corresponsal',
            'freelancer' => 'Freelancer',
            'prensa' => 'Contacto de Prensa',
            'comunicacion' => 'Comunicación',
            'relaciones_publicas' => 'Relaciones Públicas',
            'fotografo' => 'Fotógrafo',
            'camarografo' => 'Camarógrafo',
            'tecnico' => 'Técnico',
        ];

        return $types[$this->type] ?? 'Desconocido';
    }

    /**
     * Obtener nivel de prioridad en texto.
     */
    public function getPriorityLevelNameAttribute()
    {
        $levels = [
            1 => 'Muy Baja',
            2 => 'Baja',
            3 => 'Media',
            4 => 'Alta',
            5 => 'Muy Alta',
        ];

        return $levels[$this->priority_level] ?? 'No definida';
    }

    /**
     * Obtener estado de verificación.
     */
    public function getVerificationStatusAttribute()
    {
        if ($this->is_verified && $this->verified_at) {
            return "Verificado el " . $this->verified_at->format('d/m/Y');
        }

        return 'No verificado';
    }

    /**
     * Obtener interacciones recientes.
     */
    public function getRecentInteractions($limit = 10)
    {
        if (!$this->interaction_history) {
            return collect();
        }

        return collect($this->interaction_history)
                ->take($limit)
                ->map(function($interaction) {
                    $interaction['timestamp_formatted'] = \Carbon\Carbon::parse($interaction['timestamp'])->diffForHumans();
                    return $interaction;
                });
    }
}
