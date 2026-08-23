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
<body class="min-h-screen bg-slate-100 font-bengali text-ink-900 antialiased">
<div class="fixed inset-x-0 top-0 z-50 h-1 bg-gradient-to-r from-brand-700 via-brand-500 to-amber-400"></div>
<div x-data="{ open: false }" class="flex min-h-screen pt-1">
    {{-- Sidebar --}}
    <aside class="fixed bottom-0 left-0 top-1 z-40 flex w-72 -translate-x-full flex-col border-r border-slate-200 bg-white text-ink-700 shadow-2xl transition-transform duration-300 lg:sticky lg:top-1 lg:h-[calc(100vh-0.25rem)] lg:translate-x-0 lg:shadow-none"
           :class="open && 'translate-x-0'">
        <div class="flex h-16 shrink-0 items-center gap-3 border-b border-slate-100 px-5">
            <span class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 text-lg font-black text-white shadow-lg shadow-brand-200">ব</span>
            <div class="min-w-0">
                <span class="block truncate text-base font-black text-ink-900">@setting('site_name', 'Barta')</span>
                <span class="block text-[10px] font-bold uppercase tracking-[.18em] text-ink-400">{{ __('Newsroom CMS') }}</span>
            </div>
            <button @click="open = false" class="ml-auto rounded-lg p-2 text-ink-400 hover:bg-ink-50 lg:hidden" aria-label="{{ __('Close menu') }}">&times;</button>
        </div>

        @php
            $nav = [
                ['heading' => __('Content')],
                ['route' => 'admin.dashboard', 'label' => __('Dashboard'), 'match' => 'admin.dashboard', 'icon' => '▦'],
                ['route' => 'admin.posts.index', 'label' => __('Posts'), 'match' => 'admin.posts.*', 'icon' => '▤'],
                ['route' => 'admin.categories.index', 'label' => __('Categories'), 'match' => 'admin.categories.*', 'icon' => '□'],
                ['route' => 'admin.tags.index', 'label' => __('Tags'), 'match' => 'admin.tags.*', 'icon' => '◇'],
                ['route' => 'admin.comments.index', 'label' => __('Comments'), 'match' => 'admin.comments.*', 'icon' => '◯'],
                ['route' => 'admin.media.index', 'label' => __('Media library'), 'match' => 'admin.media.*', 'icon' => '▧'],
                ['heading' => __('Appearance')],
                ['route' => 'admin.menus.index', 'label' => __('Menus'), 'match' => 'admin.menus.*', 'icon' => '☷'],
                ['route' => 'admin.widgets.index', 'label' => __('Widgets'), 'match' => 'admin.widgets.*', 'icon' => '◫'],
                ['route' => 'admin.themes.index', 'label' => __('Themes'), 'match' => 'admin.themes.*', 'icon' => '◩'],
                ['route' => 'admin.plugins.index', 'label' => __('Plugins'), 'match' => 'admin.plugins.*', 'icon' => '⬡'],
                ['heading' => __('Monetisation')],
                ['route' => 'admin.ads.index', 'label' => __('Advertisements'), 'match' => 'admin.ads.*', 'icon' => '⌁'],
                ['route' => 'admin.plans.index', 'label' => __('Plans'), 'match' => 'admin.plans.*', 'icon' => '৳'],
                ['heading' => __('Audience')],
                ['route' => 'admin.subscribers.index', 'label' => __('Subscribers'), 'match' => 'admin.subscribers.*', 'icon' => '♙'],
                ['route' => 'admin.newsletters.index', 'label' => __('Newsletters'), 'match' => 'admin.newsletters.*', 'icon' => '✉'],
                ['heading' => __('System'), 'can' => 'manage users'],
                ['route' => 'admin.users.index', 'label' => __('Users'), 'match' => 'admin.users.*', 'can' => 'manage users', 'icon' => '♧'],
                ['route' => 'admin.settings', 'label' => __('Settings'), 'match' => 'admin.settings', 'can' => 'manage settings', 'icon' => '⚙'],
            ];
        @endphp

        <nav class="flex-1 space-y-0.5 overflow-y-auto px-3 py-3 text-sm">
            @foreach ($nav as $item)
                @if (isset($item['can']) && ! auth()->user()->can($item['can']))
                    @continue
                @endif
                @if (isset($item['heading']))
                    <p class="px-3 pb-1.5 pt-4 text-[10px] font-black uppercase tracking-[.18em] text-slate-400">{{ $item['heading'] }}</p>
                @else
                    <a href="{{ route($item['route']) }}"
                       @class([
                           'group flex items-center gap-3 rounded-xl px-3 py-2.5 font-semibold transition',
                           'bg-brand-50 text-brand-700 shadow-sm ring-1 ring-brand-100' => request()->routeIs($item['match']),
                           'text-slate-600 hover:bg-slate-50 hover:text-ink-900' => ! request()->routeIs($item['match']),
                       ])>
                        <span @class(['grid h-7 w-7 place-items-center rounded-lg text-base', 'bg-brand-600 text-white' => request()->routeIs($item['match']), 'bg-slate-100 text-slate-500 group-hover:bg-white' => ! request()->routeIs($item['match'])])>{{ $item['icon'] }}</span>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </nav>
        <div class="shrink-0 border-t border-slate-100 p-3">
            <div class="flex items-center gap-3 rounded-xl bg-slate-50 p-3">
                <img src="{{ auth()->user()->avatarUrl() }}" alt="" class="h-9 w-9 rounded-xl object-cover ring-2 ring-white">
                <div class="min-w-0 flex-1"><p class="truncate text-sm font-bold">{{ auth()->user()->name }}</p><p class="truncate text-[10px] text-slate-400">{{ auth()->user()->getRoleNames()->first() ?? __('Staff') }}</p></div>
                <a href="{{ route('account') }}" class="text-slate-400 hover:text-brand-600">↗</a>
            </div>
        </div>
    </aside>

    {{-- Backdrop for mobile --}}
    <div x-show="open" @click="open = false" x-cloak class="fixed inset-0 z-30 bg-black/40 lg:hidden"></div>

    {{-- Main --}}
    <div class="flex min-w-0 flex-1 flex-col">
        <header class="sticky top-1 z-20 flex h-16 items-center justify-between gap-4 border-b border-slate-200 bg-white/95 px-4 backdrop-blur lg:px-7">
            <button @click="open = !open" class="rounded-lg p-2 text-ink-600 hover:bg-ink-100 lg:hidden" aria-label="Menu">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            <div class="hidden min-w-0 lg:block">
                <p class="truncate text-sm font-black text-ink-800">{{ $title ?? __('Dashboard') }}</p>
                <p class="text-[10px] text-slate-400">{{ __('Admin panel') }} / {{ $title ?? __('Dashboard') }}</p>
            </div>

            <div class="ml-auto flex items-center gap-2 sm:gap-3">
                <a href="{{ route('home') }}" target="_blank" class="hidden text-sm font-semibold text-ink-600 hover:text-brand-600 sm:block">
                    {{ __('View site') }} ↗
                </a>

                {{-- Language switch --}}
                <div class="hidden items-center gap-1 rounded-lg bg-slate-100 p-1 text-xs sm:flex">
                    @foreach (barta_locales() as $loc)
                        <a href="{{ route('lang.switch', $loc) }}"
                           @class(['rounded-md px-2 py-1 font-bold', 'bg-white text-brand-700 shadow-sm' => app()->getLocale() === $loc, 'text-ink-500 hover:text-ink-800' => app()->getLocale() !== $loc])>
                            {{ strtoupper($loc) }}
                        </a>
                    @endforeach
                </div>

                {{-- User menu --}}
                <div x-data="{ menu: false }" class="relative">
                    <button @click="menu = !menu" class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white p-1 pr-2 hover:bg-slate-50">
                        <img src="{{ auth()->user()->avatarUrl() }}" alt="" class="h-8 w-8 rounded-lg object-cover">
                        <span class="hidden text-left sm:block"><strong class="block max-w-28 truncate text-xs">{{ auth()->user()->name }}</strong><small class="block text-[9px] capitalize text-slate-400">{{ auth()->user()->getRoleNames()->first() ?? __('Staff') }}</small></span>
                        <span class="text-xs text-slate-400">⌄</span>
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

        <main class="flex-1 p-4 sm:p-5 lg:p-7">
            {{ $slot }}
        </main>
    </div>
</div>

@livewireScripts
</body>
</html>
