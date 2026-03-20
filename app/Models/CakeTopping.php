<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

/**
 * Class CakeTopping
 *
 * Represents an extra topping (e.g., Sprinkles, Glaze) that can be applied to custom cakes.
 *
 * @property int $id
 * @property string $name
 * @property int|null $topping_category_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read ToppingCategory|null $category
 * @property-read Collection<int, CakeShape> $shapes
 */
class CakeTopping extends Model implements HasMedia
{
    use InteractsWithMedia, HasFactory;

    protected $fillable = [
        'name',
        'topping_category_id',
    ];

    public function category()
    {
        return $this->belongsTo(ToppingCategory::class, 'topping_category_id');
    }

    public function shapes(): BelongsToMany
    {
        return $this->belongsToMany(CakeShape::class, 'shape_toppings')
            ->using(ShapeTopping::class)
            ->withPivot(['id', 'image_layer', 'price'])
            ->withTimestamps();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')->singleFile();
    }
}
