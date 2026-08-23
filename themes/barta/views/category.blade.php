@extends('theme::layouts.app')

@section('content')
@php($loc = app()->getLocale())
<div class="mx-auto max-w-7xl px-4 py-6">

    {{-- Archive header --}}
    <header class="border-b-2 border-brand-600 pb-3">
        <p class="text-xs font-bold uppercase tracking-widest text-brand-600">{{ app()->getLocale() === 'bn' ? 'বিভাগ' : 'Category' }}</p>
        <h1 class="mt-1 text-3xl font-black sm:text-4xl">{{ $category->getTranslation('name', $loc, false) }}</h1>
        @if ($desc = $category->getTranslation('description', $loc, false))
            <p class="mt-2 max-w-2xl text-ink-500">{{ $desc }}</p>
        @endif

        @if ($children->isNotEmpty())
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach ($children as $child)
                    <a href="{{ $child->url() }}" class="rounded-full bg-ink-100 px-3 py-1 text-sm font-semibold text-ink-600 hover:bg-brand-600 hover:text-white">
                        {{ $child->getTranslation('name', $loc, false) }}
                    </a>
                @endforeach
            </div>
        @endif
    </header>

    <div class="mt-8 grid gap-8 lg:grid-cols-3">
        <div class="lg:col-span-2">
            @if ($posts->isNotEmpty())
                {{-- Lead story --}}
                <div class="mb-8">
                    @include('theme::partials.card', ['post' => $posts->first(), 'variant' => 'hero', 'showExcerpt' => true])
                </div>
                {{-- Rest --}}
                <div class="grid gap-6 sm:grid-cols-2">
                    @foreach ($posts->slice(1) as $post)
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
