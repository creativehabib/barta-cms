@extends('theme::layouts.app')

@section('content')
@php($loc = app()->getLocale())
<div class="mx-auto max-w-7xl px-4 py-6">

    {{-- Author header --}}
    <header class="flex flex-col items-center gap-4 rounded-2xl bg-ink-50 p-8 text-center sm:flex-row sm:text-left">
        <img src="{{ $author->avatarUrl() }}" alt="{{ $author->name }}" class="h-24 w-24 rounded-full object-cover ring-4 ring-white">
        <div>
            <h1 class="text-3xl font-black">{{ $author->name }}</h1>
            @if ($author->role_label ?? false)
                <p class="mt-1 text-sm font-semibold text-brand-600">{{ $author->role_label }}</p>
            @endif
            @if ($author->bio)
                <p class="mt-2 max-w-2xl text-ink-500">{{ $author->bio }}</p>
            @endif
            <div class="mt-3 flex flex-wrap items-center justify-center gap-4 text-sm text-ink-400 sm:justify-start">
                @if ($author->website)<a href="{{ $author->website }}" target="_blank" rel="noopener" class="hover:text-brand-600">{{ __('Website') }}</a>@endif
                @if ($author->twitter_handle ?? false)<a href="https://twitter.com/{{ ltrim($author->twitter_handle, '@') }}" target="_blank" rel="noopener" class="hover:text-brand-600">X / Twitter</a>@endif
            </div>
        </div>
    </header>

    <div class="mt-8 grid gap-8 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <h2 class="mb-4 flex items-center gap-2 text-xl font-black">
                <span class="h-6 w-1.5 rounded bg-brand-600"></span>
                {{ app()->getLocale() === 'bn' ? 'সর্বশেষ লেখা' : 'Latest articles' }}
            </h2>
            @if ($posts->isNotEmpty())
                <div class="grid gap-6 sm:grid-cols-2">
                    @foreach ($posts as $post)
                        @include('theme::partials.card', ['post' => $post, 'variant' => 'md', 'showExcerpt' => true])
                    @endforeach
                </div>
                <div class="mt-8">{{ $posts->links() }}</div>
            @else
                <p class="rounded-xl border border-dashed border-ink-200 py-16 text-center text-ink-400">{{ __('No articles found.') }}</p>
            @endif
        </div>

        <div class="lg:col-span-1">
            @include('theme::partials.sidebar')
        </div>
    </div>
</div>
@endsection
