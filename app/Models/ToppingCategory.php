<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

/**
 * Class ToppingCategory
 *
 * Represents an organizational category (e.g., Nuts, Candies) for grouping cake toppings.
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read Collection<int, CakeTopping> $toppings
 */
class ToppingCategory extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function toppings()
    {
        return $this->hasMany(CakeTopping::class);
    }
}
