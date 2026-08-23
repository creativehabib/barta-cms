<div class="mx-auto max-w-5xl space-y-5">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-black">{{ __('Categories') }}</h1>
        <button wire:click="create" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white hover:bg-brand-700">+ {{ __('New category') }}</button>
    </div>

    <div class="overflow-hidden rounded-xl border border-ink-100 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-ink-100 bg-ink-50 text-xs uppercase tracking-wide text-ink-500">
                <tr>
                    <th class="px-4 py-3">{{ __('Name') }}</th>
                    <th class="px-4 py-3">{{ __('Slug') }}</th>
                    <th class="px-4 py-3">{{ __('Posts') }}</th>
                    <th class="px-4 py-3">{{ __('In menu') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-50">
                @forelse ($categories as $cat)
                    <tr class="hover:bg-ink-50/50">
                        <td class="px-4 py-3">
                            <span class="inline-block h-3 w-3 rounded-full align-middle" style="background: {{ $cat->color ?: '#c81420' }}"></span>
                            <span class="font-semibold">{{ $cat->getTranslation('name', app()->getLocale(), false) }}</span>
                            @if ($cat->parent) <span class="text-xs text-ink-400">↳ {{ $cat->parent->getTranslation('name', app()->getLocale(), false) }}</span> @endif
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-ink-500">{{ $cat->slug }}</td>
                        <td class="px-4 py-3 text-ink-500">{{ to_bn_number($cat->posts_count) }}</td>
                        <td class="px-4 py-3">{{ $cat->show_in_menu ? '✓' : '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            <button wire:click="edit({{ $cat->id }})" class="font-semibold text-brand-600 hover:underline">{{ __('Edit') }}</button>
                            <button wire:click="delete({{ $cat->id }})" wire:confirm="{{ __('Delete this category?') }}" class="ml-2 text-ink-400 hover:text-brand-600">{{ __('Delete') }}</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-ink-400">{{ __('No categories yet.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" wire:click.self="$set('showModal', false)">
            <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
                <h2 class="mb-4 text-lg font-black">{{ $editingId ? __('Edit category') : __('New category') }}</h2>
                <form wire:submit="save" class="space-y-3">
                    @foreach (barta_locales() as $loc)
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Name') }} ({{ locale_name($loc) }})</label>
                            <input type="text" wire:model="name.{{ $loc }}" class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500">
                            @error('name.'.$loc) <p class="mt-1 text-sm text-brand-600">{{ $message }}</p> @enderror
                        </div>
                    @endforeach

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Parent') }}</label>
                            <select wire:model="parent_id" class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                                <option value="">{{ __('— none —') }}</option>
                                @foreach ($categories as $c)
                                    @if ($c->id !== $editingId)
                                        <option value="{{ $c->id }}">{{ $c->getTranslation('name', app()->getLocale(), false) }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Color') }}</label>
                            <input type="color" wire:model="color" class="h-10 w-full rounded-lg border-ink-200">
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-semibold">{{ __('Slug') }}</label>
                        <input type="text" wire:model="slug" class="w-full rounded-lg border-ink-200 font-mono text-sm focus:border-brand-500 focus:ring-brand-500" placeholder="{{ __('auto') }}">
                    </div>

                    <div class="flex gap-4 text-sm">
                        <label class="flex items-center gap-2"><input type="checkbox" wire:model="show_in_menu" class="rounded border-ink-300 text-brand-600"> {{ __('Show in menu') }}</label>
                        <label class="flex items-center gap-2"><input type="checkbox" wire:model="is_active" class="rounded border-ink-300 text-brand-600"> {{ __('Active') }}</label>
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
