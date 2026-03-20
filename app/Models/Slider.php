<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Class Slider
 *
 * Represents a promotional slider item that may link to a custom builder or a specific ready cake.
 *
 * @property int $id
 * @property string $action_type
 * @property int|null $ready_cake_id
 * @property bool $is_active
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\ReadyCake|null $readyCake
 */
class Slider extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'action_type',
        'ready_cake_id',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Register Spatie media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }

    /**
     * The ready cake associated with this slider (if action_type is 'ready_cake').
     */
    public function readyCake(): BelongsTo
    {
        return $this->belongsTo(ReadyCake::class);
    }
}
