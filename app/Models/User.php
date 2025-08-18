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
}
