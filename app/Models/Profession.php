<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Profession
 *
 * Represents a profession or occupation.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $category
 * @property bool $is_public_facing
 */
class Profession extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'category',
        'is_public_facing',
    ];
}
