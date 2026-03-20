<div class="animate-fade-in">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="font-display text-2xl font-semibold text-foreground">{{ __('admin.cake_colors.title') }}</h2>
            <p class="mt-1 text-sm text-foreground-muted">{{ __('admin.cake_colors.subtitle') }}</p>
        </div>
        @can('create colors')
            <button wire:click="openCreate" class="btn-base bg-primary text-primary-foreground hover:bg-primary-hover">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                {{ __('admin.cake_colors.add_color') }}
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

    <div class="mb-4">
        <input wire:model.live.debounce.300ms="search" type="text"
            placeholder="{{ __('admin.cake_colors.search_placeholder') }}" class="input-base w-full sm:max-w-sm" />
    </div>

    <div class="card-base overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-border bg-surface-alt">
                        <th class="table-header px-6 py-3.5">{{ __('admin.cake_colors.table_swatch') }}</th>
                        <th class="table-header px-6 py-3.5">{{ __('admin.cake_colors.table_name') }}</th>
                        <th class="table-header px-6 py-3.5">{{ __('admin.cake_colors.table_hex') }}</th>
                        <th class="table-header px-6 py-3.5 text-end">{{ __('admin.common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($colors as $color)
                        <tr wire:key="color-{{ $color->id }}"
                            class="hover:bg-surface-alt/50 transition-colors duration-150">
                            <td class="px-6 py-4">
                                <div class="h-10 w-10 rounded-xl border border-border"
                                    style="background-color: {{ $color->hex_code }}"></div>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-foreground">{{ $color->name }}</td>
                            <td class="px-6 py-4 text-sm text-foreground-muted font-mono">{{ $color->hex_code }}</td>
                            <td class="px-6 py-4 text-end">
                                <div class="flex items-center justify-end gap-1">
                                    @can('update colors')
                                        <button wire:click="openEdit({{ $color->id }})"
                                            class="rounded-xl p-2 text-foreground-muted hover:bg-surface-alt hover:text-foreground transition-colors cursor-pointer"
                                            title="{{ __('admin.common.edit') }}">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </button>
                                    @endcan
                                    @can('delete colors')
                                        <button wire:click="confirmDelete({{ $color->id }})"
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
                            <td colspan="4" class="px-6 py-12 text-center text-sm text-foreground-muted">
                                {{ __('admin.cake_colors.no_colors') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($colors->hasPages())
            <div class="border-t border-border px-6 py-4">{{ $colors->links() }}</div>
        @endif
    </div>

    {{-- Modal --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-espresso/40 backdrop-blur-sm" wire:click="$set('showModal', false)"></div>
            <div class="relative w-full max-w-md rounded-2xl bg-surface p-6 shadow-modal animate-scale-in">
                <h3 class="font-display text-lg font-semibold text-foreground mb-5">
                    {{ $editingId ? __('admin.cake_colors.edit_title') : __('admin.cake_colors.create_title') }}
                </h3>
                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label
                            class="mb-1.5 block text-sm font-medium text-foreground">{{ __('admin.cake_colors.name_label') }}</label>
                        <input wire:model="name" type="text" class="input-base"
                            placeholder="{{ __('admin.cake_colors.name_placeholder') }}" />
                        @error('name') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label
                            class="mb-1.5 block text-sm font-medium text-foreground">{{ __('admin.cake_colors.hex_code_label') }}</label>
                        <div class="flex items-center gap-3">
                            <input wire:model.live="hex_code" type="color"
                                class="h-10 w-14 cursor-pointer rounded-xl border border-border p-1" />
                            <input wire:model.live="hex_code" type="text" class="input-base font-mono"
                                placeholder="{{ __('admin.cake_colors.hex_code_placeholder') }}" />
                        </div>
                        @error('hex_code') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="$set('showModal', false)"
                            class="btn-base border border-border bg-surface text-foreground hover:bg-surface-alt">{{ __('admin.common.cancel') }}</button>
                        <button type="submit" class="btn-base bg-primary text-primary-foreground hover:bg-primary-hover"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove
                                wire:target="save">{{ $editingId ? __('admin.common.update') : __('admin.common.create') }}</span>
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
                <h3 class="text-center font-display text-lg font-semibold text-foreground">
                    {{ __('admin.cake_colors.delete_title') }}
                </h3>
                <p class="mt-2 text-center text-sm text-foreground-muted">{{ __('admin.cake_colors.delete_message') }}</p>
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