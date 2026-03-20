<?php

use App\Models\CakeColor;
use App\Models\CakeFlavor;
use App\Models\CakeShape;
use App\Models\CakeTopping;
use App\Models\Order;
use App\Models\ReadyCake;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class OrderForm (Livewire 4 Multi-File Component)
 *
 * This Livewire component handles the complex creation and updating forms for Orders, 
 * including dynamic cart-like line item manipulation for Ready and Custom Cakes.
 */
new #[Layout('layouts::admin', ['title' => 'Manage Order'])] class extends Component {
    use WithFileUploads;

    public ?Order $order = null;
    public bool $isEditing = false;

    // Customer Info
    public string $customer_name = '';
    public string $customer_phone = '';
    public string $customer_email = '';

    // Order Info
    public string $scheduled_at = '';
    public string $order_source = 'call_center';
    public string $fulfillment_type = 'pickup';
    public string $address_text = '';
    public string $payment_method = 'cash';
    public string $notes = '';
    public string $status = 'pending';
    public $attachments = []; // For file uploads

    // Items
    public array $items = [];
    public float $subtotal_price = 0.0;
    public float $tax_amount = 0.0;
    public float $delivery_fee = 0.0;
    public float $total_price = 0.0;

    // Modal State
    public bool $showingItemModal = false;
    public array $tempItem = [
        'id' => null,
        'type' => 'ready', // ready, custom
        'ready_cake_id' => '',
        'cake_shape_id' => '',
        'cake_flavor_id' => '',
        'cake_color_id' => '',
        'cake_topping_id' => '',
        'quantity' => 1,
        'final_price' => 0,
    ];

    public ?int $editingItemIndex = null;

    public function mount(?Order $order = null): void
    {
        if ($order && $order->exists) {
            $this->isEditing = true;
            $this->order = $order;
            $order->load('items');
            $this->authorize('update orders');

            $this->customer_name = $order->customer_name;
            $this->customer_phone = $order->customer_phone;
            $this->customer_email = $order->customer_email ?? '';
            $this->scheduled_at = $order->scheduled_at->format('Y-m-d\TH:i');
            $this->order_source = $order->order_source;
            $this->fulfillment_type = $order->fulfillment_type;
            $this->address_text = $order->address_text ?? '';
            $this->payment_method = $order->payment_method;
            $this->notes = $order->notes ?? '';
            $this->status = $order->status ?? 'pending';
            $this->subtotal_price = (float) $order->subtotal_price;
            $this->tax_amount = (float) $order->tax_amount;
            $this->delivery_fee = (float) $order->delivery_fee;
            $this->total_price = (float) $order->total_price;

            foreach ($order->items as $item) {
                $this->items[] = [
                    'id' => $item->id, // Track existing ID
                    'type' => $item->ready_cake_id ? 'ready' : 'custom',
                    'ready_cake_id' => $item->ready_cake_id,
                    'cake_shape_id' => $item->cake_shape_id,
                    'cake_flavor_id' => $item->cake_flavor_id,
                    'cake_color_id' => $item->cake_color_id,
                    'cake_topping_id' => $item->cake_topping_id,
                    'quantity' => $item->quantity, // Load quantity
                    'final_price' => $item->final_price,
                    'base_price' => $item->base_price,
                    'extra_price' => $item->extra_price,
                    'topping_price' => $item->topping_price,
                ];
            }
            $this->order->unsetRelation('items');
            $this->calculateTotal();
        } else {
            $this->authorize('create orders');
            $this->scheduled_at = now()->addDay()->format('Y-m-d\TH:i');
        }
    }

    public function addItem(): void
    {
        $this->resetTempItem();
        $this->showingItemModal = true;
    }

    public function editItem($index): void
    {
        if (isset($this->items[$index])) {
            $this->tempItem = $this->items[$index];
            $this->editingItemIndex = $index;
            $this->showingItemModal = true;
        }
    }

    public function resetTempItem(): void
    {
        $this->editingItemIndex = null;
        $this->tempItem = [
            'id' => null,
            'type' => 'ready',
            'ready_cake_id' => '',
            'cake_shape_id' => '',
            'cake_flavor_id' => '',
            'cake_color_id' => '',
            'cake_topping_id' => '',
            'quantity' => 1,
            'final_price' => 0,
            'base_price' => 0,
            'extra_price' => 0,
            'topping_price' => 0,
        ];
    }

    public function switchItemType($type): void
    {
        $this->tempItem['type'] = $type;
    }

    public function calculateTempItemPrice(): float
    {
        $calculatePrice = app(\App\Actions\Orders\CalculateItemPriceAction::class);
        return $calculatePrice->execute($this->tempItem, false)['final_price'];
    }

    public function saveItem(\App\Actions\Orders\CalculateItemPriceAction $calculatePrice): void
    {
        $pricing = $calculatePrice->execute($this->tempItem, true);

        $this->tempItem = array_merge($this->tempItem, $pricing);

        if ($this->editingItemIndex !== null) {
            $this->items[$this->editingItemIndex] = $this->tempItem;
        } else {
            $this->items[] = $this->tempItem;
        }

        $this->calculateTotal();
        $this->showingItemModal = false;
        $this->resetTempItem();
    }

    public function removeItem($index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotal();
    }

    public function calculateTotal(): void
    {
        $this->subtotal_price = 0;
        foreach ($this->items as $item) {
            $this->subtotal_price += ($item['final_price'] ?? 0) * ($item['quantity'] ?? 1);
        }

        $this->delivery_fee = ($this->fulfillment_type === 'delivery')
            ? (float) (settings(\App\Settings\OrderSettings::class)->delivery_fee ?? 0)
            : 0;

        $taxPercentage = (float) (settings(\App\Settings\OrderSettings::class)->tax_percentage ?? 0);
        $this->tax_amount = $this->subtotal_price * ($taxPercentage / 100);

        $this->total_price = $this->subtotal_price + $this->tax_amount + $this->delivery_fee;
    }

    public function save(
        \App\Actions\Orders\CreateOrderAction $createOrder,
        \App\Actions\Orders\UpdateOrderAction $updateOrder
    ): void {
        $this->authorize($this->isEditing ? 'update orders' : 'create orders');

        $this->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email',
            'scheduled_at' => ['required', 'date', 'after:now'],
            'order_source' => ['required', \Illuminate\Validation\Rule::in(array_keys(settings(\App\Settings\OrderSettings::class)->sources))],
            'fulfillment_type' => ['required', \Illuminate\Validation\Rule::in(['pickup', 'delivery'])],
            'address_text' => 'required_if:fulfillment_type,delivery',
            'payment_method' => ['required', \Illuminate\Validation\Rule::in(['cash', 'stripe', 'paypal'])],
            'status' => ['required', \Illuminate\Validation\Rule::in(['pending', 'confirmed', 'paid', 'in_progress', 'completed', 'cancelled'])],
            'items' => 'required|array|min:1',
            'items.*.ready_cake_id' => 'nullable|exists:ready_cakes,id',
            'items.*.cake_shape_id' => 'nullable|exists:cake_shapes,id',
            'items.*.cake_flavor_id' => 'nullable|exists:cake_flavors,id',
            'items.*.cake_color_id' => 'nullable|exists:cake_colors,id',
            'items.*.cake_topping_id' => 'nullable|exists:cake_toppings,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.base_price' => 'nullable|numeric|min:0',
            'items.*.extra_price' => 'nullable|numeric|min:0',
            'items.*.topping_price' => 'nullable|numeric|min:0',
            'items.*.final_price' => 'nullable|numeric|min:0',
            'attachments.*' => 'nullable|image|max:10240', // 10MB max
        ]);

        $data = [
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'customer_email' => $this->customer_email,
            'scheduled_at' => $this->scheduled_at,
            'order_source' => $this->order_source,
            'fulfillment_type' => $this->fulfillment_type,
            'address_text' => $this->address_text,
            'payment_method' => $this->payment_method,
            'notes' => $this->notes,
            'status' => $this->status,
            'subtotal_price' => $this->subtotal_price,
            'tax_amount' => $this->tax_amount,
            'delivery_fee' => $this->delivery_fee,
            'total_price' => $this->total_price,
        ];

        if ($this->isEditing) {
            $updateOrder->execute($this->order, $data, $this->items, $this->attachments);
            session()->flash('success', __('admin.order_form.updated_successfully'));
        } else {
            $createOrder->execute($data, $this->items, $this->attachments);
            session()->flash('success', __('admin.order_form.created_successfully'));
        }

        $this->redirect(route('admin.orders'), navigate: true);
    }

    /**
     * Provide component state exclusively to the view file using Livewire 4 array mapping.
     *
     * @return array{
     *   readyCakes: Collection, 
     *   shapes: Collection, 
     *   allFlavors: Collection, 
     *   allToppings: Collection, 
     *   flavors: Collection, 
     *   colors: Collection, 
     *   toppings: Collection
     * }
     */
    public function with(): array
    {
        return [
            'readyCakes' => ReadyCake::with(['cakeShape.media', 'cakeFlavor.media', 'cakeColor', 'cakeTopping.media', 'media'])->where('is_active', true)->get(),
            'shapes' => CakeShape::with('media')->get(),
            'allFlavors' => CakeFlavor::with('media')->get(),
            'allToppings' => CakeTopping::with('media')->get(),
            'flavors' => $this->tempItem['cake_shape_id']
                ? CakeShape::find($this->tempItem['cake_shape_id'])?->flavors ?? collect()
                : collect(),
            'colors' => CakeColor::all(),
            'toppings' => $this->tempItem['cake_shape_id']
                ? CakeShape::find($this->tempItem['cake_shape_id'])?->toppings ?? collect()
                : collect(),
        ];
    }
};
