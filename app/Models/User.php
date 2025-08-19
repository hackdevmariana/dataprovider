<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function cooperativeMemberships()
    {
        return $this->hasMany(\App\Models\CooperativeUserMember::class);
    }

    public function cooperatives()
    {
        return $this->belongsToMany(\App\Models\Cooperative::class, 'cooperative_user_members')
            ->withPivot(['role', 'joined_at', 'is_active'])
            ->withTimestamps();
    }

    public function userDevices()
    {
        return $this->hasMany(\App\Models\UserDevice::class);
    }

    public function notificationSettings()
    {
        return $this->hasMany(\App\Models\NotificationSetting::class);
    }

    public function stats()
    {
        return $this->morphMany(\App\Models\Stat::class, 'subject');
    }

    public function achievements()
    {
        return $this->belongsToMany(\App\Models\Achievement::class, 'user_achievements')
                    ->withPivot([
                        'progress',
                        'level',
                        'is_completed',
                        'completed_at',
                        'metadata',
                        'value_achieved',
                        'points_earned',
                        'is_notified'
                    ])
                    ->withTimestamps();
    }

    public function userAchievements()
    {
        return $this->hasMany(\App\Models\UserAchievement::class);
    }

    public function completedAchievements()
    {
        return $this->belongsToMany(\App\Models\Achievement::class, 'user_achievements')
                    ->wherePivot('is_completed', true)
                    ->withPivot(['completed_at', 'level', 'points_earned'])
                    ->withTimestamps();
    }

    public function challenges()
    {
        return $this->belongsToMany(\App\Models\Challenge::class, 'user_challenges')
                    ->withPivot([
                        'status',
                        'joined_at',
                        'completed_at',
                        'progress',
                        'current_value',
                        'ranking_position',
                        'points_earned',
                        'reward_earned'
                    ])
                    ->withTimestamps();
    }

    public function userChallenges()
    {
        return $this->hasMany(\App\Models\UserChallenge::class);
    }

    public function activeChallenges()
    {
        return $this->belongsToMany(\App\Models\Challenge::class, 'user_challenges')
                    ->wherePivot('status', 'active')
                    ->withPivot(['current_value', 'ranking_position'])
                    ->withTimestamps();
    }

    // FASE 5: Relaciones del Feed Social

    /**
     * Actividades generadas por este usuario
     */
    public function activityFeeds()
    {
        return $this->hasMany(\App\Models\ActivityFeed::class);
    }

    /**
     * Actividades públicas del usuario
     */
    public function publicActivities()
    {
        return $this->hasMany(\App\Models\ActivityFeed::class)
                    ->where('visibility', 'public')
                    ->where('status', 'active')
                    ->where('show_in_feed', true);
    }

    /**
     * Actividades destacadas del usuario
     */
    public function featuredActivities()
    {
        return $this->hasMany(\App\Models\ActivityFeed::class)
                    ->where('is_featured', true)
                    ->where('status', 'active');
    }

    /**
     * Hitos del usuario
     */
    public function milestones()
    {
        return $this->hasMany(\App\Models\ActivityFeed::class)
                    ->where('is_milestone', true)
                    ->where('status', 'active');
    }

    /**
     * Interacciones sociales realizadas por este usuario
     */
    public function socialInteractions()
    {
        return $this->hasMany(\App\Models\SocialInteraction::class);
    }

    /**
     * Likes dados por este usuario
     */
    public function likesGiven()
    {
        return $this->hasMany(\App\Models\SocialInteraction::class)
                    ->where('interaction_type', 'like')
                    ->where('status', 'active');
    }

    /**
     * Shares realizados por este usuario
     */
    public function sharesGiven()
    {
        return $this->hasMany(\App\Models\SocialInteraction::class)
                    ->where('interaction_type', 'share')
                    ->where('status', 'active');
    }

    /**
     * Bookmarks guardados por este usuario
     */
    public function bookmarksGiven()
    {
        return $this->hasMany(\App\Models\SocialInteraction::class)
                    ->where('interaction_type', 'bookmark')
                    ->where('status', 'active');
    }

    /**
     * Usuarios que sigue este usuario
     */
    public function following()
    {
        return $this->belongsToMany(\App\Models\User::class, 'user_follows', 'follower_id', 'following_id')
                    ->withPivot([
                        'follow_type',
                        'notification_frequency',
                        'show_in_main_feed',
                        'prioritize_in_feed',
                        'feed_weight',
                        'engagement_score',
                        'is_mutual',
                        'status',
                        'followed_at'
                    ])
                    ->withTimestamps();
    }

    /**
     * Usuarios que siguen a este usuario
     */
    public function followers()
    {
        return $this->belongsToMany(\App\Models\User::class, 'user_follows', 'following_id', 'follower_id')
                    ->withPivot([
                        'follow_type',
                        'notification_frequency',
                        'show_in_main_feed',
                        'prioritize_in_feed',
                        'feed_weight',
                        'engagement_score',
                        'is_mutual',
                        'status',
                        'followed_at'
                    ])
                    ->withTimestamps();
    }

    /**
     * Seguimientos activos que hace este usuario
     */
    public function activeFollowing()
    {
        return $this->following()->wherePivot('status', 'active');
    }

    /**
     * Seguidores activos de este usuario
     */
    public function activeFollowers()
    {
        return $this->followers()->wherePivot('status', 'active');
    }

    /**
     * Seguimientos mutuos
     */
    public function mutualFollows()
    {
        return $this->following()->wherePivot('is_mutual', true)->wherePivot('status', 'active');
    }

    /**
     * Relaciones de seguimiento salientes
     */
    public function followingRelationships()
    {
        return $this->hasMany(\App\Models\UserFollow::class, 'follower_id');
    }

    /**
     * Relaciones de seguimiento entrantes
     */
    public function followerRelationships()
    {
        return $this->hasMany(\App\Models\UserFollow::class, 'following_id');
    }

    // Métodos auxiliares para el feed social

    /**
     * Obtener feed personalizado para este usuario
     */
    public function getFeedActivities($limit = 20)
    {
        return \App\Models\ActivityFeed::feedFor($this)
                                      ->with(['user', 'related'])
                                      ->limit($limit)
                                      ->get();
    }

    /**
     * Verificar si este usuario sigue a otro usuario
     */
    public function isFollowing(User $user): bool
    {
        return $this->following()
                    ->wherePivot('following_id', $user->id)
                    ->wherePivot('status', 'active')
                    ->exists();
    }

    /**
     * Verificar si otro usuario sigue a este usuario
     */
    public function isFollowedBy(User $user): bool
    {
        return $this->followers()
                    ->wherePivot('follower_id', $user->id)
                    ->wherePivot('status', 'active')
                    ->exists();
    }

    /**
     * Verificar si hay seguimiento mutuo con otro usuario
     */
    public function hasMutualFollowWith(User $user): bool
    {
        return $this->mutualFollows()->wherePivot('following_id', $user->id)->exists();
    }

    /**
     * Obtener estadísticas sociales del usuario
     */
    public function getSocialStats(): array
    {
        return [
            'following_count' => $this->activeFollowing()->count(),
            'followers_count' => $this->activeFollowers()->count(),
            'mutual_follows_count' => $this->mutualFollows()->count(),
            'activities_count' => $this->publicActivities()->count(),
            'featured_activities_count' => $this->featuredActivities()->count(),
            'milestones_count' => $this->milestones()->count(),
            'likes_given_count' => $this->likesGiven()->count(),
            'shares_given_count' => $this->sharesGiven()->count(),
            'bookmarks_given_count' => $this->bookmarksGiven()->count(),
            'total_engagement_score' => $this->activityFeeds()->sum('engagement_score'),
        ];
    }

    // ========================================
    // RELACIONES DE MONETIZACIÓN (FASE 7)
    // ========================================

    /**
     * Suscripciones del usuario
     */
    public function subscriptions()
    {
        return $this->hasMany(\App\Models\UserSubscription::class);
    }

    /**
     * Suscripción activa actual
     */
    public function activeSubscription()
    {
        return $this->hasOne(\App\Models\UserSubscription::class)
                    ->where('status', 'active')
                    ->where(function ($query) {
                        $query->whereNull('ends_at')
                              ->orWhere('ends_at', '>', now());
                    });
    }

    /**
     * Plan de suscripción actual
     */
    public function subscriptionPlan()
    {
        return $this->hasOneThrough(
            \App\Models\SubscriptionPlan::class,
            \App\Models\UserSubscription::class,
            'user_id', // Foreign key on UserSubscription
            'id', // Foreign key on SubscriptionPlan
            'id', // Local key on User
            'subscription_plan_id' // Local key on UserSubscription
        )->where('user_subscriptions.status', 'active');
    }

    /**
     * Comisiones que debe pagar este usuario
     */
    public function commissions()
    {
        return $this->hasMany(\App\Models\ProjectCommission::class);
    }

    /**
     * Comisiones pendientes
     */
    public function pendingCommissions()
    {
        return $this->commissions()->where('status', 'pending');
    }

    /**
     * Verificaciones solicitadas por este usuario
     */
    public function projectVerifications()
    {
        return $this->hasMany(\App\Models\ProjectVerification::class, 'requested_by');
    }

    /**
     * Verificaciones realizadas por este usuario
     */
    public function verificationsDone()
    {
        return $this->hasMany(\App\Models\ProjectVerification::class, 'verified_by');
    }

    /**
     * Consultas como consultor
     */
    public function consultationsAsConsultant()
    {
        return $this->hasMany(\App\Models\ConsultationService::class, 'consultant_id');
    }

    /**
     * Consultas como cliente
     */
    public function consultationsAsClient()
    {
        return $this->hasMany(\App\Models\ConsultationService::class, 'client_id');
    }

    /**
     * Pagos realizados por este usuario
     */
    public function payments()
    {
        return $this->hasMany(\App\Models\Payment::class);
    }

    /**
     * Pagos completados
     */
    public function completedPayments()
    {
        return $this->payments()->where('status', 'completed');
    }

    // Métodos auxiliares de monetización

    /**
     * Verificar si tiene suscripción activa
     */
    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription()->exists();
    }

    /**
     * Verificar si tiene una característica específica
     */
    public function hasFeature(string $feature): bool
    {
        $subscription = $this->activeSubscription;
        
        if (!$subscription) {
            return false;
        }

        return $subscription->subscriptionPlan->hasFeature($feature);
    }

    /**
     * Verificar si ha alcanzado un límite
     */
    public function hasReachedLimit(string $limit): bool
    {
        $subscription = $this->activeSubscription;
        
        if (!$subscription) {
            return true; // Sin suscripción = límites máximos
        }

        return $subscription->hasReachedLimit($limit);
    }

    /**
     * Obtener estadísticas como consultor
     */
    public function getConsultantStats(): array
    {
        return \App\Models\ConsultationService::getConsultantStats($this->id);
    }

    /**
     * Verificar si puede ser consultor
     */
    public function canBeConsultant(): bool
    {
        // Lógica para determinar si puede ser consultor
        // Por ejemplo: verificar experiencia, certificaciones, etc.
        return $this->hasFeature('consultation_services') || 
               $this->verificationsDone()->count() > 0;
    }

    /**
     * Calcular ingresos totales como consultor
     */
    public function getTotalConsultantEarnings(): float
    {
        return $this->consultationsAsConsultant()
                   ->where('status', 'completed')
                   ->sum('total_amount');
    }

    /**
     * Calcular gastos totales como cliente
     */
    public function getTotalClientSpending(): float
    {
        return $this->completedPayments()->sum('amount');
    }
}
