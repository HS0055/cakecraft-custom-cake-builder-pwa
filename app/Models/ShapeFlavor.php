<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * Class ShapeFlavor
 *
 * Pivot model representing the specific combination linkage between a CakeShape and a CakeFlavor.
 * Manages the specific relational pricing and representative cross-section cut media.
 *
 * @property int $id
 * @property int $cake_shape_id
 * @property int $cake_flavor_id
 * @property string|float $extra_price
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read CakeShape|null $shape
 * @property-read CakeFlavor|null $flavor
 */
class ShapeFlavor extends Pivot implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'shape_flavors';

    public $incrementing = true;

    protected $fillable = [
        'cake_shape_id',
        'cake_flavor_id',
        'extra_price',
    ];

    protected function casts(): array
    {
        return [
            'extra_price' => 'decimal:2',
        ];
    }

    public function shape(): BelongsTo
    {
        return $this->belongsTo(CakeShape::class, 'cake_shape_id');
    }

    public function flavor(): BelongsTo
    {
        return $this->belongsTo(CakeFlavor::class, 'cake_flavor_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('full_image')->singleFile();
        $this->addMediaCollection('cut_image')->singleFile();
    }
}
