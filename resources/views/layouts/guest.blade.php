<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ in_array(app()->getLocale(), ['ar', 'he', 'fa', 'ur']) ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? settings(\App\Settings\GeneralSettings::class)->store_name }} - {{ __('auth.login.title') }}
    </title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300..700&family=DM+Sans:ital,opsz,wght@0,9..40,300..700;1,9..40,300..700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @php $favVer = file_exists(public_path('favicon.ico')) ? '?v=' . filemtime(public_path('favicon.ico')) : ''; @endphp
    <link rel="icon" type="image/png"
        href="{{ settings(\App\Settings\BrandingSettings::class)->favicon_url ?: asset('images/logo.png') }}{{ $favVer }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}{{ $favVer }}">
    <x-appearance-styles />
    @livewireStyles
</head>

<body
    class="min-h-screen bg-background text-foreground font-sans antialiased flex selection:bg-primary selection:text-white">

    {{-- LEFT SIDE: Branding & Presentation --}}
    <div
        class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-sidebar flex-col justify-between p-12 lg:p-16 border-r border-border">
        <!-- Abstract Branding Element -->
        <div class="absolute -top-32 -start-32 w-96 h-96 bg-primary/10 rounded-full blur-3xl mix-blend-multiply"></div>
        <div class="absolute top-1/2 -end-32 w-80 h-80 bg-accent/10 rounded-full blur-3xl mix-blend-multiply"></div>
        <div
            class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0IiBoZWlnaHQ9IjQiPgo8cmVjdCB3aWR0aD0iNCIgaGVpZ2h0PSI0IiBmaWxsPSIjMDAwIiBmaWxsLW9wYWNpdHk9IjAuMDIiLz4KPC9zdmc+')] mix-blend-overlay">
        </div>

        <div class="relative z-10">
            <div class="flex items-center gap-3 text-sidebar-text">
                <div class="flex h-12 w-12 items-center justify-center overflow-hidden">
                    @if(settings(\App\Settings\BrandingSettings::class)->logo_url)
                        <img src="{{ settings(\App\Settings\BrandingSettings::class)->logo_url }}" alt="Logo"
                            class="h-full w-full object-contain">
                    @else
                        <img src="{{ asset('images/logo.png') }}" alt="Default Logo" class="h-full w-full object-contain">
                    @endif
                </div>
                <span class="font-display text-2xl font-bold tracking-tight text-sidebar-text">
                    {{ settings(\App\Settings\GeneralSettings::class)->store_name ?? 'CakeCraft' }}
                </span>
            </div>
        </div>

        <div class="relative z-10 max-w-lg mt-auto pb-12">
            <h2 class="font-display text-4xl lg:text-5xl font-bold text-sidebar-text leading-[1.15] mb-6">
                {{ __('auth.guest_layout.manage_creations') }} <br> <span
                    class="text-primary">{{ __('auth.guest_layout.sweet') }}</span>
                {{ __('auth.guest_layout.creations') }}
            </h2>
        </div>
    </div>

    {{-- RIGHT SIDE: Form Area --}}
    <div class="w-full lg:w-1/2 flex flex-col items-center justify-center p-6 sm:p-12 lg:p-24 bg-background relative">
        <div class="absolute top-8 start-8 lg:hidden flex items-center gap-2 text-primary">
            @if(settings(\App\Settings\BrandingSettings::class)->logo_url)
                <img src="{{ settings(\App\Settings\BrandingSettings::class)->logo_url }}" alt="Logo"
                    class="h-7 w-7 object-contain">
            @else
                <img src="{{ asset('images/logo.png') }}" alt="Default Logo" class="h-7 w-7 object-contain">
            @endif
            <span
                class="font-display text-xl font-bold">{{ settings(\App\Settings\GeneralSettings::class)->store_name ?? 'CakeCraft' }}</span>
        </div>

        <div class="w-full max-w-[440px]">
            {{ $slot }}
        </div>
    </div>
    @livewireScripts
</body>

</html>