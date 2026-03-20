<?php

use Livewire\Component;
use Livewire\Attributes\On;

new class extends Component {
    public int $cartCount = 0;

    public function mount()
    {
        $this->cartCount = count(session('cart', []));
    }

    #[On('cart-updated')]
    public function refreshCartCount()
    {
        $this->cartCount = count(session('cart', []));
    }

    public function with(): array
    {
        return [
            'general' => settings(\App\Settings\GeneralSettings::class),
            'branding' => settings(\App\Settings\BrandingSettings::class),
            'activeLanguages' => \App\Models\Language::where('is_active', true)->get(),
        ];
    }
};
?>
<header x-data="{ scrolled: false, mobileOpen: false }"
    x-effect="document.body.style.overflow = mobileOpen ? 'hidden' : ''"
    x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 20)"
    class="fixed top-0 start-0 end-0 z-50 bg-white border-b border-gray-200 transition-shadow duration-200"
    :class="scrolled && 'shadow-sm'">
    <div class="front-container">
        <div class="flex items-center justify-between h-14 md:h-16">
            {{-- Logo --}}
            <a href="{{ route('front.home') }}" wire:navigate class="flex items-center gap-2.5">
                @if($branding->logo_url)
                    <img src="{{ $branding->logo_url }}" alt="{{ $general->store_name }}"
                        class="h-10 md:h-14 object-contain">
                @else
                    <img src="{{ asset('images/logo.png') }}" alt="{{ $general->store_name }}"
                        class="h-10 md:h-14 object-contain">
                @endif
            </a>

            {{-- Desktop Nav --}}
            <nav class="hidden md:flex items-center gap-0.5">
                <a href="{{ route('front.home') }}" wire:navigate @class([
                    'px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-150',
                    'text-primary font-semibold bg-primary/10' => request()->routeIs('front.home'),
                    'text-gray-600 hover:text-gray-900 hover:bg-primary/5' => !request()->routeIs('front.home'),
                ])>
                    {{ __('front.nav.home') }}
                </a>
                <a href="{{ route('front.shop') }}" wire:navigate @class([
                    'px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-150',
                    'text-primary font-semibold bg-primary/10' => request()->routeIs('front.shop'),
                    'text-gray-600 hover:text-gray-900 hover:bg-primary/5' => !request()->routeIs('front.shop'),
                ])>
                    {{ __('front.nav.shop') }}
                </a>
                <a href="{{ route('front.cake-builder') }}" wire:navigate @class([
                    'px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-150',
                    'text-primary font-semibold bg-primary/10' => request()->routeIs('front.cake-builder'),
                    'text-gray-600 hover:text-gray-900 hover:bg-primary/5' => !request()->routeIs('front.cake-builder'),
                ])>
                    {{ __('front.nav.build_a_cake') }}
                </a>
            </nav>


            {{-- Right Actions --}}
            <div class="flex shrink-0 items-center gap-0.5">
                {{-- Language Switcher (Desktop) --}}
                @if(isset($activeLanguages) && $activeLanguages->count() > 1)
                    <div x-data="{ open: false }" class="relative hidden sm:block">
                        <button @click="open = !open" @click.away="open = false"
                            class="flex items-center gap-1.5 px-2.5 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-primary/5 hover:text-gray-900 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                            </svg>
                            <span class="uppercase tracking-wider text-xs">{{ app()->getLocale() }}</span>
                            <svg class="w-3 h-3 text-gray-400 transition-transform duration-200"
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
                            class="absolute end-0 mt-1.5 w-40 origin-top-right rounded-xl bg-white border border-gray-200 shadow-lg focus:outline-none overflow-hidden z-50">
                            <div class="p-1">
                                @foreach($activeLanguages as $lang)
                                    <a href="{{ route('set-locale', $lang->code) }}" @class([
                                        'block px-3 py-2 rounded-lg text-sm transition-colors duration-150 flex items-center justify-between',
                                        'font-semibold text-primary bg-primary/10' => app()->getLocale() === $lang->code,
                                        'text-gray-600 font-medium hover:bg-primary/5 hover:text-gray-900' => app()->getLocale() !== $lang->code,
                                    ])>
                                        {{ $lang->name }}
                                        @if(app()->getLocale() === $lang->code)
                                            <svg class="w-4 h-4 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor"
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

                {{-- Search --}}
                <div class="block">
                    <livewire:front.header.global-search />
                </div>
                {{-- Cart Button & Offcanvas --}}
                <livewire:front.header.cart-icon />

                {{-- Mobile Menu Toggle --}}
                <button @click="mobileOpen = !mobileOpen" aria-label="{{ __('front.nav.toggle_menu') }}"
                    :aria-expanded="mobileOpen"
                    class="md:hidden p-2 rounded-lg hover:bg-primary/5 transition-colors duration-150">
                    <svg x-show="!mobileOpen" class="w-5 h-5 text-gray-700" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <svg x-show="mobileOpen" x-cloak class="w-5 h-5 text-gray-700" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile Offcanvas Sidebar --}}
        <div x-show="mobileOpen" x-cloak class="fixed inset-0 z-[999] md:hidden">

            {{-- Backdrop --}}
            <div x-show="mobileOpen" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" @click="mobileOpen = false"
                class="absolute inset-0 bg-black/20 backdrop-blur-sm">
            </div>

            {{-- Sidebar Panel --}}
            <div class="fixed inset-y-0 start-0 w-full max-w-[280px] flex">
                <div x-show="mobileOpen" x-trap.noscroll="mobileOpen"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="ltr:-translate-x-full rtl:translate-x-full"
                    x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="ltr:-translate-x-full rtl:translate-x-full"
                    class="w-full bg-white border-e border-gray-200 flex flex-col shadow-xl">

                    {{-- Sidebar Header --}}
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                        <a href="{{ route('front.home') }}" wire:navigate @click="mobileOpen = false"
                            class="flex items-center gap-2">
                            @if($branding->logo_url)
                                <img src="{{ $branding->logo_url }}" alt="{{ $general->store_name }}"
                                    class="h-8 object-contain">
                            @else
                                <img src="{{ asset('images/logo.png') }}" alt="{{ $general->store_name }}"
                                    class="h-8 object-contain">
                            @endif
                        </a>
                        <button @click="mobileOpen = false"
                            class="p-2 -me-2 text-gray-400 hover:text-gray-600 transition-colors rounded-lg hover:bg-primary/5">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Sidebar Body --}}
                    <div class="flex-1 overflow-y-auto py-3 px-3">
                        <nav class="space-y-1">
                            <a href="{{ route('front.home') }}" wire:navigate @click="mobileOpen = false" @class([
                                'flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-150',
                                'text-primary bg-primary/10 font-semibold' => request()->routeIs('front.home'),
                                'text-gray-700 hover:bg-primary/5 hover:text-gray-900' => !request()->routeIs('front.home'),
                            ])>
                                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                </svg>
                                {{ __('front.nav.home') }}
                            </a>

                            <a href="{{ route('front.shop') }}" wire:navigate @click="mobileOpen = false" @class([
                                'flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-150',
                                'text-primary bg-primary/10 font-semibold' => request()->routeIs('front.shop'),
                                'text-gray-700 hover:bg-primary/5 hover:text-gray-900' => !request()->routeIs('front.shop'),
                            ])>
                                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75v-3.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.5c0 .414.336.75.75.75Z" />
                                </svg>
                                {{ __('front.nav.shop') }}
                            </a>

                            <a href="{{ route('front.cake-builder') }}" wire:navigate @click="mobileOpen = false"
                                @class([
                                    'flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-150',
                                    'text-primary bg-primary/10 font-semibold' => request()->routeIs('front.cake-builder'),
                                    'text-gray-700 hover:bg-primary/5 hover:text-gray-900' => !request()->routeIs('front.cake-builder'),
                                ])>
         <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 0 0-2.455 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                                </svg>
                                {{ __('front.nav.build_a_cake') }}
                            </a>
                        </nav>

                        {{-- Language Switcher --}}
                        @if(isset($activeLanguages) && $activeLanguages->count() > 1)
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <p
                                    class="px-4 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                                    </svg>
                                    {{ __('front.nav.language_selection') }}
                                </p>
                                <div class="space-y-1">
                                    @foreach($activeLanguages as $lang)
                                        <a href="{{ route('set-locale', $lang->code) }}" @click="mobileOpen = false" @class([
                                            'flex items-center justify-between px-4 py-2.5 rounded-xl text-sm transition-colors duration-150',
                                            'font-semibold text-primary bg-primary/10' => app()->getLocale() === $lang->code,
                                            'font-medium text-gray-600 hover:bg-primary/5 hover:text-gray-900' => app()->getLocale() !== $lang->code,
                                        ])>
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="w-6 text-center text-xs font-bold uppercase {{ app()->getLocale() === $lang->code ? 'text-gray-900' : 'text-gray-400' }}">{{ $lang->code }}</span>
                                                <span>{{ $lang->name }}</span>
                                            </div>
                                            @if(app()->getLocale() === $lang->code)
                                                <svg class="w-4 h-4 text-gray-900" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m4.5 12.75 6 6 9-13.5" />
                                                </svg>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>