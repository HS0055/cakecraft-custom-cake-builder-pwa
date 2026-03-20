<div class="animate-fade-in" x-data="{ reordering: false }">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="font-display text-2xl font-semibold text-foreground">{{ __('admin.sliders.title') }}</h2>
            <p class="mt-1 text-sm text-foreground-muted">{{ __('admin.sliders.subtitle') }}</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            @can('create sliders')
                <button wire:click="openCreate"
                    class="btn-base w-full sm:w-auto justify-center bg-primary text-primary-foreground hover:bg-primary-hover">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    {{ __('admin.sliders.add_slide') }}
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
                        <th class="table-header px-6 py-3.5 text-start w-12 text-center"></th>
                        <th class="table-header px-6 py-3.5 text-start w-24">{{ __('admin.sliders.table_image') }}</th>
                        <th class="table-header px-6 py-3.5 text-start">{{ __('admin.sliders.table_action') }}</th>
                        <th class="table-header px-6 py-3.5 text-start">{{ __('admin.common.status') }}</th>
                        <th class="table-header px-6 py-3.5 text-end">{{ __('admin.common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border" wire:sort="updateOrder">
                    @forelse ($sliders as $slider)
                        <tr wire:key="slider-{{ $slider->id }}" wire:sort:item="{{ $slider->id }}"
                            class="hover:bg-surface-alt/50 transition-colors duration-150 group">
                            <!-- Drag Handle -->
                            <td class="px-6 py-4 cursor-grab active:cursor-grabbing" wire:sort:handle>
                                <svg class="h-5 w-5 text-foreground-muted opacity-50 hover:opacity-100 transition-opacity"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 8h16M4 16h16" />
                                </svg>
                            </td>
                            <td class="px-6 py-4">
                                <div
                                    class="relative h-12 w-24 overflow-hidden rounded-md border border-border bg-checkered">
                                    <img src="{{ $slider->getFirstMediaUrl('image') }}"
                                        class="h-full w-full object-contain">
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span
                                        class="inline-flex items-center rounded-md bg-surface-alt px-2 py-0.5 text-xs font-medium text-foreground-muted ring-1 ring-inset ring-border w-fit">
                                        {{ $slider->action_type === 'ready_cake' ? __('admin.sliders.action_ready_cake') : __('admin.sliders.action_custom_builder') }}
                                    </span>
                                    @if($slider->ready_cake_id)
                                        <span class="text-xs font-medium text-primary">{{ $slider->readyCake->name }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <button wire:click="toggleActive({{ $slider->id }})"
                                    class="inline-flex cursor-pointer items-center rounded-full px-2.5 py-1 text-xs font-medium transition-colors {{ $slider->is_active ? 'bg-success-bg text-success' : 'bg-surface-alt text-foreground-muted' }}">
                                    {{ $slider->is_active ? __('admin.common.active') : __('admin.sliders.inactive') }}
                                </button>
                            </td>
                            <td class="px-6 py-4 text-end">
                                <div class="flex items-center justify-end gap-1">
                                    @can('update sliders')
                                        <button wire:click="openEdit({{ $slider->id }})"
                                            class="rounded-xl p-2 text-foreground-muted hover:bg-surface-alt hover:text-foreground transition-colors cursor-pointer"
                                            title="{{ __('admin.common.edit') }}">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </button>
                                    @endcan
                                    @can('delete sliders')
                                        <button wire:click="confirmDelete({{ $slider->id }})"
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
                                {{ __('admin.sliders.no_slides') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($sliders->hasPages())
            <div class="border-t border-border px-6 py-4">
                {{ $sliders->links() }}
            </div>
        @endif
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-espresso/40 backdrop-blur-sm" wire:click="$set('showModal', false)"></div>
            <div class="relative w-full max-w-lg rounded-2xl bg-surface p-6 shadow-modal animate-scale-in">
                <h3 class="font-display text-lg font-semibold text-foreground mb-4">
                    {{ $editingId ? __('admin.sliders.edit_slide') : __('admin.sliders.create_slide') }}
                </h3>

                <form wire:submit="save" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <x-media-upload model="image" label="{{ __('admin.sliders.image_label') }}" accept="image/*" hint="{{ __('admin.sliders.image_hint') }}"
                                :preview="$existingImage" removeSignal="remove_image" />
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-foreground">{{ __('admin.sliders.action_type') }}</label>
                            <select wire:model.live="action_type" class="select-base">
                                <option value="ready_cake">{{ __('admin.sliders.action_ready_cake') }}</option>
                                <option value="custom_builder">{{ __('admin.sliders.action_custom_builder') }}</option>
                            </select>
                            @error('action_type') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                        </div>

                        @if($action_type === 'ready_cake')
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-foreground">{{ __('admin.sliders.select_cake') }}</label>
                                <select wire:model="ready_cake_id" class="select-base">
                                    <option value="">{{ __('admin.sliders.select_cake_placeholder') }}</option>
                                    @foreach($readyCakes as $cake)
                                        <option value="{{ $cake->id }}">{{ $cake->name }}</option>
                                    @endforeach
                                </select>
                                @error('ready_cake_id') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                            </div>
                        @else
                            <div class="flex items-end pb-3">
                                <span class="text-xs text-foreground-muted italic">{{ __('admin.sliders.custom_builder_hint') }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" wire:model="is_active" id="is_active_chk"
                            class="h-4 w-4 rounded border-border text-primary focus:ring-primary/20 cursor-pointer" />
                        <label for="is_active_chk" class="text-sm font-medium text-foreground cursor-pointer">{{ __('admin.sliders.published_active') }}</label>
                    </div>

                    <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-border">
                        <button type="button" wire:click="$set('showModal', false)"
                            class="btn-base border border-border bg-surface text-foreground hover:bg-surface-alt">{{ __('admin.common.cancel') }}</button>
                        <button type="submit" class="btn-base bg-primary text-primary-foreground hover:bg-primary-hover"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove >{{ __('admin.sliders.save_slide') }}</span>
                            <span wire:loading class="flex items-center gap-2 text-espresso">
                                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
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
                <h3 class="text-center font-display text-lg font-semibold text-foreground">{{ __('admin.sliders.delete_title') }}</h3>
                <p class="mt-2 text-center text-sm text-foreground-muted">{{ __('admin.sliders.delete_message') }}</p>
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