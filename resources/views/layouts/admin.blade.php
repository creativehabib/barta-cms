<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? __('Dashboard') }} — @setting('site_name', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-white font-bengali text-zinc-800 antialiased">
<div x-data="{ sidebarOpen: false }" class="flex min-h-screen bg-zinc-50">
    @php
        $nav = [
            ['heading' => __('Content')],
            ['route' => 'admin.dashboard', 'label' => __('Dashboard'), 'match' => 'admin.dashboard', 'icon' => 'home'],
            ['route' => 'admin.posts.index', 'label' => __('Posts'), 'match' => 'admin.posts.*', 'icon' => 'document'],
            ['route' => 'admin.categories.index', 'label' => __('Categories'), 'match' => 'admin.categories.*', 'icon' => 'folder'],
            ['route' => 'admin.tags.index', 'label' => __('Tags'), 'match' => 'admin.tags.*', 'icon' => 'tag'],
            ['route' => 'admin.comments.index', 'label' => __('Comments'), 'match' => 'admin.comments.*', 'icon' => 'chat'],
            ['route' => 'admin.media.index', 'label' => __('Media library'), 'match' => 'admin.media.*', 'icon' => 'photo'],
            ['heading' => __('Appearance')],
            ['route' => 'admin.menus.index', 'label' => __('Menus'), 'match' => 'admin.menus.*', 'icon' => 'bars'],
            ['route' => 'admin.widgets.index', 'label' => __('Widgets'), 'match' => 'admin.widgets.*', 'icon' => 'squares'],
            ['route' => 'admin.themes.index', 'label' => __('Themes'), 'match' => 'admin.themes.*', 'icon' => 'brush'],
            ['route' => 'admin.plugins.index', 'label' => __('Plugins'), 'match' => 'admin.plugins.*', 'icon' => 'puzzle'],
            ['heading' => __('Monetisation')],
            ['route' => 'admin.ads.index', 'label' => __('Advertisements'), 'match' => 'admin.ads.*', 'icon' => 'megaphone'],
            ['route' => 'admin.plans.index', 'label' => __('Plans'), 'match' => 'admin.plans.*', 'icon' => 'credit-card'],
            ['heading' => __('Audience')],
            ['route' => 'admin.subscribers.index', 'label' => __('Subscribers'), 'match' => 'admin.subscribers.*', 'icon' => 'users'],
            ['route' => 'admin.newsletters.index', 'label' => __('Newsletters'), 'match' => 'admin.newsletters.*', 'icon' => 'mail'],
            ['heading' => __('System'), 'can' => 'manage users'],
            ['route' => 'admin.users.index', 'label' => __('Users'), 'match' => 'admin.users.*', 'can' => 'manage users', 'icon' => 'user'],
            ['route' => 'admin.settings', 'label' => __('Settings'), 'match' => 'admin.settings', 'can' => 'manage settings', 'icon' => 'cog'],
        ];
    @endphp

    {{-- Flux-style stashable sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col border-r border-zinc-200 bg-zinc-50 transition-transform duration-200 lg:translate-x-0" :class="sidebarOpen && 'translate-x-0'">
        <div class="flex h-16 shrink-0 items-center px-5">
            <a href="{{ route('admin.dashboard') }}" class="flex min-w-0 items-center gap-2.5">
                <span class="grid size-8 shrink-0 place-items-center rounded-lg bg-zinc-900 text-sm font-bold text-white">ব</span>
                <span class="truncate text-base font-semibold text-zinc-900">@setting('site_name', 'Barta')</span>
            </a>
            <button @click="sidebarOpen = false" class="ml-auto rounded-md p-2 text-zinc-500 hover:bg-zinc-200 lg:hidden" aria-label="{{ __('Close menu') }}">&times;</button>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 pb-4">
            @foreach ($nav as $item)
                @if (isset($item['can']) && ! auth()->user()->can($item['can'])) @continue @endif
                @if (isset($item['heading']))
                    <p class="mb-1 mt-5 px-2 text-xs font-medium text-zinc-400 first:mt-2">{{ $item['heading'] }}</p>
                @else
                    <a href="{{ route($item['route']) }}" @class([
                        'mb-0.5 flex items-center gap-3 rounded-lg px-2.5 py-2 text-sm font-medium transition-colors',
                        'bg-white text-zinc-950 shadow-sm ring-1 ring-zinc-200' => request()->routeIs($item['match']),
                        'text-zinc-600 hover:bg-zinc-200/70 hover:text-zinc-950' => ! request()->routeIs($item['match']),
                    ])>
                        <x-admin.icon :name="$item['icon']" @class(['text-zinc-900' => request()->routeIs($item['match']), 'text-zinc-400' => ! request()->routeIs($item['match'])]) />
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </nav>

        <div x-data="{ userMenu: false }" class="relative border-t border-zinc-200 p-3">
            <div x-show="userMenu" @click.outside="userMenu = false" x-cloak class="absolute bottom-full left-3 right-3 mb-2 overflow-hidden rounded-lg border border-zinc-200 bg-white p-1 shadow-lg">
                <a href="{{ route('account') }}" class="flex items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-zinc-100"><x-admin.icon name="user" class="size-4" />{{ __('My account') }}</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="flex w-full items-center gap-2 rounded-md px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50"><x-admin.icon name="logout" class="size-4" />{{ __('Log out') }}</button></form>
            </div>
            <button @click="userMenu = !userMenu" class="flex w-full items-center gap-3 rounded-lg p-2 text-left hover:bg-zinc-200/70">
                <img src="{{ auth()->user()->avatarUrl() }}" alt="" class="size-8 rounded-lg object-cover">
                <span class="min-w-0 flex-1"><strong class="block truncate text-sm font-medium text-zinc-900">{{ auth()->user()->name }}</strong><small class="block truncate text-xs text-zinc-500">{{ auth()->user()->email }}</small></span>
                <span class="text-zinc-400">•••</span>
            </button>
        </div>
    </aside>

    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak class="fixed inset-0 z-40 bg-zinc-950/30 backdrop-blur-sm lg:hidden"></div>

    <div class="flex min-w-0 flex-1 flex-col lg:pl-64">
        <header class="sticky top-0 z-30 flex h-16 items-center gap-3 border-b border-zinc-200 bg-white/90 px-4 backdrop-blur-xl sm:px-6">
            <button @click="sidebarOpen = true" class="rounded-lg p-2 text-zinc-600 hover:bg-zinc-100 lg:hidden" aria-label="{{ __('Menu') }}"><x-admin.icon name="bars" /></button>
            <div class="min-w-0"><h1 class="truncate text-sm font-semibold text-zinc-900">{{ $title ?? __('Dashboard') }}</h1><p class="hidden text-xs text-zinc-500 sm:block">{{ __('Manage your publication') }}</p></div>
            <div class="ml-auto flex items-center gap-2">
                <a href="{{ route('home') }}" target="_blank" class="inline-flex items-center gap-2 rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm font-medium text-zinc-700 shadow-sm hover:bg-zinc-50"><x-admin.icon name="eye" class="size-4" /><span class="hidden sm:inline">{{ __('View site') }}</span></a>
                <a href="{{ route('admin.posts.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-zinc-900 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-zinc-700"><x-admin.icon name="plus" class="size-4" /><span class="hidden sm:inline">{{ __('New post') }}</span></a>
            </div>
        </header>

        @if (session('status') || session('error'))
            <div class="px-4 pt-5 sm:px-6 lg:px-8">
                @if (session('status')) <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div> @endif
                @if (session('error')) <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div> @endif
            </div>
        @endif

        <main class="flex-1 p-4 sm:p-6 lg:p-8">{{ $slot }}</main>
    </div>
</div>
@livewireScripts
</body>
</html>
