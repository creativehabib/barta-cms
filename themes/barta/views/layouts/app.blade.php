<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO: title, description, canonical, Open Graph, Twitter, JSON-LD --}}
    {!! app('barta.seo')->render() !!}

    @if ($ga = setting('google_analytics_id'))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $ga }}');
        </script>
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ theme_asset('theme.css') }}">
    <link rel="alternate" type="application/rss+xml" title="{{ setting('site_name', config('app.name')) }}" href="{{ url('/sitemap.xml') }}">
    @livewireStyles
    @stack('head')
    @doAction('theme.head')
</head>
<body class="min-h-screen bg-white font-bengali text-ink-900 antialiased">
    <div class="flex min-h-screen flex-col">
        @include('theme::partials.topbar')
        @include('theme::partials.header')
        @include('theme::partials.breaking')

        <main class="flex-1">
            @yield('content')
        </main>

        @include('theme::partials.footer')
    </div>

    @livewireScripts
    @stack('scripts')
    @doAction('theme.footer')
</body>
</html>
