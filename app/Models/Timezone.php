<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Timezone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'offset',
        'dst_offset',
    ];

    // Relaciones
    public function countries()
    {
        return $this->hasMany(Country::class);
    }

    public function municipalities()
    {
        return $this->hasMany(Municipality::class);
    }
}
