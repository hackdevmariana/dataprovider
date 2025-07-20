<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ElectricityPriceInterval extends Model
{
    use HasFactory;

    protected $fillable = [
        'electricity_price_id',
        'interval_index',
        'start_time',
        'end_time',
        'price_eur_mwh',
    ];

    public function electricityPrice()
    {
        return $this->belongsTo(ElectricityPrice::class);
    }
}
