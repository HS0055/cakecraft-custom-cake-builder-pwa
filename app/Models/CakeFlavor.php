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
 * Class CakeFlavor
 *
 * Represents a flavor option (e.g., Vanilla, Chocolate) that can be linked to physical Cake Shapes.
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read Collection<int, CakeShape> $shapes
 */
class CakeFlavor extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'name',
    ];

    public function shapes(): BelongsToMany
    {
        return $this->belongsToMany(CakeShape::class, 'shape_flavors')
            ->using(ShapeFlavor::class)
            ->withPivot(['id', 'full_image', 'cut_image', 'extra_price'])
            ->withTimestamps();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')->singleFile();
    }
}
