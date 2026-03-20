<div class="animate-fade-in">
    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="font-display text-2xl font-semibold text-foreground">{{ __('admin.languages.title') }}</h2>
            <p class="mt-1 text-sm text-foreground-muted">{{ __('admin.languages.subtitle') }}</p>
        </div>
        <button wire:click="create" class="btn-base bg-primary text-primary-foreground hover:bg-primary-hover">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            {{ __('admin.languages.add_language') }}
        </button>
    </div>

    {{-- Flash --}}
    @if (session('success'))
        <div class="mb-4 flex items-center gap-2 rounded-xl bg-success-bg px-4 py-3 text-sm text-success"
            x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition>
            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 flex items-center gap-2 rounded-xl bg-danger-bg px-4 py-3 text-sm text-danger"
            x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition>
            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Search --}}
    <div class="mb-4">
        <input wire:model.live.debounce.300ms="search" type="text"
            placeholder="{{ __('admin.languages.search_languages') }}" class="input-base max-w-sm" />
    </div>

    {{-- Content --}}
    <div class="card-base overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-border bg-surface-alt">
                        <th class="table-header px-6 py-3.5 text-start">{{ __('admin.common.name') }}</th>
                        <th class="table-header px-6 py-3.5 text-start">{{ __('admin.languages.table_code') }}</th>
                        <th class="table-header px-6 py-3.5 text-center">{{ __('admin.common.status') }}</th>
                        <th class="table-header px-6 py-3.5 text-end">{{ __('admin.common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($languagesList as $lang)
                        <tr class="hover:bg-surface-alt/50 transition-colors duration-150" wire:key="lang-{{ $lang->id }}">
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-foreground">{{ $lang->name }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center rounded-md bg-surface-alt px-2 py-0.5 text-xs font-medium text-foreground-muted ring-1 ring-inset ring-border">
                                    {{ strtoupper($lang->code) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button wire:click="toggleActive({{ $lang->id }})"
                                    class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer items-center justify-center rounded-full focus:outline-none transition-colors duration-200 ease-in-out">
                                    <span class="sr-only">Toggle active status</span>
                                    <span aria-hidden="true"
                                        class="pointer-events-none absolute h-full w-full rounded-md bg-transparent"></span>
                                    <span aria-hidden="true"
                                        class="{{ $lang->is_active ? 'bg-primary' : 'bg-surface-alt border border-border' }} pointer-events-none absolute mx-auto h-4 w-9 rounded-full transition-colors duration-200 ease-in-out"></span>
                                    <span aria-hidden="true"
                                        class="{{ $lang->is_active ? 'translate-x-5 rtl:-translate-x-5 border-transparent' : 'translate-x-0 rtl:-translate-x-0 border-border' }} pointer-events-none absolute start-0 inline-block h-5 w-5 transform rounded-full border bg-white shadow ring-0 transition-transform duration-200 ease-in-out"></span>
                                </button>
                            </td>
                            <td class="px-6 py-4 text-end">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.languages.translations', $lang->code) }}" wire:navigate
                                        class="rounded-xl p-2 text-primary hover:bg-primary/10 hover:text-primary-hover transition-colors cursor-pointer"
                                        title="{{ __('admin.languages.manage_translations') }}">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m10.5 21 5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 0 1 6-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.502m9.334-12.138c.896.061 1.785.147 2.666.257m-4.589 8.495a18.023 18.023 0 0 1-3.827-5.802" />
                                        </svg>
                                    </a>

                                    <button wire:click="edit({{ $lang->id }})"
                                        class="rounded-xl p-2 text-foreground-muted hover:bg-surface-alt hover:text-foreground transition-colors cursor-pointer"
                                        title="{{ __('admin.common.edit') }}">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                    </button>

                                    <button wire:click="confirmDelete({{ $lang->id }})"
                                        class="rounded-xl p-2 text-foreground-muted hover:bg-danger-bg hover:text-danger transition-colors cursor-pointer"
                                        title="{{ __('admin.common.delete') }}">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-sm text-foreground-muted">
                                <svg class="mx-auto h-10 w-10 text-foreground-subtle mb-3" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                                </svg>
                                {{ __('admin.common.no_results') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Form Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data
            x-init="$el.querySelector('input[type=text]')?.focus()">
            <div class="fixed inset-0 bg-espresso/40 backdrop-blur-sm" wire:click="$set('showModal', false)"></div>
            <div class="relative w-full max-w-lg rounded-2xl bg-surface p-6 shadow-modal animate-scale-in">
                <h3 class="font-display text-lg font-semibold text-foreground mb-4">
                    {{ $isEditing ? __('admin.languages.edit_language') : __('admin.languages.create_language') }}
                </h3>

                <form wire:submit="save" class="space-y-4">
                    {{-- Name Input --}}
                    <div>
                        <label for="name"
                            class="mb-1.5 block text-sm font-medium text-foreground">{{ __('admin.common.name') }}</label>
                        <input type="text" id="name" wire:model="name" placeholder="e.g. French" class="input-base">
                        @error('name') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                    </div>

                    {{-- Code Input --}}
                    <div>
                        <label for="code"
                            class="mb-1.5 block text-sm font-medium text-foreground">{{ __('admin.languages.language_code') }}</label>
                        <input type="text" id="code" wire:model="code" placeholder="e.g. fr" maxlength="2"
                            class="input-base">
                        <p class="text-xs text-foreground-muted mt-1">{{ __('admin.languages.language_code_hint') }}</p>
                        @error('code') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                    </div>

                    {{-- Active Checkbox --}}
                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" wire:model="is_active" id="is_active"
                            class="h-4 w-4 rounded border-border text-primary focus:ring-primary/20 cursor-pointer" />
                        <label for="is_active"
                            class="text-sm font-medium text-foreground cursor-pointer">{{ __('admin.languages.active_checkbox') }}</label>
                        @error('is_active') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                    </div>

                    {{-- Form Actions --}}
                    <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-border">
                        <button type="button" wire:click="resetForm"
                            class="btn-base border border-border bg-surface text-foreground hover:bg-surface-alt">
                            {{ __('admin.common.cancel') }}
                        </button>
                        <button type="submit" class="btn-base bg-primary text-primary-foreground hover:bg-primary-hover"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove
                                wire:target="save">{{ $isEditing ? __('admin.languages.save_language') : __('admin.languages.create_button') }}</span>
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

    {{-- Delete Confirmation --}}
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
                    {{ __('admin.languages.delete_title') }}
                </h3>
                <p class="mt-2 text-center text-sm text-foreground-muted">{{ __('admin.languages.delete_message') }}</p>
                <div class="mt-6 flex gap-3">
                    <button wire:click="$set('showDeleteModal', false)"
                        class="btn-base flex-1 border border-border bg-surface text-foreground hover:bg-surface-alt">
                        {{ __('admin.common.cancel') }}
                    </button>
                    <button wire:click="delete" class="btn-base flex-1 bg-danger text-white hover:opacity-90">
                        {{ __('admin.common.delete') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>