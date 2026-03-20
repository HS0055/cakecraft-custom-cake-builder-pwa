<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ in_array(app()->getLocale(), ['ar', 'he', 'fa', 'ur']) ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="description" content="Application installation wizard">
    <title>Install — {{ config('app.name', 'CakeCraft') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300..700&family=DM+Sans:ital,opsz,wght@0,9..40,300..700;1,9..40,300..700&family=Playfair+Display:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <x-appearance-styles />
    @livewireStyles
</head>

<body class="min-h-screen font-sans antialiased bg-gray-50 flex items-center justify-center p-4">
    <div class="w-full max-w-4xl bg-white shadow-xl rounded-2xl overflow-hidden border border-border">
        {{ $slot }}
    </div>
    @livewireScripts
</body>

</html>