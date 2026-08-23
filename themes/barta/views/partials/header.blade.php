{{-- Masthead: logo + optional header ad. --}}
<header class="border-b border-ink-100">
    <div class="mx-auto flex max-w-7xl flex-col items-center gap-4 px-4 py-5 sm:flex-row sm:justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            @if ($logo = setting('site_logo'))
                <img src="{{ $logo }}" alt="{{ setting('site_name', config('app.name')) }}" class="h-11 w-auto">
            @else
                <span class="grid h-11 w-11 place-items-center rounded-lg bg-brand-600 text-2xl font-black text-white">ব</span>
                <span class="flex flex-col leading-none">
                    <span class="text-3xl font-black tracking-tight text-ink-900">{{ setting('site_name', config('app.name')) }}</span>
                    @if ($tagline = setting('site_tagline'))
                        <span class="mt-1 text-xs font-medium text-ink-400">{{ $tagline }}</span>
                    @endif
                </span>
            @endif
        </a>

        <div class="hidden md:block">
            @include('theme::partials.ad', ['slot' => 'header'])
        </div>
    </div>

    {{-- Primary navigation --}}
    <nav x-data="{ open: false }" class="sticky top-0 z-40 border-y border-ink-100 bg-brand-600 text-white shadow-sm">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4">
            <ul class="hidden items-center md:flex">
                <li>
                    <a href="{{ route('home') }}" class="block px-3 py-3 text-sm font-bold hover:bg-brand-700">{{ app()->getLocale() === 'bn' ? 'হোম' : 'Home' }}</a>
                </li>
                @php($primary = $siteMenus->get('primary'))
                @if ($primary && $primary->items->isNotEmpty())
                    @include('theme::partials.menu', ['items' => $primary->items])
                @else
                    @foreach (\App\Models\Category::active()->parents()->orderBy('position')->take(8)->get() as $cat)
                        <li>
                            <a href="{{ $cat->url() }}" class="block px-3 py-3 text-sm font-bold hover:bg-brand-700">
                                {{ $cat->getTranslation('name', app()->getLocale(), false) }}
                            </a>
                        </li>
                    @endforeach
                @endif
            </ul>

            {{-- Mobile toggle --}}
            <button x-on:click="open = !open" class="py-3 md:hidden" aria-label="Menu">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            {{-- Search --}}
            <form method="GET" action="{{ route('search') }}" class="hidden items-center md:flex">
                <input type="search" name="q" value="{{ request('q') }}"
                       placeholder="{{ __('Search') }}…"
                       class="w-40 rounded-l-md border-0 py-1.5 text-sm text-ink-900 focus:ring-2 focus:ring-brand-300 lg:w-56">
                <button type="submit" class="rounded-r-md bg-brand-800 px-3 py-1.5 hover:bg-brand-900" aria-label="{{ __('Search') }}">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
                </button>
            </form>
        </div>

        {{-- Mobile menu panel --}}
        <div x-show="open" x-cloak class="border-t border-brand-500 md:hidden">
            <form method="GET" action="{{ route('search') }}" class="flex p-3">
                <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('Search') }}…"
                       class="w-full rounded-l-md border-0 py-2 text-sm text-ink-900">
                <button type="submit" class="rounded-r-md bg-brand-800 px-4">🔍</button>
            </form>
            <ul class="pb-3">
                @php($primary = $siteMenus->get('primary'))
                @if ($primary && $primary->items->isNotEmpty())
                    @foreach ($primary->items as $item)
                        <li><a href="{{ $item->resolveUrl() }}" class="block px-4 py-2 text-sm font-semibold hover:bg-brand-700">{{ $item->getTranslation('label', app()->getLocale(), false) }}</a></li>
                    @endforeach
                @else
                    @foreach (\App\Models\Category::active()->parents()->orderBy('position')->take(10)->get() as $cat)
                        <li><a href="{{ $cat->url() }}" class="block px-4 py-2 text-sm font-semibold hover:bg-brand-700">{{ $cat->getTranslation('name', app()->getLocale(), false) }}</a></li>
                    @endforeach
                @endif
            </ul>
        </div>
    </nav>
</header>
