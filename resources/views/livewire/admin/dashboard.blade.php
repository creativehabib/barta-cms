<div class="mx-auto max-w-7xl space-y-7">
    <div class="overflow-hidden rounded-2xl bg-gradient-to-br from-ink-900 via-ink-800 to-brand-900 p-6 text-white shadow-lg sm:p-8">
        <div class="flex flex-wrap items-center justify-between gap-5">
            <div>
                <p class="text-sm font-semibold text-brand-200">{{ now()->translatedFormat('l, F j') }}</p>
                <h1 class="mt-1 text-2xl font-black sm:text-3xl">{{ __('Welcome back, :name', ['name' => auth()->user()->name]) }}</h1>
                <p class="mt-2 max-w-2xl text-sm text-ink-300">{{ __('Manage your newsroom, audience and publishing workflow from one place.') }}</p>
            </div>
            <a href="{{ route('admin.posts.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-brand-950/30 transition hover:-translate-y-0.5 hover:bg-brand-500">
                <span class="text-xl leading-none">+</span> {{ __('Create post') }}
            </a>
        </div>
    </div>

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-black">{{ __('Overview') }}</h2>
            <p class="text-xs text-ink-400">{{ __('A live snapshot of your publication.') }}</p>
        </div>
        <a href="{{ route('home') }}" target="_blank" class="rounded-lg border border-ink-200 bg-white px-3 py-2 text-xs font-bold text-ink-600 hover:border-brand-200 hover:text-brand-600">
            {{ __('View website') }} ↗
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        @php
            $cards = [
                ['icon' => '✎', 'tone' => 'brand', 'label' => __('Published'), 'value' => $stats['published'], 'sub' => $stats['drafts'].' '.__('drafts')],
                ['icon' => '◌', 'tone' => 'amber', 'label' => __('Pending comments'), 'value' => $stats['pending_comments'], 'sub' => __('awaiting review')],
                ['icon' => '♙', 'tone' => 'blue', 'label' => __('Subscribers'), 'value' => $stats['subscribers'], 'sub' => $stats['users'].' '.__('users')],
                ['icon' => '৳', 'tone' => 'green', 'label' => __('Subscriptions'), 'value' => $stats['active_subscriptions'], 'sub' => money($stats['revenue']).' '.__('earned')],
            ];
        @endphp
        @foreach ($cards as $c)
            <div class="group rounded-2xl border border-ink-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-ink-500">{{ $c['label'] }}</p>
                        <p class="mt-2 text-3xl font-black tracking-tight text-ink-900">{{ to_bn_number($c['value']) }}</p>
                    </div>
                    <span @class([
                        'grid h-10 w-10 place-items-center rounded-xl text-lg font-black',
                        'bg-brand-50 text-brand-700' => $c['tone'] === 'brand',
                        'bg-amber-50 text-amber-700' => $c['tone'] === 'amber',
                        'bg-blue-50 text-blue-700' => $c['tone'] === 'blue',
                        'bg-green-50 text-green-700' => $c['tone'] === 'green',
                    ])>{{ $c['icon'] }}</span>
                </div>
                <p class="mt-3 border-t border-ink-50 pt-3 text-xs text-ink-400">{{ $c['sub'] }}</p>
            </div>
        @endforeach
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
