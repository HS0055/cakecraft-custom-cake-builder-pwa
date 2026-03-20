<div class="animate-fade-in space-y-6">
    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="font-display text-2xl font-semibold text-foreground">{{ __('admin.audit_log.title') }}</h1>
        <div class="!flex items-center gap-2">
            <span class="text-sm text-foreground-muted">{{ __('admin.audit_log.total_logs', ['count' => $logs->total()]) }}</span>
        </div>
    </div>

    {{-- Filters --}}
    <div class="grid gap-4 sm:grid-cols-4">
        <div class="relative w-full">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('admin.audit_log.search_logs') }}"
                class="input-base w-full">
        </div>

        <div class="relative w-full">
            <select wire:model.live="groupFilter" class="select-base w-full">
                <option value="">{{ __('admin.audit_log.all_groups') }}</option>
                @foreach ($groups as $group)
                    <option value="{{ $group }}">{{ ucfirst($group) }}</option>
                @endforeach
            </select>
        </div>

        <div class="relative w-full">
            <select wire:model.live="actionFilter" class="select-base w-full">
                <option value="">{{ __('admin.audit_log.all_actions') }}</option>
                <option value="created">Created</option>
                <option value="updated">Updated</option>
                <option value="deleted">Deleted</option>
            </select>
        </div>
        
        <div class="relative w-full">
             <input type="date" wire:model.live="dateFilter" class="input-base w-full">
        </div>
    </div>

    {{-- Table --}}
    <div class="card-base overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-start text-sm text-foreground">
                <thead class="bg-surface-alt text-xs uppercase text-foreground-muted">
                    <tr>
                        <th class="px-6 py-3 font-medium">{{ __('admin.audit_log.table_date') }}</th>
                        <th class="px-6 py-3 font-medium">{{ __('admin.audit_log.table_user') }}</th>
                        <th class="px-6 py-3 font-medium">{{ __('admin.audit_log.table_group') }}</th>
                        <th class="px-6 py-3 font-medium">{{ __('admin.audit_log.table_key') }}</th>
                        <th class="px-6 py-3 font-medium">{{ __('admin.audit_log.table_action') }}</th>
                        <th class="px-6 py-3 font-medium">{{ __('admin.audit_log.table_old_value') }}</th>
                        <th class="px-6 py-3 font-medium">{{ __('admin.audit_log.table_new_value') }}</th>
                        <th class="px-6 py-3 font-medium text-end">{{ __('admin.audit_log.table_details') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-surface-alt/50 transition-colors duration-150">
                            <td class="whitespace-nowrap px-6 py-4 text-foreground-muted">
                                {{ $log->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 font-medium">
                                <div class="!flex items-center gap-2">
                                     <div class="flex h-6 w-6 items-center justify-center rounded-full bg-accent/10 text-xs font-bold text-accent">
                                        {{ substr($log->user->name ?? __('admin.audit_log.system'), 0, 1) }}
                                    </div>
                                    {{ $log->user->name ?? __('admin.audit_log.system') }}
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="inline-flex items-center rounded-full bg-surface-alt px-2.5 py-0.5 text-xs font-medium text-foreground-muted">
                                    {{ ucfirst($log->group) }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 font-mono text-xs text-foreground-muted">
                                {{ $log->key }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @php
                                    $actionColors = [
                                        'created' => 'bg-success-bg text-success',
                                        'updated' => 'bg-primary/10 text-primary',
                                        'deleted' => 'bg-danger-bg text-danger',
                                    ];
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $actionColors[$log->action] ?? 'bg-surface-alt text-foreground-muted' }}">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs font-mono text-danger truncate max-w-[150px]">
                                {{ is_array($log->old_value) || is_object($log->old_value) ? json_encode($log->old_value) : Str::limit($log->old_value, 20) }}
                            </td>
                            <td class="px-6 py-4 text-xs font-mono text-success truncate max-w-[150px]">
                                {{ is_array($log->new_value) || is_object($log->new_value) ? json_encode($log->new_value) : Str::limit($log->new_value, 20) }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-end">
                                <button wire:click="viewDiff({{ $log->id }})" class="text-foreground-muted hover:text-accent transition-colors">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-foreground-muted">
                                {{ __('admin.common.no_results') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($logs->hasPages())
            <div class="border-t border-border bg-surface-alt px-6 py-4">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

    {{-- Diff Modal --}}
    @if($showDiffModal && $selectedLog)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-espresso/40 backdrop-blur-sm p-4 md:p-0">
            <div class="relative w-full max-w-4xl rounded-2xl bg-surface shadow-modal animate-scale-in ring-1 ring-border">
                <div class="flex items-center justify-between border-b border-border px-6 py-4">
                    <h3 class="text-lg font-semibold text-foreground">
                        {{ __('admin.audit_log.details_title') }} <span class="text-foreground-subtle">#{{ $selectedLog->id }}</span>
                    </h3>
                    <button wire:click="closeDiffModal" class="text-foreground-subtle hover:text-foreground transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div>
                            <span class="text-xs font-medium uppercase tracking-wider text-foreground-muted">{{ __('admin.audit_log.table_user') }}</span>
                            <p class="mt-1 text-sm font-medium text-foreground">{{ $selectedLog->user->name ?? __('admin.audit_log.system') }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-medium uppercase tracking-wider text-foreground-muted">{{ __('admin.audit_log.table_date') }}</span>
                            <p class="mt-1 text-sm font-medium text-foreground">{{ $selectedLog->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-medium uppercase tracking-wider text-foreground-muted">{{ __('admin.audit_log.table_action') }}</span>
                            <p class="mt-1 text-sm font-medium text-foreground uppercase">{{ $selectedLog->action }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-medium uppercase tracking-wider text-foreground-muted">{{ __('admin.audit_log.ip_address') }}</span>
                            <p class="mt-1 text-sm font-medium text-foreground">{{ $selectedLog->ip_address ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="rounded-xl border border-danger/20 bg-danger-bg p-4">
                            <h4 class="mb-2 text-sm font-bold text-danger">{{ __('admin.audit_log.table_old_value') }}</h4>
                            <pre class="overflow-x-auto text-xs text-danger/80 whitespace-pre-wrap">{{ json_encode($selectedLog->old_value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                        </div>
                        <div class="rounded-xl border border-success/20 bg-success-bg p-4">
                            <h4 class="mb-2 text-sm font-bold text-success">{{ __('admin.audit_log.table_new_value') }}</h4>
                            <pre class="overflow-x-auto text-xs text-success/80 whitespace-pre-wrap">{{ json_encode($selectedLog->new_value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end border-t border-border px-6 py-4">
                    <button wire:click="closeDiffModal" class="btn-base border border-border bg-surface text-foreground hover:bg-surface-alt">
                        {{ __('admin.common.close') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
