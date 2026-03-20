<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * Class ShapeTopping
 *
 * Pivot model representing the specific combination linkage between a CakeShape and a CakeTopping.
 * Manages the specific relational pricing and representative image layer media.
 *
 * @property int $id
 * @property int $cake_shape_id
 * @property int $cake_topping_id
 * @property string|float $price
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read CakeShape|null $shape
 * @property-read CakeTopping|null $topping
 */
class ShapeTopping extends Pivot implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'shape_toppings';

    public $incrementing = true;

    protected $fillable = [
        'cake_shape_id',
        'cake_topping_id',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function shape(): BelongsTo
    {
        return $this->belongsTo(CakeShape::class, 'cake_shape_id');
    }

    public function topping(): BelongsTo
    {
        return $this->belongsTo(CakeTopping::class, 'cake_topping_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image_layer')->singleFile();
    }
}
