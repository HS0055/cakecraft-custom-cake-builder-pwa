<div class="animate-fade-in space-y-6">
    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.orders') }}" wire:navigate
                class="flex h-10 w-10 items-center justify-center rounded-xl bg-surface border border-border text-foreground-muted transition-colors hover:bg-surface-alt hover:text-foreground">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="font-display text-2xl font-bold text-foreground">
                        {{ __('admin.order_details.order_title', ['id' => $order->id]) }}
                    </h2>
                    @php
                        $statusColors = [
                            'pending' => 'bg-warning/10 text-warning ring-warning/20',
                            'confirmed' => 'bg-info/10 text-info ring-info/20',
                            'paid' => 'bg-success/10 text-success ring-success/20',
                            'in_progress' => 'bg-primary/10 text-primary ring-primary/20',
                            'completed' => 'bg-success/10 text-success ring-success/20',
                            'cancelled' => 'bg-danger/10 text-danger ring-danger/20',
                        ];
                        $color = $statusColors[$order->status] ?? 'bg-surface-alt text-foreground-muted ring-border';
                    @endphp
                    <span
                        class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $color }}">
                        {{ settings(\App\Settings\OrderSettings::class)->statuses[$order->status] ?? ucfirst($order->status) }}
                    </span>
                </div>
                <p class="text-sm text-foreground-muted">
                    {{ __('admin.order_details.placed_on', ['date' => $order->created_at->format('F d, Y \a\t h:i A')]) }}
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @can('update orders')
                <a href="{{ route('admin.orders.edit', $order) }}" wire:navigate
                    class="btn-base bg-surface text-foreground border border-border hover:bg-surface-alt">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                    </svg>
                    {{ __('admin.order_details.edit_order') }}
                </a>
            @endcan
            <div class="relative">
                <select wire:model.live="status" class="select-base w-40 ps-9">
                    <option value="pending">{{ __('admin.order_details.status_pending') }}</option>
                    <option value="confirmed">{{ __('admin.order_details.status_confirmed') }}</option>
                    <option value="paid">{{ __('admin.order_details.status_paid') }}</option>
                    <option value="in_progress">{{ __('admin.order_details.status_in_progress') }}</option>
                    <option value="completed">{{ __('admin.order_details.status_completed') }}</option>
                    <option value="cancelled">{{ __('admin.order_details.status_cancelled') }}</option>
                </select>
                <div
                    class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-foreground-muted">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="flex items-center gap-2 rounded-xl bg-success-bg px-4 py-3 text-sm text-success" x-data="{ show: true }"
            x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition>
            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Order Items --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="card-base overflow-hidden">
                <div class="bg-surface-alt/50 px-6 py-4 border-b border-border flex items-center justify-between">
                    <h3 class="font-display font-semibold text-foreground flex items-center gap-2">
                        <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                        {{ __('admin.order_details.order_items') }}
                    </h3>
                    <span
                        class="text-sm text-foreground-muted">{{ __('admin.order_details.items_count', ['count' => $order->items->count()]) }}</span>
                </div>
                <div class="divide-y divide-border">
                    @forelse ($order->items as $item)
                        <div
                            class="flex flex-col sm:flex-row items-start gap-4 p-4 sm:p-6 hover:bg-surface-alt/20 transition-colors">
                            <div
                                class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-xl border border-border bg-surface-alt mx-auto sm:mx-0">
                                @php
                                    $shape = $item->cakeShape;
                                    $flavor = $item->cakeFlavor;
                                    $color = $item->cakeColor;
                                    $toppingLayers = collect();

                                    // Fallback for Ready Cakes with missing item details (legacy/bug fix)
                                    if ($item->readyCake) {
                                        $shape = $shape ?? $item->readyCake->cakeShape;
                                        $flavor = $flavor ?? $item->readyCake->cakeFlavor;
                                        $color = $color ?? $item->readyCake->cakeColor;
                                    }

                                    $shapeId = $item->cake_shape_id ?? ($item->readyCake ? $item->readyCake->cake_shape_id : null);
                                    $toppingId = $item->cake_topping_id ?? ($item->readyCake ? $item->readyCake->cake_topping_id : null);

                                    if ($shapeId && $toppingId) {
                                        $toppingLayers = \App\Models\ShapeTopping::with('media')
                                            ->where('cake_shape_id', $shapeId)
                                            ->where('cake_topping_id', $toppingId)
                                            ->get();
                                    }
                                @endphp
                                @if ($item->readyCake && $item->readyCake->getFirstMediaUrl('preview'))
                                    <img src="{{ $item->readyCake->getFirstMediaUrl('preview') }}"
                                        alt="{{ $item->readyCake->name }}" class="h-full w-full object-cover">
                                @elseif ($shape)
                                    <x-cake-visual class="h-full w-full" :shape="$shape" :flavorLayer="$flavor" :color="$color"
                                        :toppingLayers="$toppingLayers" mode="final" />
                                @else
                                    <div class="flex h-full w-full items-center justify-center text-foreground-muted">
                                        <svg class="h-8 w-8 opacity-20" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-5.5-2.5l7.51-3.22-7.52-1.72 1.93-3.03 2.92 6.94-6.84 1.03z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 w-full min-w-0">
                                <div class="flex flex-col sm:flex-row sm:justify-between items-start gap-2 mb-2">
                                    <h4
                                        class="font-semibold text-foreground text-center sm:text-start text-lg w-full sm:w-auto">
                                        @if ($item->readyCake)
                                            {{ $item->readyCake->name }}
                                        @else
                                            {{ __('admin.order_details.custom_cake') }}
                                        @endif
                                    </h4>
                                    <div
                                        class="flex justify-center sm:justify-end w-full sm:w-auto mt-2 sm:mt-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-border">
                                        <p class="font-bold text-foreground font-mono text-center sm:text-end">
                                            {{ settings(\App\Settings\CurrencySettings::class)->currency_symbol }}{{ number_format($item->final_price * $item->quantity, 2) }}
                                            <span class="block text-xs text-foreground-muted font-normal mt-0.5">
                                                {{ $item->quantity }} x
                                                {{ settings(\App\Settings\CurrencySettings::class)->currency_symbol }}{{ number_format($item->final_price, 2) }}
                                            </span>
                                        </p>
                                    </div>
                                </div>

                                <div
                                    class="flex flex-wrap justify-center sm:justify-start gap-2 text-sm text-foreground-muted">
                                    @if ($item->readyCake)
                                        <span
                                            class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">{{ __('admin.order_details.ready_cake') }}</span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-md bg-purple-50 px-2 py-1 text-xs font-medium text-purple-700 ring-1 ring-inset ring-purple-700/10">{{ __('admin.order_details.custom_design') }}</span>
                                    @endif

                                    @if ($item->cakeShape)
                                        <span
                                            class="inline-flex items-center rounded-md bg-surface-alt px-2 py-1 text-xs font-medium text-foreground-muted ring-1 ring-inset ring-border">
                                            {{ $item->cakeShape->name }}
                                        </span>
                                    @endif
                                    @if ($item->cakeFlavor)
                                        <span
                                            class="inline-flex items-center rounded-md bg-surface-alt px-2 py-1 text-xs font-medium text-foreground-muted ring-1 ring-inset ring-border">
                                            {{ $item->cakeFlavor->name }}
                                        </span>
                                    @endif
                                    @if ($item->cakeTopping)
                                        <span
                                            class="inline-flex items-center rounded-md bg-surface-alt px-2 py-1 text-xs font-medium text-foreground-muted ring-1 ring-inset ring-border">
                                            {{ $item->cakeTopping->name }}
                                        </span>
                                    @endif
                                    @if ($item->cakeColor)
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-md bg-surface-alt px-2 py-1 text-xs font-medium text-foreground-muted ring-1 ring-inset ring-border">
                                            <span class="h-2 w-2 rounded-full ring-1 ring-inset ring-black/10"
                                                style="background-color: {{ $item->cakeColor->hex_code }}"></span>
                                            {{ $item->cakeColor->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-foreground-subtle" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-foreground">{{ __('admin.order_details.no_items') }}
                            </h3>
                            <p class="mt-1 text-sm text-foreground-muted">{{ __('admin.order_details.no_items_message') }}
                            </p>
                        </div>
                    @endforelse
                </div>

                {{-- Summary Section --}}
                <div class="border-t border-border bg-surface-alt/30 p-6">
                    <div class="flex flex-col items-end gap-2">
                        <div class="flex items-center justify-between w-full max-w-xs">
                            <span class="text-sm text-foreground-muted">{{ __('admin.order_details.subtotal') }}</span>
                            <span
                                class="text-sm font-medium text-foreground">{{ settings(\App\Settings\CurrencySettings::class)->currency_symbol }}{{ number_format($order->subtotal_price ?? array_sum($order->items->map(fn($item) => $item->final_price * $item->quantity)->toArray()), 2) }}</span>
                        </div>

                        <div class="flex items-center justify-between w-full max-w-xs">
                            <span
                                class="text-sm text-foreground-muted">{{ __('admin.order_details.delivery_fee') }}</span>
                            <span
                                class="text-sm font-medium text-foreground">{{ settings(\App\Settings\CurrencySettings::class)->currency_symbol }}{{ number_format($order->delivery_fee, 2) }}</span>
                        </div>

                        <div class="flex items-center justify-between w-full max-w-xs">
                            <span class="text-sm text-foreground-muted">{{ __('admin.order_details.tax') }}</span>
                            <span
                                class="text-sm font-medium text-foreground">{{ settings(\App\Settings\CurrencySettings::class)->currency_symbol }}{{ number_format($order->tax_amount, 2) }}</span>
                        </div>

                        <div class="flex items-center justify-between w-full max-w-xs pt-2 border-t border-border mt-2">
                            <span
                                class="text-base font-semibold text-foreground">{{ __('admin.order_details.total') }}</span>
                            <span
                                class="text-2xl font-bold text-primary">{{ settings(\App\Settings\CurrencySettings::class)->currency_symbol }}{{ number_format($order->total_price, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            @if ($order->getMedia('attachments')->isNotEmpty())
                <div class="card-base p-6">
                    <h3 class="font-display font-semibold text-foreground mb-4 flex items-center gap-2">
                        <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13" />
                        </svg>
                        {{ __('admin.order_details.attachments') }}
                    </h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        @foreach ($order->getMedia('attachments') as $media)
                            <a href="{{ $media->getUrl() }}" target="_blank"
                                class="group relative aspect-square overflow-hidden rounded-xl border border-border bg-surface-alt">
                                <img src="{{ $media->getUrl() }}" alt="Attachment"
                                    class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-110">
                                <div class="absolute inset-0 bg-black/0 transition-colors group-hover:bg-black/10"></div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar Info --}}
        <div class="space-y-6">
            {{-- Customer Card --}}
            <div class="card-base p-6">
                <h3 class="font-display font-semibold text-foreground mb-4 flex items-center gap-2">
                    <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                    {{ __('admin.order_details.customer_info') }}
                </h3>
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div
                            class="mt-0.5 flex h-8 w-8 items-center justify-center rounded-lg bg-surface-alt text-foreground-muted">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-foreground-muted uppercase tracking-wider">
                                {{ __('admin.order_details.name') }}
                            </p>
                            <p class="font-medium text-foreground">{{ $order->customer_name }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div
                            class="mt-0.5 flex h-8 w-8 items-center justify-center rounded-lg bg-surface-alt text-foreground-muted">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-foreground-muted uppercase tracking-wider">
                                {{ __('admin.order_details.phone') }}
                            </p>
                            <a href="tel:{{ $order->customer_phone }}"
                                class="font-medium text-primary hover:underline">{{ $order->customer_phone }}</a>
                        </div>
                    </div>

                    @if ($order->customer_email)
                        <div class="flex items-start gap-3">
                            <div
                                class="mt-0.5 flex h-8 w-8 items-center justify-center rounded-lg bg-surface-alt text-foreground-muted">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-foreground-muted uppercase tracking-wider">
                                    {{ __('admin.order_details.email') }}
                                </p>
                                <a href="mailto:{{ $order->customer_email }}"
                                    class="font-medium text-primary hover:underline">{{ $order->customer_email }}</a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Order Details --}}
            <div class="card-base p-6">
                <h3 class="font-display font-semibold text-foreground mb-4 flex items-center gap-2">
                    <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                    {{ __('admin.order_details.details') }}
                </h3>
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div
                            class="mt-0.5 flex h-8 w-8 items-center justify-center rounded-lg bg-surface-alt text-foreground-muted">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-foreground-muted uppercase tracking-wider">
                                {{ __('admin.order_details.scheduled_for') }}
                            </p>
                            <p class="font-medium text-foreground">{{ $order->scheduled_at->format('F d, Y h:i A') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div
                            class="mt-0.5 flex h-8 w-8 items-center justify-center rounded-lg bg-surface-alt text-foreground-muted">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-foreground-muted uppercase tracking-wider">
                                {{ __('admin.order_details.fulfillment') }}
                            </p>
                            <p class="font-medium text-foreground capitalize">
                                {{ settings(\App\Settings\FulfillmentSettings::class)->types[$order->fulfillment_type] ?? $order->fulfillment_type }}
                            </p>
                            @if ($order->fulfillment_type === 'delivery')
                                <p class="text-sm text-foreground-muted mt-1">{{ $order->address_text }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div
                            class="mt-0.5 flex h-8 w-8 items-center justify-center rounded-lg bg-surface-alt text-foreground-muted">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-foreground-muted uppercase tracking-wider">
                                {{ __('admin.order_details.payment') }}
                            </p>
                            <p class="font-medium text-foreground">
                                {{ settings(\App\Settings\PaymentSettings::class)->methods[$order->payment_method] ?? $order->payment_method }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div
                            class="mt-0.5 flex h-8 w-8 items-center justify-center rounded-lg bg-surface-alt text-foreground-muted">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-foreground-muted uppercase tracking-wider">
                                {{ __('admin.order_details.source') }}
                            </p>
                            <p class="font-medium text-foreground">
                                {{ settings(\App\Settings\OrderSettings::class)->sources[$order->order_source] ?? $order->order_source }}
                            </p>
                        </div>
                    </div>

                    @if ($order->notes)
                        <div class="mt-4 pt-4 border-t border-border">
                            <p class="text-xs font-medium text-foreground-muted uppercase tracking-wider mb-2">
                                {{ __('admin.order_details.order_notes') }}
                            </p>
                            <div
                                class="bg-warning/10 rounded-lg p-3 text-sm text-foreground italic border border-warning/10">
                                "{{ $order->notes }}"
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>