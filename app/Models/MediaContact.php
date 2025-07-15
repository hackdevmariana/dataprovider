<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MediaContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'media_outlet_id',
        'type',
        'contact_name',
        'phone',
        'email',
    ];

    public function mediaOutlet()
    {
        return $this->belongsTo(MediaOutlet::class);
    }
}
