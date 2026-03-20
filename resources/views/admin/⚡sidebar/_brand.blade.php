{{-- Brand Area --}}
<a wire:navigate href="{{ route('admin.dashboard') }}"
    class="flex h-16 items-center gap-3 border-b border-sidebar-border px-5">
    <div class="flex h-9 w-9 items-center justify-center rounded-xl overflow-hidden">
        <img src="{{ $logoUrl ?: $defaultLogo }}" alt="Logo" class="h-full w-full object-cover">
    </div>
    <div>
        <span class="font-display text-base font-semibold text-white tracking-tight">{{ $storeName }}</span>
        <span
            class="block text-[10px] font-semibold uppercase tracking-[0.2em] text-primary-light/60">{{ __('admin.sidebar.admin_panel') }}</span>
    </div>
</a>