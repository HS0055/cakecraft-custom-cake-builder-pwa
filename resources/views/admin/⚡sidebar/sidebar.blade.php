@php
    $logoUrl = settings(\App\Settings\BrandingSettings::class)->logo_url;
    $defaultLogo = asset('images/logo.png');
    $storeName = settings(\App\Settings\GeneralSettings::class)->store_name;
@endphp

<div class="contents">
    {{-- Desktop Sidebar --}}
    <aside class="hidden lg:flex w-[17rem] flex-col bg-sidebar border-r border-sidebar-border">
        @include('admin.⚡sidebar._brand', ['logoUrl' => $logoUrl, 'defaultLogo' => $defaultLogo, 'storeName' => $storeName])
        @include('admin.⚡sidebar._nav')
        @include('admin.⚡sidebar._user-footer')
    </aside>

    {{-- Mobile Sidebar Drawer --}}
    <div x-show="sidebarOpen" class="lg:hidden fixed inset-0 z-50" x-cloak>
        {{-- Backdrop --}}
        <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 bg-espresso/50 backdrop-blur-sm"
            @click="sidebarOpen = false">
        </div>

        {{-- Drawer panel --}}
        <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="fixed inset-y-0 start-0 flex w-[17rem] flex-col bg-gradient-to-b from-sidebar via-sidebar to-[oklch(20%_0.025_45)] shadow-modal">

            {{-- Close button --}}
            <button @click="sidebarOpen = false"
                class="absolute top-3 end-3 z-10 flex h-8 w-8 items-center justify-center rounded-xl text-sidebar-text-muted hover:text-white hover:bg-sidebar-hover transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>

            @include('admin.⚡sidebar._brand', ['logoUrl' => $logoUrl, 'defaultLogo' => $defaultLogo, 'storeName' => $storeName])
            @include('admin.⚡sidebar._nav')
            @include('admin.⚡sidebar._user-footer')
        </div>
    </div>
</div>