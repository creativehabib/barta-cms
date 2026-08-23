<div class="mx-auto max-w-5xl space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-black">{{ __('Subscribers') }}</h1>
        <button wire:click="create" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white hover:bg-brand-700">+ {{ __('Add subscriber') }}</button>
    </div>

    <div class="grid grid-cols-3 gap-3">
        <div class="rounded-xl border border-ink-100 bg-white p-4">
            <div class="text-xs uppercase tracking-wide text-ink-400">{{ __('Total') }}</div>
            <div class="mt-1 text-2xl font-black">{{ to_bn_number($total) }}</div>
        </div>
        <div class="rounded-xl border border-ink-100 bg-white p-4">
            <div class="text-xs uppercase tracking-wide text-ink-400">{{ __('Subscribed') }}</div>
            <div class="mt-1 text-2xl font-black text-green-600">{{ to_bn_number($subscribedCount) }}</div>
        </div>
        <div class="rounded-xl border border-ink-100 bg-white p-4">
            <div class="text-xs uppercase tracking-wide text-ink-400">{{ __('Pending') }}</div>
            <div class="mt-1 text-2xl font-black text-amber-600">{{ to_bn_number($pendingCount) }}</div>
        </div>
    </div>

    <div class="flex flex-wrap gap-3 rounded-xl border border-ink-100 bg-white p-4">
        <input type="search" wire:model.live.debounce.400ms="search" placeholder="{{ __('Search email or name…') }}"
               class="min-w-48 flex-1 rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
        <select wire:model.live="status" class="rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
            <option value="">{{ __('All statuses') }}</option>
            <option value="subscribed">{{ __('Subscribed') }}</option>
            <option value="pending">{{ __('Pending') }}</option>
            <option value="unsubscribed">{{ __('Unsubscribed') }}</option>
        </select>
    </div>

    <div class="overflow-hidden rounded-xl border border-ink-100 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-ink-100 bg-ink-50 text-xs uppercase tracking-wide text-ink-500">
                <tr>
                    <th class="px-4 py-3">{{ __('Email') }}</th>
                    <th class="px-4 py-3">{{ __('Name') }}</th>
                    <th class="px-4 py-3">{{ __('Language') }}</th>
                    <th class="px-4 py-3">{{ __('Status') }}</th>
                    <th class="px-4 py-3">{{ __('Joined') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-50">
                @forelse ($subscribers as $sub)
                    <tr class="hover:bg-ink-50/50">
                        <td class="px-4 py-3 font-semibold">{{ $sub->email }}</td>
                        <td class="px-4 py-3 text-ink-500">{{ $sub->name ?: '—' }}</td>
                        <td class="px-4 py-3 text-ink-500">{{ locale_name($sub->locale) }}</td>
                        <td class="px-4 py-3">
                            <span @class([
                                'rounded-full px-2.5 py-0.5 text-xs font-semibold',
                                'bg-green-100 text-green-700' => $sub->status === 'subscribed',
                                'bg-amber-100 text-amber-700' => $sub->status === 'pending',
                                'bg-ink-100 text-ink-600' => $sub->status === 'unsubscribed',
                            ])>{{ __(ucfirst($sub->status)) }}</span>
                        </td>
                        <td class="px-4 py-3 text-ink-500">{{ $sub->created_at?->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-right">
                            <button wire:click="delete({{ $sub->id }})" wire:confirm="{{ __('Remove this subscriber?') }}" class="text-ink-400 hover:text-brand-600">{{ __('Delete') }}</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-ink-400">{{ __('No subscribers yet.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $subscribers->links() }}</div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" wire:click.self="$set('showModal', false)">
            <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
                <h2 class="mb-4 text-lg font-black">{{ __('Add subscriber') }}</h2>
                <form wire:submit="save" class="space-y-3">
                    <div>
                        <label class="mb-1 block text-sm font-semibold">{{ __('Email') }}</label>
                        <input type="email" wire:model="email" class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500">
                        @error('email') <p class="mt-1 text-sm text-brand-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold">{{ __('Name') }}</label>
                        <input type="text" wire:model="name" class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold">{{ __('Language') }}</label>
                        <select wire:model="locale" class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500">
                            @foreach (barta_locales() as $loc)
                                <option value="{{ $loc }}">{{ locale_name($loc) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="$set('showModal', false)" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-semibold hover:bg-ink-50">{{ __('Cancel') }}</button>
                        <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white hover:bg-brand-700">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
