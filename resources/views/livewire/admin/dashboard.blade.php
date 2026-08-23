<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-black">{{ __('Dashboard') }}</h1>
        <a href="{{ route('admin.posts.create') }}" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white hover:bg-brand-700">
            + {{ __('New post') }}
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        @php
            $cards = [
                ['label' => __('Published'), 'value' => $stats['published'], 'sub' => $stats['drafts'].' '.__('drafts')],
                ['label' => __('Pending comments'), 'value' => $stats['pending_comments'], 'sub' => __('awaiting review')],
                ['label' => __('Subscribers'), 'value' => $stats['subscribers'], 'sub' => $stats['users'].' '.__('users')],
                ['label' => __('Active subscriptions'), 'value' => $stats['active_subscriptions'], 'sub' => money($stats['revenue']).' '.__('earned')],
            ];
        @endphp
        @foreach ($cards as $c)
            <div class="rounded-xl border border-ink-100 bg-white p-5">
                <p class="text-sm font-medium text-ink-500">{{ $c['label'] }}</p>
                <p class="mt-1 text-3xl font-black text-ink-900">{{ to_bn_number($c['value']) }}</p>
                <p class="mt-1 text-xs text-ink-400">{{ $c['sub'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Recent posts --}}
        <div class="rounded-xl border border-ink-100 bg-white lg:col-span-2">
            <div class="flex items-center justify-between border-b border-ink-100 px-5 py-3">
                <h2 class="font-bold">{{ __('Recent posts') }}</h2>
                <a href="{{ route('admin.posts.index') }}" class="text-sm font-semibold text-brand-600 hover:underline">{{ __('View all') }}</a>
            </div>
            <div class="divide-y divide-ink-50">
                @forelse ($recentPosts as $post)
                    <div class="flex items-center justify-between gap-3 px-5 py-3">
                        <div class="min-w-0">
                            <a href="{{ route('admin.posts.edit', $post) }}" class="block truncate font-semibold hover:text-brand-600">{{ $post->title ?: __('(untitled)') }}</a>
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
        <div class="rounded-xl border border-ink-100 bg-white">
            <div class="flex items-center justify-between border-b border-ink-100 px-5 py-3">
                <h2 class="font-bold">{{ __('Recent comments') }}</h2>
                <a href="{{ route('admin.comments.index') }}" class="text-sm font-semibold text-brand-600 hover:underline">{{ __('Moderate') }}</a>
            </div>
            <div class="divide-y divide-ink-50">
                @forelse ($recentComments as $comment)
                    <div class="px-5 py-3">
                        <p class="text-sm text-ink-700 line-clamp-2">{{ $comment->body }}</p>
                        <p class="mt-1 text-xs text-ink-400">{{ $comment->author_name ?: $comment->user?->name }} · {{ $comment->post?->title }}</p>
                    </div>
                @empty
                    <p class="px-5 py-8 text-center text-sm text-ink-400">{{ __('No comments yet.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
