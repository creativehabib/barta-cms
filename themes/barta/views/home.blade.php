@extends('theme::layouts.app')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6">

    {{-- Home top ad + widget area --}}
    @include('theme::partials.ad', ['slot' => 'home-hero', 'class' => 'mb-6 text-center'])
    @include('theme::partials.widget-area', ['area' => 'home-top'])

    @php
        $lead = $featured->isNotEmpty() ? $featured : $latest->getCollection()->take(5);
        $leadMain = $lead->first();
        $leadRest = $lead->skip(1)->take(4);
    @endphp

    {{-- ============ HERO ============ --}}
    @if ($leadMain)
        <section class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                @include('theme::partials.card', ['post' => $leadMain, 'variant' => 'hero', 'showExcerpt' => true])
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
                @foreach ($leadRest as $post)
                    @include('theme::partials.card', ['post' => $post, 'variant' => 'overlay'])
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============ BODY: main + sidebar ============ --}}
    <div class="mt-10 grid gap-8 lg:grid-cols-3">
        <div class="space-y-10 lg:col-span-2">

            {{-- Latest feed --}}
            <section>
                <h2 class="mb-4 flex items-center gap-2 text-xl font-black">
                    <span class="h-6 w-1.5 rounded bg-brand-600"></span>
                    {{ app()->getLocale() === 'bn' ? 'সর্বশেষ খবর' : 'Latest news' }}
                </h2>
                <div class="grid gap-6 sm:grid-cols-2">
                    @foreach ($latest->take(6) as $post)
                        @include('theme::partials.card', ['post' => $post, 'variant' => 'md', 'showExcerpt' => true])
                    @endforeach
                </div>
            </section>

            {{-- In-content ad --}}
            @include('theme::partials.ad', ['slot' => 'in-article', 'class' => 'text-center'])

            {{-- Category rails --}}
            @foreach ($sections as $section)
                @php
                    $rail = $section->latestPosts;
                @endphp
                <section>
                    <h2 class="mb-4 flex items-center justify-between border-b-2 border-brand-600 pb-1">
                        <a href="{{ $section->url() }}" class="text-xl font-black hover:text-brand-700">
                            {{ $section->getTranslation('name', app()->getLocale(), false) }}
                        </a>
                        <a href="{{ $section->url() }}" class="text-sm font-semibold text-brand-600 hover:underline">{{ __('View all') }} →</a>
                    </h2>
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>@include('theme::partials.card', ['post' => $rail->first(), 'variant' => 'md', 'showExcerpt' => true])</div>
                        <div class="divide-y divide-ink-100">
                            @foreach ($rail->skip(1) as $post)
                                @include('theme::partials.card', ['post' => $post, 'variant' => 'list'])
                            @endforeach
                        </div>
                    </div>
                </section>
            @endforeach

            {{-- More from latest (paginated) --}}
            @if ($latest->count() > 6)
                <section>
                    <h2 class="mb-4 flex items-center gap-2 text-xl font-black">
                        <span class="h-6 w-1.5 rounded bg-brand-600"></span>
                        {{ app()->getLocale() === 'bn' ? 'আরও খবর' : 'More stories' }}
                    </h2>
                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($latest->slice(6) as $post)
                            @include('theme::partials.card', ['post' => $post, 'variant' => 'md'])
                        @endforeach
                    </div>
                    <div class="mt-6">{{ $latest->links() }}</div>
                </section>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1">
            @include('theme::partials.sidebar')
        </div>
    </div>

    {{-- Home bottom widget area --}}
    @include('theme::partials.widget-area', ['area' => 'home-bottom'])
    @include('theme::partials.ad', ['slot' => 'footer', 'class' => 'mt-8 text-center'])
</div>
@endsection
