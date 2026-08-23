{{--
    Renders a single widget by type. Params: $widget (Widget model).
    Types: recent_posts, popular_posts, category_list, tag_cloud, newsletter, html, ad
--}}
@php
    $loc = app()->getLocale();
    $title = $widget->getTranslation('title', $loc, false);
    $count = (int) ($widget->setting('count', 5) ?: 5);
@endphp

@if ($widget->type === 'html')
    <section class="mb-6">
        @if ($title)<h3 class="mb-2 border-l-4 border-brand-600 pl-2 text-lg font-black">{{ $title }}</h3>@endif
        <div class="prose prose-sm max-w-none">{!! $widget->setting('html') !!}</div>
    </section>

@elseif ($widget->type === 'ad')
    <section class="mb-6">
        @include('theme::partials.ad', ['slot' => $widget->setting('slot', 'sidebar'), 'class' => 'text-center'])
    </section>

@elseif ($widget->type === 'newsletter')
    <section class="mb-6">
        @include('theme::partials.newsletter', ['heading' => $title])
    </section>

@elseif ($widget->type === 'category_list')
    @php($cats = \App\Models\Category::active()->parents()->withCount(['posts' => fn ($q) => $q->published()])->orderBy('position')->get())
    <section class="mb-6">
        <h3 class="mb-2 border-l-4 border-brand-600 pl-2 text-lg font-black">{{ $title ?: __('Categories') }}</h3>
        <ul class="divide-y divide-ink-100 rounded-lg border border-ink-100">
            @foreach ($cats as $cat)
                <li class="flex items-center justify-between px-3 py-2 text-sm hover:bg-ink-50">
                    <a href="{{ $cat->url() }}" class="font-semibold hover:text-brand-600">{{ $cat->getTranslation('name', $loc, false) }}</a>
                    <span class="rounded-full bg-ink-100 px-2 text-xs text-ink-500">{{ localized_number($cat->posts_count) }}</span>
                </li>
            @endforeach
        </ul>
    </section>

@elseif ($widget->type === 'tag_cloud')
    @php($tags = \App\Models\Tag::withCount('posts')->orderByDesc('posts_count')->take($count ?: 20)->get()->filter(fn ($t) => $t->posts_count > 0))
    @if ($tags->isNotEmpty())
        <section class="mb-6">
            <h3 class="mb-2 border-l-4 border-brand-600 pl-2 text-lg font-black">{{ $title ?: __('Tags') }}</h3>
            <div class="flex flex-wrap gap-2">
                @foreach ($tags as $tag)
                    <a href="{{ $tag->url() }}" class="rounded-full bg-ink-100 px-3 py-1 text-xs font-semibold text-ink-600 hover:bg-brand-600 hover:text-white">
                        {{ $tag->getTranslation('name', $loc, false) }}
                    </a>
                @endforeach
            </div>
        </section>
    @endif

@elseif ($widget->type === 'popular_posts' || $widget->type === 'recent_posts')
    @php
        $isPopular = $widget->type === 'popular_posts';
        // Use a widget-local name (not $posts) so it can never be shadowed by
        // a variable of the same name inherited from the including view's scope.
        $widgetPosts = \App\Models\Post::published()
            ->when(
                $isPopular,
                fn ($q) => $q->orderByDesc('views'),
                fn ($q) => $q->latest('published_at')
            )
            ->take($count)
            ->get();
    @endphp
    @if ($widgetPosts->isNotEmpty())
        <section class="mb-6">
            <h3 class="mb-2 border-l-4 border-brand-600 pl-2 text-lg font-black">
                {{ $title ?: ($isPopular ? __('Popular') : __('Recent posts')) }}
            </h3>
            <div class="divide-y divide-ink-100">
                @foreach ($widgetPosts as $index => $post)
                    <div class="flex items-center gap-3">
                        @if ($isPopular)
                            <span class="text-2xl font-black text-ink-200">{{ localized_number($index + 1) }}</span>
                        @endif
                        <div class="min-w-0 flex-1">@include('theme::partials.card', ['post' => $post, 'variant' => 'list'])</div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
@endif
