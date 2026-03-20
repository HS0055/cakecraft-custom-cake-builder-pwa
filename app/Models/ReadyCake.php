<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class ReadyCake
 *
 * Represents a pre-configured cake composition (shape, flavor, color, topping) that is ready to be sold directly.
 * 
 * @property int $id
 * @property string $name
 * @property int $cake_shape_id
 * @property int $cake_flavor_id
 * @property int|null $cake_color_id
 * @property string|null $custom_color_hex
 * @property float $price
 * @property bool $is_active
 * @property bool $is_customizable
 * @property int|null $cake_topping_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read \App\Models\CakeShape $cakeShape
 * @property-read \App\Models\CakeFlavor $cakeFlavor
 * @property-read \App\Models\CakeColor|null $cakeColor
 * @property-read \App\Models\CakeTopping|null $cakeTopping
 */
class ReadyCake extends Model implements HasMedia
{
    use InteractsWithMedia, HasFactory;

    protected $fillable = [
        'name',
        'cake_shape_id',
        'cake_flavor_id',
        'cake_color_id',
        'custom_color_hex',
        'price',
        'is_active',
        'is_customizable',
        'cake_topping_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
            'is_customizable' => 'boolean',
        ];
    }

    /**
     * The structural shape assigned to this ready cake.
     */
    public function cakeShape(): BelongsTo
    {
        return $this->belongsTo(CakeShape::class);
    }

    /**
     * The primary flavor assigned to this ready cake.
     */
    public function cakeFlavor(): BelongsTo
    {
        return $this->belongsTo(CakeFlavor::class);
    }

    /**
     * An optional predefined frosting color assigned.
     */
    public function cakeColor(): BelongsTo
    {
        return $this->belongsTo(CakeColor::class);
    }

    /**
     * An optional singular topping assigned to the cake.
     */
    public function cakeTopping(): BelongsTo
    {
        return $this->belongsTo(CakeTopping::class);
    }

    /**
     * All shape-topping layers for this cake's shape (eager-loadable).
     * Filter by cake_topping_id in the view after eager loading.
     */
    public function shapeToppings(): HasMany
    {
        return $this->hasMany(ShapeTopping::class, 'cake_shape_id', 'cake_shape_id');
    }

    /**
     * Register Spatie media handling collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('preview')->singleFile();
    }
}
