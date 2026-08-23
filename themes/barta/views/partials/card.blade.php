{{--
    Reusable article card.
    Params: $post (Post), $variant in [hero, overlay, md, list, text], $showExcerpt (bool)
--}}
@php
    $variant = $variant ?? 'md';
    $showExcerpt = $showExcerpt ?? false;
    $loc = app()->getLocale();
    $title = $post->getTranslation('title', $loc, false) ?: __('(untitled)');
    $cover = $post->coverUrl($variant === 'hero' || $variant === 'overlay' ? 'large' : 'medium');
    $catName = $post->category?->getTranslation('name', $loc, false);
    $date = optional($post->published_at)->translatedFormat('j F Y');
@endphp

@if ($variant === 'list')
    {{-- Small thumbnail + title, side by side (rails / sidebars) --}}
    <article class="flex gap-3 py-3">
        <a href="{{ $post->url() }}" class="block h-16 w-24 shrink-0 overflow-hidden rounded-lg bg-ink-100">
            @if ($cover)<img src="{{ $cover }}" alt="{{ $title }}" class="h-full w-full object-cover" loading="lazy">@endif
        </a>
        <div class="min-w-0">
            <h3 class="barta-clamp-2 text-sm font-bold leading-snug">
                <a href="{{ $post->url() }}" class="hover:text-brand-600">{{ $title }}</a>
            </h3>
            <p class="mt-1 text-xs text-ink-400">{{ localized_number($date) }}</p>
        </div>
    </article>

@elseif ($variant === 'text')
    {{-- No image; headline + meta (dense lists) --}}
    <article class="border-b border-ink-100 py-2.5 last:border-0">
        <h3 class="barta-clamp-2 text-sm font-bold leading-snug">
            <a href="{{ $post->url() }}" class="hover:text-brand-600">{{ $title }}</a>
        </h3>
        @if ($catName)<span class="mt-1 inline-block text-xs font-semibold text-brand-600">{{ $catName }}</span>@endif
    </article>

@elseif ($variant === 'overlay')
    {{-- Image with gradient overlay + title on top (hero secondary) --}}
    <article class="group relative overflow-hidden rounded-xl bg-ink-900">
        <a href="{{ $post->url() }}" class="block">
            <div class="aspect-[16/10] w-full">
                @if ($cover)
                    <img src="{{ $cover }}" alt="{{ $title }}" class="h-full w-full object-cover opacity-80 transition group-hover:scale-105 group-hover:opacity-70" loading="lazy">
                @else
                    <div class="h-full w-full bg-gradient-to-br from-brand-700 to-ink-900"></div>
                @endif
            </div>
            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 to-transparent p-4">
                @if ($catName)<span class="mb-1 inline-block rounded bg-brand-600 px-1.5 py-0.5 text-[11px] font-bold text-white">{{ $catName }}</span>@endif
                <h3 class="barta-clamp-2 text-base font-black leading-snug text-white">{{ $title }}</h3>
            </div>
        </a>
        @include('theme::partials.badges', ['post' => $post, 'onDark' => true])
    </article>

@elseif ($variant === 'hero')
    {{-- Big lead story --}}
    <article class="group">
        <a href="{{ $post->url() }}" class="block overflow-hidden rounded-2xl bg-ink-100">
            <div class="aspect-[16/9] w-full">
                @if ($cover)
                    <img src="{{ $cover }}" alt="{{ $title }}" class="h-full w-full object-cover transition group-hover:scale-105" loading="lazy">
                @else
                    <div class="h-full w-full bg-gradient-to-br from-brand-600 to-brand-900"></div>
                @endif
            </div>
        </a>
        <div class="mt-3">
            <div class="flex items-center gap-2">
                @if ($catName)<a href="{{ $post->category?->url() }}" class="text-sm font-bold uppercase tracking-wide text-brand-600">{{ $catName }}</a>@endif
                @include('theme::partials.badges', ['post' => $post])
            </div>
            <h2 class="mt-1 text-2xl font-black leading-tight sm:text-3xl">
                <a href="{{ $post->url() }}" class="hover:text-brand-700">{{ $title }}</a>
            </h2>
            @if ($showExcerpt)
                <p class="barta-clamp-3 mt-2 text-ink-500">{{ excerpt($post->getTranslation('excerpt', $loc, false) ?: $post->getTranslation('body', $loc, false), 34) }}</p>
            @endif
            <p class="mt-2 text-xs text-ink-400">
                @if ($post->author)<span class="font-semibold text-ink-600">{{ $post->author->name }}</span> · @endif
                {{ localized_number($date) }}
            </p>
        </div>
    </article>

@else
    {{-- Default medium card for grids --}}
    <article class="group flex flex-col">
        <a href="{{ $post->url() }}" class="block overflow-hidden rounded-xl bg-ink-100">
            <div class="aspect-[16/10] w-full">
                @if ($cover)
                    <img src="{{ $cover }}" alt="{{ $title }}" class="h-full w-full object-cover transition group-hover:scale-105" loading="lazy">
                @else
                    <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-ink-100 to-ink-200 text-3xl text-ink-300">ব</div>
                @endif
            </div>
        </a>
        <div class="mt-3 flex flex-1 flex-col">
            <div class="flex items-center gap-2">
                @if ($catName)<a href="{{ $post->category?->url() }}" class="text-xs font-bold uppercase tracking-wide text-brand-600">{{ $catName }}</a>@endif
                @include('theme::partials.badges', ['post' => $post])
            </div>
            <h3 class="barta-clamp-3 mt-1 text-lg font-bold leading-snug">
                <a href="{{ $post->url() }}" class="hover:text-brand-700">{{ $title }}</a>
            </h3>
            @if ($showExcerpt)
                <p class="barta-clamp-2 mt-1 text-sm text-ink-500">{{ excerpt($post->getTranslation('excerpt', $loc, false) ?: $post->getTranslation('body', $loc, false), 22) }}</p>
            @endif
            <p class="mt-2 text-xs text-ink-400">
                @if ($post->author)<span class="font-semibold text-ink-600">{{ $post->author->name }}</span> · @endif
                {{ localized_number($date) }}
            </p>
        </div>
    </article>
@endif
