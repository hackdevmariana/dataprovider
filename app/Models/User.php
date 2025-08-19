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
}
