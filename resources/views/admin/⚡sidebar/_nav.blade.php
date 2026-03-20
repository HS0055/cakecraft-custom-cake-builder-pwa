{{-- Navigation --}}
<nav class="flex-1 space-y-0.5 px-3 py-5 overflow-y-auto">
    {{-- Main Group --}}
    <p class="mb-2.5 px-3.5 text-[10px] font-bold uppercase tracking-[0.15em] text-sidebar-text-muted">
        {{ __('admin.sidebar.group_main') }}
    </p>

    <a href="{{ route('admin.dashboard') }}" wire:navigate @class([
        'admin-link group relative',
        'bg-sidebar-active text-white shadow-md shadow-sidebar-active/30' => request()->routeIs('admin.dashboard'),
        'text-sidebar-text hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.dashboard'),
    ])>
        @if(request()->routeIs('admin.dashboard'))
            <span class="absolute start-0 top-1/2 -translate-y-1/2 w-1 h-5 bg-primary rounded-r-full"></span>
        @endif
        <svg class="h-[18px] w-[18px] shrink-0 transition-transform duration-200 group-hover:scale-110" fill="none"
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
        </svg>
        {{ __('admin.sidebar.dashboard') }}
    </a>

    @can('view orders')
        <a href="{{ route('admin.orders') }}" wire:navigate @class([
            'admin-link group relative',
            'bg-sidebar-active text-white shadow-md shadow-sidebar-active/30' => request()->routeIs('admin.orders*'),
            'text-sidebar-text hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.orders*'),
        ])>
            @if(request()->routeIs('admin.orders*'))
                <span class="absolute start-0 top-1/2 -translate-y-1/2 w-1 h-5 bg-primary rounded-r-full"></span>
            @endif
            <svg class="h-[18px] w-[18px] shrink-0 transition-transform duration-200 group-hover:scale-110" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
            </svg>
            {{ __('admin.sidebar.orders') }}
        </a>
    @endcan

    {{-- Marketing Group --}}
    @can('view sliders')
        <div class="pt-5 mt-3 border-t border-sidebar-border/50">
            <p class="mb-2.5 px-3.5 text-[10px] font-bold uppercase tracking-[0.15em] text-sidebar-text-muted">
                {{ __('admin.sidebar.group_marketing') }}
            </p>
        </div>
        <a href="{{ route('admin.sliders') }}" wire:navigate @class([
            'admin-link group relative',
            'bg-sidebar-active text-white shadow-md shadow-sidebar-active/30' => request()->routeIs('admin.sliders*'),
            'text-sidebar-text hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.sliders*'),
        ])>
            @if(request()->routeIs('admin.sliders*'))
                <span class="absolute start-0 top-1/2 -translate-y-1/2 w-1 h-5 bg-primary rounded-r-full"></span>
            @endif
            <svg class="h-[18px] w-[18px] shrink-0 transition-transform duration-200 group-hover:scale-110" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0V3.75m0 12.75V21m0 0h-1.5m1.5 0h1.5" />
            </svg>
            {{ __('admin.sidebar.sliders') }}
        </a>
    @endcan

    @can('view newsletter subscribers')
        <a href="{{ route('admin.newsletter-subscribers') }}" wire:navigate @class([
            'admin-link group relative',
            'bg-sidebar-active text-white shadow-md shadow-sidebar-active/30' => request()->routeIs('admin.newsletter-subscribers*'),
            'text-sidebar-text hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.newsletter-subscribers*'),
        ])>
            @if(request()->routeIs('admin.newsletter-subscribers*'))
                <span class="absolute start-0 top-1/2 -translate-y-1/2 w-1 h-5 bg-primary rounded-r-full"></span>
            @endif
            <svg class="h-[18px] w-[18px] shrink-0 transition-transform duration-200 group-hover:scale-110" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
            </svg>
            {{ __('admin.sidebar.newsletter_subs') }}
        </a>
    @endcan

    {{-- Content Management Group --}}
    @canany(['view pages', 'view faqs'])
        <div class="pt-5 mt-3 border-t border-sidebar-border/50">
            <p class="mb-2.5 px-3.5 text-[10px] font-bold uppercase tracking-[0.15em] text-sidebar-text-muted">
                {{ __('admin.sidebar.group_content') }}
            </p>
        </div>
        @can('view pages')
            <a href="{{ route('admin.pages') }}" wire:navigate @class([
                'admin-link group relative',
                'bg-sidebar-active text-white shadow-md shadow-sidebar-active/30' => request()->routeIs('admin.pages*'),
                'text-sidebar-text hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.pages*'),
            ])>
                @if(request()->routeIs('admin.pages*'))
                    <span class="absolute start-0 top-1/2 -translate-y-1/2 w-1 h-5 bg-primary rounded-r-full"></span>
                @endif
                <svg class="h-[18px] w-[18px] shrink-0 transition-transform duration-200 group-hover:scale-110" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
                {{ __('admin.sidebar.pages') }}
            </a>
        @endcan

        @can('view faqs')
            <a href="{{ route('admin.faqs') }}" wire:navigate @class([
                'admin-link group relative',
                'bg-sidebar-active text-white shadow-md shadow-sidebar-active/30' => request()->routeIs('admin.faqs*'),
                'text-sidebar-text hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.faqs*'),
            ])>
                @if(request()->routeIs('admin.faqs*'))
                    <span class="absolute start-0 top-1/2 -translate-y-1/2 w-1 h-5 bg-primary rounded-r-full"></span>
                @endif
                <svg class="h-[18px] w-[18px] shrink-0 transition-transform duration-200 group-hover:scale-110" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                </svg>
                {{ __('admin.sidebar.faqs') }}
            </a>
        @endcan
    @endcanany

    {{-- Catalog Group --}}
    @can('view ready cakes')
        <div class="pt-5 mt-3 border-t border-sidebar-border/50">
            <p class="mb-2.5 px-3.5 text-[10px] font-bold uppercase tracking-[0.15em] text-sidebar-text-muted">
                {{ __('admin.sidebar.group_catalog') }}
            </p>
        </div>
        <a href="{{ route('admin.ready-cakes') }}" wire:navigate @class([
            'admin-link group relative',
            'bg-sidebar-active text-white shadow-md shadow-sidebar-active/30' => request()->routeIs('admin.ready-cakes*'),
            'text-sidebar-text hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.ready-cakes*'),
        ])>
            @if(request()->routeIs('admin.ready-cakes*'))
                <span class="absolute start-0 top-1/2 -translate-y-1/2 w-1 h-5 bg-primary rounded-r-full"></span>
            @endif
            <svg class="h-[18px] w-[18px] shrink-0 transition-transform duration-200 group-hover:scale-110" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 6.75c-3.642 0-6.75 1.678-6.75 3.75s3.108 3.75 6.75 3.75 6.75-1.678 6.75-3.75S15.642 6.75 12 6.75Zm0 0V5.25m0 1.5c2.946 0 5.25-1.007 5.25-2.25S14.946 2.25 12 2.25 6.75 3.257 6.75 4.5 9.054 6.75 12 6.75Zm-6.75 7.5c0 2.072 3.108 3.75 6.75 3.75s6.75-1.678 6.75-3.75" />
            </svg>
            {{ __('admin.sidebar.ready_cakes') }}
        </a>
    @endcan

    {{-- Cake Components Group --}}
    @canany(['view shapes', 'view flavors', 'view colors', 'view toppings', 'view topping categories'])
        <div class="pt-5 mt-3 border-t border-sidebar-border/50">
            <p class="mb-2.5 px-3.5 text-[10px] font-bold uppercase tracking-[0.15em] text-sidebar-text-muted">
                {{ __('admin.sidebar.group_cake_components') }}
            </p>
        </div>
    @endcanany

    @can('view shapes')
        <a href="{{ route('admin.cake-shapes') }}" wire:navigate @class([
            'admin-link group relative',
            'bg-sidebar-active text-white shadow-md shadow-sidebar-active/30' => request()->routeIs('admin.cake-shapes'),
            'text-sidebar-text hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.cake-shapes'),
        ])>
            @if(request()->routeIs('admin.cake-shapes'))
                <span class="absolute start-0 top-1/2 -translate-y-1/2 w-1 h-5 bg-primary rounded-r-full"></span>
            @endif
            <svg class="h-[18px] w-[18px] shrink-0 transition-transform duration-200 group-hover:scale-110" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25a2.25 2.25 0 0 1-2.25-2.25v-2.25Z" />
            </svg>
            {{ __('admin.sidebar.shapes') }}
        </a>
    @endcan

    @can('view flavors')
        <a href="{{ route('admin.cake-flavors') }}" wire:navigate @class([
            'admin-link group relative',
            'bg-sidebar-active text-white shadow-md shadow-sidebar-active/30' => request()->routeIs('admin.cake-flavors'),
            'text-sidebar-text hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.cake-flavors'),
        ])>
            @if(request()->routeIs('admin.cake-flavors'))
                <span class="absolute start-0 top-1/2 -translate-y-1/2 w-1 h-5 bg-primary rounded-r-full"></span>
            @endif
            <svg class="h-[18px] w-[18px] shrink-0 transition-transform duration-200 group-hover:scale-110" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
            </svg>
            {{ __('admin.sidebar.flavors') }}
        </a>
    @endcan

    @can('view colors')
        <a href="{{ route('admin.cake-colors') }}" wire:navigate @class([
            'admin-link group relative',
            'bg-sidebar-active text-white shadow-md shadow-sidebar-active/30' => request()->routeIs('admin.cake-colors'),
            'text-sidebar-text hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.cake-colors'),
        ])>
            @if(request()->routeIs('admin.cake-colors'))
                <span class="absolute start-0 top-1/2 -translate-y-1/2 w-1 h-5 bg-primary rounded-r-full"></span>
            @endif
            <svg class="h-[18px] w-[18px] shrink-0 transition-transform duration-200 group-hover:scale-110" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M4.098 19.902a3.75 3.75 0 0 0 5.304 0l6.401-6.402M6.75 21A3.75 3.75 0 0 1 3 17.25V4.125C3 3.504 3.504 3 4.125 3h5.25c.621 0 1.125.504 1.125 1.125v4.072M6.75 21a3.75 3.75 0 0 0 3.75-3.75V8.197M6.75 21h13.125c.621 0 1.125-.504 1.125-1.125v-5.25c0-.621-.504-1.125-1.125-1.125h-4.072M10.5 8.197l2.88-2.88c.438-.439 1.15-.439 1.59 0l3.712 3.713c.44.44.44 1.152 0 1.59l-2.879 2.88M6.75 17.25h.008v.008H6.75v-.008Z" />
            </svg>
            {{ __('admin.sidebar.colors') }}
        </a>
    @endcan

    @can('view topping categories')
        <a href="{{ route('admin.topping-categories') }}" wire:navigate @class([
            'admin-link group relative',
            'bg-sidebar-active text-white shadow-md shadow-sidebar-active/30' => request()->routeIs('admin.topping-categories'),
            'text-sidebar-text hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.topping-categories'),
        ])>
            @if(request()->routeIs('admin.topping-categories'))
                <span class="absolute start-0 top-1/2 -translate-y-1/2 w-1 h-5 bg-primary rounded-r-full"></span>
            @endif
            <svg class="h-[18px] w-[18px] shrink-0 transition-transform duration-200 group-hover:scale-110" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
            </svg>
            {{ __('admin.sidebar.topping_categories') }}
        </a>
    @endcan

    @can('view toppings')
        <a href="{{ route('admin.cake-toppings') }}" wire:navigate @class([
            'admin-link group relative',
            'bg-sidebar-active text-white shadow-md shadow-sidebar-active/30' => request()->routeIs('admin.cake-toppings'),
            'text-sidebar-text hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.cake-toppings'),
        ])>
            @if(request()->routeIs('admin.cake-toppings'))
                <span class="absolute start-0 top-1/2 -translate-y-1/2 w-1 h-5 bg-primary rounded-r-full"></span>
            @endif
            <svg class="h-[18px] w-[18px] shrink-0 transition-transform duration-200 group-hover:scale-110" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
            </svg>
            {{ __('admin.sidebar.toppings') }}
        </a>
    @endcan

    @can('view shapes')
        <a href="{{ route('admin.shape-flavors') }}" wire:navigate @class([
            'admin-link group relative',
            'bg-sidebar-active text-white shadow-md shadow-sidebar-active/30' => request()->routeIs('admin.shape-flavors'),
            'text-sidebar-text hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.shape-flavors'),
        ])>
            @if(request()->routeIs('admin.shape-flavors'))
                <span class="absolute start-0 top-1/2 -translate-y-1/2 w-1 h-5 bg-primary rounded-r-full"></span>
            @endif
            <svg class="h-[18px] w-[18px] shrink-0 transition-transform duration-200 group-hover:scale-110" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
            </svg>
            {{ __('admin.sidebar.shape_flavors') }}
        </a>

        <a href="{{ route('admin.shape-toppings') }}" wire:navigate @class([
            'admin-link group relative',
            'bg-sidebar-active text-white shadow-md shadow-sidebar-active/30' => request()->routeIs('admin.shape-toppings'),
            'text-sidebar-text hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.shape-toppings'),
        ])>
            @if(request()->routeIs('admin.shape-toppings'))
                <span class="absolute start-0 top-1/2 -translate-y-1/2 w-1 h-5 bg-primary rounded-r-full"></span>
            @endif
            <svg class="h-[18px] w-[18px] shrink-0 transition-transform duration-200 group-hover:scale-110" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
            </svg>
            {{ __('admin.sidebar.shape_toppings') }}
        </a>
    @endcan

    {{-- User Management Group --}}
    @canany(['view users', 'view roles'])
        <div class="pt-5 mt-3 border-t border-sidebar-border/50">
            <p class="mb-2.5 px-3.5 text-[10px] font-bold uppercase tracking-[0.15em] text-sidebar-text-muted">
                {{ __('admin.sidebar.group_users_roles') }}
            </p>
        </div>

        @can('view users')
            <a href="{{ route('admin.users') }}" wire:navigate @class([
                'admin-link group relative',
                'bg-sidebar-active text-white shadow-md shadow-sidebar-active/30' => request()->routeIs('admin.users*'),
                'text-sidebar-text hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.users*'),
            ])>
                @if(request()->routeIs('admin.users*'))
                    <span class="absolute start-0 top-1/2 -translate-y-1/2 w-1 h-5 bg-primary rounded-r-full"></span>
                @endif
                <svg class="h-[18px] w-[18px] shrink-0 transition-transform duration-200 group-hover:scale-110" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                </svg>
                {{ __('admin.sidebar.users') }}
            </a>
        @endcan

        @can('view roles')
            <a href="{{ route('admin.roles') }}" wire:navigate @class([
                'admin-link group relative',
                'bg-sidebar-active text-white shadow-md shadow-sidebar-active/30' => request()->routeIs('admin.roles*'),
                'text-sidebar-text hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.roles*'),
            ])>
                @if(request()->routeIs('admin.roles*'))
                    <span class="absolute start-0 top-1/2 -translate-y-1/2 w-1 h-5 bg-primary rounded-r-full"></span>
                @endif
                <svg class="h-[18px] w-[18px] shrink-0 transition-transform duration-200 group-hover:scale-110" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12.75L11.25 15 15 9.75m-3-5.25L3.375 7.5A2.25 2.25 0 003 9v4.5a10.5 10.5 0 0010.5 10.5 10.5 10.5 0 0010.5-10.5V9a2.25 2.25 0 00-.375-1.5L12 4.5z" />
                </svg>
                {{ __('admin.sidebar.roles') }}
            </a>
        @endcan
    @endcanany

    {{-- System Group --}}
    @can('view settings')
        <div class="pt-5 mt-3 border-t border-sidebar-border/50">
            <p class="mb-2.5 px-3.5 text-[10px] font-bold uppercase tracking-[0.15em] text-sidebar-text-muted">
                {{ __('admin.sidebar.group_system') }}
            </p>
        </div>
        <a href="{{ route('admin.settings') }}" wire:navigate @class([
            'admin-link group relative',
            'bg-sidebar-active text-white shadow-md shadow-sidebar-active/30' => request()->routeIs('admin.settings'),
            'text-sidebar-text hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.settings'),
        ])>
            @if(request()->routeIs('admin.settings'))
                <span class="absolute start-0 top-1/2 -translate-y-1/2 w-1 h-5 bg-primary rounded-r-full"></span>
            @endif
            <svg class="h-[18px] w-[18px] shrink-0 transition-transform duration-200 group-hover:scale-110" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 0 1 0 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 0 1 0-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
            {{ __('admin.sidebar.settings') }}
        </a>
        <a href="{{ route('admin.languages') }}" wire:navigate @class([
            'admin-link group relative',
            'bg-sidebar-active text-white shadow-md shadow-sidebar-active/30' => request()->routeIs('admin.languages'),
            'text-sidebar-text hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.languages'),
        ])>
            @if(request()->routeIs('admin.languages'))
                <span class="absolute start-0 top-1/2 -translate-y-1/2 w-1 h-5 bg-primary rounded-r-full"></span>
            @endif
            <svg class="h-[18px] w-[18px] shrink-0 transition-transform duration-200 group-hover:scale-110" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
            </svg>
            {{ __('admin.sidebar.languages') }}
        </a>
    @endcan

    @can('view settings audit')
        <a href="{{ route('admin.audit-logs') }}" wire:navigate @class([
            'admin-link group relative',
            'bg-sidebar-active text-white shadow-md shadow-sidebar-active/30' => request()->routeIs('admin.audit-logs'),
            'text-sidebar-text hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.audit-logs'),
        ])>
            @if(request()->routeIs('admin.audit-logs'))
                <span class="absolute start-0 top-1/2 -translate-y-1/2 w-1 h-5 bg-primary rounded-r-full"></span>
            @endif
            <svg class="h-[18px] w-[18px] shrink-0 transition-transform duration-200 group-hover:scale-110" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            {{ __('admin.sidebar.settings_log') }}
        </a>
    @endcan

    @can('view settings')
        <a href="{{ route('admin.assets-importer') }}" wire:navigate @class([
            'admin-link group relative',
            'bg-sidebar-active text-white shadow-md shadow-sidebar-active/30' => request()->routeIs('admin.assets-importer'),
            'text-sidebar-text hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.assets-importer'),
        ])>
            @if(request()->routeIs('admin.assets-importer'))
                <span class="absolute start-0 top-1/2 -translate-y-1/2 w-1 h-5 bg-primary rounded-r-full"></span>
            @endif
            <svg class="h-[18px] w-[18px] shrink-0 transition-transform duration-200 group-hover:scale-110" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
            </svg>
            {{ __('admin.sidebar.asset_import') }}
        </a>
    @endcan
</nav>