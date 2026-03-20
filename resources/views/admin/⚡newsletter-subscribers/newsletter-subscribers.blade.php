<div class="animate-fade-in">
    {{-- Page header with actions --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="font-display text-2xl font-semibold text-foreground">
                {{ __('admin.newsletter_subscribers.title') }}
            </h2>
            <p class="mt-1 text-sm text-foreground-muted">{{ __('admin.newsletter_subscribers.subtitle') }}</p>
        </div>
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

    {{-- Search --}}
    <div class="mb-4">
        <input wire:model.live.debounce.300ms="search" type="text"
            placeholder="{{ __('admin.newsletter_subscribers.search_placeholder') }}"
            class="input-base w-full sm:max-w-sm" />
    </div>

    {{-- Table --}}
    <div class="card-base overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-border bg-surface-alt">
                        <th class="table-header px-6 py-3.5 min-w-[50px]">
                            {{ __('admin.newsletter_subscribers.table_id') }}
                        </th>
                        <th class="table-header px-6 py-3.5 min-w-[250px]">
                            {{ __('admin.newsletter_subscribers.table_email') }}
                        </th>
                        <th class="table-header px-6 py-3.5 min-w-[150px]">
                            {{ __('admin.newsletter_subscribers.table_subscribed_on') }}
                        </th>
                        <th class="table-header px-6 py-3.5 text-end min-w-[100px]">{{ __('admin.common.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($this->subscribers as $subscriber)
                        <tr wire:key="subscriber-{{ $subscriber->id }}"
                            class="hover:bg-surface-alt/50 transition-colors duration-150">
                            <td class="px-6 py-4 text-sm font-medium text-foreground">{{ $subscriber->id }}</td>
                            <td class="px-6 py-4 text-sm text-foreground">{{ $subscriber->email }}</td>
                            <td class="px-6 py-4 text-sm text-foreground-muted">
                                {{ $subscriber->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-end">
                                <div class="flex items-center justify-end gap-1">
                                    @can('delete newsletter subscribers')
                                        <button wire:click="confirmDelete({{ $subscriber->id }})"
                                            class="rounded-xl p-2 text-foreground-muted hover:bg-danger-bg hover:text-danger transition-colors cursor-pointer"
                                            title="{{ __('admin.newsletter_subscribers.remove') }}">
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
                                <svg class="mx-auto h-10 w-10 text-foreground-subtle mb-3" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                </svg>
                                {{ __('admin.newsletter_subscribers.no_subscribers') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($this->subscribers->hasPages())
            <div class="border-t border-border px-6 py-4">
                {{ $this->subscribers->links() }}
            </div>
        @endif
    </div>

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
                    {{ __('admin.newsletter_subscribers.delete_title') }}
                </h3>
                <p class="mt-2 text-center text-sm text-foreground-muted">
                    {{ __('admin.newsletter_subscribers.delete_message') }}
                </p>
                <div class="mt-6 flex gap-3">
                    <button wire:click="$set('showDeleteModal', false)"
                        class="btn-base flex-1 border border-border bg-surface text-foreground hover:bg-surface-alt">
                        {{ __('admin.common.cancel') }}
                    </button>
                    <button wire:click="delete" class="btn-base flex-1 bg-danger text-white hover:opacity-90">
                        {{ __('admin.newsletter_subscribers.remove') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>