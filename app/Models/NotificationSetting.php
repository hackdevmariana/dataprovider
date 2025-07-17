<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationSetting extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'target_id',
        'threshold',
        'delivery_method',
        'is_silent',
        'active',
    ];

    protected $casts = [
        'threshold' => 'decimal:4',
        'is_silent' => 'boolean',
        'active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
