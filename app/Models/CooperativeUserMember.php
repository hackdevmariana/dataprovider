<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CooperativeUserMember extends Pivot
{
    use HasFactory;

    protected $table = 'cooperative_user_members';

    protected $fillable = [
        'cooperative_id',
        'user_id',
        'role',
        'joined_at',
        'is_active',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function cooperative()
    {
        return $this->belongsTo(Cooperative::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
