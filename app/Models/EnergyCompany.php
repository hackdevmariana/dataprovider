<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnergyCompany extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'website',
        'phone_customer',
        'phone_commercial',
        'email_customer',
        'email_commercial',
        'highlighted_offer',
        'cnmc_id',
        'logo_url',
        'image_id',
        'company_type',
        'address',
        'latitude',
        'longitude',
        'coverage_scope',
        'municipality_id',
    ];

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    public function image()
    {
        return $this->belongsTo(Image::class);
    }


}
