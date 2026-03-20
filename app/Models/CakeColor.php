<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Support\Carbon;

/**
 * Class CakeColor
 *
 * Represents a color option (e.g., Pink, Blue) with an associated hex code that can be assigned to custom cakes.
 *
 * @property int $id
 * @property string $name
 * @property string $hex_code
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class CakeColor extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'hex_code',
    ];
}
