<?php

namespace App\Actions\Orders;

use App\Models\CakeShape;
use App\Models\Order;
use App\Models\ReadyCake;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

/**
 * Class CreateOrderAction
 *
 * Handles the creation of a new customer order. This action wraps the entire
 * process (saving the order details, adding order items, and attaching media)
 * within a database transaction to ensure data integrity.
 */
class CreateOrderAction
{
    /**
     * Executes the order creation process.
     *
     * @param array $data General order data (e.g., customer_name, total_price, scheduled_at).
     * @param array $items Array of order items to be associated with the new order.
     * @param array $attachments Array of uploaded files to attach to the order.
     * @return Order The newly created order instance.
     */
    public function execute(array $data, array $items, array $attachments = []): Order
    {
        return DB::transaction(function () use ($data, $items, $attachments) {
            $order = Order::create([
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'customer_email' => $data['customer_email'] ?? null,
                'scheduled_at' => $data['scheduled_at'],
                'order_source' => $data['order_source'],
                'fulfillment_type' => $data['fulfillment_type'],
                'address_text' => $data['address_text'] ?? null,
                'payment_method' => $data['payment_method'],
                'notes' => $data['notes'] ?? null,
                'subtotal_price' => $data['subtotal_price'] ?? $data['total_price'],
                'tax_amount' => $data['tax_amount'] ?? 0,
                'delivery_fee' => $data['delivery_fee'] ?? 0,
                'total_price' => $data['total_price'], // Should ideally be recalculated here for security
                'status' => 'pending',
            ]);

            foreach ($items as $item) {
                $this->createOrderItem($order, $item);
            }

            if (!empty($attachments)) {
                foreach ($attachments as $attachment) {
                    $order->addMedia($attachment)->toMediaCollection('attachments');
                }
            }

            return $order;
        });
    }

    /**
     * Creates an individual order item and associates it with the parent order.
     *
     * Handles both 'ready' cakes (pulling configuration directly from the ReadyCake model)
     * and 'custom' cakes (building configuration based on user-selected shape, flavor, etc.).
     *
     * @param Order $order The parent order instance.
     * @param array $itemData The specific data for this order item.
     * @return void
     */
    private function createOrderItem(Order $order, array $itemData): void
    {
        if ($itemData['type'] === 'ready') {
            $readyCake = ReadyCake::find($itemData['ready_cake_id']);
            $order->items()->create([
                'ready_cake_id' => $itemData['ready_cake_id'],
                'cake_shape_id' => $readyCake->cake_shape_id,
                'cake_flavor_id' => $readyCake->cake_flavor_id,
                'cake_color_id' => $readyCake->cake_color_id,
                'cake_topping_id' => $readyCake->cake_topping_id,
                'base_price' => 0,
                'extra_price' => 0,
                'topping_price' => 0,
                'final_price' => $itemData['final_price'] ?? $readyCake->price,
                'quantity' => $itemData['quantity'] ?? 1,
            ]);
        } else {
            $shape = CakeShape::find($itemData['cake_shape_id']);
            $flavor = $shape->flavors()->where('cake_flavor_id', $itemData['cake_flavor_id'])->first();
            $topping = $itemData['cake_topping_id']
                ? $shape->toppings()->where('cake_topping_id', $itemData['cake_topping_id'])->first()
                : null;

            $order->items()->create([
                'cake_shape_id' => $itemData['cake_shape_id'],
                'cake_flavor_id' => $itemData['cake_flavor_id'],
                'cake_color_id' => $itemData['cake_color_id'] ?: null,
                'cake_topping_id' => $itemData['cake_topping_id'] ?: null,
                'base_price' => $shape->base_price ?? 0,
                'extra_price' => $flavor ? $flavor->pivot->extra_price : 0,
                'topping_price' => $topping ? $topping->pivot->price : 0,
                'final_price' => $itemData['final_price'],
                'quantity' => $itemData['quantity'] ?? 1,
            ]);
        }
    }
}
