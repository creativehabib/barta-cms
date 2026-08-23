<div class="mx-auto max-w-5xl space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-black">{{ __('Tags') }}</h1>
        <button wire:click="create" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white hover:bg-brand-700">+ {{ __('New tag') }}</button>
    </div>

    <div class="rounded-xl border border-ink-100 bg-white p-4">
        <input type="search" wire:model.live.debounce.400ms="search" placeholder="{{ __('Search tags…') }}"
               class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
    </div>

    <div class="overflow-hidden rounded-xl border border-ink-100 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-ink-100 bg-ink-50 text-xs uppercase tracking-wide text-ink-500">
                <tr>
                    <th class="px-4 py-3">{{ __('Name') }}</th>
                    <th class="px-4 py-3">{{ __('Slug') }}</th>
                    <th class="px-4 py-3">{{ __('Posts') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-50">
                @forelse ($tags as $tag)
                    <tr class="hover:bg-ink-50/50">
                        <td class="px-4 py-3 font-semibold">{{ $tag->name }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-ink-500">{{ $tag->slug }}</td>
                        <td class="px-4 py-3 text-ink-500">{{ to_bn_number($tag->posts_count) }}</td>
                        <td class="px-4 py-3 text-right">
                            <button wire:click="edit({{ $tag->id }})" class="font-semibold text-brand-600 hover:underline">{{ __('Edit') }}</button>
                            <button wire:click="delete({{ $tag->id }})" wire:confirm="{{ __('Delete this tag?') }}" class="ml-2 text-ink-400 hover:text-brand-600">{{ __('Delete') }}</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-10 text-center text-ink-400">{{ __('No tags yet.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $tags->links() }}</div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" wire:click.self="$set('showModal', false)">
            <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
                <h2 class="mb-4 text-lg font-black">{{ $editingId ? __('Edit tag') : __('New tag') }}</h2>
                <form wire:submit="save" class="space-y-3">
                    @foreach (barta_locales() as $loc)
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Name') }} ({{ locale_name($loc) }})</label>
                            <input type="text" wire:model="name.{{ $loc }}" class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500">
                            @error('name.'.$loc) <p class="mt-1 text-sm text-brand-600">{{ $message }}</p> @enderror
                        </div>
                    @endforeach

                    <div>
                        <label class="mb-1 block text-sm font-semibold">{{ __('Slug') }}</label>
                        <input type="text" wire:model="slug" class="w-full rounded-lg border-ink-200 font-mono text-sm focus:border-brand-500 focus:ring-brand-500" placeholder="{{ __('auto') }}">
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
