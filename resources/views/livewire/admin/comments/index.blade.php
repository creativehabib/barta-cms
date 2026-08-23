<div class="mx-auto max-w-5xl space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-black">{{ __('Comments') }}</h1>
        <div class="flex gap-2 text-sm">
            <span class="rounded-full bg-amber-100 px-3 py-1 font-semibold text-amber-700">{{ __('Pending') }}: {{ to_bn_number($pendingCount) }}</span>
            <span class="rounded-full bg-green-100 px-3 py-1 font-semibold text-green-700">{{ __('Approved') }}: {{ to_bn_number($approvedCount) }}</span>
        </div>
    </div>

    <div class="flex flex-wrap gap-3 rounded-xl border border-ink-100 bg-white p-4">
        <input type="search" wire:model.live.debounce.400ms="search" placeholder="{{ __('Search comments…') }}"
               class="min-w-48 flex-1 rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
        <select wire:model.live="status" class="rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
            <option value="">{{ __('All statuses') }}</option>
            <option value="pending">{{ __('Pending') }}</option>
            <option value="approved">{{ __('Approved') }}</option>
            <option value="spam">{{ __('Spam') }}</option>
            <option value="trash">{{ __('Trash') }}</option>
        </select>
    </div>

    <div class="overflow-hidden rounded-xl border border-ink-100 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-ink-100 bg-ink-50 text-xs uppercase tracking-wide text-ink-500">
                <tr>
                    <th class="px-4 py-3">{{ __('Comment') }}</th>
                    <th class="px-4 py-3">{{ __('In response to') }}</th>
                    <th class="px-4 py-3">{{ __('Status') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-50">
                @forelse ($comments as $comment)
                    <tr class="hover:bg-ink-50/50 align-top">
                        <td class="px-4 py-3">
                            <div class="font-semibold">{{ $comment->authorName() }}</div>
                            @if ($comment->author_email)
                                <div class="text-xs text-ink-400">{{ $comment->author_email }}</div>
                            @endif
                            <p class="mt-1 text-ink-600">{{ str($comment->body)->limit(140) }}</p>
                            <div class="mt-1 text-xs text-ink-400">{{ $comment->created_at?->diffForHumans() }}</div>
                        </td>
                        <td class="px-4 py-3 text-ink-500">
                            @if ($comment->post)
                                <a href="{{ $comment->post->url() }}" target="_blank" class="hover:text-brand-600">{{ str($comment->post->title)->limit(40) }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span @class([
                                'rounded-full px-2.5 py-0.5 text-xs font-semibold',
                                'bg-green-100 text-green-700' => $comment->status === 'approved',
                                'bg-amber-100 text-amber-700' => $comment->status === 'pending',
                                'bg-red-100 text-red-700' => $comment->status === 'spam',
                                'bg-ink-100 text-ink-600' => $comment->status === 'trash',
                            ])>{{ __(ucfirst($comment->status)) }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if ($comment->status !== 'approved')
                                    <button wire:click="approve({{ $comment->id }})" class="font-semibold text-green-600 hover:underline">{{ __('Approve') }}</button>
                                @endif
                                @if ($comment->status !== 'spam')
                                    <button wire:click="markSpam({{ $comment->id }})" class="text-ink-400 hover:text-amber-600">{{ __('Spam') }}</button>
                                @endif
                                <button wire:click="delete({{ $comment->id }})" wire:confirm="{{ __('Delete this comment?') }}" class="text-ink-400 hover:text-brand-600">{{ __('Delete') }}</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-10 text-center text-ink-400">{{ __('No comments found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $comments->links() }}</div>
</div>
