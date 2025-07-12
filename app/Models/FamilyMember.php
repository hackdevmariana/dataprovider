<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FamilyMember extends Model
{
    protected $fillable = [
        'person_id',
        'relative_id',
        'relationship_type',
        'is_biological',
    ];

    protected $casts = [
        'is_biological' => 'boolean',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function relative()
    {
        return $this->belongsTo(Person::class, 'relative_id');
    }
}
