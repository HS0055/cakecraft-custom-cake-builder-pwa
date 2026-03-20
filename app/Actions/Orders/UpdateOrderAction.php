<?php

namespace App\Actions\Orders;

use App\Models\CakeShape;
use App\Models\Order;
use App\Models\ReadyCake;
use Illuminate\Support\Facades\DB;

/**
 * Class UpdateOrderAction
 *
 * Handles the modification of an existing order. This includes updating root
 * order details, syncing (deleting/upserting) order items, and appending
 * new attachments inside a database transaction.
 */
class UpdateOrderAction
{
    /**
     * Executes the order update process.
     *
     * @param Order $order The existing order instance to be updated.
     * @param array $data General updated order attributes.
     * @param array $items Array of items (new and existing) to sync with the order.
     * @param array $attachments Array of new uploaded files to attach to the order.
     * @return Order The updated order instance.
     */
    public function execute(Order $order, array $data, array $items, array $attachments = []): Order
    {
        return DB::transaction(function () use ($order, $data, $items, $attachments) {
            $order->update([
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
                'total_price' => $data['total_price'],
            ]);

            // Sync items
            $payloadItemIds = collect($items)
                ->pluck('id')
                ->filter(fn($id) => !empty($id))
                ->toArray();

            // Delete items that are no longer in the payload
            $order->items()
                ->whereNotIn('id', $payloadItemIds)
                ->delete();

            // Upsert remaining items
            foreach ($items as $item) {
                $this->upsertOrderItem($order, $item);
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
     * Upserts an individual order item.
     *
     * If the item payload contains an 'id', it attempts to update the existing record.
     * Otherwise, it creates a new record. Automatically handles data mapping
     * between 'ready' cakes and 'custom' builder cakes.
     *
     * @param Order $order The parent order instance.
     * @param array $itemData The data array for the specific order item.
     * @return void
     */
    private function upsertOrderItem(Order $order, array $itemData): void
    {
        $itemId = $itemData['id'] ?? null;
        $attributes = [];

        if ($itemData['type'] === 'ready') {
            $readyCake = ReadyCake::find($itemData['ready_cake_id']);
            $attributes = [
                'ready_cake_id' => $itemData['ready_cake_id'],
                'cake_shape_id' => $readyCake->cake_shape_id,
                'cake_flavor_id' => $readyCake->cake_flavor_id,
                'cake_color_id' => $readyCake->cake_color_id,
                'cake_topping_id' => $readyCake->cake_topping_id,
                'base_price' => $itemData['base_price'] ?? 0,
                'extra_price' => $itemData['extra_price'] ?? 0,
                'topping_price' => $itemData['topping_price'] ?? 0,
                'final_price' => $itemData['final_price'] ?? $readyCake->price,
                'quantity' => $itemData['quantity'] ?? 1,
            ];
        } else {
            $shape = CakeShape::find($itemData['cake_shape_id']);
            $flavor = $shape->flavors()->where('cake_flavor_id', $itemData['cake_flavor_id'])->first();
            $topping = $itemData['cake_topping_id']
                ? $shape->toppings()->where('cake_topping_id', $itemData['cake_topping_id'])->first()
                : null;

            $attributes = [
                'ready_cake_id' => null, // Explicitly nullify if switching from ready to custom
                'cake_shape_id' => $itemData['cake_shape_id'],
                'cake_flavor_id' => $itemData['cake_flavor_id'],
                'cake_color_id' => $itemData['cake_color_id'] ?: null,
                'cake_topping_id' => $itemData['cake_topping_id'] ?: null,
                'base_price' => $itemData['base_price'] ?? ($shape->base_price ?? 0),
                'extra_price' => $itemData['extra_price'] ?? ($flavor ? $flavor->pivot->extra_price : 0),
                'topping_price' => $itemData['topping_price'] ?? ($topping ? $topping->pivot->price : 0),
                'final_price' => $itemData['final_price'],
                'quantity' => $itemData['quantity'] ?? 1,
            ];
        }

        if ($itemId) {
            $orderItem = $order->items()->find($itemId);
            if ($orderItem) {
                $orderItem->update($attributes);
                return;
            }
        }

        // Create new item
        $order->items()->create($attributes);
    }
}
