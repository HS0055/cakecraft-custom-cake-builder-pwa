<?php

use App\Models\{Order, OrderItem, ReadyCake};
use App\Settings\{FulfillmentSettings, PaymentSettings, OrderSettings, CurrencySettings};
use App\Livewire\Traits\WithVisualData;
use Livewire\Attributes\{Layout, Title, Computed, Validate};

new
    #[Layout('layouts::front')]
    #[Title('Checkout')]
    class extends \Livewire\Component {
    use WithVisualData;
    use \Livewire\WithFileUploads;

    // Customer Info
    #[Validate('required|string|max:255')]
    public string $customer_name = '';

    #[Validate('required|string|max:20')]
    public string $customer_phone = '';

    #[Validate('nullable|email|max:255')]
    public string $customer_email = '';

    // Fulfillment
    #[Validate('required|string')]
    public string $fulfillment_type = 'pickup';

    #[Validate('nullable|string|max:500')]
    public string $address_text = '';

    #[Validate('required|date')]
    public ?string $scheduled_at = null;

    // Payment
    #[Validate('required|string')]
    public string $payment_method = 'cash';

    // Notes
    #[Validate('nullable|string|max:1000')]
    public string $notes = '';

    // File Upload for Special Requests
    #[Validate('nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:5120')] // Max 5MB
    public $media = null;

    // State
    public bool $orderPlaced = false;
    public ?int $orderId = null;

    public function mount(): void
    {
        $fulfillment = settings(FulfillmentSettings::class);
        if ($fulfillment->enable_pickup) {
            $this->fulfillment_type = 'pickup';
        } elseif ($fulfillment->enable_delivery) {
            $this->fulfillment_type = 'delivery';
        }

        $payment = settings(PaymentSettings::class);
        if ($payment->enable_cash) {
            $this->payment_method = 'cash';
        } elseif ($payment->enable_stripe || $payment->enable_paypal) {
            $this->payment_method = 'card';
        }
    }

    public function getItemsProperty(): array
    {
        return session('cart', []);
    }

    #[Computed]
    public function subtotal(): float
    {
        return collect($this->items)->sum(fn($item) => ($item['price'] ?? 0) * ($item['quantity'] ?? 1));
    }

    #[Computed]
    public function deliveryFee(): float
    {
        if ($this->fulfillment_type !== 'delivery')
            return 0;
        return (float) (settings(OrderSettings::class)->delivery_fee ?? 0);
    }

    #[Computed]
    public function taxPercentage(): float
    {
        return (float) (settings(OrderSettings::class)->tax_percentage ?? 0);
    }

    #[Computed]
    public function taxAmount(): float
    {
        return $this->subtotal * ($this->taxPercentage / 100);
    }

    #[Computed]
    public function total(): float
    {
        return $this->subtotal + $this->taxAmount + $this->deliveryFee;
    }

    public function updatedScheduledAt()
    {
        $this->validateOnly('scheduled_at');
        // Run custom logic since after hooks don't easily trigger in validateOnly
        $validator = \Illuminate\Support\Facades\Validator::make(
            ['scheduled_at' => $this->scheduled_at],
            ['scheduled_at' => 'required|date']
        );
        $this->validateScheduleTime($validator);
        if ($validator->fails()) {
            $this->addError('scheduled_at', $validator->errors()->first('scheduled_at'));
        }
    }

    private function validateScheduleTime($validator)
    {
        if ($this->scheduled_at) {
            $tz = config('app.timezone', 'Asia/Riyadh');
            $prepTime = settings(\App\Settings\FulfillmentSettings::class)->default_preparation_time ?? 0;
            $minTime = \Carbon\Carbon::now($tz)->addMinutes($prepTime);
            $selectedTime = \Carbon\Carbon::parse($this->scheduled_at, $tz);

            if ($selectedTime->lt($minTime)) {
                $validator->errors()->add('scheduled_at', 'Schedule time must be at least ' . $prepTime . ' minutes from now (' . $minTime->format('h:i A') . ').');
            }
        }
    }

    public function placeOrder(?string $paymentMethodId = null): void
    {
        $this->validate();

        if ($this->scheduled_at) {
            $tz = config('app.timezone', 'Asia/Riyadh');
            $prepTime = settings(\App\Settings\FulfillmentSettings::class)->default_preparation_time ?? 0;
            $minTime = \Carbon\Carbon::now($tz)->addMinutes($prepTime);
            $selectedTime = \Carbon\Carbon::parse($this->scheduled_at, $tz);

            if ($selectedTime->lt($minTime)) {
                $this->addError('scheduled_at', 'Schedule time must be at least ' . $prepTime . ' minutes from now (' . $minTime->format('h:i A') . ').');
                return;
            }
        }

        $items = $this->items;
        if (empty($items))
            return;

        // Fulfillment validation
        if ($this->fulfillment_type === 'delivery' && empty($this->address_text)) {
            $this->addError('address_text', 'Delivery address is required.');
            return;
        }

        // 1. Calculate Amount in Cents for Stripe
        $amountInCents = (int) ($this->total * 100);

        // 2. Handle Payment
        $paymentTransactionId = null;

        if ($this->payment_method === 'card') {
            if (!$paymentMethodId) {
                // If we got here without a paymentMethodId, something is wrong with frontend JS
                $this->addError('payment_method', 'Payment information is missing.');
                return;
            }

            try {
                \Stripe\Stripe::setApiKey(config('services.stripe.secret') ?? settings(PaymentSettings::class)->stripe_secret_key);

                $paymentIntent = \Stripe\PaymentIntent::create([
                    'amount' => $amountInCents,
                    'currency' => strtolower(settings(\App\Settings\CurrencySettings::class)->currency_code ?? 'USD'),
                    'payment_method' => $paymentMethodId,
                    'confirmation_method' => 'manual',
                    'confirm' => true,
                    'return_url' => route('front.checkout'), // Just a placeholder, we handle via JS
                    'metadata' => [
                        'customer_name' => $this->customer_name,
                        'customer_email' => $this->customer_email,
                    ],
                ]);

                if (
                    $paymentIntent->status === 'requires_action' ||
                    $paymentIntent->status === 'requires_source_action'
                ) {
                    $this->dispatch('stripe-requires-action', clientSecret: $paymentIntent->client_secret);
                    return;
                } elseif ($paymentIntent->status === 'succeeded') {
                    $paymentTransactionId = $paymentIntent->id;
                } else {
                    $this->addError('payment_method', 'Payment failed status: ' . $paymentIntent->status);
                    return;
                }

            } catch (\Exception $e) {
                $this->addError('payment_method', 'Payment refused: ' . $e->getMessage());
                return;
            }
        } elseif ($this->payment_method === 'online') {
            // PayPal
            if (!$paymentMethodId) {
                $this->addError('payment_method', 'Payment information is missing.');
                return;
            }

            $details = json_decode($paymentMethodId, true);
            if (!$details || !isset($details['id'])) {
                $this->addError('payment_method', 'Invalid payment details received.');
                return;
            }

            $paypalOrderId = $details['id'];
            $settings = settings(PaymentSettings::class);

            // Determine REST API base URL based on sandbox/live mode
            $baseUrl = $settings->paypal_mode === 'sandbox'
                ? 'https://api-m.sandbox.paypal.com'
                : 'https://api-m.paypal.com';

            try {
                // 1. Get PayPal Access Token
                $authResponse = \Illuminate\Support\Facades\Http::asForm()
                    ->withBasicAuth($settings->paypal_client_id, $settings->paypal_secret)
                    ->post("$baseUrl/v1/oauth2/token", [
                        'grant_type' => 'client_credentials',
                    ]);

                if (!$authResponse->successful()) {
                    \Illuminate\Support\Facades\Log::error('PayPal Auth Failed', ['response' => $authResponse->json()]);
                    $this->addError('payment_method', 'Could not authenticate with PayPal.');
                    return;
                }

                $accessToken = $authResponse->json('access_token');

                // 2. Fetch the Order from PayPal to verify its status
                $orderResponse = \Illuminate\Support\Facades\Http::withToken($accessToken)
                    ->get("$baseUrl/v2/checkout/orders/$paypalOrderId");

                if (!$orderResponse->successful()) {
                    \Illuminate\Support\Facades\Log::error('PayPal Order Fetch Failed', ['response' => $orderResponse->json()]);
                    $this->addError('payment_method', 'Could not verify PayPal order details.');
                    return;
                }

                $paypalOrder = $orderResponse->json();

                // 3. Verify status is COMPLETED or APPROVED
                if (isset($paypalOrder['status']) && in_array($paypalOrder['status'], ['COMPLETED', 'APPROVED'])) {
                    $paymentTransactionId = $paypalOrder['id'];
                } else {
                    $this->addError('payment_method', 'PayPal Payment not completed. Status: ' . ($paypalOrder['status'] ?? 'Unknown'));
                    return;
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('PayPal Verification Exception', ['message' => $e->getMessage()]);
                $this->addError('payment_method', 'Error verifying payment: ' . $e->getMessage());
                return;
            }
        }


        $this->createOrderRecords($paymentTransactionId);
    }

    public function finalizeStripeOrder(string $paymentIntentId): void
    {
        $this->validate();

        \Stripe\Stripe::setApiKey(config('services.stripe.secret') ?? settings(PaymentSettings::class)->stripe_secret_key);
        try {
            $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);
            if ($paymentIntent->status === 'succeeded') {
                $this->createOrderRecords($paymentIntent->id);
            } else {
                $this->addError('payment_method', 'Payment confirmation failed: ' . $paymentIntent->status);
            }
        } catch (\Exception $e) {
            $this->addError('payment_method', 'Error confirming payment: ' . $e->getMessage());
        }
    }

    private function createOrderRecords(?string $paymentTransactionId): void
    {
        $items = $this->items;

        // Create Order
        $order = Order::create([
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'customer_email' => $this->customer_email ?: null,
            'fulfillment_type' => $this->fulfillment_type,
            'address_text' => $this->fulfillment_type === 'delivery' ? $this->address_text : null,
            'scheduled_at' => $this->scheduled_at ? \Carbon\Carbon::parse($this->scheduled_at) : null,
            'payment_method' => $this->payment_method,
            'notes' => $this->notes ?: null,
            'order_source' => 'web',
            'status' => ($this->payment_method === 'card' || $this->payment_method === 'online') ? 'paid' : 'pending', // Auto-mark paid if card/online
            'subtotal_price' => $this->subtotal,
            'tax_amount' => $this->taxAmount,
            'delivery_fee' => $this->deliveryFee,
            'total_price' => $this->total,
            'payment_id' => $paymentTransactionId,
        ]);

        if ($this->media) {
            $order->addMedia($this->media)->toMediaCollection('attachments');
        }

        // Create Order Items
        foreach ($items as $item) {
            $orderItemData = [
                'order_id' => $order->id,
                'quantity' => $item['quantity'] ?? 1,
                'final_price' => $item['price'] ?? 0, // final_price is the unit price, not the total price
                'base_price' => $item['price'] ?? 0,
                'extra_price' => 0,
                'topping_price' => 0,
            ];

            if ($item['type'] === 'ready' && !empty($item['ready_cake_id'])) {
                $readyCake = ReadyCake::find($item['ready_cake_id']);
                if ($readyCake) {
                    $orderItemData['ready_cake_id'] = $readyCake->id;
                    $orderItemData['cake_shape_id'] = $readyCake->cake_shape_id;
                    $orderItemData['cake_flavor_id'] = $readyCake->cake_flavor_id;
                    $orderItemData['cake_color_id'] = $readyCake->cake_color_id;
                    $orderItemData['cake_topping_id'] = $readyCake->cake_topping_id;
                }
            } elseif ($item['type'] === 'custom' && !empty($item['details'])) {
                $details = $item['details'];
                $orderItemData['cake_shape_id'] = $details['shape_id'] ?? null;
                $orderItemData['cake_flavor_id'] = $details['flavor_id'] ?? null;
                $orderItemData['cake_color_id'] = $details['color_id'] ?? null;
                $orderItemData['cake_topping_id'] = $details['topping_id'] ?? null;
            }

            OrderItem::create($orderItemData);
        }

        // Clear cart
        session()->forget('cart');
        $this->dispatch('cart-updated');

        $this->orderPlaced = true;
        $this->orderId = $order->id;
    }

    public function validateForm(): bool
    {
        try {
            $this->validate();
            return true;
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Livewire automatically adds errors to the error bag
            return false;
        }
    }
};
