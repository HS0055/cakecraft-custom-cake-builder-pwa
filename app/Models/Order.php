<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Class Order
 *
 * Represents a customer's bakery order containing multiple order items.
 *
 * @property int $id
 * @property string $customer_name
 * @property string|null $customer_phone
 * @property string|null $customer_email
 * @property string $status
 * @property string $fulfillment_type
 * @property Carbon|null $scheduled_at
 * @property float $total_price
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @property-read Collection<int, OrderItem> $items
 */
class Order extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'subtotal_price' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * Get the items associated with this order.
     *
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
