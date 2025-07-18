<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_name',
        'device_type',
        'platform',
        'browser',
        'ip_address',
        'token',
        'notifications_enabled',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
