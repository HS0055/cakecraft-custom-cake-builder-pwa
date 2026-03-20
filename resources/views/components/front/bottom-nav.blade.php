<div
    class="md:hidden fixed bottom-0 start-0 w-full z-50 px-4 pb-safe pt-2 pointer-events-none {{ request()->routeIs('front.cake-builder') ? 'hidden' : '' }}">
    {{-- Floating Pill Navigation --}}
    <nav
        class="pointer-events-auto relative mx-auto max-w-sm flex items-center justify-between px-2 py-2 bg-white border border-gray-200 shadow-lg rounded-2xl text-gray-700 mb-2">

        {{-- Home --}}
        <a href="{{ route('front.home') }}" wire:navigate
            class="flex flex-col items-center justify-center w-16 h-12 group relative rounded-xl transition-colors duration-150 {{ request()->routeIs('front.home') ? 'bg-primary/10' : 'hover:bg-primary/5' }}">
            <div
                class="{{ request()->routeIs('front.home') ? 'text-primary' : 'text-gray-500 group-hover:text-gray-900' }} transition-colors duration-150">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
            </div>
            <span
                class="text-[9px] font-semibold mt-0.5 {{ request()->routeIs('front.home') ? 'text-primary' : 'text-gray-500' }} transition-colors">{{ __('front.nav.home') }}</span>
        </a>

        {{-- Shop --}}
        <a href="{{ route('front.shop') }}" wire:navigate
            class="flex flex-col items-center justify-center w-16 h-12 group relative rounded-xl transition-colors duration-150 {{ request()->routeIs('front.shop') ? 'bg-primary/10' : 'hover:bg-primary/5' }}">
            <div
                class="{{ request()->routeIs('front.shop') ? 'text-primary' : 'text-gray-500 group-hover:text-gray-900' }} transition-colors duration-150">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75v-3.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.5c0 .414.336.75.75.75Z" />
                </svg>
            </div>
            <span
                class="text-[9px] font-semibold mt-0.5 {{ request()->routeIs('front.shop') ? 'text-primary' : 'text-gray-500' }} transition-colors">{{ __('front.nav.shop') }}</span>
        </a>

        {{-- Builder --}}
        <a href="{{ route('front.cake-builder') }}" wire:navigate
            class="flex flex-col items-center justify-center w-16 h-12 group relative rounded-xl transition-colors duration-150 {{ request()->routeIs('front.cake-builder') ? 'bg-primary/10' : 'hover:bg-primary/5' }}">
            <div
                class="{{ request()->routeIs('front.cake-builder') ? 'text-primary' : 'text-gray-500 group-hover:text-gray-900' }} transition-colors duration-150">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 0 0-2.455 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                </svg>
            </div>
            <span
                class="text-[9px] font-semibold mt-0.5 {{ request()->routeIs('front.cake-builder') ? 'text-primary' : 'text-gray-500' }} transition-colors">{{ __('front.nav.builder') }}</span>
        </a>

        {{-- Cart --}}
        <livewire:front.header.mobile-nav-cart />

    </nav>
    <style>
        .pb-safe {
            padding-bottom: env(safe-area-inset-bottom, 1rem);
        }
    </style>
</div>