<div class="animate-fade-in" x-data="{ showForm: @entangle('showModal') }">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="font-display text-2xl font-semibold text-foreground">{{ __('admin.pages.title') }}</h2>
            <p class="mt-1 text-sm text-foreground-muted">{{ __('admin.pages.subtitle') }}</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            @can('create pages')
                <button wire:click="openCreate"
                    class="btn-base w-full sm:w-auto justify-center bg-primary text-primary-foreground hover:bg-primary-hover">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    {{ __('admin.pages.create_page') }}
                </button>
            @endcan
        </div>
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

    <div class="card-base overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-border bg-surface-alt">
                        <th class="table-header px-6 py-3.5 text-start">{{ __('admin.pages.table_info') }}</th>
                        <th class="table-header px-6 py-3.5 text-start">{{ __('admin.pages.table_content_preview') }}
                        </th>
                        <th class="table-header px-6 py-3.5 text-start w-32">{{ __('admin.common.status') }}</th>
                        <th class="table-header px-6 py-3.5 text-end w-24">{{ __('admin.common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($pages as $page)
                        <tr wire:key="page-{{ $page->id }}"
                            class="hover:bg-surface-alt/50 transition-colors duration-150 group">
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-foreground">{{ $page->title }}</p>
                                <p class="text-xs text-foreground-muted mt-0.5 font-mono">/page/{{ $page->slug }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-foreground-muted line-clamp-1 max-w-sm">
                                    {{ strip_tags($page->content) ?: 'No content.' }}
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                <button wire:click="toggleActive({{ $page->id }})"
                                    class="inline-flex cursor-pointer items-center rounded-full px-2.5 py-1 text-xs font-medium transition-colors {{ $page->is_active ? 'bg-success-bg text-success' : 'bg-surface-alt text-foreground-muted' }}">
                                    {{ $page->is_active ? __('admin.common.active') : __('admin.pages.draft') }}
                                </button>
                            </td>
                            <td class="px-6 py-4 text-end">
                                <div class="flex items-center justify-end gap-1">
                                    @can('update pages')
                                        <button wire:click="openEdit({{ $page->id }})"
                                            class="rounded-xl p-2 text-foreground-muted hover:bg-surface-alt hover:text-foreground transition-colors cursor-pointer"
                                            title="{{ __('admin.common.edit') }}">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </button>
                                    @endcan
                                    @can('delete pages')
                                        <button wire:click="confirmDelete({{ $page->id }})"
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
                                {{ __('admin.pages.no_pages') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($pages->hasPages())
            <div class="border-t border-border px-6 py-4">
                {{ $pages->links() }}
            </div>
        @endif
    </div>

    <!-- Edit/Create Overlay Modal -->
    <template x-teleport="body">
        <div x-show="showForm" style="display: none;"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            <!-- Backdrop -->
            <div class="fixed inset-0 bg-espresso/40 backdrop-blur-sm transition-opacity"
                wire:click="$set('showModal', false)"></div>

            <!-- Modal Panel -->
            <div class="relative w-full max-w-4xl max-h-[90vh] overflow-y-auto rounded-2xl bg-surface p-6 shadow-modal transform transition-all"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                <div class="mb-6 flex items-center justify-between">
                    <h3 class="font-display text-xl font-semibold text-foreground">
                        {{ $editingId ? __('admin.pages.edit_page') : __('admin.pages.create_page') }}
                    </h3>
                    <button wire:click="$set('showModal', false)"
                        class="rounded-xl p-2 text-foreground-muted hover:bg-surface-alt hover:text-foreground transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="save" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="mb-1.5 block text-sm font-medium text-foreground">{{ __('admin.pages.page_title') }}</label>
                            <input wire:model="title" type="text" class="input-base" placeholder="e.g., About Us"
                                required />
                            @error('title') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label
                                class="mb-1.5 block text-sm font-medium text-foreground">{{ __('admin.pages.url_slug') }}</label>
                            <input wire:model="slug" type="text" class="input-base"
                                placeholder="{{ __('admin.pages.url_slug_hint') }}" />
                            @error('slug') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label
                                class="block text-sm font-medium text-foreground">{{ __('admin.pages.page_content') }}</label>
                            <span class="text-xs text-foreground-muted">{{ __('admin.pages.page_content_hint') }}</span>
                        </div>

                        <div wire:ignore x-data="{
                                content: @entangle('content'),
                                init() {
                                    this.$watch('showForm', (value) => {
                                        if (value) {
                                            this.$refs.trix.editor.loadHTML(this.content || '');
                                        }
                                    });
                                    this.$refs.trix.addEventListener('trix-change', (e) => {
                                        this.content = e.target.value;
                                    });
                                }
                            }">
                            <input id="page_content_trix" type="hidden" name="content">
                            <trix-editor x-ref="trix" input="page_content_trix"
                                class="trix-content prose max-w-none bg-surface border border-border rounded-xl min-h-[300px]"></trix-editor>
                        </div>
                        <style>
                            trix-toolbar [data-trix-button-group="file-tools"] {
                                display: none;
                            }
                        </style>

                        @error('content') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-2 pt-2 pb-4 border-b border-border">
                        <input type="checkbox" wire:model="is_active" id="is_active_page"
                            class="h-5 w-5 rounded border-border text-primary focus:ring-primary/20 cursor-pointer" />
                        <label for="is_active_page" class="text-sm font-medium text-foreground cursor-pointer">
                            {{ __('admin.pages.visibility') }}
                        </label>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="$set('showModal', false)"
                            class="btn-base border border-border bg-surface text-foreground hover:bg-surface-alt">{{ __('admin.common.cancel') }}</button>
                        <button type="submit"
                            class="btn-base bg-primary text-primary-foreground hover:bg-primary-hover shadow-sm"
                            wire:loading.attr="disabled">
                            <span
                                wire:loading.remove>{{ $editingId ? __('admin.pages.edit_page') : __('admin.pages.create_page') }}</span>
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
    </template>

    <!-- Delete Confirmation Modal -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-espresso/40 backdrop-blur-sm" wire:click="$set('showDeleteModal', false)"></div>
            <div class="relative w-full max-w-sm rounded-2xl bg-surface p-6 shadow-modal animate-scale-in">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-danger-bg text-danger mx-auto">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                </div>
                <h3 class="text-center font-display text-lg font-semibold text-foreground">
                    {{ __('admin.pages.delete_title') }}
                </h3>
                <p class="mt-2 text-center text-sm text-foreground-muted">{{ __('admin.pages.delete_message') }}</p>
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