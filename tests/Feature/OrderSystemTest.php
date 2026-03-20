<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CakeShape;
use App\Models\CakeFlavor;
use App\Models\CakeTopping;
use App\Models\CakeColor;
use App\Models\ReadyCake;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seed minimal data required for tests
    $this->shape = CakeShape::create(['name' => 'Round', 'base_price' => 10.00]);
    $this->flavor = CakeFlavor::create(['name' => 'Vanilla']);
    $this->topping = CakeTopping::create(['name' => 'Sprinkles']);

    $this->shape->flavors()->attach($this->flavor->id, ['extra_price' => 5.00]);
    $this->shape->toppings()->attach($this->topping->id, ['price' => 2.00]);
    $this->color = CakeColor::create(['name' => 'Blue', 'hex_code' => '#0000FF']);

    $this->readyCake = ReadyCake::create([
        'name' => 'Birthday Special',
        'cake_shape_id' => $this->shape->id,
        'cake_flavor_id' => $this->flavor->id,
        'cake_color_id' => $this->color->id,
        'price' => 50.00,
        'is_active' => true,
        'is_customizable' => false,
    ]);
});

test('guest can create order with required fields', function () {
    // Simulate user with permission to create orders (guest logic handled via public form or admin)
    // Assuming admin form used for now based on context, but guest order creation implies public access?
    // The prompt says "Can create guest order". In Admin panel, we can create orders for guests.
    // We'll test the Admin component as that's what we audited.

    $user = App\Models\User::factory()->create();
    // Assign permission if needed, or assume super admin for test simplicity if roles not set up
    // But audit showed 'authorize' calls. Let's mock permission.
    // The previous implementation used 'authorize', so we ideally need a user with permission.
    // However, "No factories if not present". UserFactory exists.

    // Bypass authorization for unit testing logic if possible, or actAs user.
    $this->actingAs($user);

    // Mock the permission check to pass
    // Or simpler: The component uses `authorize('create orders')`.
    // We can define a gate or just ignore it for this specific test if focusing on validation?
    // Better: Define the gate.
    Illuminate\Support\Facades\Gate::define('create orders', fn() => true);

    Livewire::test('admin::order-form')
        ->set('customer_name', 'Guest User')
        ->set('customer_phone', '1234567890')
        ->set('scheduled_at', now()->addDay()->format('Y-m-d\TH:i'))
        ->set('order_source', 'web')
        ->set('fulfillment_type', 'pickup')
        ->set('payment_method', 'cash')
        ->set('items', [
            [
                'type' => 'ready',
                'ready_cake_id' => $this->readyCake->id,
                'final_price' => 50.00,
                'quantity' => 1
            ]
        ])
        ->call('save')
        ->assertHasNoErrors();

    expect(Order::count())->toBe(1);
    expect(Order::first()->customer_name)->toBe('Guest User');
});

test('order creation fails when required fields missing', function () {
    Illuminate\Support\Facades\Gate::define('create orders', fn() => true);
    $this->actingAs(\App\Models\User::factory()->create());

    Livewire::test('admin::order-form')
        ->set('customer_name', '') // Missing
        ->set('items', [
            [
                'type' => 'ready',
                'ready_cake_id' => $this->readyCake->id,
                'final_price' => 50.00,
                'quantity' => 1
            ]
        ])
        ->call('save')
        ->assertHasErrors(['customer_name' => 'required']);
});

test('delivery requires address', function () {
    Illuminate\Support\Facades\Gate::define('create orders', fn() => true);
    $this->actingAs(\App\Models\User::factory()->create());

    Livewire::test('admin::order-form')
        ->set('fulfillment_type', 'delivery')
        ->set('address_text', '') // Missing address
        ->set('items', [
            [
                'type' => 'ready',
                'ready_cake_id' => $this->readyCake->id,
                'final_price' => 50.00,
                'quantity' => 1
            ]
        ])
        ->call('save')
        ->assertHasErrors(['address_text' => 'required_if']);
});



test('pricing snapshot stores correct values for custom cake', function () {
    Illuminate\Support\Facades\Gate::define('create orders', fn() => true);

    $user = App\Models\User::factory()->create();
    $this->actingAs($user);

    // Create Order with Custom Item
    Livewire::test('admin::order-form')
        ->set('customer_name', 'Tester')
        ->set('customer_phone', '123')
        ->set('scheduled_at', now()->addDay()->format('Y-m-d\TH:i'))
        ->set('fulfillment_type', 'pickup')
        ->set('payment_method', 'cash')
        ->set('items', [
            [
                'type' => 'custom',
                'cake_shape_id' => $this->shape->id,
                'cake_flavor_id' => $this->flavor->id,
                'cake_color_id' => $this->color->id,
                'cake_topping_id' => $this->topping->id,
                'final_price' => 17.00, // 10 + 5 + 2
                'quantity' => 1
            ]
        ])
        ->call('save');

    $orderItem = OrderItem::first();

    expect($orderItem->base_price)->toEqual(10.00); // 10.00 decimal
    expect($orderItem->extra_price)->toEqual(5.00);
    expect($orderItem->topping_price)->toEqual(2.00);
    expect($orderItem->final_price)->toEqual(17.00); // 10+5+2
});

test('pricing snapshot stores correct values for ready cake', function () {
    Illuminate\Support\Facades\Gate::define('create orders', fn() => true);
    $user = App\Models\User::factory()->create();
    $this->actingAs($user);

    Livewire::test('admin::order-form')
        ->set('customer_name', 'Tester')
        ->set('customer_phone', '123')
        ->set('scheduled_at', now()->addDay()->format('Y-m-d\TH:i'))
        ->set('fulfillment_type', 'pickup')
        ->set('payment_method', 'cash')
        ->set('items', [
            [
                'type' => 'ready',
                'ready_cake_id' => $this->readyCake->id,
                'final_price' => 50.00,
                'quantity' => 1
            ]
        ])
        ->call('save');

    $orderItem = OrderItem::first();

    // Ready cakes have 0 base/extra/topping breakdown in current logic (createOrderItem)
    expect($orderItem->base_price)->toEqual(0.00);
    expect($orderItem->extra_price)->toEqual(0.00);
    expect($orderItem->topping_price)->toEqual(0.00);
    expect($orderItem->final_price)->toEqual(50.00);
});

test('optional topping allows null cake_topping_id', function () {
    Illuminate\Support\Facades\Gate::define('create orders', fn() => true);
    $user = App\Models\User::factory()->create();
    $this->actingAs($user);

    // Custom cake without topping
    Livewire::test('admin::order-form')
        ->set('customer_name', 'Tester')
        ->set('customer_phone', '123')
        ->set('scheduled_at', now()->addDay()->format('Y-m-d\TH:i'))
        ->set('fulfillment_type', 'pickup')
        ->set('payment_method', 'cash')
        ->set('items', [
            [
                'type' => 'custom',
                'cake_shape_id' => $this->shape->id,
                'cake_flavor_id' => $this->flavor->id,
                'cake_color_id' => $this->color->id,
                'cake_topping_id' => null, // No topping
                'final_price' => 15.00, // 10 + 5
                'quantity' => 1
            ]
        ])
        ->call('save')
        ->assertHasNoErrors();

    $orderItem = OrderItem::first();
    expect($orderItem->cake_topping_id)->toBeNull();
    expect($orderItem->topping_price)->toEqual(0.00);
    expect($orderItem->final_price)->toEqual(15.00);
});

test('order total is sum of items', function () {
    Illuminate\Support\Facades\Gate::define('create orders', fn() => true);
    $user = App\Models\User::factory()->create();
    $this->actingAs($user);

    Livewire::test('admin::order-form')
        ->set('customer_name', 'Tester')
        ->set('customer_phone', '123')
        ->set('scheduled_at', now()->addDay()->format('Y-m-d\TH:i'))
        ->set('fulfillment_type', 'pickup')
        ->set('payment_method', 'cash')
        ->set('items', [
            [
                'type' => 'ready',
                'ready_cake_id' => $this->readyCake->id,
                'final_price' => 50.00,
                'quantity' => 1
            ],
            [
                'type' => 'custom',
                'cake_shape_id' => $this->shape->id,
                'cake_flavor_id' => $this->flavor->id,
                'cake_color_id' => $this->color->id,
                'cake_topping_id' => $this->topping->id,
                'final_price' => 17.00,
                'quantity' => 1
            ]
        ])
        ->call('calculateTotal')
        ->call('save');

    $order = Order::first();
    expect($order->total_price)->toEqual(67.00); // 50 + 17
});

test('default status is pending', function () {
    Illuminate\Support\Facades\Gate::define('create orders', fn() => true);
    $user = App\Models\User::factory()->create();
    $this->actingAs($user);

    Livewire::test('admin::order-form')
        ->set('customer_name', 'Tester')
        ->set('customer_phone', '123')
        ->set('scheduled_at', now()->addDay()->format('Y-m-d\TH:i'))
        ->set('fulfillment_type', 'pickup')
        ->set('payment_method', 'cash')
        ->set('items', [
            [
                'type' => 'ready',
                'ready_cake_id' => $this->readyCake->id,
                'final_price' => 50.00,
                'quantity' => 1
            ]
        ])
        ->call('save');

    $order = Order::first();
    expect($order->status)->toBe('pending');
});

test('update order preserves base_price for existing items', function () {
    Illuminate\Support\Facades\Gate::define('create orders', fn() => true);
    Illuminate\Support\Facades\Gate::define('update orders', fn() => true);
    $user = App\Models\User::factory()->create();
    $this->actingAs($user);

    // Create initial order with item having specific base_price
    $order = Order::create([
        'customer_name' => 'Original Name',
        'customer_phone' => '123',
        'scheduled_at' => now()->addDay(),
        'order_source' => 'web',
        'fulfillment_type' => 'pickup',
        'payment_method' => 'cash',
        'total_price' => 100,
    ]);

    $item = OrderItem::create([
        'order_id' => $order->id,
        'cake_shape_id' => $this->shape->id,
        'base_price' => 123.45, // Specific price to test preservation
        'final_price' => 123.45,
        'quantity' => 1,
    ]);

    // Perform update via Livewire (loading the order triggers mount which loads items)
    Livewire::test('admin::order-form', ['order' => $order])
        ->set('customer_name', 'Updated Name')
        ->call('save')
        ->assertHasNoErrors();

    $updatedItem = $order->refresh()->items->first();
    expect($updatedItem->base_price)->toEqual(123.45);
});

test('update order preserves base_price for existing ready cake items', function () {
    Illuminate\Support\Facades\Gate::define('create orders', fn() => true);
    Illuminate\Support\Facades\Gate::define('update orders', fn() => true);
    $user = App\Models\User::factory()->create();
    $this->actingAs($user);

    // Create initial order with ready cake item having specific base_price (e.g. overridden or legacy)
    $order = Order::create([
        'customer_name' => 'Original Name',
        'customer_phone' => '123',
        'scheduled_at' => now()->addDay(),
        'order_source' => 'web',
        'fulfillment_type' => 'pickup',
        'payment_method' => 'cash',
        'total_price' => 100,
    ]);

    $item = OrderItem::create([
        'order_id' => $order->id,
        'ready_cake_id' => $this->readyCake->id,
        'base_price' => 83.00, // Specific price to test preservation
        'final_price' => 83.00,
        'quantity' => 1,
    ]);

    // Perform update via Livewire
    Livewire::test('admin::order-form', ['order' => $order])
        ->set('customer_name', 'Updated Name')
        ->call('save')
        ->assertHasNoErrors();

    $updatedItem = $order->refresh()->items->first();
    expect($updatedItem->base_price)->toEqual(83.00);
});

test('can edit existing item in order form', function () {
    Illuminate\Support\Facades\Gate::define('create orders', fn() => true);
    Illuminate\Support\Facades\Gate::define('update orders', fn() => true);
    $user = App\Models\User::factory()->create();
    $this->actingAs($user);

    // Create an order with one item
    $order = Order::create([
        'customer_name' => 'John Doe',
        'customer_phone' => '123456',
        'scheduled_at' => now()->addDay(),
        'order_source' => 'web',
        'fulfillment_type' => 'pickup',
        'payment_method' => 'cash',
        'total_price' => 50.00,
    ]);

    $item = OrderItem::create([
        'order_id' => $order->id,
        'ready_cake_id' => $this->readyCake->id,
        'cake_shape_id' => $this->readyCake->cake_shape_id,
        'cake_flavor_id' => $this->readyCake->cake_flavor_id,
        'cake_color_id' => $this->readyCake->cake_color_id,
        'quantity' => 1,
        'final_price' => 50.00,
        'base_price' => 50.00,
        'extra_price' => 0,
        'topping_price' => 0,
    ]);

    Livewire::test('admin::order-form', ['order' => $order])
        ->call('editItem', 0)
        ->assertSet('editingItemIndex', 0)
        ->assertSet('tempItem.quantity', 1)
        ->set('tempItem.quantity', 3)
        ->call('saveItem')
        ->assertSet('items.0.quantity', 3)
        ->assertSet('items.0.final_price', 50.00)
        ->call('save');

    $order->refresh();
    expect($order->items->first()->quantity)->toBe(3);
    expect($order->total_price)->toEqual(150.00);
});
