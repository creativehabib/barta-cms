<div class="mx-auto max-w-7xl space-y-7">
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-wrap items-center justify-between gap-5">
            <div class="flex items-center gap-4">
                <img src="{{ auth()->user()->avatarUrl() }}" alt="" class="h-14 w-14 rounded-2xl object-cover ring-4 ring-brand-50">
                <div>
                    <h1 class="text-xl font-black text-ink-900 sm:text-2xl">{{ __('Welcome, :name', ['name' => auth()->user()->name]) }}</h1>
                    <p class="mt-1 text-sm text-slate-400">{{ __('Here is what is happening in your newsroom today.') }}</p>
                </div>
            </div>
            <div class="text-left sm:text-right"><p class="text-xs font-bold text-slate-600">{{ now()->translatedFormat('l, j F Y') }}</p><p class="mt-1 text-[10px] capitalize text-slate-400">{{ auth()->user()->getRoleNames()->first() ?? __('Staff') }}</p></div>
        </div>
    </div>

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-black">{{ __('Overview') }}</h2>
            <p class="text-xs text-ink-400">{{ __('A live snapshot of your publication.') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('home') }}" target="_blank" class="hidden rounded-lg border border-ink-200 bg-white px-3 py-2 text-xs font-bold text-ink-600 hover:border-brand-200 hover:text-brand-600 sm:block">{{ __('View website') }} ↗</a>
            <a href="{{ route('admin.posts.create') }}" class="rounded-lg bg-brand-600 px-3 py-2 text-xs font-bold text-white shadow-sm hover:bg-brand-700">+ {{ __('Create post') }}</a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-6">
        @php
            $cards = [
                ['icon' => '▤', 'tone' => 'brand', 'label' => __('Published'), 'value' => $stats['published'], 'sub' => __('Live stories')],
                ['icon' => '✎', 'tone' => 'amber', 'label' => __('Drafts'), 'value' => $stats['drafts'], 'sub' => __('In progress')],
                ['icon' => '◌', 'tone' => 'amber', 'label' => __('Review'), 'value' => $stats['pending_comments'], 'sub' => __('Pending comments')],
                ['icon' => '◉', 'tone' => 'blue', 'label' => __('Total views'), 'value' => $stats['views'], 'sub' => __('All-time reach')],
                ['icon' => '♙', 'tone' => 'green', 'label' => __('Subscribers'), 'value' => $stats['subscribers'], 'sub' => $stats['users'].' '.__('users')],
                ['icon' => '□', 'tone' => 'brand', 'label' => __('Categories'), 'value' => $stats['categories'], 'sub' => __('Content sections')],
            ];
        @endphp
        @foreach ($cards as $c)
            <div class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-ink-500">{{ $c['label'] }}</p>
                        <p class="mt-1 text-2xl font-black tracking-tight text-ink-900">{{ localized_number($c['value']) }}</p>
                    </div>
                    <span @class([
                        'grid h-10 w-10 place-items-center rounded-xl text-lg font-black',
                        'bg-brand-50 text-brand-700' => $c['tone'] === 'brand',
                        'bg-amber-50 text-amber-700' => $c['tone'] === 'amber',
                        'bg-blue-50 text-blue-700' => $c['tone'] === 'blue',
                        'bg-green-50 text-green-700' => $c['tone'] === 'green',
                    ])>{{ $c['icon'] }}</span>
                </div>
                <p class="mt-2 truncate text-[11px] text-ink-400">{{ $c['sub'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid gap-5 xl:grid-cols-3">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-2">
            <header class="border-b border-slate-100 px-5 py-4"><h2 class="font-black">{{ __('Publishing activity') }}</h2><p class="text-xs text-slate-400">{{ __('Posts published during the last 14 days') }}</p></header>
            <div class="flex h-64 items-end gap-2 overflow-hidden px-4 pb-4 pt-8 sm:gap-3">
                @foreach ($publishingActivity as $day)
                    <div class="group flex h-full min-w-0 flex-1 flex-col items-center justify-end gap-2">
                        <span class="opacity-0 text-[10px] font-bold text-brand-700 transition group-hover:opacity-100">{{ localized_number($day['count']) }}</span>
                        <div class="w-full rounded-t-md bg-gradient-to-t from-brand-600 to-brand-400 transition-all group-hover:from-brand-700" style="height: {{ max(3, ($day['count'] / $maxActivity) * 85) }}%"></div>
                        <span class="hidden whitespace-nowrap text-[9px] text-slate-400 sm:block">{{ $day['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </section>
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="border-b border-slate-100 px-5 py-4"><h2 class="font-black">{{ __('News by category') }}</h2><p class="text-xs text-slate-400">{{ __('Published content distribution') }}</p></header>
            <div class="space-y-4 p-5">
                @forelse ($categoryStats as $category)
                    @php
                        $categoryTotal = max(1, $categoryStats->max('posts_count'));
                    @endphp
                    <div>
                        <div class="mb-1.5 flex justify-between gap-3 text-xs"><span class="truncate font-bold">{{ $category->getTranslation('name', app()->getLocale(), false) }}</span><span class="text-slate-400">{{ localized_number($category->posts_count) }}</span></div>
                        <div class="h-2 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-brand-500" style="width: {{ ($category->posts_count / $categoryTotal) * 100 }}%"></div></div>
                    </div>
                @empty
                    <p class="py-12 text-center text-sm text-slate-400">{{ __('No category data yet.') }}</p>
                @endforelse
            </div>
        </section>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Recent posts --}}
        <div class="overflow-hidden rounded-2xl border border-ink-100 bg-white shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between border-b border-ink-100 px-5 py-4">
                <h2 class="font-bold">{{ __('Recent posts') }}</h2>
                <a href="{{ route('admin.posts.index') }}" class="text-sm font-semibold text-brand-600 hover:underline">{{ __('View all') }}</a>
            </div>
            <div class="divide-y divide-ink-50">
                @forelse ($recentPosts as $post)
                    <div class="flex items-center justify-between gap-3 px-5 py-3.5 transition hover:bg-ink-50/70">
                        <div class="min-w-0">
                            <a href="{{ route('admin.posts.edit', $post) }}" class="block truncate font-bold hover:text-brand-600">{{ $post->getTranslation('title', app()->getLocale(), false) ?: __('(untitled)') }}</a>
                            <p class="text-xs text-ink-400">{{ $post->author?->name }} · {{ $post->created_at->diffForHumans() }}</p>
                        </div>
                        <span @class([
                            'shrink-0 rounded-full px-2.5 py-0.5 text-xs font-semibold',
                            'bg-green-100 text-green-700' => $post->status === 'published',
                            'bg-amber-100 text-amber-700' => $post->status === 'draft',
                            'bg-ink-100 text-ink-600' => ! in_array($post->status, ['published', 'draft']),
                        ])>{{ __(ucfirst($post->status)) }}</span>
                    </div>
                @empty
                    <p class="px-5 py-8 text-center text-sm text-ink-400">{{ __('No posts yet.') }}</p>
                @endforelse
            </div>
        </div>

        {{-- Recent comments --}}
        <div class="overflow-hidden rounded-2xl border border-ink-100 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-ink-100 px-5 py-4">
                <h2 class="font-bold">{{ __('Recent comments') }}</h2>
                <a href="{{ route('admin.comments.index') }}" class="text-sm font-semibold text-brand-600 hover:underline">{{ __('Moderate') }}</a>
            </div>
            <div class="divide-y divide-ink-50">
                @forelse ($recentComments as $comment)
                    <div class="px-5 py-3.5 transition hover:bg-ink-50/70">
                        <p class="text-sm text-ink-700 line-clamp-2">{{ $comment->body }}</p>
                        <p class="mt-1 text-xs text-ink-400">{{ $comment->author_name ?: $comment->user?->name }} · {{ $comment->post?->getTranslation('title', app()->getLocale(), false) }}</p>
                    </div>
                @empty
                    <p class="px-5 py-8 text-center text-sm text-ink-400">{{ __('No comments yet.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
