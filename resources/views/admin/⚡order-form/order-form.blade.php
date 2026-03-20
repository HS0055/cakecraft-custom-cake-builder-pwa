<div class="animate-fade-in">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="!flex items-center gap-2">
                <a href="{{ route('admin.orders') }}" wire:navigate class="text-foreground-muted hover:text-foreground">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                </a>
                <h2 class="font-display text-2xl font-semibold text-foreground">
                    {{ $isEditing ? __('admin.order_form.edit_title', ['id' => $order->id]) : __('admin.order_form.create_title') }}
                </h2>
            </div>
            <p class="mt-1 text-sm text-foreground-muted">
                {{ $isEditing ? __('admin.order_form.edit_subtitle') : __('admin.order_form.create_subtitle') }}
            </p>
        </div>
        <button wire:click="save" class="btn-base bg-primary text-primary-foreground hover:bg-primary-hover">
            {{ $isEditing ? __('admin.order_form.update_button') : __('admin.order_form.create_button') }}
        </button>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-xl bg-danger-bg px-4 py-3 text-sm text-danger">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Left Column: Items (Spans 2 columns) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="card-base overflow-hidden">
                <div class="bg-surface-alt/50 px-6 py-4 border-b border-border flex items-center justify-between">
                    <h3 class="font-display font-semibold text-foreground flex items-center gap-2">
                        <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                        {{ __('admin.order_form.order_items') }}
                    </h3>
                    <button wire:click="addItem"
                        class="btn-base text-xs bg-surface text-foreground border border-border hover:bg-surface-alt shadow-sm">
                        <svg class="h-4 w-4 me-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        {{ __('admin.order_form.add_item') }}
                    </button>
                </div>

                <div class="divide-y divide-border">
                    @forelse ($items as $index => $item)
                        <div class="flex flex-col sm:flex-row items-start gap-4 p-4 sm:p-6 hover:bg-surface-alt/20 transition-colors">
                            <div class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-xl border border-border bg-surface-alt mx-auto sm:mx-0">
                                @if ($item['type'] === 'ready')
                                    @php 
                                        $readyCake = $readyCakes->find($item['ready_cake_id']);
                                    @endphp
                                    @if($readyCake && $readyCake->getFirstMediaUrl('preview'))
                                        <img src="{{ $readyCake->getFirstMediaUrl('preview') }}"
                                            alt="{{ $readyCake->name }}" class="h-full w-full object-cover">
                                    @elseif($readyCake && $readyCake->cakeShape)
                                        @php
                                            $toppingLayers = collect();
                                            if ($readyCake->cake_shape_id && $readyCake->cake_topping_id) {
                                                $toppingLayers = \App\Models\ShapeTopping::with('media')
                                                    ->where('cake_shape_id', $readyCake->cake_shape_id)
                                                    ->where('cake_topping_id', $readyCake->cake_topping_id)
                                                    ->get();
                                            }
                                        @endphp
                                        <x-cake-visual class="h-full w-full" 
                                            :shape="$readyCake->cakeShape"
                                            :flavorLayer="$readyCake->cakeFlavor"
                                            :color="$readyCake->cakeColor"
                                            :toppingLayers="$toppingLayers"
                                            mode="final" />
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-foreground-muted">
                                            <svg class="h-8 w-8 opacity-20" fill="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-5.5-2.5l7.51-3.22-7.52-1.72 1.93-3.03 2.92 6.94-6.84 1.03z" />
                                            </svg>
                                        </div>
                                    @endif
                                @else
                                    @php
                                        $customShape = collect($shapes)->firstWhere('id', $item['cake_shape_id']);
                                        $customFlavor = collect($allFlavors)->firstWhere('id', $item['cake_flavor_id']);
                                        $customColor = collect($colors)->firstWhere('id', $item['cake_color_id']);
                                        $customTopping = $item['cake_topping_id'] ? collect($allToppings)->firstWhere('id', $item['cake_topping_id']) : null;
                                        $customToppingLayers = collect();
                                        if ($item['cake_shape_id'] && $item['cake_topping_id']) {
                                            $customToppingLayers = \App\Models\ShapeTopping::with('media')
                                                ->where('cake_shape_id', $item['cake_shape_id'])
                                                ->where('cake_topping_id', $item['cake_topping_id'])
                                                ->get();
                                        }
                                    @endphp
                                    <x-cake-visual class="h-full w-full" 
                                        :shape="$customShape"
                                        :flavorLayer="$customFlavor"
                                        :toppingLayers="$customToppingLayers"
                                        :color="$customColor" />
                                @endif
                            </div>

                            <div class="flex-1 w-full min-w-0">
                                <div class="flex flex-col sm:flex-row sm:justify-between items-start gap-2 mb-2">
                                    <h4 class="font-semibold text-foreground text-center sm:text-start text-lg w-full sm:w-auto">
                                        @if ($item['type'] === 'ready')
                                            {{ $readyCakes->find($item['ready_cake_id'])->name ?? __('admin.order_form.ready_cake') }}
                                        @else
                                            {{ __('admin.order_form.custom_cake') }}
                                        @endif
                                    </h4>
                                    <div class="flex items-center justify-between sm:justify-end gap-4 w-full sm:w-auto mt-2 sm:mt-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-border">
                                        <div class="text-start sm:text-end">
                                            <p class="font-bold text-foreground font-mono">
                                                {{ settings(\App\Settings\CurrencySettings::class)->currency_symbol }}{{ number_format(($item['final_price'] ?? 0) * ($item['quantity'] ?? 1), 2) }}
                                                <span class="block text-xs text-foreground-muted font-normal mt-0.5">
                                                    {{ $item['quantity'] ?? 1 }} x
                                                    {{ settings(\App\Settings\CurrencySettings::class)->currency_symbol }}{{ number_format($item['final_price'] ?? 0, 2) }}
                                                </span>
                                            </p>
                                        </div>
                                        <!-- Edit and Remove Icons -->
                                        <div class="flex items-center gap-1 border-l border-border ps-4 ms-2">
                                            <button type="button" wire:click="editItem({{ $index }})" title="{{ __('admin.common.edit') }}"
                                                class="p-2 text-foreground-muted hover:text-primary transition-colors hover:bg-primary/10 rounded-lg">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                </svg>
                                            </button>
                                            <button type="button" wire:click="removeItem({{ $index }})" title="{{ __('admin.common.delete') }}"
                                                class="p-2 text-foreground-muted hover:text-danger transition-colors hover:bg-danger/10 rounded-lg">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-wrap justify-center sm:justify-start gap-2 text-sm text-foreground-muted">
                                    @if ($item['type'] === 'ready')
                                        <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">{{ __('admin.order_form.ready_cake') }}</span>
                                        @php $readyCake = $readyCakes->find($item['ready_cake_id']); @endphp
                                        @if($readyCake)
                                            @if($readyCake->cakeShape)
                                                <span class="inline-flex items-center rounded-md bg-surface-alt px-2 py-1 text-xs font-medium text-foreground-muted ring-1 ring-inset ring-border">
                                                    {{ $readyCake->cakeShape->name }}
                                                </span>
                                            @endif
                                            @if($readyCake->cakeFlavor)
                                                <span class="inline-flex items-center rounded-md bg-surface-alt px-2 py-1 text-xs font-medium text-foreground-muted ring-1 ring-inset ring-border">
                                                    {{ $readyCake->cakeFlavor->name }}
                                                </span>
                                            @endif
                                            @if($readyCake->cakeTopping)
                                                <span class="inline-flex items-center rounded-md bg-surface-alt px-2 py-1 text-xs font-medium text-foreground-muted ring-1 ring-inset ring-border">
                                                    {{ $readyCake->cakeTopping->name }}
                                                </span>
                                            @endif
                                            @if($readyCake->cakeColor)
                                                <span class="inline-flex items-center gap-1.5 rounded-md bg-surface-alt px-2 py-1 text-xs font-medium text-foreground-muted ring-1 ring-inset ring-border">
                                                    <span class="h-2 w-2 rounded-full ring-1 ring-inset ring-black/10"
                                                        style="background-color: {{ $readyCake->cakeColor->hex_code }}"></span>
                                                    {{ $readyCake->cakeColor->name }}
                                                </span>
                                            @endif
                                        @endif
                                    @else
                                        <span class="inline-flex items-center rounded-md bg-purple-50 px-2 py-1 text-xs font-medium text-purple-700 ring-1 ring-inset ring-purple-700/10">{{ __('admin.order_form.custom_cake') }}</span>
                                        @php
                                            $customShape = collect($shapes)->firstWhere('id', $item['cake_shape_id']);
                                            $customFlavor = collect($allFlavors)->firstWhere('id', $item['cake_flavor_id']);
                                            $customColor = collect($colors)->firstWhere('id', $item['cake_color_id']);
                                            $customTopping = $item['cake_topping_id'] ? collect($allToppings)->firstWhere('id', $item['cake_topping_id']) : null;
                                        @endphp
                                        @if($customShape)
                                            <span class="inline-flex items-center rounded-md bg-surface-alt px-2 py-1 text-xs font-medium text-foreground-muted ring-1 ring-inset ring-border">
                                                {{ $customShape->name }}
                                            </span>
                                        @endif
                                        @if($customFlavor)
                                            <span class="inline-flex items-center rounded-md bg-surface-alt px-2 py-1 text-xs font-medium text-foreground-muted ring-1 ring-inset ring-border">
                                                {{ $customFlavor->name }}
                                            </span>
                                        @endif
                                        @if($customTopping)
                                            <span class="inline-flex items-center rounded-md bg-surface-alt px-2 py-1 text-xs font-medium text-foreground-muted ring-1 ring-inset ring-border">
                                                {{ $customTopping->name }}
                                            </span>
                                        @endif
                                        @if($customColor)
                                            <span class="inline-flex items-center gap-1.5 rounded-md bg-surface-alt px-2 py-1 text-xs font-medium text-foreground-muted ring-1 ring-inset ring-border">
                                                <span class="h-2 w-2 rounded-full ring-1 ring-inset ring-black/10"
                                                    style="background-color: {{ $customColor->hex_code }}"></span>
                                                {{ $customColor->name }}
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center border-b border-border">
                            <svg class="mx-auto h-12 w-12 text-foreground-subtle" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-foreground">{{ __('admin.order_form.no_items_title') }}</h3>
                            <p class="mt-1 text-sm text-foreground-muted">{{ __('admin.order_form.no_items_desc') }}</p>
                        </div>
                    @endforelse
                </div>

                <div class="border-t border-border bg-surface-alt/30 p-6">
                    <div class="flex flex-col items-end gap-2">
                        <div class="flex items-center justify-between w-full max-w-xs text-sm text-foreground-muted">
                            <span>{{ __('admin.order_form.subtotal') }}</span>
                            <span class="font-medium text-foreground">{{ settings(\App\Settings\CurrencySettings::class)->currency_symbol }}{{ number_format($subtotal_price, 2) }}</span>
                        </div>
                        @if($delivery_fee > 0)
                        <div class="flex items-center justify-between w-full max-w-xs text-sm text-foreground-muted">
                            <span>{{ __('admin.order_form.delivery_fee') }}</span>
                            <span class="font-medium text-foreground">{{ settings(\App\Settings\CurrencySettings::class)->currency_symbol }}{{ number_format($delivery_fee, 2) }}</span>
                        </div>
                        @endif
                        @if($tax_amount > 0)
                        <div class="flex items-center justify-between w-full max-w-xs text-sm text-foreground-muted">
                            <span>{{ __('admin.order_form.tax') }}</span>
                            <span class="font-medium text-foreground">{{ settings(\App\Settings\CurrencySettings::class)->currency_symbol }}{{ number_format($tax_amount, 2) }}</span>
                        </div>
                        @endif
                        <div class="flex items-center justify-between w-full max-w-xs pt-2 mt-2 border-t border-border">
                            <span class="text-base font-semibold text-foreground">{{ __('admin.order_form.total_amount') }}</span>
                            <span class="text-2xl font-bold text-primary">{{ settings(\App\Settings\CurrencySettings::class)->currency_symbol }}{{ number_format($total_price, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Customer Info & Order Details -->
        <div class="space-y-6">
            {{-- Customer Info --}}
            <div class="card-base p-6 space-y-4">
                <h3 class="text-lg font-medium text-foreground">{{ __('admin.order_form.customer_info') }}</h3>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-foreground">{{ __('admin.order_form.name_label') }}</label>
                    <input type="text" wire:model="customer_name" class="input-base" placeholder="{{ __('admin.order_form.name_placeholder') }}" />
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-foreground">{{ __('admin.order_form.phone_label') }}</label>
                    <input type="text" wire:model="customer_phone" class="input-base" placeholder="{{ __('admin.order_form.phone_placeholder') }}" />
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-foreground">{{ __('admin.order_form.email_label') }}</label>
                    <input type="email" wire:model="customer_email" class="input-base" placeholder="{{ __('admin.order_form.email_placeholder') }}" />
                </div>
            </div>

            {{-- Order Details --}}
            <div class="card-base p-6 space-y-4">
                <h3 class="text-lg font-medium text-foreground">{{ __('admin.order_form.order_details') }}</h3>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-foreground">{{ __('admin.order_form.scheduled_at') }}</label>
                    <input type="datetime-local" wire:model="scheduled_at" class="input-base" />
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-foreground">{{ __('admin.order_form.source') }}</label>
                    <select wire:model="order_source" class="select-base">
                        @foreach(settings(\App\Settings\OrderSettings::class)->sources as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-foreground">{{ __('admin.order_form.fulfillment') }}</label>
                    <select wire:model.live="fulfillment_type" class="select-base">
                        @foreach(settings(\App\Settings\FulfillmentSettings::class)->types as $key => $label)
                            @if(($key === 'pickup' && settings(\App\Settings\FulfillmentSettings::class)->enable_pickup) || ($key === 'delivery' && settings(\App\Settings\FulfillmentSettings::class)->enable_delivery))
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                @if ($fulfillment_type === 'delivery')
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-foreground">{{ __('admin.order_form.address') }}</label>
                        <textarea wire:model="address_text" class="input-base h-24"
                            placeholder="{{ __('admin.order_form.address_placeholder') }}"></textarea>
                    </div>
                @endif
                <div class="space-y-1">
                    <label class="text-sm font-medium text-foreground">{{ __('admin.order_form.payment_method') }}</label>
                    <select wire:model="payment_method" class="select-base">
                        @foreach(settings(\App\Settings\PaymentSettings::class)->methods as $key => $label)
                            @if(($key === 'cash' && settings(\App\Settings\PaymentSettings::class)->enable_cash) || ($key === 'card' && settings(\App\Settings\PaymentSettings::class)->enable_stripe) || ($key === 'online' && (settings(\App\Settings\PaymentSettings::class)->enable_stripe || settings(\App\Settings\PaymentSettings::class)->enable_paypal)))
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                @if ($isEditing)
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-foreground">{{ __('admin.order_form.status') }}</label>
                        <select wire:model="status" class="select-base">
                            <option value="pending">{{ __('admin.order_details.status_pending') }}</option>
                            <option value="confirmed">{{ __('admin.order_details.status_confirmed') }}</option>
                            <option value="paid">{{ __('admin.order_details.status_paid') }}</option>
                            <option value="in_progress">{{ __('admin.order_details.status_in_progress') }}</option>
                            <option value="completed">{{ __('admin.order_details.status_completed') }}</option>
                            <option value="cancelled">{{ __('admin.order_details.status_cancelled') }}</option>
                        </select>
                    </div>
                @endif
                <div class="space-y-1">
                    <label class="text-sm font-medium text-foreground">{{ __('admin.order_form.notes') }}</label>
                    <textarea wire:model="notes" class="input-base h-24"
                        placeholder="{{ __('admin.order_form.notes_placeholder') }}"></textarea>
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-foreground">{{ __('admin.order_form.attachments') }}</label>
                    <input type="file" wire:model="attachments" multiple class="input-base" />
                    @if ($attachments)
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ($attachments as $attachment)
                                <img src="{{ $attachment->temporaryUrl() }}"
                                    class="h-16 w-16 rounded object-cover border border-border">
                            @endforeach
                        </div>
                    @endif
                    @if($isEditing && $order->getMedia('attachments')->isNotEmpty())
                        <div class="mt-2 text-sm text-foreground-muted">{{ __('admin.order_form.existing_attachments') }}</div>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ($order->getMedia('attachments') as $media)
                                <a href="{{ $media->getUrl() }}" target="_blank" class="block">
                                    <img src="{{ $media->getUrl() }}"
                                        class="h-16 w-16 rounded object-cover border border-border">
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div> <!-- End Grid -->

    {{-- Item Modal --}}
    @if ($showingItemModal)
         <div class="fixed inset-0 z-50 flex items-center justify-center p-4" wire:key="item-modal-wrapper">
            <div class="fixed inset-0 bg-espresso/40 backdrop-blur-sm" wire:click="$set('showingItemModal', false)" wire:key="item-modal-backdrop" wire:ignore.self></div>
            <div wire:key="item-modal-content"
                class="relative w-full max-w-2xl rounded-2xl bg-surface p-6 shadow-modal max-h-[90vh] overflow-y-auto"
                x-data="{ shown: false }" x-init="$nextTick(() => shown = true)"
                x-show="shown" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                <h3 class="mb-4 text-lg font-medium text-foreground">
                    {{ $editingItemIndex !== null ? __('admin.order_form.item_modal_edit') : __('admin.order_form.item_modal_add') }}
                </h3>

                {{-- Type Tabs --}}
                <div class="mb-6 flex gap-1 rounded-xl bg-surface-alt p-1">
                    <button wire:click="switchItemType('ready')"
                        class="flex-1 rounded-md px-3 py-1.5 text-sm font-medium transition-colors {{ $tempItem['type'] === 'ready' ? 'bg-white text-foreground shadow-sm' : 'text-foreground-muted hover:text-foreground' }}">
                        {{ __('admin.order_form.tab_ready') }}
                    </button>
                    <button wire:click="switchItemType('custom')"
                        class="flex-1 rounded-md px-3 py-1.5 text-sm font-medium transition-colors {{ $tempItem['type'] === 'custom' ? 'bg-white text-foreground shadow-sm' : 'text-foreground-muted hover:text-foreground' }}">
                        {{ __('admin.order_form.tab_custom') }}
                    </button>
                </div>

                @if ($tempItem['type'] === 'ready')
                    <div class="space-y-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-foreground">{{ __('admin.order_form.select_ready_cake') }}</label>
                            <select wire:model.live="tempItem.ready_cake_id" class="select-base">
                                <option value="">{{ __('admin.order_form.choose_a_cake') }}</option>
                                @foreach ($readyCakes as $cake)
                                    <option value="{{ $cake->id }}">{{ $cake->name }} -
                                        {{ settings(\App\Settings\CurrencySettings::class)->currency_symbol }}{{ number_format($cake->price, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @else
                    {{-- Resolve cake visual data --}}
                    @php
                        $vizShape = collect($shapes)->firstWhere('id', $tempItem['cake_shape_id']);
                        $vizFlavorLayer = ($tempItem['cake_shape_id'] && $tempItem['cake_flavor_id'])
                            ? \App\Models\ShapeFlavor::with('media')
                                ->where('cake_shape_id', $tempItem['cake_shape_id'])
                                ->where('cake_flavor_id', $tempItem['cake_flavor_id'])
                                ->first()
                            : null;
                        $vizColor = collect($colors)->firstWhere('id', $tempItem['cake_color_id']);
                        $vizToppingLayers = collect();
                        if ($tempItem['cake_shape_id'] && $tempItem['cake_topping_id']) {
                            $vizToppingLayers = \App\Models\ShapeTopping::with('media')
                                ->where('cake_shape_id', $tempItem['cake_shape_id'])
                                ->where('cake_topping_id', $tempItem['cake_topping_id'])
                                ->get();
                        }

                        // Dynamic preview mode based on selections (matching wizard logic)
                        $vizMode = 'final';
                        if (!$tempItem['cake_flavor_id']) {
                            $vizMode = 'shape';
                        } elseif (!$tempItem['cake_color_id'] && !$tempItem['cake_topping_id']) {
                            $vizMode = 'flavor';
                        } elseif ($tempItem['cake_color_id'] && $vizColor) {
                            $vizMode = 'final';
                        }
                    @endphp

                    <div class="flex gap-6">
                        {{-- Dropdowns --}}
                        <div class="flex-1 space-y-4">
                            <div class="space-y-1">
                                <label class="text-sm font-medium text-foreground">{{ __('admin.order_form.shape_label') }}</label>
                                <select wire:model.live="tempItem.cake_shape_id" class="select-base">
                                    <option value="">{{ __('admin.order_form.select_shape') }}</option>
                                    @foreach ($shapes as $shape)
                                        <option value="{{ $shape->id }}">{{ $shape->name }}
                                            (+{{ settings(\App\Settings\CurrencySettings::class)->currency_symbol }}{{ $shape->base_price }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="text-sm font-medium text-foreground">{{ __('admin.order_form.flavor_label') }}</label>
                                <select wire:model.live="tempItem.cake_flavor_id" class="select-base">
                                    <option value="">{{ __('admin.order_form.select_flavor') }}</option>
                                    @foreach ($flavors as $flavor)
                                        <option value="{{ $flavor->id }}">{{ $flavor->name }}
                                            (+{{ settings(\App\Settings\CurrencySettings::class)->currency_symbol }}{{ optional($flavor->pivot)->extra_price }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="text-sm font-medium text-foreground">{{ __('admin.order_form.color_label') }}</label>
                                <select wire:model.live="tempItem.cake_color_id" class="select-base">
                                    <option value="">{{ __('admin.order_form.select_color') }}</option>
                                    @foreach ($colors as $color)
                                        <option value="{{ $color->id }}">{{ $color->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="text-sm font-medium text-foreground">{{ __('admin.order_form.topping_label') }}</label>
                                <select wire:model.live="tempItem.cake_topping_id" class="select-base">
                                    <option value="">{{ __('admin.order_form.no_topping') }}</option>
                                    @foreach ($toppings as $topping)
                                        <option value="{{ $topping->id }}">{{ $topping->name }}
                                            (+{{ settings(\App\Settings\CurrencySettings::class)->currency_symbol }}{{ optional($topping->pivot)->price }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="text-sm font-medium text-foreground">{{ __('admin.order_form.quantity') }}</label>
                                <input type="number" wire:model.live="tempItem.quantity" min="1" step="1" class="input-base" />
                            </div>
                        </div>

                        {{-- Cake Visual Preview --}}
                        <div class="flex flex-col items-center shrink-0 w-48">
                            <div class="relative w-full rounded-2xl border border-border bg-gradient-to-br from-surface to-surface-alt/50 overflow-hidden shadow-sm">
                                <div class="absolute inset-0 bg-checkered opacity-[0.25] mix-blend-multiply"></div>
                                <div class="relative p-3">
                                    <div class="aspect-square w-full flex items-center justify-center">
                                        @if ($vizShape)
                                            <x-cake-visual
                                                class="w-full h-full drop-shadow-xl"
                                                :shape="$vizShape"
                                                :flavorLayer="$vizFlavorLayer"
                                                :color="$vizColor"
                                                :toppingLayers="$vizToppingLayers"
                                                :mode="$vizMode" />
                                        @else
                                            <div class="text-center text-foreground-muted flex flex-col items-center">
                                                <svg class="h-10 w-10 opacity-30 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                                <p class="text-[10px] font-medium">{{ __('admin.order_form.select_shape') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($tempItem['type'] === 'ready')
                     <div class="mt-4 space-y-1">
                        <label class="text-sm font-medium text-foreground">{{ __('admin.order_form.quantity') }}</label>
                        <input type="number" wire:model.live="tempItem.quantity" min="1" step="1" class="input-base" />
                    </div>
                @endif

                <div class="mt-4 border-t border-border pt-4 flex justify-between items-center">
                    <div>
                        <span class="text-sm text-foreground-muted">{{ __('admin.order_form.price_label') }}</span>
                        <span
                            class="text-lg font-semibold text-foreground">{{ settings(\App\Settings\CurrencySettings::class)->currency_symbol }}{{ number_format($this->calculateTempItemPrice() * ($tempItem['quantity'] ?? 1), 2) }}</span>
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button wire:click="$set('showingItemModal', false)"
                        class="btn-base flex-1 border border-border bg-surface text-foreground hover:bg-surface-alt">{{ __('admin.common.cancel') }}</button>
                    <button wire:click="saveItem"
                        class="btn-base flex-1 bg-primary text-primary-foreground hover:bg-primary-hover">
                        {{ $editingItemIndex !== null ? __('admin.order_form.item_modal_edit') : __('admin.order_form.item_modal_add') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>