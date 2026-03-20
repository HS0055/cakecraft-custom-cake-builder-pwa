<?php

use App\Actions\Orders\UpdateOrderAction;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ReadyCake;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('update order action currently destroys and recreates items', function () {
    $order = Order::factory()->create();
    $readyCake = ReadyCake::factory()->create();

    // Create original item
    $originalItem = OrderItem::create([
        'order_id' => $order->id,
        'ready_cake_id' => $readyCake->id,
        'quantity' => 1,
        'quantity' => 1,
        'final_price' => 100,
        'base_price' => 100,
    ]);

    $originalItemId = $originalItem->id;

    // Simulate payload for update (same item, new quantity)
    $itemsPayload = [
        [
            'id' => $originalItemId, // Even if we pass ID, currently it's ignored
            'type' => 'ready',
            'ready_cake_id' => $readyCake->id,
            'quantity' => 5,
            'final_price' => 100,
            'base_price' => 100,
        ]
    ];

    $action = app(UpdateOrderAction::class);
    $action->execute($order, [
        'customer_name' => 'Test',
        'customer_phone' => '123',
        'scheduled_at' => now(),
        'order_source' => 'web',
        'fulfillment_type' => 'pickup',
        'payment_method' => 'cash',
        'total_price' => 500,
    ], $itemsPayload);

    $order->refresh();
    $newItem = $order->items->first();

    // Verification:
    // With current logic, IDs should be DIFFERENT because it deletes and creates.
    // My goal is to fails this test later (make them EQUAL).
    expect($order->items->count())->toBe(1);
    expect($newItem->id)->toBe($originalItemId);
});
