@extends('theme::layouts.app')

@section('content')
@php
    $loc = app()->getLocale();
@endphp
<div class="mx-auto max-w-7xl px-4 py-6">

    {{-- Search header + form --}}
    <header class="border-b-2 border-brand-600 pb-4">
        <h1 class="text-2xl font-black sm:text-3xl">
            @if ($query !== '')
                {{ app()->getLocale() === 'bn' ? 'অনুসন্ধান:' : 'Search:' }} <span class="text-brand-600">“{{ $query }}”</span>
            @else
                {{ app()->getLocale() === 'bn' ? 'অনুসন্ধান করুন' : 'Search' }}
            @endif
        </h1>
        <form method="GET" action="{{ route('search') }}" class="mt-4 flex max-w-xl gap-2">
            <input type="search" name="q" value="{{ $query }}" placeholder="{{ app()->getLocale() === 'bn' ? 'খবর খুঁজুন…' : 'Search news…' }}"
                   class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
            <button type="submit" class="shrink-0 rounded-lg bg-brand-600 px-5 py-2 text-sm font-bold text-white hover:bg-brand-700">
                {{ app()->getLocale() === 'bn' ? 'খুঁজুন' : 'Search' }}
            </button>
        </form>
        @if ($query !== '')
            <p class="mt-3 text-sm text-ink-400">
                {{ localized_number($results->total()) }} {{ app()->getLocale() === 'bn' ? 'টি ফলাফল পাওয়া গেছে' : 'results found' }}
            </p>
        @endif
    </header>

    <div class="mt-8 grid gap-8 lg:grid-cols-3">
        <div class="lg:col-span-2">
            @if ($results->isNotEmpty())
                <div class="divide-y divide-ink-100">
                    @foreach ($results as $post)
                        @include('theme::partials.card', ['post' => $post, 'variant' => 'list', 'showExcerpt' => true])
                    @endforeach
                </div>
                <div class="mt-8">{{ $results->links() }}</div>
            @elseif ($query !== '')
                <div class="rounded-xl border border-dashed border-ink-200 py-16 text-center">
                    <p class="text-ink-500">{{ app()->getLocale() === 'bn' ? 'কোনো ফলাফল পাওয়া যায়নি।' : 'No results found.' }}</p>
                    <p class="mt-1 text-sm text-ink-400">{{ app()->getLocale() === 'bn' ? 'অন্য কিছু লিখে আবার চেষ্টা করুন।' : 'Try a different keyword.' }}</p>
                </div>
            @endif
        </div>

        <div class="lg:col-span-1">
            @include('theme::partials.sidebar')
        </div>
    </div>
</div>
@endsection
