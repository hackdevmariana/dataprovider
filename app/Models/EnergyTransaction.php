<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnergyTransaction extends Model
{
    protected $fillable = [
        'producer_id', 'consumer_id', 'installation_id',
        'amount_kwh', 'price_per_kwh', 'transaction_datetime',
    ];

    protected $casts = [
        'amount_kwh' => 'float',
        'price_per_kwh' => 'float',
        'transaction_datetime' => 'datetime',
    ];

    public function producer()
    {
        return $this->belongsTo(User::class, 'producer_id');
    }

    public function consumer()
    {
        return $this->belongsTo(User::class, 'consumer_id');
    }

    public function installation()
    {
        return $this->belongsTo(EnergyInstallation::class);
    }
}
