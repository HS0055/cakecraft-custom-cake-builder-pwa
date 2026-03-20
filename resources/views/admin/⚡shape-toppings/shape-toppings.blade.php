<div class="animate-fade-in">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="font-display text-2xl font-semibold text-foreground">{{ __('admin.shape_toppings.title') }}</h2>
            <p class="mt-1 text-sm text-foreground-muted">{{ __('admin.shape_toppings.subtitle') }}</p>
        </div>
        @can('update shapes')
            <button wire:click="openCreate" class="btn-base bg-primary text-primary-foreground hover:bg-primary-hover">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                {{ __('admin.shape_toppings.add_combo') }}
            </button>
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

    <div class="mb-4 flex flex-col gap-4 sm:flex-row">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="{{ __('admin.shape_toppings.search_placeholder') }}"
            class="input-base w-full sm:max-w-sm" />
        <select wire:model.live="filter_shape_id" class="select-base w-full sm:max-w-xs">
            <option value="">{{ __('admin.shape_toppings.all_shapes') }}</option>
            @foreach ($shapes as $shape)
                <option value="{{ $shape->id }}">{{ $shape->name }}</option>
            @endforeach
        </select>
        <select wire:model.live="filter_topping_category_id" class="select-base w-full sm:max-w-xs">
            <option value="">{{ __('admin.shape_toppings.all_categories') }}</option>
            @foreach ($toppingCategories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
        <select wire:model.live="filter_topping_id" class="select-base w-full sm:max-w-xs" @if (!$filter_topping_category_id) disabled @endif>
            <option value="">{{ $filter_topping_category_id ? __('admin.shape_toppings.all_toppings') : __('admin.shape_toppings.select_category_first') }}</option>
            @foreach ($toppings as $topping)
                <option value="{{ $topping->id }}">{{ $topping->name }}</option>
            @endforeach
        </select>
        @if ($search || $filter_shape_id || $filter_topping_category_id || $filter_topping_id)
            <button wire:click="resetFilters" class="btn-base bg-surface-alt text-foreground hover:bg-surface-alt/80">
                {{ __('admin.common.reset') }}
            </button>
        @endif
    </div>

    <div class="card-base overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-border bg-surface-alt">
                        <th class="table-header px-6 py-3.5">{{ __('admin.shape_toppings.table_shape') }}</th>
                        <th class="table-header px-6 py-3.5">{{ __('admin.shape_toppings.table_topping') }}</th>
                        <th class="table-header px-6 py-3.5">{{ __('admin.shape_toppings.table_price') }}</th>
                        <th class="table-header px-6 py-3.5">{{ __('admin.shape_toppings.table_layer') }}</th>
                        <th class="table-header px-6 py-3.5 text-end">{{ __('admin.common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($combos as $combo)
                        <tr wire:key="st-{{ $combo->id }}" class="hover:bg-surface-alt/50 transition-colors duration-150">
                            <td class="px-6 py-4 text-sm font-medium text-foreground">{{ $combo->shape->name ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-foreground">{{ $combo->topping->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-foreground-muted">
                                {{ settings(\App\Settings\CurrencySettings::class)->currency_symbol }}{{ number_format($combo->price, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="relative group w-max">
                                    @if ($url = $combo->getFirstMediaUrl('image_layer'))
                                        <img src="{{ $url }}" alt="Layer"
                                            class="h-10 w-10 rounded-xl object-contain border border-border bg-checkerboard" />
                                    @else
                                        <div
                                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-surface-alt text-foreground-subtle border border-border">
                                            <svg class="h-5 w-5 opacity-50" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M18 13.5V18a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-4.5" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-end">
                                <div class="flex items-center justify-end gap-1">
                                    @can('update shapes')
                                        <button wire:click="openEdit({{ $combo->id }})"
                                            class="rounded-xl p-2 text-foreground-muted hover:bg-surface-alt hover:text-foreground transition-colors cursor-pointer"
                                            title="{{ __('admin.common.edit') }}">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </button>
                                        <button wire:click="confirmDelete({{ $combo->id }})"
                                            class="rounded-xl p-2 text-foreground-muted hover:bg-danger-bg hover:text-danger transition-colors cursor-pointer"
                                            title="{{ __('admin.common.delete') }}">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-foreground-muted">
                                {{ __('admin.shape_toppings.no_combos') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($combos->hasPages())
            <div class="border-t border-border px-6 py-4">{{ $combos->links() }}</div>
        @endif
    </div>

    {{-- Modal --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-espresso/40 backdrop-blur-sm" wire:click="$set('showModal', false)"></div>
            <div
                class="relative w-full max-w-lg rounded-2xl bg-surface p-6 shadow-modal animate-scale-in max-h-[90vh] overflow-y-auto">
                <h3 class="font-display text-lg font-semibold text-foreground mb-5">
                    {{ $editingId ? __('admin.shape_toppings.edit_title') : __('admin.shape_toppings.create_title') }}
                </h3>
                <form wire:submit="save" class="space-y-4">
                    @php
                        $editingModel = $editingId ? $combos->firstWhere('id', $editingId) : null;
                    @endphp
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-foreground">{{ __('admin.shape_toppings.shape_label') }}</label>
                            <select wire:model="cake_shape_id" class="select-base">
                                <option value="">{{ __('admin.shape_toppings.shape_placeholder') }}</option>
                                @foreach ($shapes as $shape)
                                    <option value="{{ $shape->id }}">{{ $shape->name }}</option>
                                @endforeach
                            </select>
                            @error('cake_shape_id') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-foreground">{{ __('admin.shape_toppings.topping_label') }}</label>
                            <select wire:model="cake_topping_id" class="select-base">
                                <option value="">{{ __('admin.shape_toppings.topping_placeholder') }}</option>
                                @foreach ($toppings as $topping)
                                    <option value="{{ $topping->id }}">{{ $topping->name }}</option>
                                @endforeach
                            </select>
                            @error('cake_topping_id') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-foreground">{{ __('admin.shape_toppings.price_label', ['currency' => settings(\App\Settings\CurrencySettings::class)->currency_symbol]) }}</label>
                        <input wire:model="price" type="number" step="0.01" min="0" class="input-base" />
                        @error('price') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-media-upload model="image_layer" label="{{ __('admin.shape_toppings.image_layer_label') }}" hint="{{ __('admin.shape_toppings.image_layer_hint') }}"
                            :preview="$editingModel?->getFirstMediaUrl('image_layer')" remove-signal="delete_image_layer" />
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="$set('showModal', false)"
                            class="btn-base border border-border bg-surface text-foreground hover:bg-surface-alt">{{ __('admin.common.cancel') }}</button>
                        <button type="submit" class="btn-base bg-primary text-primary-foreground hover:bg-primary-hover" wire:loading.attr="disabled">
                            <span wire:loading.remove>{{ $editingId ? __('admin.common.update') : __('admin.common.create') }}</span>
                            <span wire:loading class="flex items-center gap-2 text-espresso">
                                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Delete Modal --}}
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-espresso/40 backdrop-blur-sm" wire:click="$set('showDeleteModal', false)"></div>
            <div class="relative w-full max-w-sm rounded-2xl bg-surface p-6 shadow-modal animate-scale-in">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-danger-bg text-danger mx-auto">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                </div>
                <h3 class="text-center font-display text-lg font-semibold text-foreground">{{ __('admin.shape_toppings.delete_title') }}</h3>
                <p class="mt-2 text-center text-sm text-foreground-muted">{{ __('admin.shape_toppings.delete_message') }}</p>
                <div class="mt-6 flex gap-3">
                    <button wire:click="$set('showDeleteModal', false)"
                        class="btn-base flex-1 border border-border bg-surface text-foreground hover:bg-surface-alt">{{ __('admin.common.cancel') }}</button>
                    <button wire:click="delete"
                        class="btn-base flex-1 bg-danger text-white hover:opacity-90">{{ __('admin.common.delete') }}</button>
                </div>
            </div>
        </div>
    @endif
</div>