<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ in_array(app()->getLocale(), ['ar', 'he', 'fa', 'ur']) ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
        $generalSettings = settings(\App\Settings\GeneralSettings::class);
        $brandingSettings = settings(\App\Settings\BrandingSettings::class);
        $pageTitle = isset($title) && $title ? $title : $generalSettings->store_name;
        $pageDescription = isset($metaDescription) && $metaDescription
            ? $metaDescription
            : __('front.home.default_meta_description');
        $pageImage = isset($ogImage) && $ogImage
            ? $ogImage
            : ($brandingSettings->logo_url ?: asset('images/logo.png'));
        $pageUrl = isset($canonicalUrl) && $canonicalUrl ? $canonicalUrl : request()->url();
        $pageOgType = isset($ogType) && $ogType ? $ogType : 'website';
    @endphp

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <link rel="canonical" href="{{ $pageUrl }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="{{ $pageOgType }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:image" content="{{ $pageImage }}">
    <meta property="og:url" content="{{ $pageUrl }}">
    <meta property="og:site_name" content="{{ $generalSettings->store_name }}">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $pageImage }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Caveat:wght@400;500;600;700&family=Comfortaa:wght@300..700&family=DM+Sans:ital,opsz,wght@0,9..40,300..700;1,9..40,300..700&family=Playfair+Display:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    @php
        $faviconHref = $brandingSettings->favicon_url ?: asset('images/logo.png');
        $faviconVersion = file_exists(public_path('favicon.ico')) ? filemtime(public_path('favicon.ico')) : '';
    @endphp
    <link rel="icon" type="image/png" href="{{ $faviconHref }}{{ $faviconVersion ? '?v=' . $faviconVersion : '' }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}{{ $faviconVersion ? '?v=' . $faviconVersion : '' }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-appearance-styles />

    @stack('seo')
    @stack('styles')
    @livewireStyles
    @laravelPWA
</head>

<body class="min-h-screen bg-white font-sans antialiased text-espresso" x-data
    x-init="Alpine.store('cartDrawer', { open: false, toggle() { this.open = !this.open; document.body.style.overflow = this.open ? 'hidden' : '' }, show() { this.open = true; document.body.style.overflow = 'hidden' }, hide() { this.open = false; document.body.style.overflow = '' } })">
    {{-- Header --}}
    <livewire:front.header.header />

    {{-- Spacer to prevent content from going behind fixed header --}}
    <div class="h-16 md:h-20"></div>

    {{-- Page Content --}}
    <main class="pb-20 md:pb-0">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <div class="hidden md:block">
        <x-front.footer />
    </div>

    {{-- Mobile Bottom Navigation --}}
    <x-front.bottom-nav />

    {{-- Global Offcanvas Cart Drawer --}}
    <livewire:front.header.cart-drawer />

    {{-- Toast Notifications --}}
    <div x-data="{
            show: false,
            message: '',
            type: 'success',
            timeout: null,
            init() {
                Livewire.on('cart-updated', () => {
                    this.toast('{{ __('front.cart.added_success') }}', 'success');
                });
                Livewire.on('toast', (params) => {
                    const p = Array.isArray(params) ? params[0] : params;
                    this.toast(p.message || '', p.type || 'success');
                });
            },
            toast(msg, toastType = 'success') {
                this.message = msg;
                this.type = toastType;
                this.show = true;
                clearTimeout(this.timeout);
                this.timeout = setTimeout(() => this.show = false, toastType === 'error' ? 4000 : 2500);
            }
        }" x-show="show" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4" x-cloak :class="type === 'error' ? 'bg-danger' : 'bg-espresso'"
        class="fixed bottom-24 md:bottom-8 start-1/2 -translate-x-1/2 z-[9999] px-6 py-3 text-white rounded-full font-medium text-sm shadow-lg"
        x-text="message">
    </div>

    {{-- Global PWA Install Modal --}}
    <x-front.pwa-install-modal />

    @stack('scripts')
    @livewireScripts
</body>

</html>