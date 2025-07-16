<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceUnit extends Model
{
    protected $fillable = [
        'name',
        'short_name',
        'unit_code',
        'conversion_factor_to_kwh',
    ];
}
