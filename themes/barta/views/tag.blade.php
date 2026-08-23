@extends('theme::layouts.app')

@section('content')
@php($loc = app()->getLocale())
<div class="mx-auto max-w-7xl px-4 py-6">

    <header class="border-b-2 border-brand-600 pb-3">
        <p class="text-xs font-bold uppercase tracking-widest text-brand-600">{{ app()->getLocale() === 'bn' ? 'ট্যাগ' : 'Tag' }}</p>
        <h1 class="mt-1 text-3xl font-black sm:text-4xl">#{{ $tag->getTranslation('name', $loc, false) }}</h1>
    </header>

    <div class="mt-8 grid gap-8 lg:grid-cols-3">
        <div class="lg:col-span-2">
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
