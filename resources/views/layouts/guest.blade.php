<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? __('Sign in') }} — @setting('site_name', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-ink-50 font-bengali text-ink-900 antialiased">
    <div class="flex min-h-screen flex-col items-center justify-center px-4 py-10">
        <a href="{{ route('home') }}" class="mb-8 flex items-center gap-2">
            <span class="grid h-10 w-10 place-items-center rounded-lg bg-brand-600 text-lg font-black text-white">ব</span>
            <span class="text-2xl font-black tracking-tight">@setting('site_name', config('app.name'))</span>
        </a>

        <main class="w-full max-w-md rounded-2xl border border-ink-100 bg-white p-8 shadow-sm">
            {{ $slot }}
        </main>

        <p class="mt-6 text-sm text-ink-500">
            &copy; {{ date('Y') }} @setting('site_name', config('app.name')). {{ __('All rights reserved.') }}
        </p>
    </div>

    @livewireScripts
</body>
</html>
