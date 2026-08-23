<div class="mx-auto max-w-6xl space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-black">{{ $type === 'page' ? __('Pages') : __('Posts') }}</h1>
        <a href="{{ route('admin.posts.create') }}" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white hover:bg-brand-700">
            + {{ __('New post') }}
        </a>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap gap-3 rounded-xl border border-ink-100 bg-white p-4">
        <input type="search" wire:model.live.debounce.400ms="search" placeholder="{{ __('Search titles…') }}"
               class="min-w-48 flex-1 rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
        <select wire:model.live="status" class="rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
            <option value="">{{ __('All statuses') }}</option>
            <option value="published">{{ __('Published') }}</option>
            <option value="draft">{{ __('Draft') }}</option>
            <option value="pending">{{ __('Pending') }}</option>
            <option value="scheduled">{{ __('Scheduled') }}</option>
        </select>
        <select wire:model.live="category" class="rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
            <option value="">{{ __('All categories') }}</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>
        <select wire:model.live="type" class="rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
            <option value="post">{{ __('Posts') }}</option>
            <option value="page">{{ __('Pages') }}</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-xl border border-ink-100 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-ink-100 bg-ink-50 text-xs uppercase tracking-wide text-ink-500">
                <tr>
                    <th class="px-4 py-3">{{ __('Title') }}</th>
                    <th class="px-4 py-3">{{ __('Author') }}</th>
                    <th class="px-4 py-3">{{ __('Category') }}</th>
                    <th class="px-4 py-3">{{ __('Status') }}</th>
                    <th class="px-4 py-3">{{ __('Views') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-50">
                @forelse ($posts as $post)
                    <tr class="hover:bg-ink-50/50">
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.posts.edit', $post) }}" class="font-semibold hover:text-brand-600">{{ $post->title ?: __('(untitled)') }}</a>
                            <div class="mt-0.5 flex gap-1">
                                @if ($post->is_featured) <span class="rounded bg-amber-100 px-1.5 text-xs text-amber-700">★</span> @endif
                                @if ($post->is_breaking) <span class="rounded bg-brand-100 px-1.5 text-xs text-brand-700">{{ __('breaking') }}</span> @endif
                                @if ($post->is_premium) <span class="rounded bg-purple-100 px-1.5 text-xs text-purple-700">{{ __('premium') }}</span> @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-ink-500">{{ $post->author?->name }}</td>
                        <td class="px-4 py-3 text-ink-500">{{ $post->category?->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span @class([
                                'rounded-full px-2.5 py-0.5 text-xs font-semibold',
                                'bg-green-100 text-green-700' => $post->status === 'published',
                                'bg-amber-100 text-amber-700' => $post->status === 'draft',
                                'bg-blue-100 text-blue-700' => in_array($post->status, ['pending', 'scheduled']),
                            ])>{{ __(ucfirst($post->status)) }}</span>
                        </td>
                        <td class="px-4 py-3 text-ink-500">{{ to_bn_number($post->views) }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <button wire:click="toggleFeatured({{ $post->id }})" title="{{ __('Toggle featured') }}" class="text-ink-400 hover:text-amber-500">★</button>
                                <a href="{{ route('admin.posts.edit', $post) }}" class="font-semibold text-brand-600 hover:underline">{{ __('Edit') }}</a>
                                <button wire:click="delete({{ $post->id }})" wire:confirm="{{ __('Move this post to trash?') }}" class="text-ink-400 hover:text-brand-600">{{ __('Trash') }}</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-ink-400">{{ __('No posts found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $posts->links() }}</div>
</div>
