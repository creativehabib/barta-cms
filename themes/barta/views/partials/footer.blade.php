{{-- Site footer: three widget columns, footer menu, copyright. --}}
<footer class="mt-12 border-t-4 border-brand-600 bg-ink-900 text-ink-300">
    <div class="mx-auto max-w-7xl px-4 py-10">
        <div class="grid gap-8 md:grid-cols-4">
            {{-- Brand column --}}
            <div>
                <div class="flex items-center gap-2">
                    <span class="grid h-9 w-9 place-items-center rounded-lg bg-brand-600 text-lg font-black text-white">ব</span>
                    <span class="text-xl font-black text-white">{{ setting('site_name', config('app.name')) }}</span>
                </div>
                <p class="mt-3 text-sm text-ink-400">{{ setting('site_description', setting('site_tagline', '')) }}</p>
            </div>

            {{-- Footer widget areas --}}
            <div class="[&_h3]:text-white [&_a]:text-ink-300 [&_a:hover]:text-white">
                @include('theme::partials.widget-area', ['area' => 'footer-1'])
            </div>
            <div class="[&_h3]:text-white [&_a]:text-ink-300 [&_a:hover]:text-white">
                @include('theme::partials.widget-area', ['area' => 'footer-2'])
            </div>
            <div class="[&_h3]:text-white [&_a]:text-ink-300 [&_a:hover]:text-white">
                @include('theme::partials.widget-area', ['area' => 'footer-3'])
            </div>
        </div>

        {{-- Footer menu --}}
        @php($footerMenu = $siteMenus->get('footer'))
        @if ($footerMenu && $footerMenu->items->isNotEmpty())
            <nav class="mt-8 flex flex-wrap gap-x-5 gap-y-2 border-t border-ink-800 pt-6 text-sm">
                @foreach ($footerMenu->items as $item)
                    <a href="{{ $item->resolveUrl() }}" class="text-ink-300 hover:text-white">
                        {{ $item->getTranslation('label', app()->getLocale(), false) }}
                    </a>
                @endforeach
            </nav>
        @endif
    </div>

    <div class="border-t border-ink-800">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-2 px-4 py-4 text-xs text-ink-500 sm:flex-row">
            <p>&copy; {{ localized_number(date('Y')) }} {{ setting('site_name', config('app.name')) }}. {{ __('All rights reserved.') }}</p>
            <div class="flex items-center gap-4">
                <a href="{{ url('/sitemap.xml') }}" class="hover:text-white">Sitemap</a>
                @if ($fb = setting('facebook_url'))<a href="{{ $fb }}" target="_blank" rel="noopener" class="hover:text-white">Facebook</a>@endif
                @if ($tw = setting('twitter_handle'))<a href="https://twitter.com/{{ ltrim($tw, '@') }}" target="_blank" rel="noopener" class="hover:text-white">X / Twitter</a>@endif
                @if ($yt = setting('youtube_url'))<a href="{{ $yt }}" target="_blank" rel="noopener" class="hover:text-white">YouTube</a>@endif
            </div>
        </div>
    </div>
</footer>
