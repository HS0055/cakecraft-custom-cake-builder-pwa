<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'base_price' => 'decimal:2',
        'extra_price' => 'decimal:2',
        'topping_price' => 'decimal:2',
        'final_price' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function readyCake(): BelongsTo
    {
        return $this->belongsTo(ReadyCake::class);
    }

    public function cakeShape(): BelongsTo
    {
        return $this->belongsTo(CakeShape::class);
    }

    public function cakeFlavor(): BelongsTo
    {
        return $this->belongsTo(CakeFlavor::class);
    }

    public function cakeColor(): BelongsTo
    {
        return $this->belongsTo(CakeColor::class);
    }

    public function cakeTopping(): BelongsTo
    {
        return $this->belongsTo(CakeTopping::class);
    }

    // Pricing Logic Helper (can be used on create/update)
    public function calculateFinalPrice(): float
    {
        if ($this->ready_cake_id) {
            return $this->readyCake->price ?? 0;
        }

        $base = $this->cakeShape->base_price ?? 0;
        $extra = $this->cakeFlavor->extra_price ?? 0;
        $topping = $this->cakeTopping->price ?? 0;

        return $base + $extra + $topping;
    }
}
