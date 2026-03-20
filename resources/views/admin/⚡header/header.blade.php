<header class="sticky top-0 z-30 admin-glass">
    <div class="flex h-16 items-center justify-between px-6 lg:px-8">
        <div class="flex items-center gap-4">
            {{-- Mobile hamburger --}}
            <button @click="sidebarOpen = true"
                class="lg:hidden p-2 -ms-2 text-foreground-muted hover:text-foreground hover:bg-surface-alt rounded-xl transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
            <h1 class="font-display text-xl font-semibold text-foreground tracking-tight">
                {{ __('admin.header.' . strtolower(str_replace([' ', '-'], '', $title))) }}
            </h1>
        </div>

        <div class="!flex items-center gap-2">
            {{-- Visit Store --}}
            <a href="{{ route('front.home') }}" target="_blank"
                class="hidden sm:inline-flex items-center gap-1.5 rounded-xl px-3.5 py-2 text-xs font-medium text-foreground-muted hover:text-accent hover:bg-primary-pale/50 transition-all duration-200">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                </svg>
                {{ __('admin.header.visit_store') }}
            </a>

            {{-- Clear Cache Action --}}
            <button wire:click="clearCache" wire:loading.attr="disabled"
                class="hidden sm:inline-flex items-center gap-1.5 rounded-xl px-3.5 py-2 text-xs font-medium text-foreground-muted hover:text-warning hover:bg-warning/10 transition-all duration-200 disabled:opacity-50">
                <svg wire:loading.remove wire:target="clearCache" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                <svg wire:loading wire:target="clearCache" class="h-3.5 w-3.5 animate-spin" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="display: none;">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                {{ __('admin.header.clear_cache') }}
            </button>

            {{-- Divider --}}
            <div class="hidden sm:block h-6 w-px bg-border"></div>

            {{-- Language Switcher --}}
            @if(isset($activeLanguages) && $activeLanguages->count() > 1)
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.away="open = false"
                        class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-medium text-foreground-muted hover:bg-surface-alt hover:text-foreground transition-all duration-200">
                        <svg class="h-4 w-4 text-foreground-muted/70" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                        </svg>
                        <span class="uppercase tracking-wider text-xs font-semibold">{{ app()->getLocale() }}</span>
                        <svg class="h-3.5 w-3.5 text-foreground-muted transition-transform duration-200"
                            :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>

                    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="transform opacity-0 scale-95 translate-y-1"
                        x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="transform opacity-0 scale-95 translate-y-1"
                        class="absolute end-0 mt-1 w-40 origin-top-right rounded-2xl bg-surface border border-border/80 shadow-card-hover focus:outline-none overflow-hidden z-50">
                        <div class="p-1.5">
                            @foreach($activeLanguages as $lang)
                                <a href="{{ route('set-locale', $lang->code) }}" @class([
                                    'block px-3 py-2 rounded-xl text-sm transition-all duration-150 flex items-center justify-between',
                                    'font-semibold text-primary bg-primary/10' => app()->getLocale() === $lang->code,
                                    'text-foreground-muted font-medium hover:bg-surface-alt hover:text-foreground' => app()->getLocale() !== $lang->code,
                                ])>
                                    {{ $lang->name }}
                                    @if(app()->getLocale() === $lang->code)
                                        <svg class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Divider --}}
            <div class="hidden sm:block h-6 w-px bg-border"></div>

            {{-- User menu --}}
            <div class="flex items-center gap-3" x-data="{ open: false }">
                <button @click="open = !open" @click.outside="open = false"
                    class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-sm text-foreground-muted hover:bg-surface-alt transition-all duration-200 cursor-pointer">

                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-primary to-accent text-xs font-bold text-white shadow-sm ring-2 ring-transparent group-hover:ring-primary/20 transition-all">
                        {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                    </div>

                    <span
                        class="hidden sm:inline font-medium text-foreground">{{ auth()->user()->name ?? 'Administrator' }}</span>
                    <svg class="h-4 w-4 transition-transform duration-200" :class="open && 'rotate-180'" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>

                {{-- Dropdown --}}
                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-1.5 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 -translate-y-1.5 scale-95"
                    class="absolute end-6 top-14 z-50 w-52 rounded-2xl border border-border/80 bg-surface p-2 shadow-card-hover"
                    style="display: none;">

                    <div class="px-3 py-2 mb-1">
                        <p class="text-xs font-medium text-foreground-muted">{{ __('admin.header.signed_in_as') }}</p>
                        <p class="text-sm font-semibold text-foreground truncate">{{ auth()->user()->email ?? '' }}</p>
                        @if(auth()->check() && auth()->user()->roles->count() > 0)
                            <p class="text-[10px] font-bold uppercase tracking-wider text-primary mt-1">
                                {{ auth()->user()->roles->first()->name }}
                            </p>
                        @endif
                    </div>

                    <div class="h-px bg-border/50 my-1"></div>

                    <a href="{{ route('admin.profile') }}" wire:navigate
                        class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2 text-sm text-foreground hover:bg-surface-alt transition-all duration-200">
                        <svg class="h-4 w-4 text-foreground-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        {{ __('admin.header.my_profile') }}
                    </a>

                    <button wire:click="logout"
                        class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm text-danger hover:bg-danger-bg transition-all duration-200 cursor-pointer">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                        </svg>
                        {{ __('admin.header.sign_out') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</header>