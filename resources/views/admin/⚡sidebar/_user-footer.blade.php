{{-- User footer --}}
<div class="border-t border-sidebar-border/60 p-3">
    <div class="flex items-center gap-3 rounded-xl px-3.5 py-3 bg-sidebar-hover/40">
        <div
            class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-primary to-accent text-xs font-bold text-white shadow-sm">
            {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
        </div>
        <div class="flex-1 min-w-0">
            <p class="truncate text-sm font-semibold text-sidebar-text">{{ auth()->user()->name ?? 'Admin' }}</p>
            <p class="truncate text-[11px] text-sidebar-text-muted">{{ auth()->user()->email ?? '' }}</p>
        </div>
    </div>
</div>