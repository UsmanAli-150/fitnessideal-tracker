<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ea580c">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-slate-50 via-orange-50/60 to-slate-100 relative overflow-hidden">

        <!-- Decorative background blobs -->
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-brand-200/40 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-brand-300/30 rounded-full blur-3xl"></div>

        <div class="relative w-full sm:max-w-md mt-6 px-6 py-8 bg-white/90 backdrop-blur-sm shadow-xl rounded-2xl border border-gray-100 animate-fade-in-up">
            <div class="flex flex-col items-center mb-6">
                <a href="/" class="text-2xl font-bold text-brand-600 mb-1">{{ config('app.name', 'Routine Tracker') }}</a>
                <p class="text-sm text-gray-400">Build better habits, one day at a time</p>
            </div>

            {{ $slot }}
        </div>
    </div>

    @stack('scripts')
</body>

</html>