@extends('theme::layouts.app')

@section('content')
@php
    $loc = app()->getLocale();
    $title = $post->getTranslation('title', $loc, false) ?: __('(untitled)');
    $body = (string) $post->getTranslation('body', $loc, false);
    $deck = $post->getTranslation('excerpt', $loc, false);
    $cover = $post->coverUrl('large');
    $shareUrl = urlencode($post->url());
    $shareText = urlencode($title);
@endphp

<article class="mx-auto max-w-7xl px-4 py-6">
    <div class="grid gap-8 lg:grid-cols-3">
        <div class="lg:col-span-2">

            {{-- Breadcrumb --}}
            <nav class="mb-3 flex flex-wrap items-center gap-1 text-xs text-ink-400">
                <a href="{{ route('home') }}" class="hover:text-brand-600">{{ app()->getLocale() === 'bn' ? 'হোম' : 'Home' }}</a>
                @if ($post->category)
                    <span>/</span>
                    <a href="{{ $post->category->url() }}" class="hover:text-brand-600">{{ $post->category->getTranslation('name', $loc, false) }}</a>
                @endif
            </nav>

            {{-- Category + title --}}
            @if ($post->category)
                <a href="{{ $post->category->url() }}" class="text-sm font-bold uppercase tracking-wide text-brand-600">{{ $post->category->getTranslation('name', $loc, false) }}</a>
            @endif
            <h1 class="mt-1 text-3xl font-black leading-tight sm:text-4xl">{{ $title }}</h1>
            @if ($deck)
                <p class="mt-3 text-lg text-ink-500">{{ $deck }}</p>
            @endif

            {{-- Meta --}}
            <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 border-y border-ink-100 py-3 text-sm text-ink-500">
                @if ($post->author)
                    <a href="{{ route('author', $post->author->username) }}" class="flex items-center gap-2 font-semibold text-ink-700 hover:text-brand-600">
                        <img src="{{ $post->author->avatarUrl() }}" alt="{{ $post->author->name }}" class="h-8 w-8 rounded-full object-cover">
                        {{ $post->author->name }}
                    </a>
                @endif
                <span>{{ localized_number(optional($post->published_at)->translatedFormat('j F Y, g:i a')) }}</span>
                <span>· {{ localized_number($post->readingTime()) }} {{ app()->getLocale() === 'bn' ? 'মিনিট পড়া' : 'min read' }}</span>
                <span>· {{ localized_number($post->views) }} {{ __('Views') }}</span>
                @include('theme::partials.badges', ['post' => $post])
            </div>

            {{-- Cover --}}
            @if ($cover)
                <figure class="mt-5">
                    <img src="{{ $cover }}" alt="{{ $title }}" class="w-full rounded-2xl object-cover">
                    @if ($post->source)<figcaption class="mt-1 text-xs text-ink-400">{{ __('Source') }}: {{ $post->source }}</figcaption>@endif
                </figure>
            @endif

            {{-- Body / paywall --}}
            <div class="barta-article prose prose-lg mt-6 max-w-none font-serif text-ink-800">
                @if ($locked)
                    <p>{{ str(strip_tags($body))->limit(420, '…') }}</p>
                    @include('theme::partials.paywall')
                @else
                    {!! $body !!}
                @endif
            </div>

            @unless ($locked)
                {{-- In-article ad --}}
                @include('theme::partials.ad', ['slot' => 'in-article', 'class' => 'my-6 text-center'])

                {{-- Tags --}}
                @if ($post->tags->isNotEmpty())
                    <div class="mt-6 flex flex-wrap items-center gap-2">
                        <span class="text-sm font-bold text-ink-500">{{ __('Tags') }}:</span>
                        @foreach ($post->tags as $tag)
                            <a href="{{ $tag->url() }}" class="rounded-full bg-ink-100 px-3 py-1 text-xs font-semibold text-ink-600 hover:bg-brand-600 hover:text-white">
                                {{ $tag->getTranslation('name', $loc, false) }}
                            </a>
                        @endforeach
                    </div>
                @endif

                {{-- Share --}}
                <div class="mt-6 flex flex-wrap items-center gap-2 border-y border-ink-100 py-4">
                    <span class="text-sm font-bold text-ink-500">{{ app()->getLocale() === 'bn' ? 'শেয়ার করুন' : 'Share' }}:</span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" rel="noopener" class="rounded-lg bg-[#1877f2] px-3 py-1.5 text-xs font-bold text-white">Facebook</a>
                    <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareText }}" target="_blank" rel="noopener" class="rounded-lg bg-ink-900 px-3 py-1.5 text-xs font-bold text-white">X</a>
                    <a href="https://api.whatsapp.com/send?text={{ $shareText }}%20{{ $shareUrl }}" target="_blank" rel="noopener" class="rounded-lg bg-[#25d366] px-3 py-1.5 text-xs font-bold text-white">WhatsApp</a>
                    <a href="https://t.me/share/url?url={{ $shareUrl }}&text={{ $shareText }}" target="_blank" rel="noopener" class="rounded-lg bg-[#0088cc] px-3 py-1.5 text-xs font-bold text-white">Telegram</a>
                </div>

                {{-- Author box --}}
                @if ($post->author)
                    <div class="mt-6 flex gap-4 rounded-2xl bg-ink-50 p-5">
                        <img src="{{ $post->author->avatarUrl() }}" alt="{{ $post->author->name }}" class="h-14 w-14 rounded-full object-cover">
                        <div>
                            <a href="{{ route('author', $post->author->username) }}" class="text-lg font-black hover:text-brand-600">{{ $post->author->name }}</a>
                            @if ($post->author->bio)<p class="mt-1 text-sm text-ink-500">{{ $post->author->bio }}</p>@endif
                        </div>
                    </div>
                @endif

                {{-- Comments --}}
                @include('theme::partials.comments', ['post' => $post])
            @endunless
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1">
            @include('theme::partials.sidebar')
        </div>
    </div>

    {{-- Related --}}
    @if ($related->isNotEmpty())
        <section class="mt-12">
            <h2 class="mb-4 flex items-center gap-2 text-xl font-black">
                <span class="h-6 w-1.5 rounded bg-brand-600"></span>
                {{ app()->getLocale() === 'bn' ? 'সম্পর্কিত খবর' : 'Related stories' }}
            </h2>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($related as $post)
                    @include('theme::partials.card', ['post' => $post, 'variant' => 'md'])
                @endforeach
            </div>
        </section>
    @endif
</article>
@endsection
