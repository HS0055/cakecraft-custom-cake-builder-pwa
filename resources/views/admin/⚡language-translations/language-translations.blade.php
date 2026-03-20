<div class="animate-fade-in">
    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.languages') }}" wire:navigate class="text-foreground-muted hover:text-foreground transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </a>
                <h2 class="font-display text-2xl font-semibold text-foreground">
                    {{ __('admin.language_translations.title', ['language' => $targetLanguage->name, 'code' => strtoupper($targetLanguage->code)]) }}
                </h2>
            </div>
            <p class="text-sm text-foreground-muted">{{ __('admin.language_translations.subtitle') }}</p>
        </div>
        <div class="flex flex-col sm:flex-row items-center gap-2">

            <button wire:click="importFromFiles" wire:loading.attr="disabled" class="btn-base bg-surface-alt text-foreground hover:bg-surface-alt/80 border border-border">
                <svg wire:loading.remove wire:target="importFromFiles" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                </svg>
                <svg wire:loading wire:target="importFromFiles" class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ __('admin.language_translations.import_files') }}
            </button>
            <button wire:click="openCreateModal" class="btn-base bg-primary text-primary-foreground hover:bg-primary-hover">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                {{ __('admin.language_translations.add_new_key') }}
            </button>
        </div>
    </div>

    {{-- Flash --}}
    @if (session('success'))
        <div wire:key="flash-{{ \Illuminate\Support\Str::random() }}" class="mb-4 flex items-center gap-2 rounded-xl bg-success-bg px-4 py-3 text-sm text-success"
            x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition>
            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Search --}}
    <div class="mb-4">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="{{ __('admin.language_translations.search_translations') }}"
            class="input-base w-full sm:max-w-sm" />
    </div>

    {{-- Content --}}
    <div class="card-base overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-border bg-surface-alt">
                        <th class="table-header px-6 py-3.5 text-start w-1/4">{{ __('admin.language_translations.table_group_key') }}</th>
                        <th class="table-header px-6 py-3.5 text-start w-1/3">{{ __('admin.language_translations.table_english') }}</th>
                        <th class="table-header px-6 py-3.5 text-start w-1/3">{{ $targetLanguage->name }} {{ __('admin.language_translations.table_translation') }}</th>
                        <th class="table-header px-6 py-3.5 text-end">{{ __('admin.common.delete') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($lines as $line)
                        @php
                            $enText = $line->text['en'] ?? '';
                        @endphp
                        <tr class="hover:bg-surface-alt/50 transition-colors duration-150" wire:key="line-{{ $line->id }}">
                            <td class="px-6 py-4 align-top">
                                <div class="flex flex-col gap-1">
                                    <span class="inline-flex w-fit items-center rounded-md bg-surface-alt px-2 py-0.5 text-xs font-medium text-foreground-muted ring-1 ring-inset ring-border">
                                        {{ $line->group }}
                                    </span>
                                    <span class="text-sm font-medium text-foreground font-mono">{{ $line->key }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <div class="text-sm text-foreground-muted whitespace-pre-wrap">{{ $enText ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <div class="relative">
                                    <textarea 
                                        wire:model="translations.{{ $line->id }}"
                                        wire:blur="saveTranslation({{ $line->id }})"
                                        wire:keyup.enter.prevent="saveTranslation({{ $line->id }})"
                                        class="input-base w-full min-h-[60px] resize-y py-2 {{ empty($translations[$line->id]) ? 'border-dashed border-danger-bg bg-danger-bg/20 focus:bg-surface focus:border-primary' : '' }}"
                                        placeholder="{{ __('admin.language_translations.enter_translation') }}"
                                        {{ $targetLanguage->code === 'ar' ? 'dir=rtl' : '' }}
                                    ></textarea>

                                    @if(empty($translations[$line->id]))
                                        <div class="absolute bottom-2 end-2 flex items-center gap-1 text-[10px] items-center text-danger font-medium px-2 py-0.5 rounded bg-danger-bg pointer-events-none">
                                            <span class="w-1.5 h-1.5 rounded-full bg-danger animate-pulse"></span> {{ __('admin.language_translations.missing') }}
                                        </div>
                                    @endif

                                    <div wire:loading wire:target="saveTranslation({{ $line->id }})" class="absolute end-2 top-2">
                                        <svg class="h-4 w-4 animate-spin text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 align-top text-end">
                                <button wire:click="confirmDelete({{ $line->id }})"
                                    class="rounded-xl p-2 text-foreground-muted hover:bg-danger-bg hover:text-danger transition-colors cursor-pointer"
                                    title="{{ __('admin.language_translations.delete_key_title') }}">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-sm text-foreground-muted">
                                <svg class="mx-auto h-10 w-10 text-foreground-subtle mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m10.5 21 5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 0 1 6-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.502m9.334-12.138c.896.061 1.785.147 2.666.257m-4.589 8.495a18.023 18.023 0 0 1-3.827-5.802" />
                                </svg>
                                {{ __('admin.language_translations.no_translations') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($lines->hasPages())
            <div class="border-t border-border px-6 py-4">
                {{ $lines->links() }}
            </div>
        @endif
    </div>

    {{-- Create Key Modal --}}
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data
            x-init="$el.querySelector('input[type=text]')?.focus()">
            <div class="fixed inset-0 bg-espresso/40 backdrop-blur-sm" wire:click="$set('showCreateModal', false)"></div>

            <div class="relative w-full max-w-lg rounded-2xl bg-surface p-6 shadow-modal animate-scale-in">

                <div class="flex items-center justify-between p-6 border-b border-border">
                    <h3 class="font-display text-lg font-semibold text-foreground">
                        {{ __('admin.language_translations.add_new_title') }}
                    </h3>
                    <button wire:click="$set('showCreateModal', false)" class="text-foreground-muted hover:text-foreground transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="saveNewKey" class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-foreground">{{ __('admin.language_translations.group_label') }}</label>
                            <input type="text" wire:model="newGroup" placeholder="e.g. front" class="input-base">
                            @error('newGroup') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-foreground">{{ __('admin.language_translations.key_name_label') }}</label>
                            <input type="text" wire:model="newKey" placeholder="e.g. welcome_message" class="input-base">
                            @error('newKey') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-foreground">{{ __('admin.language_translations.english_text_label') }}</label>
                        <textarea wire:model="newEnglishText" placeholder="Enter English text..." class="input-base min-h-[80px]"></textarea>
                        @error('newEnglishText') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-foreground">{{ __('admin.language_translations.translation_optional_label', ['language' => $targetLanguage->name]) }}</label>
                        <textarea wire:model="newTargetText" placeholder="{{ __('admin.language_translations.enter_translation') }}" class="input-base min-h-[80px]" {{ $targetLanguage->code === 'ar' ? 'dir=rtl' : '' }}></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-border mt-6">
                        <button type="button" wire:click="$set('showCreateModal', false)"
                            class="btn-base border border-border bg-surface text-foreground hover:bg-surface-alt">
                            {{ __('admin.common.cancel') }}
                        </button>
                        <button type="submit" class="btn-base bg-primary text-primary-foreground hover:bg-primary-hover">
                            {{ __('admin.language_translations.add_button') }}
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
                    {{ __('admin.language_translations.delete_key_title') }}
                </h3>
                <p class="mt-2 text-center text-sm text-foreground-muted">{{ __('admin.language_translations.delete_key_confirm') }}</p>
                <div class="mt-6 flex gap-3">
                    <button wire:click="$set('showDeleteModal', false)"
                        class="btn-base flex-1 border border-border bg-surface text-foreground hover:bg-surface-alt">
                        {{ __('admin.common.cancel') }}
                    </button>
                    <button wire:click="deleteTranslation" class="btn-base flex-1 bg-danger text-white hover:opacity-90">
                        {{ __('admin.common.delete') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
