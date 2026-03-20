<div class="animate-fade-in space-y-8">
    {{-- Header --}}
    <div class="flex flex-col gap-1">
        <h2 class="font-display text-2xl md:text-3xl font-bold text-foreground tracking-tight">
            {{ __('admin.dashboard.title') }}
        </h2>
        <p class="text-sm text-foreground-muted">{{ __('admin.dashboard.subtitle') }}</p>
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

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Total Revenue --}}
        <div wire:click="$refresh"
            class="card-base p-6 flex flex-col justify-between h-32 relative overflow-hidden group hover:border-primary/20 transition-colors">
            <div class="absolute top-0 end-0 p-4 opacity-20 group-hover:opacity-40 transition-opacity">
                <svg class="h-16 w-16 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-sm font-medium text-foreground-muted z-10">{{ __('admin.dashboard.total_revenue') }}</p>
            <div>
                <p class="text-2xl font-bold text-foreground z-10">
                    {{ settings(\App\Settings\CurrencySettings::class)->currency_symbol }}{{ number_format($this->totalRevenue(), 2) }}
                </p>
                <p class="text-xs text-success mt-1 flex items-center gap-1 z-10">
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    {{ settings(\App\Settings\CurrencySettings::class)->currency_symbol }}{{ number_format($this->currentMonthRevenue(), 2) }}
                    {{ __('admin.dashboard.this_month') }}
                </p>
            </div>
        </div>

        {{-- Total Orders --}}
        <div
            class="card-base p-6 flex flex-col justify-between h-32 relative overflow-hidden group hover:border-accent/20 transition-colors">
            <div class="absolute top-0 end-0 p-4 opacity-20 group-hover:opacity-40 transition-opacity">
                <svg class="h-16 w-16 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
            <p class="text-sm font-medium text-foreground-muted z-10">{{ __('admin.dashboard.total_orders') }}</p>
            <div>
                <div class="flex items-baseline gap-2">
                    <p class="text-2xl font-bold text-foreground z-10">{{ $this->totalOrders() }}</p>
                    <span class="text-xs text-foreground-muted">{{ __('admin.dashboard.orders') }}</span>
                </div>
                <p class="text-xs text-foreground-muted mt-1 z-10">{{ __('admin.dashboard.all_time') }}</p>
            </div>
        </div>

        {{-- Pending Orders --}}
        <div
            class="card-base p-6 flex flex-col justify-between h-32 relative overflow-hidden group hover:border-warning/20 transition-colors">
            <div class="absolute top-0 end-0 p-4 opacity-20 group-hover:opacity-40 transition-opacity">
                <svg class="h-16 w-16 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-sm font-medium text-foreground-muted z-10">{{ __('admin.dashboard.pending_orders') }}</p>
            <div>
                <div class="flex items-baseline gap-2">
                    <p class="text-2xl font-bold text-foreground z-10">{{ $this->pendingOrders() }}</p>
                    <span class="text-xs text-foreground-muted">{{ __('admin.dashboard.pending') }}</span>
                </div>
                <p class="text-xs text-warning mt-1 z-10">{{ __('admin.dashboard.needs_attention') }}</p>
            </div>
        </div>

        {{-- Products --}}
        <div
            class="card-base p-6 flex flex-col justify-between h-32 relative overflow-hidden group hover:border-info/20 transition-colors">
            <div class="absolute top-0 end-0 p-4 opacity-20 group-hover:opacity-40 transition-opacity">
                <svg class="h-16 w-16 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
            </div>
            <p class="text-sm font-medium text-foreground-muted z-10">{{ __('admin.dashboard.products') }}</p>
            <div>
                <div class="flex items-baseline gap-2">
                    <p class="text-2xl font-bold text-foreground z-10">{{ \App\Models\ReadyCake::count() }}</p>
                    <span class="text-xs text-foreground-muted">{{ __('admin.dashboard.cakes') }}</span>
                </div>
                <p class="text-xs text-foreground-muted mt-1 z-10">
                    {{ __('admin.dashboard.shapes_flavors', ['shapes' => $shapesCount, 'flavors' => $flavorsCount]) }}
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Sales Chart --}}
        <div class="lg:col-span-2 card-base p-6">
            <h3 class="font-display text-lg font-semibold text-foreground mb-6">
                {{ __('admin.dashboard.sales_overview') }}
            </h3>
            <div class="h-[300px] w-full" wire:ignore>
                <canvas id="salesChart" data-labels='@json($this->chartMap()["labels"])'
                    data-data='@json($this->chartMap()["data"])' data-color='{{ $this->chartColorTheme()["hex"] }}'
                    data-color-rgb='{{ $this->chartColorTheme()["rgb"] }}'
                    data-currency-symbol='{{ settings(\App\Settings\CurrencySettings::class)->currency_symbol }}'></canvas>
            </div>
        </div>

        {{-- Order Types Widget --}}
        <div class="card-base p-6">
            <h3 class="font-display text-lg font-semibold text-foreground mb-6">{{ __('admin.dashboard.sales_types') }}
            </h3>

            @if($this->cakeStats['ready'] === 0 && $this->cakeStats['custom'] === 0)
                <div class="h-[300px] w-full flex items-center justify-center">
                    <p class="text-foreground-muted text-sm">{{ __('admin.dashboard.no_cake_sales') }}</p>
                </div>
            @else
                <div class="h-[300px] w-full" wire:ignore>
                    <canvas id="typeChart" data-ready="{{ $this->cakeStats['ready'] }}"
                        data-custom="{{ $this->cakeStats['custom'] }}"
                        data-color="{{ settings(\App\Settings\AppearanceSettings::class)->primary_color }}"></canvas>
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        {{-- Best Sellers --}}
        <div class="card-base p-6 h-full">
            <h3 class="font-display text-lg font-semibold text-foreground mb-6">{{ __('admin.dashboard.best_sellers') }}
            </h3>
            <div class="space-y-4">
                @forelse($this->bestSellers() as $index => $item)
                    @php $cake = $item->readyCake; @endphp
                    @if($cake)
                        <div class="flex items-center gap-4 group">
                            <span
                                class="flex h-6 w-6 items-center justify-center rounded-full bg-surface-alt text-xs font-bold text-foreground-muted shrink-0">
                                {{ $index + 1 }}
                            </span>
                            <div class="h-10 w-10 rounded-lg bg-surface-alt border border-border overflow-hidden shrink-0">
                                @if ($cake->getFirstMediaUrl('preview'))
                                    <img src="{{ $cake->getFirstMediaUrl('preview') }}" alt="{{ $cake->name }}"
                                        class="h-full w-full object-cover">
                                @elseif ($cake->cakeShape)
                                    @php
                                        $toppingLayers = collect();
                                        if ($cake->cake_shape_id && $cake->cake_topping_id) {
                                            $toppingLayers = \App\Models\ShapeTopping::with('media')
                                                ->where('cake_shape_id', $cake->cake_shape_id)
                                                ->where('cake_topping_id', $cake->cake_topping_id)
                                                ->get();
                                        }
                                    @endphp
                                    <x-cake-visual class="h-full w-full" :shape="$cake->cakeShape" :flavorLayer="$cake->cakeFlavor"
                                        :color="$cake->cakeColor" :toppingLayers="$toppingLayers" mode="final" />
                                @else
                                    <div class="flex h-full w-full items-center justify-center text-foreground-subtle">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4
                                    class="text-sm font-medium text-foreground truncate group-hover:text-primary transition-colors">
                                    <a href="{{ route('admin.ready-cakes.edit', $cake) }}" wire:navigate>{{ $cake->name }}</a>
                                </h4>
                            </div>
                            <div class="text-sm font-bold text-foreground">
                                {{ $item->total_sold }} {{ __('admin.dashboard.sold') }}
                            </div>
                        </div>
                    @endif
                @empty
                    <p class="text-sm text-foreground-muted text-center py-4">{{ __('admin.dashboard.no_sales') }}</p>
                @endforelse
            </div>
        </div>

        {{-- Latest Orders --}}
        <div class="lg:col-span-2 card-base overflow-hidden h-full">
            <div class="p-6 border-b border-border">
                <h3 class="font-display text-lg font-semibold text-foreground">{{ __('admin.dashboard.latest_orders') }}
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr
                            class="bg-surface-alt text-start text-xs font-semibold text-foreground-muted uppercase tracking-wider">
                            <th class="px-6 py-3">{{ __('admin.dashboard.order_id') }}</th>
                            <th class="px-6 py-3">{{ __('admin.dashboard.customer') }}</th>
                            <th class="px-6 py-3">{{ __('admin.dashboard.date') }}</th>
                            <th class="px-6 py-3">{{ __('admin.dashboard.status') }}</th>
                            <th class="px-6 py-3 text-end">{{ __('admin.dashboard.total') }}</th>
                            <th class="px-6 py-3 text-end">{{ __('admin.dashboard.action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($this->latestOrders() as $order)
                            <tr class="hover:bg-surface-alt/50 transition-colors">
                                <td class="px-6 py-4 text-sm font-medium text-foreground">#{{ $order->id }}</td>
                                <td class="px-6 py-4 text-sm text-foreground">
                                    <div>{{ $order->customer_name }}</div>
                                    <div class="text-xs text-foreground-muted">{{ $order->customer_phone }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-foreground-muted">
                                    {{ $order->created_at->format('M d, H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-warning-bg text-warning',
                                            'confirmed' => 'bg-info-bg text-info',
                                            'paid' => 'bg-success-bg text-success',
                                            'in_progress' => 'bg-primary-bg text-primary',
                                            'completed' => 'bg-success-bg text-success',
                                            'cancelled' => 'bg-danger-bg text-danger',
                                        ];
                                        $color = $statusColors[$order->status] ?? 'bg-surface-alt text-foreground-muted';
                                    @endphp
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $color }}">
                                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-foreground text-end">
                                    {{ settings(\App\Settings\CurrencySettings::class)->currency_symbol }}{{ number_format($order->total_price, 2) }}
                                </td>
                                <td class="px-6 py-4 text-end">
                                    <a href="{{ route('admin.orders.edit', $order) }}" wire:navigate
                                        class="inline-flex items-center justify-center rounded-lg p-2 text-foreground-muted hover:text-primary hover:bg-primary/10 transition-colors"
                                        title="{{ __('admin.dashboard.view_order') }}">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-foreground-muted">
                                    {{ __('admin.dashboard.no_orders') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>