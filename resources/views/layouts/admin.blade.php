<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? __('Dashboard') }} — {{ __('Admin') }} · @setting('site_name', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-ink-50 font-bengali text-ink-900 antialiased">
<div x-data="{ open: false }" class="flex min-h-screen">
    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full overflow-y-auto bg-ink-900 text-ink-100 transition-transform lg:static lg:translate-x-0"
           :class="open && 'translate-x-0'">
        <div class="flex h-16 items-center gap-2 border-b border-white/10 px-5">
            <span class="grid h-9 w-9 place-items-center rounded-lg bg-brand-600 font-black text-white">ব</span>
            <span class="text-lg font-black">@setting('site_name', 'Barta')</span>
        </div>

        @php
            $nav = [
                ['heading' => __('Content')],
                ['route' => 'admin.dashboard', 'label' => __('Dashboard'), 'match' => 'admin.dashboard'],
                ['route' => 'admin.posts.index', 'label' => __('Posts'), 'match' => 'admin.posts.*'],
                ['route' => 'admin.categories.index', 'label' => __('Categories'), 'match' => 'admin.categories.*'],
                ['route' => 'admin.tags.index', 'label' => __('Tags'), 'match' => 'admin.tags.*'],
                ['route' => 'admin.comments.index', 'label' => __('Comments'), 'match' => 'admin.comments.*'],
                ['route' => 'admin.media.index', 'label' => __('Media'), 'match' => 'admin.media.*'],
                ['heading' => __('Appearance')],
                ['route' => 'admin.menus.index', 'label' => __('Menus'), 'match' => 'admin.menus.*'],
                ['route' => 'admin.widgets.index', 'label' => __('Widgets'), 'match' => 'admin.widgets.*'],
                ['route' => 'admin.themes.index', 'label' => __('Themes'), 'match' => 'admin.themes.*'],
                ['route' => 'admin.plugins.index', 'label' => __('Plugins'), 'match' => 'admin.plugins.*'],
                ['heading' => __('Monetisation')],
                ['route' => 'admin.ads.index', 'label' => __('Advertisements'), 'match' => 'admin.ads.*'],
                ['route' => 'admin.plans.index', 'label' => __('Plans'), 'match' => 'admin.plans.*'],
                ['heading' => __('Audience')],
                ['route' => 'admin.subscribers.index', 'label' => __('Subscribers'), 'match' => 'admin.subscribers.*'],
                ['route' => 'admin.newsletters.index', 'label' => __('Newsletters'), 'match' => 'admin.newsletters.*'],
                ['heading' => __('System'), 'can' => 'manage users'],
                ['route' => 'admin.users.index', 'label' => __('Users'), 'match' => 'admin.users.*', 'can' => 'manage users'],
                ['route' => 'admin.settings', 'label' => __('Settings'), 'match' => 'admin.settings', 'can' => 'manage settings'],
            ];
        @endphp

        <nav class="space-y-0.5 px-3 py-4 text-sm">
            @foreach ($nav as $item)
                @if (isset($item['can']) && ! auth()->user()->can($item['can']))
                    @continue
                @endif
                @if (isset($item['heading']))
                    <p class="px-3 pb-1 pt-4 text-xs font-bold uppercase tracking-wider text-ink-400">{{ $item['heading'] }}</p>
                @else
                    <a href="{{ route($item['route']) }}"
                       @class([
                           'flex items-center rounded-lg px-3 py-2 font-medium transition',
                           'bg-brand-600 text-white' => request()->routeIs($item['match']),
                           'text-ink-200 hover:bg-white/5' => ! request()->routeIs($item['match']),
                       ])>
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach
        </nav>
    </aside>

    {{-- Backdrop for mobile --}}
    <div x-show="open" @click="open = false" x-cloak class="fixed inset-0 z-30 bg-black/40 lg:hidden"></div>

    {{-- Main --}}
    <div class="flex min-w-0 flex-1 flex-col">
        <header class="sticky top-0 z-20 flex h-16 items-center justify-between gap-4 border-b border-ink-100 bg-white px-4 lg:px-6">
            <button @click="open = !open" class="rounded-lg p-2 text-ink-600 hover:bg-ink-100 lg:hidden" aria-label="Menu">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            <div class="ml-auto flex items-center gap-3">
                <a href="{{ route('home') }}" target="_blank" class="hidden text-sm font-semibold text-ink-600 hover:text-brand-600 sm:block">
                    {{ __('View site') }} ↗
                </a>

                {{-- Language switch --}}
                <div class="flex items-center gap-1 text-sm">
                    @foreach (barta_locales() as $loc)
                        <a href="{{ route('lang.switch', $loc) }}"
                           @class(['rounded px-2 py-1 font-semibold', 'bg-brand-50 text-brand-700' => app()->getLocale() === $loc, 'text-ink-500 hover:text-ink-800' => app()->getLocale() !== $loc])>
                            {{ strtoupper($loc) }}
                        </a>
                    @endforeach
                </div>

                {{-- User menu --}}
                <div x-data="{ menu: false }" class="relative">
                    <button @click="menu = !menu" class="flex items-center gap-2 rounded-lg p-1 pr-2 hover:bg-ink-100">
                        <img src="{{ auth()->user()->avatarUrl() }}" alt="" class="h-8 w-8 rounded-full object-cover">
                        <span class="hidden text-sm font-semibold sm:block">{{ auth()->user()->name }}</span>
                    </button>
                    <div x-show="menu" @click.outside="menu = false" x-cloak
                         class="absolute right-0 mt-2 w-48 rounded-lg border border-ink-100 bg-white py-1 shadow-lg">
                        <a href="{{ route('account') }}" class="block px-4 py-2 text-sm hover:bg-ink-50">{{ __('My account') }}</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-brand-600 hover:bg-ink-50">{{ __('Log out') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Flash messages --}}
        @if (session('status') || session('error'))
            <div class="px-4 pt-4 lg:px-6">
                @if (session('status'))
                    <div class="rounded-lg bg-green-50 px-4 py-3 text-sm font-medium text-green-800">{{ session('status') }}</div>
                @endif
                @if (session('error'))
                    <div class="rounded-lg bg-brand-50 px-4 py-3 text-sm font-medium text-brand-800">{{ session('error') }}</div>
                @endif
            </div>
        @endif

        <main class="flex-1 p-4 lg:p-6">
            {{ $slot }}
        </main>
    </div>
</div>

@livewireScripts
</body>
</html>
