<?php

use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Class OrderDetails (Livewire 4 Multi-File Component)
 *
 * This Livewire component handles displaying the comprehensive breakdown
 * of an individual order and manages live status mutations.
 */
new #[Layout('layouts::admin', ['title' => 'Order Details'])] class extends Component {
    public Order $order;
    public string $status;

    public function mount(Order $order): void
    {
        $this->authorize('view orders'); // Assuming generic permission or specific
        $this->order = $order->load(['items.readyCake.media', 'items.readyCake.cakeTopping.media', 'items.cakeShape.media', 'items.cakeFlavor.media', 'items.cakeColor', 'items.cakeTopping.media']);
        $this->status = $order->status;

        // Recalculate pricing for old orders that have stale/missing tax/delivery values
        $orderSettings = settings(\App\Settings\OrderSettings::class);
        $hasTaxConfig = $orderSettings->tax_percentage > 0;
        $hasDeliveryConfig = $orderSettings->delivery_fee > 0;
        $needsRecalc = ($order->tax_amount == 0 && $hasTaxConfig)
            || ($order->delivery_fee == 0 && $order->fulfillment_type === 'delivery' && $hasDeliveryConfig);

        if ($needsRecalc) {
            $subtotal = $order->items->sum(fn($item) => $item->final_price * $item->quantity);
            $deliveryFee = ($order->fulfillment_type === 'delivery') ? (float) $orderSettings->delivery_fee : 0;
            $taxAmount = $subtotal * ($orderSettings->tax_percentage / 100);

            $order->update([
                'subtotal_price' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'tax_amount' => $taxAmount,
                'total_price' => $subtotal + $taxAmount + $deliveryFee,
            ]);

            $this->order = $order->refresh();
        }
    }

    public function updatedStatus($value): void
    {
        $this->authorize('update orders');

        $validator = \Illuminate\Support\Facades\Validator::make(['status' => $value], [
            'status' => [
                'required',
                \Illuminate\Validation\Rule::in([
                    'pending',
                    'confirmed',
                    'paid',
                    'in_progress',
                    'completed',
                    'cancelled',
                ])
            ],
        ]);

        if ($validator->fails()) {
            throw \Illuminate\Validation\ValidationException::withMessages(['status' => 'Invalid status selected.']);
        }

        $this->order->update(['status' => $value]);
        session()->flash('success', __('admin.order_details.status_updated_successfully'));
    }

    /**
     * Provide component state exclusively to the view file using Livewire 4 array mapping.
     *
     * @return array<string, mixed>
     */
    public function with(): array
    {
        $this->order->loadMissing([
            'items.readyCake.media',
            'items.readyCake.cakeTopping.media',
            'items.cakeShape.media',
            'items.cakeFlavor.media',
            'items.cakeColor',
            'items.cakeTopping.media',
        ]);

        return [];
    }
};
