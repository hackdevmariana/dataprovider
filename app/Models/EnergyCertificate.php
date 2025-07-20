<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnergyCertificate extends Model
{
    protected $fillable = [
        'user_id', 'cooperative_id', 'co2_kg_certified',
        'certifying_body', 'certificate_number', 'issue_date', 'expiry_date'
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function cooperative() { return $this->belongsTo(Cooperative::class); }
}