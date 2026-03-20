<div class="animate-fade-in">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="font-display text-2xl font-semibold text-foreground">{{ __('admin.orders.title') }}</h2>
            <p class="mt-1 text-sm text-foreground-muted">{{ __('admin.orders.subtitle') }}</p>
        </div>
        @can('create orders')
            <a href="{{ route('admin.orders.create') }}" wire:navigate
                class="btn-base bg-primary text-primary-foreground hover:bg-primary-hover">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                {{ __('admin.orders.create_order') }}
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="mb-4 flex items-center gap-2 rounded-xl bg-success-bg px-4 py-3 text-sm text-success"
            x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition>
            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-4 grid grid-cols-2 gap-3 sm:flex sm:items-center sm:gap-4">
        <div class="col-span-2 sm:w-72">
            <input wire:model.live.debounce.300ms="search" type="text"
                placeholder="{{ __('admin.orders.search_placeholder') }}" class="input-base" />
        </div>

        <select wire:model.live="filter_status" class="select-base col-span-1 sm:w-40">
            <option value="">{{ __('admin.orders.all_statuses') }}</option>
            @foreach(settings(\App\Settings\OrderSettings::class)->statuses as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </select>

        <select wire:model.live="filter_fulfillment" class="select-base col-span-1 sm:w-40">
            <option value="">{{ __('admin.orders.all_fulfillment') }}</option>
            @foreach(settings(\App\Settings\FulfillmentSettings::class)->types as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </select>

        @if ($search || $filter_status || $filter_fulfillment)
            <button wire:click="resetFilters"
                class="col-span-2 sm:col-span-1 btn-base bg-surface-alt text-foreground hover:bg-surface-alt/80 sm:w-auto">
                {{ __('admin.orders.reset') }}
            </button>
        @endif
    </div>

    <div class="card-base overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-border bg-surface-alt">
                        <th class="table-header px-6 py-3.5">{{ __('admin.orders.order_id') }}</th>
                        <th class="table-header px-6 py-3.5">{{ __('admin.orders.customer') }}</th>
                        <th class="table-header px-6 py-3.5">{{ __('admin.orders.total') }}</th>
                        <th class="table-header px-6 py-3.5">{{ __('admin.orders.scheduled_for') }}</th>
                        <th class="table-header px-6 py-3.5">{{ __('admin.orders.fulfillment') }}</th>
                        <th class="table-header px-6 py-3.5">{{ __('admin.orders.status') }}</th>
                        <th class="table-header px-6 py-3.5 text-end">{{ __('admin.orders.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($orders as $order)
                                        <tr wire:key="order-{{ $order->id }}"
                                            class="hover:bg-surface-alt/50 transition-colors duration-150">
                                            <td class="px-6 py-4 text-sm font-medium text-foreground">#{{ $order->id }}</td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-foreground">{{ $order->customer_name }}</div>
                                                <div class="text-sm text-foreground-muted">{{ $order->customer_phone }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-foreground-muted">
                                                {{ settings(\App\Settings\CurrencySettings::class)->currency_symbol }}{{ number_format($order->total_price, 2) }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-foreground-muted">
                                                {{ $order->scheduled_at->format('M d, Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-foreground-muted capitalize">
                                                {{ $order->fulfillment_type }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                                                                                                                                                                {{ match ($order->status) {
                            'pending' => 'bg-warning/10 text-warning',
                            'confirmed' => 'bg-info/10 text-info',
                            'paid' => 'bg-success/10 text-success',
                            'in_progress' => 'bg-primary/10 text-primary',
                            'completed' => 'bg-success text-white',
                            'cancelled' => 'bg-danger/10 text-danger',
                            default => 'bg-surface-alt text-foreground-muted'
                        } }}">
                                                    {{ settings(\App\Settings\OrderSettings::class)->statuses[$order->status] ?? $order->status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-end">
                                                <div class="flex items-center justify-end gap-1">
                                                    <a href="{{ route('admin.orders.show', $order) }}" wire:navigate
                                                        class="rounded-xl p-2 text-foreground-muted hover:bg-surface-alt hover:text-foreground transition-colors cursor-pointer">
                                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                            stroke-width="1.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                        </svg>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-foreground-muted">
                                {{ __('admin.orders.no_orders') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($orders->hasPages())
            <div class="border-t border-border px-6 py-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>