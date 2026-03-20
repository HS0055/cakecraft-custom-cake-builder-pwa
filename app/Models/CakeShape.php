<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

/**
 * Class CakeShape
 *
 * Represents a base physical shape of a custom or ready cake (e.g., Round, Square, Heart).
 * Shapes possess relationships determining which Flavors and Toppings are compatible with them.
 *
 * @property int $id
 * @property string $name
 * @property float $base_price
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read Collection<int, CakeFlavor> $flavors
 * @property-read Collection<int, CakeTopping> $toppings
 */
class CakeShape extends Model implements HasMedia
{
    use InteractsWithMedia, HasFactory;

    protected $fillable = [
        'name',
        'base_price',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
        ];
    }

    public function flavors(): BelongsToMany
    {
        return $this->belongsToMany(CakeFlavor::class, 'shape_flavors')
            ->using(ShapeFlavor::class)
            ->withPivot(['id', 'full_image', 'cut_image', 'extra_price'])
            ->withTimestamps();
    }

    public function toppings(): BelongsToMany
    {
        return $this->belongsToMany(CakeTopping::class, 'shape_toppings')
            ->using(ShapeTopping::class)
            ->withPivot(['id', 'image_layer', 'price'])
            ->withTimestamps();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')->singleFile();
        $this->addMediaCollection('base_image')->singleFile();
        $this->addMediaCollection('base_cut_image')->singleFile();
    }
}
