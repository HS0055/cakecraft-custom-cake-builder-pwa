<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ in_array(app()->getLocale(), ['ar', 'he', 'fa', 'ur']) ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? settings(\App\Settings\GeneralSettings::class)->store_name . ' Admin' }} -
        {{ settings(\App\Settings\GeneralSettings::class)->store_name  }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300..700&family=DM+Sans:ital,opsz,wght@0,9..40,300..700;1,9..40,300..700&family=Playfair+Display:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/x-icon"
        href="{{ settings(\App\Settings\BrandingSettings::class)->favicon_url ?: asset('images/logo.png') }}">

    <x-appearance-styles />
    @livewireStyles
</head>

<body class="min-h-screen bg-background font-sans antialiased" x-data="{ sidebarOpen: false }">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <livewire:admin::sidebar />

        {{-- Main content area --}}
        <div class="flex flex-1 flex-col min-w-0 bg-background">
            {{-- Header --}}
            <livewire:admin::header :title="$title ?? settings(\App\Settings\GeneralSettings::class)->store_name" />

            {{-- Page content --}}
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>
    @livewireScripts
</body>

</html>